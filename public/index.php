<?php
require_once __DIR__ . "/../src/bootstrap.php";
$isBlog = false;
$title = "Nicoll's Homelab Blog";
require alias("@head");
?>
<pre id="ascii-art" aria-hidden="true">
             ,----------------,              ,---------,
        ,-----------------------,          ,"        ,"|
      ,"                      ,"|        ,"        ,"  |
     +-----------------------+  |      ,"        ,"    |
     |  .-----------------.  |  |     +---------+      |
     |  |                 |  |  |     | -==----'|      |
     |  |  Nicoll's       |  |  |     |         |      |
     |  |  Homelab        |  |  |/----|`---=    |      |
     |  |  C:\>_          |  |  |   ,/|==== ooo |      ;
     |  |                 |  |  |  // |(((( [33]|    ,"
     |  `-----------------'  |," .;'| |((((     |  ," 
     +-----------------------+  ;;  | |         |,"
        /_)______________(_/  //'   | +---------+
   ___________________________/___  `,
  /  oooooooooooooooo  .o.  oooo /,   \,"-----------
 / ==ooooooooooooooo==.o.  ooo= //   ,`\--{)B     ,"
/_==__==========__==_ooo__ooo=_/'   /___________,"
`-----------------------------'
</pre>

<h1>Nicoll's Homelab Blog</h1>

<p>Welcome to my homelab blog. The place where you can accompany my homelabbing process, self-hosting escapades, and quest for DevOps mastery.</p>

<section>
  <h2>Background</h2>

  <p>If you don't know what a homelab is, allow me to provide you with a definition from the web:</p>

  <blockquote>
    "A homelab is a personal setup of servers, networking equipment, and other IT infrastructure that allows individuals to self-host, experiment, learn, and test technologies at home. It provides a hands-on environment for improving IT skills without affecting real production systems."
  </blockquote>

  <p>And that's exactly what I have going on as I detail in this blog. I started work on my own personal homelab in May 2025. The idea of starting one had been sitting in the back of my mind for a while. I wanted to learn more about DevOps, CI/CD, Linux, and networking—and homelabbing was the perfect vehicle in mind I had for that. I had an old laptop that I knew I could use as a starting point and so one day, I decided to dig up that old laptop and put it to use. My homelab infrastrucure as of now consists of that laptop as well as my regular PC/workstation.</p>

  <p>The laptop is a Toshiba Satellite C50-B-14D released around 2014-15—refurbished to run Debian 12 instead of an incredibly sluggish windows 10. In terms of hardware it has a dual-core 2.16GHz Intel Celeron N2830 CPU, 4GB of RAM, and a 500GB HDD with a SATA 2 interface—plenty enough to run a few services, containers, and allow general experimentation. I keep my laptop lightweight and offload more heavy tasks to my beefier PC (such as CI/CD runners) where I run Ubuntu Desktop.</p>

  <p>As of now, some things I've done with my homelab include this self-hosted website, a Gitea instance where I self-host a couple of repos, and some basic pipelines and scripts to tie those two together. This blog is mainly a place for me to dump my thoughts and document my work process. If you want to keep up with what I'm working on, you can find an index of posts below. I also try to regularly update this site as I make more progress. If you want a more polished overview of how I've orchestrated my setup, check out the <a href="https://github.com/nicoll-douglas/homelab">GitHub repo</a> for my homelab and check out my other repos for more cool projects. Otherwise, enjoy and have a great day!</p>

  <p>- Nicoll Douglas</p>

</section>

<section>
  <h2>Posts</h2>

  <?php require alias("@src/partials/blogIndex.php") ?>
</section>
<?php
$hotLinks = false;
require alias("@tail");
