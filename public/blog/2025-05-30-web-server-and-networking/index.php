<?php
require __DIR__ . "/../../../src/partials/head.php";
?>
<section>
  <h2>Web Server</h2>
  <p>Now that I have networking and the basics setup, one of my first missions with my homelab is to set up a web server so that I can host this website locally. I mainly have experience with using Apache but I wanted to try using Nginx which is the other mainstream web server. I think Nginx also has some load balancing capabilities that would be fun to be experiment with later.</p>

  <p>After installing Nginx with <code>sudo apt install nginx</code>, I could access the default page on my local network at http://192.168.1.5 which was really cool to see:</p>

  <img src="./nginx-home.png" width="450" alt="Nginx home page">

  <p>By default Nginx listens for incoming requests on all network interfaces via port 80, hence why that worked. I also learnt that all IPv4 IPs on a machine are represented by <code>0.0.0.0</code> in networking contexts.</p>
</section>

<section>
  <h2>Port Management</h2>

  <p>Next I wanted to check what ports were open. The command I used was <code>sudo ss -tuln</code>. In the command, the <code>t</code> flag makes it specify TCP ports, <code>u</code> does UDP ports, <code>l</code> shows which ports are being listened on, and <code>n</code> shows numeric IP addresses.</p>

  <p>This was the output:</p>

  <img src="./ports.png" alt="Open ports" width="480">

  <p>The UDP line is apparently a DHCP client listening for replies which is how IP addresses are assigned automatically with DHCP. I guess this port doesn't need to be open anymore since I'm using a static IP now but its fine, it should go away eventually I imagine. 22 is the SSH port and 80 the is HTTP port where I can access the Nginx default page. <code>0.0.0.0</code> and <code>[::]</code> means I'm listening on all network interfaces, which confirms the default Nginx behaviour.</p>

  <p>But now I have basic ports managed and reliable access to my web server internally.</p>
</section>
<?php
require __DIR__ . "/../../../src/partials/tail.php";
