<?php
require __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>Gitea Actions & Act Runner</h2>

  <p>Since the next goal was to set up a redeployment workflow for the website, I had to find a CI/CD solution. Typically with CI/CD I resort to GitHub actions and I quickly found out that Gitea has its own GitHub Actions style CI/CD solution that is built in—naturally called Gitea Actions.</p>

  <p>As described in their docs, Gitea Actions doesn't run any CI/CD jobs but rather delegates them to runners. These runners have to be registered with your Gitea instance so that they are available for use to run jobs in the Gitea Actions job queue. Gitea Actions has it's own dedicated runner called Act runner which can be installed from a <a href="https://hub.docker.com/layers/gitea/act_runner/latest/images/sha256-4f277fc9d817baa05496dc7f1f7274b0fb001c191d23111036be76fc0bbf3512?context=explore">Docker image</a>.</p>

  <p>The only concern I had now was where I would have this runner running. Naturally CI/CD runners can be quite resource-intense and I wanted to keep my laptop where I have my server as lightweight as possible, using it as a just a box to host my services including Gitea. That would then force me to have the runners on my main desktop which is beefy enough but would require me to set up some interoperation with Gitea on the other machine in order for this setup to work.</p>

  <p>However, whilst reading the Gitea docs, I discovered that they actually <em>recommend</em> you having runners on a different machine to your instance in order to not affect it resource-wise. This was perfect then, and having the Act runner on my desktop would work right out of the box. The only trade-off is that this Docker container would be always running on my desktop but that doesn't bother me too much.</p>

  <p>I installed the runner on my desktop via the Docker image according to the docs, registered a new runner in my Gitea dashboard, and added the token to my Docker Compose so Gitea would be able to find the runner over the network. I then started the container and checked Gitea to see if the runner appeared. And surprisingly it did first time:</p>

  <img src="./act-runner.png" width="800" alt="Registered runner">

  <p>Now I had a runner set up and ready to run any Gitea Actions jobs. The way Gitea Actions works is that there will be a queue of jobs in Gitea, and Gitea will check to see if there are any runners available that it can delegate the job to. Any available runners will continuously poll Gitea over the network to see if it has jobs and be able to receive them if so.</p>
</section>

<section>
  <h2>Workflow & Deployment</h2>

  <p>Now that I had the runner set up, it was time to create the deployment workflow. The workflow I wanted was essentially the same process I had as before with my deployment script for this website, but this time it would run whenever I push to main. I would build the Docker image, push it to Docker Hub, SSH into the laptop (server), remove the old container, pull the new image, and start a new container. However, now that I had a dedicated homelab repo set up, I added a <code>docker-compose.yml</code> and <code>deploy.sh</code> file for the website so that after SSH'ing into the server I could <code>cd</code> to the directory where they were (<code>/srv/homelab/services/website</code>) and just run the script.</p>

  <p>I created the workflow file and <a href="https://github.com/nicoll-douglas/homelab-blog/blob/453097b1bac273692ccd273a739300b2141d6604/.gitea/workflows/homelab-deploy.yml">this</a> is what it ended up looking like. I used some repository variables and secrets for the Docker Hub login, SSH, and deploy script location details as appropriate. Much similar to GitHub Actions, the workflow file was located in <code>.gitea/workflows</code> in my repository so that Gitea could pick it up automatically. Before running the workflow I removed the old site container and made sure the Docker Compose file and deployment script were ready on the server. I tried pushing to main to test the workflow, and in the end everything ran successfully via my Act runner instance and the site was up and running just as before:</p>

  <img src="./successful-deploy.png" width="500" alt="Successful deployment">

  <p>Now I had some good CI/CD and automation in place to contiunously update this blog hassle-free. </p>
</section>

<?php
require alias("@tail");
