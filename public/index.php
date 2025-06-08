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
  <p>If you don't know what a homelab is, allow me to provide you with a definition ripped from the internet:</p>
  <blockquote>
    "A homelab is a personal setup of servers, networking equipment, and other IT infrastructure that allows individuals to experiment, learn, and test technologies at home. It provides a hands-on environment for improving IT skills without affecting real production systems."
  </blockquote>
  <p>
    I started work on my own personal homelab in May 2025. The idea of starting one had been sitting in the back of my mind for a while. I had an old laptop that I knew I could use as a starting point and eventually, I decided to dig up that old laptop and put it to use. My homelab as of now, consists of that laptop as well as my regular PC/workstation.
  </p>
  <p>
    The laptop is a Toshiba Satellite C50-B-14D released around 2014-15—refurbished to run Debian 12 instead of an incredibly sluggish windows 10. Listed below are its specs:
  </p>
  <ul>
    <li> Intel Celeron N2830 CPU / 2.16GHz (Dual-core)</li>
    <li>4GB RAM</li>
    <li>500 GB HDD SATA 3MB/s</li>
  </ul>
  <p>
    As for my main machine, I have a decently beefy PC running Ubuntu desktop. Specs:
  </p>
  <ul>
    <li>AMD Ryzen 5 5600X CPU / 3.7GHz</li>
    <li>16GB RAM</li>
    <li>250GB SSD</li>
    <li>1TB HDD</li>
  </ul>
  <p>
    At the time of writing, my homelab setup is pretty new. I'm still in the process of setting up my first few services, learning more about networking, and what I can actually do with my setup. Below you can find an index of posts where I've documented my homelabbing experience thus far.
  </p>
  <p>Check out my <a href="https://github.com/nicoll-douglas">GitHub</a> for more cool projects.</p>
  <p>- Nicoll Douglas</p>
</section>
<section>
  <h2>Posts</h2>
  <?php require __DIR__ . "/../src/partials/blogIndex.php"; ?>
</section>
<?php
$hotLinks = false;
require __DIR__ . "/../src/partials/tail.php";
