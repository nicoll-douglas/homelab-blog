<?php
require_once __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>Basic Networking</h2>

  <p>Day 2, I now have my old laptop freshly set up with Debian on it. So the way my setup is now is I have my laptop connected to my router via ethernet and my main Ubuntu PC connected via Wi-Fi. In order to be able to network between my machines, the first thing I have to do is configure their respective network interfaces with a static IP so that I can easily identify them and SSH into my laptop from my main machine.</p>

  <p>By default the Debian laptop is using <code>ifupdown</code> for network management. And so I learnt that network configuration takes place in the <code>/etc/network/</code> directory, specifically the <code>/etc/network/interfaces</code> file.</p>

  <p>When inspecting the configuration file, I learnt that by default the ethernet interface was using something called the DHCP protocol. What the DHCP protocol does is it obtains a LAN IP address automatically from the router. So my possible options were reserving a static IP on the router level or on the machine level. I logged in to my router's admin panel and saw that the pool of IPs that DHCP was using was from 192.168.1.10 to 192.168.1.254:</p>

  <img src="./dhcp.png" alt="DHCP pool" width="430">

  <p>I also had a look at the currently used IPs on the network by devices and they were all in the DHCP pool. So that meant that the free IPs were between 192.168.1.2 and 192.168.1.9 (192.168.1.1 being my router). I decided on 192.168.1.5 to be the static IP for my laptop's ethernet interface.</p>

  <p>To configure the ethernet interface the way I wanted, I had to update the ethernet interface block in <code>/etc/network/interfaces</code> as so:</p>

  <?php
  $code = <<<BASH
auto enp1s0
    iface enp1s0 inet static
    address 192.168.1.5
    netmask 255.255.255.0
BASH;
  require alias("@code");
  ?>

  <p>Then I had to restart the interface with <code>sudo ifdown enp1s0 && sudo ifup enp1s0</code> in order for the new config to take effect.</p>

  <p>Next was the static IP on the Ubuntu machine. My Ubuntu machine was/is using Network Manager and so, I had to use <code>nmcli</code> to configure the Wi-Fi network interface with a static IP. I decided to use 192.168.1.6 and so I had to enter the following commands to configure it as such, first deleting the old connection config and adding a new one:</p>

  <?php
  $code = <<<BASH
nmcli connection delete TALKTALKB42F10
nmcli connection add type wifi ifname wlp6s0 con-name static-wifi ssid "TALKTALKB42F10"
nmcli connection modify static-wifi \
  wifi-sec.key-mgmt wpa-psk
  wifi-sec.psk "12345678"
  ipv4.addresses 192.168.1.6/24
  ipv4.gateway 192.168.1.1
  ipv4.method manual
BASH;
  require alias("@code")
  ?>

  <p>Then I ran the following to bring up the network interface: <code>nmcli connection up static-wifi</code>.</p>

  <p>I tried <code>ping</code>ing the laptop's IP from my PC and I got successful output:</p>

  <?php
  $shell = ["jiggy", "ubuntu"];
  $code = <<<BASH
ping 192.168.10.5
PING 192.168.1.5 (192.168.1.5) 56(84) bytes of data.
64 bytes from 192.168.1.5: icmp_seq=1 ttl=64 time=89.7ms
64 bytes from 192.168.1.5: icmp_seq=2 ttl=64 time=9.75ms
64 bytes from 192.168.1.5: icmp_seq=3 ttl=64 time=31.3ms
64 bytes from 192.168.1.5: icmp_seq=4 ttl=64 time=54.3ms
^C
--- 192.168.1.5 ping statistics ---
4 packets transmitted, 4 received, 0% packet loss, time 3005ms
rtt min/avg/max/mdev = 9.754/46.258/89.691/29.607 ms
BASH;
  require alias("@code");
  ?>

  <p>The same also happened when I tried in the reverse direction, so now I could successfully network between my machines.</p>
</section>

