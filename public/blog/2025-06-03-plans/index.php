<?php
require __DIR__ . "/../../../src/partials/head.php";
?>

<section>
  <h2>Current Goals</h2>
  <p>So with my homelab so far I have this site running locally on my Debian machine. I also have an easy deployment script to manually push updates as well as <code>ufw</code> setup and ports managed. My current goal is to have the site exposed to the internet. Another of my major goals is to setup local repo hosting with Gitea on my laptop. That also ties into a better automation pipeline that I want to create for the website where whenever I push to the remote repo on my laptop (in Gitea), I want to rebuild and restart the Docker containers. So in order for me to achieve that, the first thing to do would be to set up Gitea at some point.</p>
</section>

<section>
  <h2>Homelab Repo</h2>
  <p>I've also created homelab repo in order for me to store service definitions such as Docker Compose files, automation scripts, configurations for <code>ufw</code>, Nginx, and whatever else for my homelab so I can have better organisation. The repo is publically available on GitHub <a href="https://github.com/nicoll-douglas/homelab">here</a>.</p>

  <p>I decided to also have a local version of the repo on my laptop in the /srv directory so that I and other necessary users can easily access it. I thought of creating a dedicated <code>homelab</code> group and adding myself and the <code>ci</code> user to it, then making the homelab directory be owned by my default user but the <code>homelab</code> group.</p>
  <p>I ran the following commands to achieve that:</p>
  <code><?php
        $code = <<<BASH
sudo groupadd homelab
sudo usermod -aG homelab jiggy
sudo usermod -aG homelab ci
cd /srv
sudo mkdir homelab
sudo chown jiggy:homelab homelab
sudo chmod 2775 homelab
cd homelab
git clone https://github.com/nicoll-douglas/homelab.git .
BASH;
        echo $code ?></code>

  <p>I also set the setGID bit with the 7th command for the directory so that group ownership is inherited for descendant files and directories.</p>
  <p>
    I've also done some minor changes to the deployment script for this website so that it can can just easily use a Docker Compose file and script I have in the homelab repo.
  </p>
</section>

<?php
require __DIR__ . "/../../../src/partials/tail.php";
