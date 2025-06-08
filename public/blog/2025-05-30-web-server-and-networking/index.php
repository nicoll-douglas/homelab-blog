<?php
require __DIR__ . "/../../../src/partials/head.php";
?>
<section>
  <h2>Web Server</h2>
  <p>Now that I have the basics setup, one of my first missions with my homelab is to set up a web server so that I can host this website locally. I mainly have experience with using Apache but I wanted to try using Nginx which is the other mainstream web server. I think Nginx also has some load balancing capabilities that would be fun to be experiment with later.</p>

  <p>After installing Nginx with <code>sudo apt install nginx</code>, I could access the default page over both the ethernet and Wi-Fi interface IPs (192.168.10.2 and 192.168.1.20 respectively) which was really cool to see:</p>

  <img src="./nginx-home.png" width="450" alt="Nginx home page">

  <p>By default Nginx listens for incoming requests on all network interfaces via port 80, hence why that worked. I also learnt that all IPv4 IPs on a machine are represented by 0.0.0.0 in networking contexts.</p>
</section>

<section>
  <h2>Wi-Fi Configuration</h2>
  <p>I then wanted to set a static IP for the Wi-Fi interface on the Debian machine so that the IP wouldn't be set dynamically. I inspected the <code>/etc/network/interfaces</code> file where the configuration for the Wi-Fi interface was as follows:</p>

  <code><?php
        $code = <<<BASH
allow-hotplug wlp2s0
  iface wlp2s0 inet dhcp
  wpa-ssid TALKTALKB42F10
  wpa-psk 12345678
BASH;
        echo $code
        ?></code>

  <p>How GPT explained it:</p>

  <ul>
    <li><code>allow-hotplug</code> means the interface is brought up automatically when it's ready/detected.</li>
    <li><code>iface wlp2s0 inet dhcp</code> configures the <code>wlp2s0</code> interface (Wi-Fi) to use the DHCP protocol which obtains an IP address automatically.</li>
    <li><code>wpa-ssid TALKTALKB42F10</code> is specifying the network name/SSID to connect to.</li>
    <li><code>wpa-psk 12345678</code> provides the Wi-Fi password (aka the pre-shared key) for the network (not my actualy Wi-Fi password btw).</li>
  </ul>

  <p>So since it's using the DHCP protocol, apparently that means that the IP can change over time as I thought. I barely understand what DHCP is (I have to learn networking theory in more detail) but I logged into my router's admin web interface and saw that the pool of IPs that DHCP is currently using is from 192.168.1.10 to 192.168.1.254:</p>

  <img src="./dhcp.png" alt="DHCP pool" width="480">

  <p>Another interesting thing is that my router's IP address is 192.168.1.1 and so when I'm accessing the web interface which is at http://192.168.1.1 it's the same scenario as accessing the Nginx page.</p>

  <p>I could've disabled DHCP, but I don't know what effects that would have since I'm still learning about networks so I left it. I thought of using an IP like 192.168.10.3 since for my ethernet interfaces I was using 192.168.10.*, but apparently that wouldn't work because that IP is on a different subnet. I'm still not sure what subnets and subnet masks are but again, networking theory is on the to-do list. So instead, it was gonna have to be something like 192.168.1.5 so I tried pinging the IP to see if it was in use:</p>

  <img src="./ip-free.png" width="500" alt="Unsuccessful ping">

  <p><code>Desintation host unreachable</code> so the IP is most likely not in use. I also checked the devices list in the admin interface of the router, and all the connected devices are just devices with IPs in the DHCP pool so it should be fine to use. So I updated the config as follows which now assigns a static IP:</p>

  <code><?php
        $code = <<<BASH
auto wlp2s0
    iface wlp2s0 inet static
    address 192.168.1.5
    netmask 255.255.255.0
    gateway 192.168.1.1
    dns-nameservers 1.1.1.1 8.8.8.8
    wpa-ssid TALKTALKB42F10
    wpa-psk Y3MNX8TU
BASH;
        echo $code ?></code>

  <p>When using DHCP, DNS servers are automatically assigned but not with static IPs. So I added a <code>dns-nameservers</code> line to the interfaces config as advised by GPT. After the saving the file, I tried to restart the interface with <code>sudo ifdown wlp2s0</code> but I got the following output: <code>RTNETLINK answers: Cannot assign requested address</code>. I tried <code>sudo ip link set wlp2s0 down</code> and it worked. Then I did <code>sudo ifup wlp2s0</code> to bring it back up.</p>

  <p>To make sure the config was in place I made the following checks:</p>

  <ul>
    <li><code>ip a show wlp2s0</code> and <code>ip route</code> to see details, and everything looked alright.</li>
    <li>I tried pinging 192.168.1.5 from my main machine and it was successful so the new IP was in effect.</li>
    <li>I tried pinging google.com and it was successful so DNS resolution and internet access was working.</li>
  </ul>

  <p>I also did <code>cat /etc/resolv.conf</code> to see the DNS settings and it had the following entry: <code>nameserver 192.168.1.1</code>. That meant my machine was using my router as the DNS server, not what I specified in the config. But that was fine because my router forwards DNS queries to its configured DNS server so DNS resolution was working as verified.</p>

  <p>So now I had a static IP on the Wi-Fi interface for easy and memorable access which was the goal.</p>
</section>

<section>
  <h2>Port Management</h2>

  <p>Next I wanted to check what ports were open. The command I used was <code>sudo ss -tuln</code>. In the command, the <code>t</code> flag makes it specify TCP ports, <code>u</code> does UDP ports, <code>l</code> shows which ports are being listened on, and <code>n</code> shows numeric IP addresses.</p>

  <p>This was the output:</p>

  <img src="./ports.png" alt="Open ports" width="480">

  <p>The UDP line is apparently a DHCP client listening for replies which is how IP addresses are assigned automatically with DHCP. I guess this port doesn't need to be open anymore since all interfaces have static IPs now but its fine. 22 is the SSH port and 80 the is HTTP port where I can access the Nginx default page. <code>0.0.0.0</code> and <code>[::]</code> means I'm listening on all network interfaces, which confirms the default Nginx behaviour.</p>

  <p> I wanted to change the SSH service to only listen on the ethernet interface. So I had to add <code>ListenAddress 192.168.10.2</code> to <code>/etc/ssh/sshd_config</code>. I restarted the SSH service with <code>sudo systemctl restart ssh</code> and now SSH was only possible over the ethernet interface (the connection to my main PC) as confirmed by running the <code>ss</code> command again. At some point I think I will need to open port 443 for HTTPS but I'm still setting things up so things are fine as-is.</p>

  <p>But now I have basic ports managed and reliable access to my web server internally.</p>
</section>
<?php
require __DIR__ . "/../../../src/partials/tail.php";