<section>
  <h2>SSH</h2>

  <p>Since I could successfully network between machines I tried SSH'ing into the laptop with password authentication, and that also worked which was huge.</p>

  <p>But naturally I want to use public key authentication because it's better than the default password authentication. Setting up and working with SSH keys is something that I've done multiple times but I always forget the ins and outs so I had to use GPT as a guide. But now I have the process documented below so I won't forget.</p>

  <p>Step 1 is generating a key pair with <code>ssh-keygen</code>. I added a passphrase to the private key when prompted so only I can use the key which is standard. I named the key <code>debian-box</code> after the target machine.</p>

  <p>Step 2 is copying the SSH public key to the server with <code>ssh-copy-id</code>. I used <code>ssh-copy-id -i ~/.ssh/debian-box.pub jiggy@192.168.1.5</code> in my case. That prompted me for the user's password which I entered and then it successfully completed.</p>

  <p>I tried SSH'ing and now it was using key authentication which was a good sign. Key authentication is set up however, every time I SSH I am prompted for the passphrase which is annoying. So I ran <code>ssh-add ~/.ssh/debian-box</code> which adds the passphrase to <code>ssh-agent</code>.</p>

  <p>Some notes on <code>ssh-agent</code> since I learnt about this for the first time:</p>

  <ul>
    <li>
      <code>ssh-agent</code> is a key manager for <code>ssh</code>
    </li>
    <li>It holds your keys and certificates in memory, unencrypted, and ready for use by <code>ssh</code>.</li>
    <li>It runs in the background and usually starts up the first time you run <code>ssh</code> after reboot.</li>
    <li>If it's not running use: <code>eval "$(ssh-agent -s)"</code>.</li>
    <li>The stuff stored in <code>ssh-agent</code> is ephemeral so it gets reset every session.</li>
  </ul>

  <p>I also added the following block to <code>~/.ssh/config</code> to make SSH'ing easier:</p>

  <?php
  unset($shell);
  $code = <<<BASH
Host debian-box
  HostName 192.168.1.5
  User jiggy
  IdentityFile ~/.ssh/debian-box
BASH;
  require alias("@code");
  ?>

  <p>However when I tried SSH'ing with the new alias using <code>ssh debian-box</code> I got this message: <code>Bad owner or permissions on /home/jiggy/.ssh/config</code>. That apparently means that the config file has too many permissions making it a security risk. That makes sense because I created the file with <code>nano</code> so the permissions weren't set correctly by default. I had to change the permissions from 644 to 600 which is apparently the ideal for the config file: <code>chmod 600 ~/.ssh/config</code>.</p>

  <p> For future reference:</p>

  <ul>
    <li><code>authorized_keys</code>, <code>known_hosts</code>, and private key files should be 600.</li>
    <li>Public key files should be 644.</li>
    <li>The <code>.ssh</code> directory should be 700.</li>
  </ul>

  <p>So after all that was sorted, I tried <code>ssh debian-box</code> again and it worked with my passphrase / private key added to <code>ssh-agent</code>. Now I have easy SSH access to my Debian laptop which was the original goal.</p>

  <p>I also disabled password authentication on the Debian machine by adding the following line to <code>/etc/ssh/sshd_config</code>: <code>PasswordAuthentication no</code>. And then restarting the SSH service: <code>sudo systemctl restart ssh</code>.</p>
</section>

<section>
  <h2>Basic Power Control</h2>

  <p>With SSH access I realised the only issue was that when closing the laptop lid, the machine went into suspense which wasn't ideal because then networking wouldn't be available. If I didn't want it to go into suspense I learnt that I had to configure the login daemon in <code>/etc/systemd/logind.conf</code>.

  <p>I had to add the following lines so that the laptop wouldn't suspend when closing the lid:</p>

  <?php
  unset($shell);
  $code = <<<BASH
HandleLidSwitch=ignore
HandleLidSwitchDocker=ignore
BASH;
  require alias("@code");
  ?>

  <p>Then I restarted the login daemon with <code>sudo systemctl restart systemd-logind</code> and now everything was good.</p>

  <p>I also learnt that <code>sudo poweroff</code> powers off the machine and you can also run that command over SSH safely which is neat.</p>
</section>
<?php
require alias("@tail");
