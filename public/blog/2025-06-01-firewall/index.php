<?php
require __DIR__ . "/../../../src/partials/head.php";
?>
<section>
  <p>Not much work done today on the homelab and setting up the website. But I realised, a couple days prior I configured SSH on my Debian machine to only listen for requests from 192.168.10.1 which is my main Ubuntu machine connected over ethernet. This was good at restricting access however, the more conventional way to do this is with a firewall. So I decided to install Uncomplicated Firewall with <code>sudo apt install ufw</code> and reconfigure things. This would allow me to manage ports easily and efficiently.</p>

  <p>I learnt that the way <code>ufw</code> works (and firewalls in general) is with a layer of rules. Each rule will typically pertain to a certain port, the IP address of the incoming access, and whether or not to allow or deny access. Then for any given request/connection to the server, the firewall will go through the stack of rules and apply the first rule that matches the constraints. Thus the ordering of the rules is important but configurable. When you add a rule with <code>ufw</code>, it adds it at the bottom of the stack.</p>

  <p>Before managing SSH access, I wanted to manage HTTP access for my web server. Conveniently, <code>ufw</code> provides a list of common rule configurations based on certain software, and Nginx is included.</p>

  <p>Running <code>sudo ufw allow 'Nginx HTTP'</code> applied these Nginx-specific rules, allowing HTTP access over port 80.</p>

  <p>The next step would be to only allow SSH access from my main machine. This was achieved with <code>sudo ufw allow from 192.168.10.1 to any port 22 proto tcp</code> which would only allow SSH access on port 22 from my PC—what I achieved when changing <code>sshd_config</code>.</p>

  <p><code>sudo ufw deny 22/tcp</code> would then add a rule afterwards in the stack to deny SSH access from everywhere else.</p>

  <p>Now I could enable the firewall with <code>sudo ufw enable</code> with the specified rules applied.</p>
  <p>I now had an active firewall with a rule stack that looked as follows, conveniently shown with <code>sudo ufw status numbered</code>:</p>
  <IMG src="./ufw-rules.png" height="180" alt="ufw rules">
  <p>I also learnt you can use <code>sudo ufw delete</code> to delete a rule by its specified number. But yea, <code>ufw</code> is a much more elegant way to control access rather than manually configuring ports on your machine.</p>
</section>
<?php
require __DIR__ . "/../../../src/partials/tail.php";
