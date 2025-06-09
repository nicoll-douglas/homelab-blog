<?php
require_once __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>Setup</h2>

  <p>It's been a couple days but today is the day I set up Gitea. I've been doing a bit of reading of the Gitea docs and have added a <a href="https://github.com/nicoll-douglas/homelab/commit/161a8acce49c52ed87243c45c6c127bb69e29ff0">Docker Compose file</a> to the repo in accordance with the installation guide.</p>

  <p>From what I understand, if you want to interact with Gitea repos with SSH like you would regular GitHub repos, you have to make sure that you have a port exposed on your machine for that. You also need a HTTP port exposed in order to do the same with HTTP and to use the Gitea web interface. I've just stuck with 3000 for HTTP and 222 for SSH as defined in the Docker Compose file provided in their docs. These map to the necessary internal ports of the Docker container.</p>

  <p>Therefore, I've exposed these ports in <code>ufw</code> with the following commands:</p>

  <?php
  $code = <<<BASH
sudo ufw allow 3000/tcp
sudo ufw allow from 192.168.1.6 to any port 222 proto tcp
BASH;
  require alias("@code");
  ?>

  <p>This now allows HTTP access from my network as well as SSH access from my main machine to Gitea on my laptop.</p>

  <p>Now, the next step is to start the container which should allow me to access the web interface and complete the installation. I've done that with <code>docker compose up -d</code> naturally.</p>

  <p>I tried navigating to 192.168.1.5:3000 in my browser and the web interface showed up which was huge. I proceeded with the final installation steps, created an account, and now Gitea was successfully set up.</p>

  <p>The next step was to generate an SSH key pair for me to use with Gitea from my main machine. I did that and then added the public key to my Gitea account in the web interface.</p>

  <p>Now it was time to test to see if SSH was working for the git user. I ran <code>ssh -p 222 git@192.168.1.5</code> and got successful output which was good:</p>

  <?php
  $shell = ["jiggy", "ubuntu"];
  $code = <<<BASH
ssh -p 222 git@192.168.1.5
PTY allocation request failed on channel 0
Hi there, nicoll-douglas! You've successfully authenticated with the key named Ubuntu Access, but Gitea does not provided shell access.
If this is unexpected, please log in with password and setup Gitea under another user.
Connection to 192.168.1.5 closed.
BASH;
  require alias("@code");
  ?>
</section>

<section>
  <h2>Repo Hosting & Push Mirroring</h2>

  <p>The final thing I wanted to do is add the repo for this website as well as the homelab to Gitea and make sure they sync up with GitHub (in case my laptop dies or something). I created empty repositories for each in the Gitea web interface and then set the remote for each repo to point to their Gitea repo. That was achieved with the following commands:</p>

  <?php
  unset($shell);
  $code = <<<BASH
git remote set-url origin ssh://192.168.1.5:222/nicoll-douglas/homelab.git
git remote set-url origin ssh://192.168.1.5:222/nicoll-douglas/homelab-blog.git
BASH;
  require alias("@code");
  ?>

  <p>I then tried pushing my commits to test if I could indeed push to the remote in Gitea and it worked for both repos:</p>

  <?php
  $shell = ["jiggy", "ubuntu", "~/Code/homelab-blog"];
  $code = <<<BASH
git push
Enumerating objects: 7, done.
Counting objects: 100% (7/7), done.
Delta compression using up to 12 threads.
Compressing objects: 100% (4/4), done.
Writing objects: 100% (4/4), 366 bytes | 366.00 KiB/s, done.
Total 4 (delta 3), reused 0 (delta 0), pack-reused 0
remote: . Processing 1 references
remote: Processed 1 references in total
To ssh://192.168.1.5:222/nicoll-douglas/homelab-blog.git
   2a97bec..5049576  main -> main
BASH;
  require alias("@code");
  ?>

  <p>The next step was to add a push mirror from Gitea to GitHub. This is a feature of Gitea that allows you to sync up your Gitea repo with a GitHub repo and so that whenever you push to Gitea, it will reflect that in GitHub. This was quite simple to do and just required me to enter the details of the mirror repo, my account, and a GitHub personal access token with read-write access into the Gitea repo settings. I then tried clicking the sync button and my newer commits showed up on GitHub—the push mirroring was now working.</p>

  <p>Now I have Gitea all set up with some locally hosted repos which enables the next few things I want to set up for the homelab.</p>
</section>
<?php
require alias("@tail");
