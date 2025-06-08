<?php
$isBlog = false;
$title = "Nicoll's Homelab Blog";
require __DIR__ . "/../src/partials/head.php";
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
<p>Welcome to my homelab blog. The place where you can accompany my homelabbing process and quest for DevOps mastery.</p>
<section>
  <h2>Background</h2>
  <p>If you don't know what a homelab is, allow me to provide you with a definition ripped from the web:</p>
  <blockquote>
    "A homelab is a personal setup of servers, networking equipment, and other IT infrastructure that allows individuals to experiment, learn, and test technologies at home. It provides a hands-on environment for improving IT skills without affecting real production systems."
  </blockquote>
  <p>
    And that's exactly what I have going on as I detail in this blog. I started work on my own personal homelab in May 2025. The idea of starting one had been sitting in the back of my mind for a while. I wanted to learn more about DevOps, CI/CD, Linux, and networking—and homelabbing was the perfect vehicle in mind I had for that. I had an old laptop that I knew I could use as a starting point and so eventually, I decided to dig up that old laptop and put it to use. My homelab infrastrucure as of now, consists of that laptop where I run a few services as well as my regular PC/workstation.
  </p>
  <p>
    The laptop is a Toshiba Satellite C50-B-14D released around 2014-15—refurbished to run Debian 12 instead of an incredibly sluggish windows 10. Listed below are its specs:
  </p>
  <ul>
    <li> Intel Celeron N2830 CPU / 2.16GHz (Dual-core)</li>
    <li>4GB RAM</li>
    <li>500 GB HDD SATA 3MB/s</li>
  </ul>
  <p>I mainly like to use the laptop as just a lightweight box where my services run, whereas for heavier tasks, I prefer to offload them to my main machine which is a decently beefy PC running Ubuntu desktop. Its specs:</p>
  <ul>
    <li>AMD Ryzen 5 5600X CPU / 3.7GHz</li>
    <li>16GB RAM</li>
    <li>250GB SSD</li>
    <li>1TB HDD</li>
  </ul>
  <p>As of now, some things I've done with my homelab include this self-hosted website, a Gitea instance where I self-host a couple of repos, and some basic pipelines and scripts to tie those two together. This blog is mainly a place for me to dump my thoughts and document my work process when it comes to my homelabbing. If you want to keep up with what I'm working on, you can find an index of posts where I've documented things thus far. I also try to regularly update this site as I make more progress. If you want a more polished overview of how I've orchestrated my setup, check out the <a href="https://github.com/nicoll-douglas/homelab">GitHub repo</a> for my homelab and check out my other repos for more cool projects. Enjoy and have a great day!
  </p>
  <p>- Nicoll Douglas</p>
</section>
<section>
  <h2>Posts</h2>
  <?php require __DIR__ . "/../src/partials/blogIndex.php"; ?>
</section>
<?php
$hotLinks = false;
require __DIR__ . "/../src/partials/tail.php";
