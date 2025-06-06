<?php
require __DIR__ . "/../../../src/partials/head.php";
?>

<p>It's been a couple days but today is the day I set up Gitea. I've been doing a bit of reading of the Gitea docs and have added a <a href="https://github.com/nicoll-douglas/homelab/commit/161a8acce49c52ed87243c45c6c127bb69e29ff0">Docker Compose file</a> to the repo in accordance with the installation guide.</p>
<p>From what I understand, if you want to interact with Gitea repos with SSH like you would regular GitHub repos, you have to make sure that you have a port exposed on your machine for that. You also need a HTTP port exposed in order to do the same with HTTP and to use the Gitea web interface. I've just stuck with 3000 for HTTP and 222 for SSH as defined in the Docker Compose file provided in their docs. These map to the necessary internal ports of the Docker container.</p>

<p>Therefore, I've exposed these ports in <code>ufw</code> with the following commands:</p>

<code><?php
      $code = <<<BASH
sudo ufw allow 3000/tcp
sudo ufw allow from 192.168.1.6 to any port 222 proto tcp
BASH;
      echo $code
      ?></code>

<p>This now allows HTTP access from my network as well as SSH access from my main machine to Gitea on my laptop.</p>
<p>Now, the next step is to start the container which should allow me to access the web interface and complete the installation. I've done that with <code>docker compose up -d</code> naturally.</p>
<p>I tried navigating to 192.168.1.5:3000 in my browser and the web interface showed up which was huge. I proceeded with the final installation steps, created an account, and now Gitea was successfully set up.</p>
<p>The next step was to generate an SSH key pair for me to use with Gitea from my main machine. I did that and then added the public key to my Gitea account in the web interface.</p>
<p>Now it was time to test to see if SSH was working for the git user. I ran <code>ssh -p 222 git@192.168.1.5</code> and got a successful response which was good:</p>

<img src="./successful-ssh.png" alt="Successful SSH" height="130">

<p>The final thing I wanted to do is add the repo for this website as well as the homelab to Gitea. I created empty repositories in the Gitea web interface and then added a remote for each repo to point to it's Gitea repo. That was achieved with the following commands in each repo:</p>
<code><?php
      $code = <<<BASH
git remote add gitea ssh://192.168.1.5:222/nicoll-douglas/homelab.git
git remote add gitea ssh://192.168.1.5:222/nicoll-douglas/homelab-blog.git
BASH;
      echo $code
      ?></code>
<p>I then tried pushing my commits and it was successful for both repos:</p>
<img src="./successful-push.png" height="200" alt="Successful push">
<p>Now I have a successful self-hosted Gitea instance which is perfect for what comes next.</p>


<?php
require __DIR__ . "/../../../src/partials/tail.php";
