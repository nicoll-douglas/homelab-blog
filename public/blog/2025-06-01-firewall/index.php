<?php
require_once __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <p>Not much work done today on the homelab and setting up the website however one of the things that I had been wanting to do is set up a firewall. So I decided to install Uncomplicated Firewall with <code>sudo apt install ufw</code>.</p>

  <p>I learnt that the way <code>ufw</code> works (and firewalls in general) is with a layer of rules. Each rule will typically pertain to a certain port, the IP address of the incoming access, and whether or not to allow or deny access. Then for any given request/connection to the server, the firewall will go through the stack of rules and apply the first rule that matches the constraints. Thus the ordering of the rules is important but configurable. If no "allow" rule matches, the connection will always be denied. When you add a rule with <code>ufw</code>, it adds it at the bottom of the stack.</p>

  <p>Before managing SSH access, I wanted to manage HTTP access for my web server. Conveniently, <code>ufw</code> provides a list of common rule configurations based on certain software, and Nginx is included.</p>

  <p>Running <code>sudo ufw allow 'Nginx HTTP'</code> applied these Nginx-specific rules, allowing HTTP access over port 80.</p>

  <p>The next step would be to only allow SSH access from my main machine. This was achieved with <code>sudo ufw allow from 192.168.1.6 to any port 22 proto tcp</code> which would only allow SSH access on port 22 from my PC.</p>

  <p>Now I could enable the firewall with <code>sudo ufw enable</code> with the specified rules applied.</p>

  <p>I now had an active firewall with a rule stack that looked as follows, conveniently shown with <code>sudo ufw status numbered</code>:</p>

  <?php
  $shell = ["jiggy", "debian-box"];
  $code = <<<BASH
sudo ufw status numbered
Status: active

     To                     Action      From
     --                     ------      ----
[ 1] Nginx HTTP             ALLOW IN    Anywhere
[ 2] 22/tcp                 ALLOW IN    192.168.10.6
[ 3] Nginx HTTP (v6)        ALLOW IN    Anywhere (v6)
BASH;
  require alias("@code");
  ?>

  <p>I also learnt you can use <code>sudo ufw delete</code> to delete a rule by its specified number. But yea, <code>ufw</code> is a very easy and elegant way to control port access on your machine and I now have that set up.</p>
</section>
<?php
require alias("@tail");
