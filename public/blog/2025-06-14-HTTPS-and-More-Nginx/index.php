<?php
require __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <p>One thing that I've been putting off is enabling HTTPS for my sites when accessing them locally. When accessing them via their public domain, Cloudflare handles TLS encryption between the browser and their network before reaching the tunnel to mine, however, I've also been wanting to enable HTTPS on the Nginx level. Although not necessary since my network is private, I thought it would be a good measure.</p>

  <p>I decided that for my locally accessible sites I would map them to *.nicolldouglas.local domains. And instead of opening ports to access them with the server IP (192.168.1.5), I would make them be reverse proxied by Nginx like their public equivalents. Since I would mainly be accessing my sites from my main PC, I added the following to my <code>/etc/hosts</code> file:</p>

  <?php
  $code = <<<CONF
192.168.1.5 gitea.nicolldouglas.local
192.168.1.5 nicolldouglas.local
192.168.1.5 homelab.nicolldouglas.local
CONF;
  require alias("@code");
  ?>

  <p>This would mean that the DNS would resolve these sites to the server IP, and since I would be sending the Host header with the respective HTTP request, Nginx would be able to reverse proxy if I configured it properly.</p>

  <p>The next step required me to decide how I would be obtaining TLS certificates, and I decided to go with <code>mkcert</code> which is a command line utility that lets you create a local certificate authority and issue your own certificates. In hindsight, Let's Encrypt would've been a lot smoother but regardless, I installed <code>mkcert</code> with the following commands on my server:</p>

  <?php
  $code = <<<BASH
sudo apt install libnss3-tools
curl -JLO "https://dl.filippo.io/mkcert/latest?for=linux/amd64"
chmod +x mkcert-v*-linux-amd64
sudo mv mkcert-v*-linux-amd64 /usr/local/bin/mkcert
BASH;
  require alias("@code");
  ?>

  <p>In my homelab repository, I have a script that automatically brings up the Docker containers for my "web" service consisting of the Nginx and Cloudflare daemon containers. I decided to modify this script so that it would use <code>mkcert</code> (which was now installed) to generate certificates and place them in an easy accessible location that I could mount into the Nginx container.</p>

  <p>After doing that I adjusted my Nginx site configurations to use a shared certificate at that location by adding lines similar to the following (example for this site's config):</p>

  <?php
  $code = <<<CONF
listen 443 ssl;
server_name homelab.nicolldouglas.dev homelab.nicolldouglas.local;
ssl_protocols TLSv1.2 TLSv1.3;
ssl_ciphers HIGH:!aNULL:!MD5;
ssl_prefer_server_ciphers on;
ssl_certificate /etc/nginx/certs/server.pem;
ssl_certificate_key /etc/nginx/certs/server-key.pem;
CONF;
  require alias("@code");
  ?>

  <p>Next I had to update my Docker Compose files for my sites and Nginx. For my sites I would stop exposing ports and instead put them on the <code>nginx-proxy-net</code> network to allow reverse proxying (if they already weren't), and for Nginx I would map host port 443 to container port 443 to allow HTTPS. I then restarted my site containers as well as the Nginx container and now they were using HTTPS albeit giving browser warnings which doesn't matter too much (I know you can add the CA to the trust store but not bothered).</p>

  <p>So now, local access to my sites were using HTTPS and all incoming connections to my services be it public or private were using Nginx. After all the tinkering, I think I've finally gotten the Nginx setup right.</p>
</section>


<?php
require alias("@tail");
