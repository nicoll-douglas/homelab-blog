<?php

require_once __DIR__ . "/../utils/parseSlug.php";
require_once __DIR__ . "/../utils/getAllBlogSlugs.php";
require_once __DIR__ . "/../utils/getBlogPath.php";
?>
<ul>
  <?php
  foreach (getAllBlogSlugs() as $slug):
    $parsedSlug = parseSlug($slug);
  ?>
    <li>
      <a href="<?= getBlogPath($slug) ?>">
        <?= $parsedSlug["date"] . " - " . $parsedSlug["title"] ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>