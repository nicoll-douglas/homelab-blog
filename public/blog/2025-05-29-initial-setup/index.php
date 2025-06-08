<?php
require __DIR__ . "/../../../src/partials/head.php";
?>
<section>
  <h2>Basic Networking</h2>
  <p>Day 2, I now have my old laptop freshly set up with Debian on it. So the way my setup is now is I have my main Ubuntu PC and Debian laptop connected to my router/internet through Wi-Fi. I have decided to connect both machines to each other with an ethernet cable for easy access. So the first thing I have to do is configure the ethernet network interfaces on each machine with a static IP so that I can easily identify them and SSH into my laptop from my main machine.</p>

  <p>By default the Debian laptop is using <code>ifupdown</code> for network management. And so I learnt that network configuration takes place specifically in the <code>/etc/network/</code> directory, specifically the <code>/etc/network/interfaces</code> file. To configure the ethernet interface the way I wanted, I had to add the following block to the file:</p>
  <code><?php
        $code = <<<BASH
auto enp1s0
    iface enp1s0 inet static
    address 192.168.10.2
    netmask 255.255.255.0
BASH;
        echo $code;
        ?></code>

  <p><code>enp1s0</code> being the name ethernet interface.</p>

  <p>I can't remember if I had to restart the networking service on the laptop for the new configuration to take effect but next was the static IP on the Ubuntu machine. My Ubuntu machine was/is using Network Manager to manage networks and apparently that's the case because I had at least one file in <code>/etc/netplan</code> prefixed as so: <code>90-NM-*.yaml</code>. And so, I had to use <code>nmcli</code> to configure the ethernet network interface.</p>

  <p>I had to enter the following command in order to configure a static IP for the ethernet interface <code>enp8s0</code>:</p>

  <code><?php
        $code = <<<BASH
nmcli con modify "Wired connection 1" \
  ipv4.addresses 192.168.10.1/24 \
  ipv4.method manual \
  connection.autoconnect yes \
  ipv4.gateway "" \
  ipv4.dns ""
BASH;
        echo $code ?></code>

  <p>And then then the following to bring up the network interface: <code>nmcli con up "Wired connection 1"</code>.</p>

  <p>I tried <code>ping</code>ing the laptop from my main and I got a successful response. The same also happened when I tried in the reverse direction, so now I could successfully network between my machines.</p>

  <img src="./ping.png" width="440" alt="Successful ping">
</section>

<section>
  <h2>SSH</h2>
  <p>Since I could successfully network between machines I tried SSH'ing into the laptop, and that also worked which was huge:</p>
  <img src="./ssh.png" width="675" alt="Successful SSH">

  <p>But naturally I want to use public key authentication because it's better than the default password authentication. Setting up and working with SSH keys is something that I've done multiple times but I always forget the ins and outs so I had to use GPT as a guide. But now I have the process documented below so I won't forget.</p>

  <p>Step 1 is generating a key pair with <code>ssh-keygen</code>. I added a passphrase to the private key when prompted so only I can use the key pair which is standard. I named the key <code>debian-box</code> after the target machine.</p>
  <p>Step 2 is copying the SSH public key to the server with <code>ssh-copy-id</code>. I used <code>ssh-copy-id -i ~/.ssh/debian-box.pub jiggy@192.168.10.2</code> in my case. That prompted me for the user's password which I entered and then it successfully completed.</p>
  <p>I tried SSH'ing and now it was using key authentication which was a good sign. Key authentication is setup however, every time I SSH I am prompted for the passphrase which is annoying. So I ran <code>ssh-add ~/.ssh/debian-box</code> which adds the passphrase to <code>ssh-agent</code>.</p>
  <p>Some notes on <code>ssh-agent</code> since I learnt about this for the first time:</p>
  <ul>
    <li>
      <code>ssh-agent</code> is a key manager for <code>ssh</code>
    </li>
    <li>It holds your keys and certificates in memory, unencrypted, and ready for use by <code>ssh</code></li>
    <li>It runs in the background and usually starts up the first time you run <code>ssh</code> after reboot</li>
    <li>If it's not running use: <code>eval "$(ssh-agent -s)"</code></li>
    <li>The stuff stored in <code>ssh-agent</code> is ephemeral so it gets reset every session</li>
  </ul>
  <p>I also added the following block to <code>~/.ssh/config</code> to make SSH'ing easier:</p>
  <code><?php
        $code = <<<BASH
Host debian-box
  HostName 192.168.10.2
  User jiggy
  IdentityFile ~/.ssh/debian-box
BASH;
        echo $code ?></code>

  <p>However when I tried SSH'ing with the new alias using <code>ssh debian-box</code> I got this message: <code>Bad owner or permissions on /home/jiggy/.ssh/config</code>. That apparently means that the config file has too many permissions making it a security risk. That makes sense because I created the file with <code>nano</code> so the permissions weren't set correctly by default. I had to change the permissions from 644 to 600 which is apparently the ideal for the config file: <code>chmod 600 ~/.ssh/config</code>.</p>

  <p> For future reference:</p>

  <ul>
    <li><code>authorized_keys</code>, <code>known_hosts</code>, and private key files should be 600.</li>
    <li>Public key files should be 644</li>
    <li>The <code>.ssh</code> directory should be 700</li>
  </ul>

  <p>So after all that was sorted, I tried <code>ssh debian-box</code> again and it worked with my passphrase / private key added to <code>ssh-agent</code>. Now I have easy SSH access to my Debian laptop which was the original goal.</p>

  <p>I also disabled password authentication on the Debian machine by adding the following line to <code>/etc/ssh/sshd_config</code>: <code>PasswordAuthentication no</code>. And then restarting the SSH service: <code>sudo systemctl restart ssh</code>.</p>
</section>

<section>
  <h2>Basic Power Control</h2>
  <p>With SSH access I realised the only issue was that when closing the laptop lid, the machine went into suspense which wasn't ideal because then networking wouldn't be available. If I didn't want it to go into suspense I learnt that I had to configure the login daemon in <code>/etc/systemd/logind.conf</code>.

  <p>I had to add the following lines so that the laptop wouldn't suspend when closing the lid:</p>

  <code><?php
        $code = <<<BASH
HandleLidSwitch=ignore
HandleLidSwitchDocker=ignore
BASH;
        echo $code ?></code>

  <p>Then I restarted the login daemon with <code>sudo systemctl restart systemd-logind</code> and now everything was good.</p>

  <p>I also learnt that <code>sudo poweroff</code> powers off the machine and you can also run that command over SSH safely which is neat.</p>
</section>
<?php
require __DIR__ . "/../../../src/partials/tail.php";
