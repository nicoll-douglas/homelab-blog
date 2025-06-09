<?php
require_once __DIR__ . "/../../../src/bootstrap.php";
require alias("@head");
?>
<section>
  <h2>Deployment Script</h2>

  <p>Since I wanted to host this website on my Debian machine, I had to come up with a deployment workflow. My idea was to have a bash script that built the Docker image on my main PC, pushed it Docker Hub, SSH'd into my server, pulled the image, and then started the container. That way the build process would be offloaded to my beefier PC and save the laptop some potential misery.</p>

  <p>Some learning outcomes whilst I was creating the script:</p>

  <ul>
    <li>
      Naturally I would have to send a batch of bash commands over SSH in order to operate on the server. One of them would be a command to login to Docker with my username and token (<code>echo "$DOCKERHUB_PASSWORD" | docker login -u "$DOCKERHUB_USERNAME" --password-stdin</code>). That would require me to inline some variables from the shell environment that I would be loading from a .env file. I learnt that when you surround a variable using double quotes or heredoc syntax in bash, the script will expand inline variables <STRONG>locally</STRONG> and evalute them to their values before sending the script over SSH. Single quotes prevent local expansion and will just evalute to the raw text (e.g <code>$DOCKERHUB_USERNAME</code>).
    </li>
    <li>
      With a command in bash we can use <code>|| true</code> to essentially say: if this command fails don't treat it as a failure. This made sense and is a common pattern of using fallbacks in boolean conditions.
    </li>
    <li>
      <code>set -e</code> configures the shell to exit when any future command exits with a non-zero exit code (failure) which would be useful in our case.
    </li>
    <li>
      When using the <code>scp</code> command, permissions for the transfered file will be set according to the <code>umask</code> of the server and ownership is transferred to the user you are using in the command.
    </li>
    <li>In production, typical permissions for a .env file should be 600 so I added a line in my script to ensure that.</li>
  </ul>

  <p>After finishing the script, I made it executable with <code>chmod +x deploy.sh</code>. I also made a <code>dev.sh</code> script to start my development container more easily. The <code>dev.sh</code> script worked but I was yet to test <code>deploy.sh</code>.</p>

  <p>Before I could test the deployment script, I had to prepare the server and my client for deployment.</p>
</section>

<section>
  <h2>Client & Server Setup</h2>

  <p>The first and easy step was installing Docker on the server and that went fine. Then I had to create a new user in order for my deployment script to SSH with. I ran <code>sudo adduser ci</code> and added them to the <code>docker</code> group with <code>sudo usermod -aG docker ci</code> so they could run Docker commands without sudo. I tested that this was now the case by switching to the user with <code>su - ci</code> and running <code>docker run hello-world</code> which went fine.</p>

  <p>Next I had to generate some SSH keys on my main machine that would be the SSH client. Out of curiosity, I ended up looking into the differences between RSA keys and ED25519 keys. Essentially, I learnt that RSA keys are slower and ED25519 keys are faster and more modern so they are preferred. I ran <code>ssh-keygen -t ed25519 -C "debian-box ci user (continuous integration)"</code> and didn't include a passphrase since I would be SSH'ing with an automated script. I aptly named the key "debian-box-ci".</p>
  <p>I then tried to copy the SSH public key to the server with <code>ssh-copy-id -i ~/.ssh/debian-box-ci.pub ci@192.168.1.5</code> but that didn't work now since I disabled password authentication.</p>

  <p>I had to manually switch to the ci user, create the .ssh directory, give 700 permissions to it, create the authorized_keys file, add the contents of the public key to it and give 600 permissions to it. A more manual process, but it made me learn about what goes into the process of <code>ssh-copy-id</code>. Now I tested to see if I could SSH with <code>ssh -i ~/.ssh/debian-box-ci</code> and it worked.</p>

  <p>Out of curiosity, I tried to SSH without specifying the private key file and it also worked. That made me question: does the <code>ssh</code> command check all possible keys when trying to authenticate? Essentially yes, <code>ssh</code> will check all keys loaded into <code>ssh-agent</code>. Since I already SSH'd once with the specified key, the private key was loaded into the agent. If nothing is loaded into the agent, <code>ssh</code> checks some common file names like <code>~/.ssh/id_ed25519</code> and <code>~/.ssh/id_rsa</code>.</p>

  <p>Some useful commands I learnt for working with the SSH agent:</p>

  <ul>
    <li><code>ssh-add -l</code> checks which private keys / credentials are loaded into <code>ssh-agent</code></li>
    <li><code>ssh-add -d</code> removes a key from ssh-agent with the specified file path (e.g <code>ssh-add -d ~/.ssh/id_ed25519</code>)</li>
    <li><code>ssh-add -D</code> removes everything stored in the agent</li>
  </ul>

  <p>But then, getting back on track, the next thing I had to do was generate an access token / password in order to read images from my Docker Hub account. I did that and added it my .env file as well as other necessary values.</p>

  <p>When using the <code>docker login</code> command, apparently there are a few ways to go about it. Initially I was going to use <code>docker login -u $DOCKERHUB_USERNAME -p $DOCKERHUB_PASSWORD</code>. However, apparently when you do that the password is exposed in your shell history and can be found using <code>ps aux</code>. So the best practice to read the password from the standard input with <code>echo "$DOCKERHUB_PASSWORD" | docker login -u $DOCKERHUB_USERNAME --password-stdin</code> which is what I included in the script and what prevents password exposure.</p>

  <p>Now everything was ready in order for me to run the script. I had SSH and Docker sorted out.</p>
