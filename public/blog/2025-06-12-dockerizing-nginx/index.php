<?php
require __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>Loose Ends</h2>

  <p>Now I had all of my main original goals achieved for this website in my homelab, it was locally accessible, publically accessible, and easily update-able with my CI/CD in place. However, things as they are are a bit all over the place. First of all, I access the site locally at http://192.168.1.5 (my server) with an Nginx reverse proxy that proxies to http://127.0.0.1:8080 (the website's container exposed to the host). But second of all however, Cloudflare accesses the container over the dedicated Docker network I set up for the tunnel. I had Nginx running on bare metal but I figured it would be cleaner if I Dockerized Nginx so that both I, locally, and the Cloudflare tunnel could access the site over a Docker network. This would also prove to more scalable as I was planning to host other sites at this point, namely my portfolio.</p>
</section>

<section>
  <h2>Dockerization</h2>

  <p>I figured that it would be easiest to add an Nginx service to my Docker Compose file in my homelab directory/repo where I defined the Cloudflare service. This would create one holistic "web" service allowing me to access this site's container as well as others more cleanly with Nginx reverse proxies, both locally and externally. I added the following definitions to the Docker compose file in order to achieve this:</p>

  <?php
  $code = <<<DC
  nginx:
    image: nginx:alpine
    container_name: nginx
    restart: unless-stopped
    networks:
      - cloudflare-tunnel-net
      - nginx-proxy-net
    volumes:
      - ../../config/nginx/nginx.conf:/etc/nginx/nginx.conf:ro
      - ../../config/nginx/sites:/etc/nginx/sites-enabled:ro
      - ../../logs/nginx:/var/log/nginx
    ports:
      - "80:80"

networks:
  cloudflare-tunnel-net:
  nginx-proxy-net:
    external: true

DC;
  require alias("@code");
  ?>

  <p>I changed the original <code>cloudflare-tunnel-net</code> network to no longer be external so that it's only responsible for connecting the Cloudflare tunnel and Nginx. Instead, now I have the external <code>nginx-proxy-net</code> network which is responsible for facilitating the reverse proxies to website containers elsewhere (provided they also use the network).</p>

  <p>Now the flow of HTTP requests through the tunnel could access the appropriate container, provided I mapped the domain name in the Cloudflare dashboard to http://nginx (which I did) which would be new the service URL in the tunnel.</p>

  <p>With the Docker Compose setup, I would also expose and bind host port 80 to the Nginx container so that I could access my website over my local network via the appropriate Nginx reverse proxy. I also made sure to uninstall Nginx from my machine to avoid any conflicts and since I wouldn't be needing it anymore.</p>

  <p>Creating this setup also taught me a bit about how Docker networks work in more detail. Networks not defined as external in a Docker Compose file will be automatically created when you bring up the containers and only accessible to services in the same Docker Compose projects, i.e <code>cloudflare-tunnel-net</code>. Networks defined otherwise have to be manually created before Docker can bring them into the Compose. Defining them as external also allows services in other Docker Compose projects to use them, i.e <code>nginx-proxy-net</code> (these networks live outside the Compose lifecycle).</p>

  <p>I then created <code>nginx-proxy-net</code> with <code>docker network create nginx-proxy-net</code>. Now everything was in place in order to Dockerize Nginx and update my setup.</p>
</section>

<section>
  <h2>Site Configuration & Starting Containers</h2>

  <p>I updated my site's <code>docker-compose.yml</code> to use the new external <code>nginx-proxy-net</code> network instead of the old <code>cloudflare-tunnel-net</code> which was repurposed. I then changed the container name and restarted the container. The container would now be accessible over <code>nginx-proxy-net</code> via http://blog, which we would use in the Nginx site configuration.</p>

  <p>My Nginx container would be using the <code>config/nginx/sites</code> directory as a bind mount for the Nginx virtual host definitions. So I created a <code>blog.conf</code> file which would be the configuration used to reverse proxy to the container. This is what the configuration ended up looking like:</p>

  <?php
  $code = <<<'DC'
server {
    listen 80;
    server_name homelab.nicolldouglas.dev homelab.nicolldouglas.local;

    location / {
        proxy_pass http://blog;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
  }
}
DC;
  require alias("@code");
  ?>

  <p>Now, any incoming HTTP requests from the tunnel where the Host header matched the <code>server_name</code>s would be reverse proxied to http://blog via the <code>nginx-proxy-net</code> network. homelab.nicolldouglas.dev would be the public facing domain, and homelab.nicolldouglas.local would the the private one solely for access from my dev machine.</p>

  <p>My site configurations were now in place in order for my Nginx-Cloudflare setup to proxy to my container. Now it was time to start my Nginx-Cloudflare setup. I ran <code>docker compose up</code>, checked that the containers were running as well as the logs. In my Cloudflare dashboard, the tunnel was also show as online. All seemed fine so I navigated to https://homelab.nicolldouglas.dev to see if my site was accessible, and indeed it was so the setup was fully functional.</p>

  <p>In order to access my site over my private network from my dev machine, I added the following line to my <code>/etc/hosts</code> file: <code>192.168.1.5 homelab.nicolldouglas.local</code>. I tested in the browser and it also showed up—double success.</p>

  <p>As well as doing that, I updated my firewall to allow forwarding to the Nginx container port (80) from my main machine. This was achieved with the following: <code>sudo ufw route allow from 192.168.1.6 to any port 80 proto tcp</code></p>

  <p>But now, I had successfully Dockerized Nginx and cleaned up the setup of my web services. Below is a diagram of how the archicture looks now:</p>

  <img src="./architecture.png" width="700" alt="Web services architecture">
</section>
<?php
require alias("@tail");
