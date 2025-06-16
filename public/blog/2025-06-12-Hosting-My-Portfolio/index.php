<?php
require __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>The Mission</h2>

  <p>My next goal with the homelab was to also self-host my portfolio website. Now that I had a nice containerized Nginx reverse proxy setup, this would be quite easy so I had no reason not to. Yesterday I spent most of the day rewriting my website in PHP and plain JavaScript instead of the old Next.js slop that it was. This was so that it could be as lightweight as possible and avoid strain on my resources.</p>

  <p>In order to host it seamlessly, I would have to follow the following steps:</p>

  <ol>
    <li>Add the public hostname (nicolldouglas.dev) to the Cloudflare tunnel and map it to http://nginx in order to pipe HTTP requests to Nginx.</li>
    <li>Create a Docker Compose file for setup, expose port 3002 for local access, and start the container.</li>
    <li>Create a workflow file for auto redeploy (I could just reuse the one for the blog).</li>
    <li>Create an Nginx site configuration to proxy requests to the site container's URL.</li>
    <li>Reload Nginx to register the new site.</li>
  </ol>

  <p>So that's exactly what I did.</p>
</section>

<section>
  <h2>Setup & Deployment</h2>

  <p>After completing steps 1 and 2, I had the following <code>docker-compose.yml</code> file defined in my homelab repo/directory (as well as <code>.env</code> and <code>deploy.sh</code> files):</p>

  <?php
  $code = <<<'DC'
services:
  portfolio:
    image: ${DOCKERHUB_IMAGE}
    container_name: portfolio_website
    restart: unless-stopped
    networks:
      - nginx-proxy-net
    environment:
      - APP_ENV=${APP_ENV}
    ports:
      - "3002:80"

networks:
  nginx-proxy-net:
    external: true
DC;
  require alias("@code");
  ?>

  <p>I copied over the workflow file from the blog repo to the portfolio repo since the deployment process was pretty much the same. After that I also copied the Nginx site configuration file for the blog and modified it appropriately for my portfolio site. Namely changing the <code>server_name</code> directive to nicolldouglas.dev and the <code>proxy_pass</code> directive to http://portfolio which would be the URL of the container on <code>nginx-proxy-net</code>.</p>

  <p>I then restarted the Nginx container (instead of reloading inside the container for whatever reason) and tested to see if the site was accessible in the browser as per usual. And indeed it was, so now you can access my portfolio at <a href="https://nicolldouglas.dev">https://nicolldouglas.dev</a>. It was also accessible locally at http://192.168.1.5:3002 which was good.</p>

  <p>As I imagined, Dockerizing Nginx the way I did made setting up new sites and reverse proxies incredibly hassle-free.</p>
</section>
<?php
require alias("@tail");
