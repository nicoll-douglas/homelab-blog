<?php
require __DIR__ . "/../../../src/partials/head.php";
?>

<section>
  <h2>Homelab Repo</h2>
  <p>sudo groupadd homelab</p>
  <p>sudo usermod -aG homelab jiggy</p>
  <p>sudo usermod -aG homelab ci</p>
  <p>cd /srv</p>
  <p>sudo mkdir homelab</p>
  <p>sudo chown jiggy:homelab homelab</p>
  <p>sudo chmod 2775 homelab</p>
  <p>setGID bit so group ownership is inherited</p>
  <p>to do</p>
  <p>set up Gitea</p>
  <p>new deploy script -> replace old one in blog</p>
  <p>port forwarding</p>
  <p>Cloudflare reverse proxy for domain</p>
  <p>Add homelab.nicolldouglas.dev DNS record</p>
  <p>Setup laptop with ethernet to router</p>
</section>


<?php
require __DIR__ . "/../../../src/partials/tail.php";
