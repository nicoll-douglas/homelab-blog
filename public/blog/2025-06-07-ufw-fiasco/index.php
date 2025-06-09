<?php
require __DIR__ . "/../../../src/partials/head.php";
?>

<section>
  <h2>The Problem</h2>
  <p>So, I encountered an interesting situation recently with <code>ufw</code>. This is what my current <code>ufw</code> ruleset looks like:</p>
  <code><?php $code = <<<BASH
     To                     Action      From
     --                     ------      ----
[ 1] Nginx HTTP             ALLOW IN    Anywhere
[ 2] 22/tcp                 ALLOW IN    192.168.10.6
[ 3] 222/tcp                ALLOW IN    192.168.10.6
[ 4] 3000/tcp               ALLOW IN    192.168.10.6
[ 5] Nginx HTTP (v6)        ALLOW IN    Anywhere (v6)
BASH;
        echo $code ?></code>
  <p>Since setting Gitea up, I changed and added some rules to restrict access to my machine on port 3000 so that only my main machine (IP 192.168.1.6) could have access to Gitea. I then tried using a different device (my phone) to see if the firewall rules were applying and surprisingly, no they weren't. I was able to access Gitea from my phone even though the firewall should only allow access from my main machine in theory. I was a bit puzzled by this because in my mind, the purpose of a firewall is to sit in front of your server and manage port access as you dictate. So the fact that it wasn't behaving as I expected was alarming.</p>

  <p>I did some research on exactly how <code>ufw</code> works and learnt that apparently under the hood it manipulates the <code>iptables</code> on your system. I also learnt (with the help of GPT to analyse my iptables) that Docker also manipulates <code>iptables</code> when delegating host ports and port forwarding to containers. This can in turn produce some conflicts in the <code>iptables</code> and unexpected behaviour when it comes to port access as I observed. This incompatibility between Docker and <code>ufw</code> is mentioned in the <a href="https://docs.docker.com/engine/network/packet-filtering-firewalls/#docker-and-ufw">Docker docs</a> and is also a well known issue amongst the community it seems.</p>

  <p>Annoyingly, I would now have to find a way to fix this as my firewall wasn't actually behaving like a firewall or try something else.</p>
</section>