</section>

<section>
  <h2>Running the Script</h2>

  <p>Then I tried running the script and everything was going fine. I got the following warning when using <code>docker login</code>:</p>

  <code>WARNING! Your credentials are stored unencrypted in '/home/ci/.docker/config.json'. Configure a credential helper to remove this warning. See https://docs.docker.com/go/credential-store/</code>

  <p>I'd seen that before but I learnt that apparently <code>docker logout</code> will remove the credentials from the file so the warning should be safe to ignore since I added that to my script. It does create a small attack window whilst the script is running, but it's not a huge concern.</p>

  <p>I had a few hiccups and bugs along the way getting the script to run from start to finish so I had to spend some time debugging, but eventually I managed to get it to run.</p>
</section>

<section>
  <h2>Reverse Proxy</h2>

  <p>My website's container was now running internally on port 8080 after successfully running the script. The next goal would be to make it accessible on my local network over HTTP which is where my web server would come in that I installed a few days prior. I was going to use a reverse proxy with Nginx in order to direct requests to my container.</p>

  <p>I made a new site configuration with <code>sudo nano /etc/nginx/sites-available/homelab-blog</code> and added the following:</p>

  <?php
  $code = <<<BASH
server {
    listen 80;
    server_name _;

    location / {
        proxy_pass http://127.0.0.1:8080;

        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
BASH;
  require alias("@code");
  ?>

  <p>This apparently will proxy incoming requests on port 80 of my host machine to my container running internally at 127.0.0.1:8080 as well as necessary headers. Then I enabled the site by creating a symlink with: <code>sudo ln -s /etc/nginx/sites-available/homelab-blog /etc/nginx/sites-enabled/homelab-blog</code>. I ran <code>sudo nginx -t</code> to test the file for syntax errors and all was good. After disabling the default site and reloading Nginx with <code>sudo systemctl reload nginx</code>, the site was now accessible on my local network at the machine's IP.</p>

  <p>So now I had my site accessible on my local network as well as a deployment script—mission accomplished.</p>

  <p>Also, some post-deployment considerations I had was that I may want to set up a pipeline for the site to auto-deploy when I push to Git. I'm thinking of running a local Git server on my Debian machine so that I can self-host the repository and then create the deployment pipeline.</p>
</section>
<?php
require alias("@tail");
