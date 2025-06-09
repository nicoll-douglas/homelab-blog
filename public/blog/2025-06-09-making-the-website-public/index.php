<?php
require_once __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>Cloudflare Tunnel</h2>

  <p>After sorting out the previous issues with <code>ufw</code> I can now get back on track to one of the original goals which was making this website public. I looked into the options for this, VPN, port forwarding, etc, and I decided on using Cloudflare tunnel. For my needs, this was the easiest solution where I just need to expose my service securely.</p>

  <p>This required me to install the Cloudflare daemon on my server and then create a tunnel in the Cloudflare web interface. I installed the Cloudflare daemon with their official <a href="https://hub.docker.com/r/cloudflare/cloudflared">Docker image</a> and then set up a Cloudflare account and a subdomain to utilise a new tunnel (given that I changed to Cloudflare nameservers on my registrar). This required me to enter the local URL of the service that the tunnel would access (the website's container). In order for the tunnel/daemon to access the service I created a new Docker network with <code>docker network create "cloudflare-tunnel-net"</code>.</p>

  <p>I ended up with a <code>docker-compose.yml</code> file along the lines of the following for the tunnel service:</p>

  <?php
  $code = <<<'DC'
services:
  cloudflared:
    image: cloudflare/cloudflared:latest
    container_name: cloudflare_daemon
    restart: always
    command: tunnel run --token ${TUNNEL_TOKEN}
    networks:
      - cloudflare-tunnel-net

networks:
  cloudflare-tunnel-net:
    external: true
DC;
  require alias("@code");
  ?>

  <p>I also added the Docker network to my website's Docker Compose and then restarted the container as well as starting the tunnel container. I tried navigating to <a href="https://homelab.nicolldouglas.dev">homelab.nicolldouglas.dev</a> and surprisingly the site showed up without issue—mission accomplished at the behest of Cloudflare convenience.</p>
</section>

<section>
  <h2>About Tunnels</h2>

  <p>I found it interesting how tunnels actually work when reading the docs so I've decided to make some notes below on it:</p>

  <ul>
    <li>A tunnel to a machine is created by initiating an outbound connection from the machine to elsewhere.</li>
    <li>The connection is kept open in order to feed back other connections through it.</li>
    <li>The machine or network on the other end pipes through these connections as if they were responses to the original connection.</li>
    <li>No new inbound connections are ever made to your machine so there is no need for port forwarding or any other form of network exposure.</li>
  </ul>

  <p>When it comes to Cloudflare tunnels, the image below from their docs sums it up pretty well:</p>

  <img src="https://developers.cloudflare.com/_astro/handshake.eh3a-Ml1_1IcAgC.webp" width="700" alt="Cloudflare tunnel">

  <p>But now this website has been successfully made public which is huge. The next goal is for me to set up some CI/CD with Gitea to automatically redeploy the site's Docker container when I push to main.</p>
</section>
<?php
require alias("@tail");