<section>
  <h2>iptables</h2>
  <p>In the end I did find a solution, however before I talk about that I want to go into <code>iptables</code> and Linux packet filtering a bit to give a high-level overview of what I learnt.</p>

  <p><code>iptables</code> is a Linux utility that lets you manipulate the underlying packet filtering framework in Linux. As suggested by the name, an <code>iptables</code> setup typically consists of several tables, either provided by the system, or by the user that define several rules which dictate what happens to incoming and outgoing network packets. The Linux system will route the packet through tables such as these and check the rules to see if they match the conditions of the packet and in turn whether to apply them. For example, a rule with a DROP action that captures a network packet will drop the connection. A rule with an ACCEPT action that captures a packet will let it continue on its way. As far as I've been concerned when it comes to <code>ufw</code> and firewalls, the main interest in <code>iptables</code> has been the incoming packet flow so I will limit the scope of <code>iptables</code> to that for now.</p>

  <p>Generally speaking, there's two types of incoming packets that can flow through the system. Either an INPUT-type packet or a FORWARD-type packet. An INPUT-type packet is a packet that is addressed to a service on the underlying host. A FORWARD-type packet is a packet that passes through the system and that is addressed to a service on a different IP than the host (the host has to <em>forward</em> the packet). These each have their own tables in <code>iptables</code> in order to apply their necessary rules. Before the system sends an incoming packet through one of these tables, it has to determine whether the packet is an INPUT-type or a FORWARD-type. This is usually done in a system-provided table labelled "PREROUTING". In the pre-routing table, there may be rules determining whether a certain packet should be forwarded and passed through the FORWARD table, or passed through the INPUT table to access a service on the host.</p>

  <p>With basic services like SSH on port 22, or Nginx serving a webpage on port 80, these are services that live on the actual host itself. And so rules that are applied to these services are usually part of the INPUT table. However, for things like Docker containers, the behaviour is a bit different. When you map a host port to a container port like "222:22", what Docker does is open up the system port (222 in this case) and adds rules to <code>iptables</code> that tell it to forward any requests to the Docker container (the destination will be something like 172.X.X.X:22 on a separate Docker network in the system). Docker does this in the PREROUTING table and so, rules that Docker apply occur <em>very</em> early on in the packet flow and re-route it through the FORWARD table. However, <code>ufw</code> typically applies rules in tables like FORWARD or INPUT that come after the PREROUTING table, thus the source of our problems. <code>ufw</code> rules that affect the underlying INPUT table won't affect Docker containers (because Docker engages in forwarding behaviour usually), and <code>ufw</code> rules that affect the underlying FORWARD table have no guarantee of applying before or after rules supplied by Docker.</p>

  <p>I was under the wrong presumption that "ALLOW IN" ufw rules affect port access before any other behaviour on the system, and that those rules affect all kinds of network packets (whose differences I wasn't aware of until now). But now that we have a good grasp of what the hell is going on under the hood, I can proceed to discuss the solution that I came across.</p>
</section>

<section>
  <h2>The Solution</h2>
  <p>Since this incompability between Docker and <code>ufw</code> is well known, I discovered that there is a popular workaround provided by this <a href="https://github.com/chaifeng/ufw-docker">GitHub repo</a> (credits to <a href="https://github.com/chaifeng">chaifeng</a>). Initially when I discovered this, I understood what it was trying to achieve but vaguely understood how. But now that I know how <code>iptables</code> work, it makes sense. Essentially, the idea is to prioritise filters provided to <code>iptables</code> by <code>ufw</code> earlier in the routing process and deny all other unsolicited connections before they reach Docker-provided rules. Docker will still expose system ports, but by default any external connections intended for Docker containers will be dropped in the <code>iptables</code> flow unless we specifically allow them with <code>ufw</code> rules—the wanted behaviour.</p>

  <p>I followed the steps in the setup in the repo which required me to add some extra configuration to the <code>/etc/ufw/after.rules</code> file. <a href="https://github.com/nicoll-douglas/homelab/blob/cc0880bbf29377816d28e7ce88cefc9a028cfc59/config/ufw/ufw-docker">This</a> is the configuration I ended up adding. I then restarted the firewall and re-added my rules. My rules failed to work again and this point is where I had to spend a couple hours debugging and diving deep into <code>iptables</code> in order to understand what I explained in the previous section. One critical issue I was having was with the following rule I added: <code>route allow from 192.168.1.6 to any port 222 proto tcp</code>. This was intended to only limit Gitea SSH access to my main machine in theory. 222 being the host port exposed and the port from which to forward packets to the Gitea Docker container with an exposed port of 22. After debugging and researching for several hours, I realised what I learnt in the last section—that this rule would only allow <strong>packet-forwarding with a destination port of 222</strong>. However, our destination port was <strong>22</strong> which was the Docker container's port. I modified the rule, reloaded, tested, and now everything was working as intended.</p>

  <p>At that point this is what the rules looked like:</p>

  <code><?php $code = <<<BASH
     To                     Action      From
     --                     ------      ----
[ 1] Nginx HTTP             ALLOW IN    Anywhere
[ 2] 22/tcp                 ALLOW IN    192.168.10.6
[ 3] 22/tcp                 ALLOW FWD   192.168.10.6
[ 4] 3000/tcp               ALLOW FWD   192.168.10.6
[ 5] Nginx HTTP (v6)        ALLOW IN    Anywhere (v6)
BASH;
        echo $code ?></code>

  <p>Explanation: from my main machine only, access is allowed (with forwarding) to the Gitea Docker container with HTTP and SSH, and to SSH on the host itself. HTTP access to the website is also allowed from anywhere which is just internal. Everything else is denied as per <code>ufw</code> and the updated configuration. I thoroughly tested that this was actually the case in practice, and indeed it was—mission success.</p>

  <p>I had to spend a day sorting out this fiasco with <code>ufw</code> because I wasn't aware that there were problems and that there were some things I didn't know, however it was a great learning experience as a result and now everything is secured as I intend. To be honest, going this length just to have the rules I need in place wasn't too necessary since I'm only configuring rules for LAN IPs and those are usually trustable, however I want to try and stick to the principle of least privilege as much as possible and so going the extra mile was worth it in the end—more security and more knowledge.</p>
</section>

<?php
require __DIR__ . "/../../../src/partials/tail.php";
