<?php
require __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>Syncthing & File Browser</h2>

  <p>One thing I've also been wanting to set up is a Google Drive-like service where I can store my files and access them from anywhere via a web interface. I also wanted to set up some kind of file backup to backup important files from my PC to my server and vice-versa.</p>

  <p>I did some research and the cleanest setup I could find for this was using Syncthing and File Browser. Syncthing is a service that lets you synchronize files between machines allowing you to back them up elsewhere. Paired with something like File Browser, that would let you browse your files with a nice web UI.</p>

  <p>In order to set up Syncthing, I would need to have it running on my server as well as my PC. Naturally I did this with Docker containers and Compose files. I set that up quite easily by following their docs as well making sure I added a new site to Nginx so I could access the web UI for the server's instance. I tried accessing each UI, both locally and the server's and each was in service. I tried setting up a folder that I wanted to synchronize between the machines and it worked, they synchronized successfully:</p>

  <img src="./sync.png" width="400" alt="Successful sync">

  <p>I also set up a container with File Browser that would mount my files from Syncthing on the server into the container, allowing me to access them from the UI. I also exposed the File Browser site publically so it could act like a personal cloud.</p>
</section>

<section>
  <h2>Vaultwarden</h2>

  <p>Another thing I wanted to set up is Vaultwarden in order for me to manage my passwords. The current password manager I use is KeePass2 however one issue I have with it is that it's not very portable. Something like Vaultwarden provides a nice web UI in order to manage your passwords and access them from anywhere. So if I'm away from my PC and I need to enter a complicated password elsewhere, I can just access the password remotely.</p>

  <p>Naturally, I set up a Docker Compose file and configured it according to the Vaultwarden docs as well as making an Nginx site config. I made sure to set the storage location to my Syncthing folder so that I could back up my Vaultwarden data to my PC. After that, I started the container and then was able to add my passwords to Vaultwarden.</p>

  <p>I also set up Cloudflare Access in order to add an extra layer of authentication to my Vaultwarden instance as well as File Browser for when accessing their public sites.</p>
</section>
<!-- content -->
<?php
require alias("@tail");
