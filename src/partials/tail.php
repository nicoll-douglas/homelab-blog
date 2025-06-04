<?php

if ($isBlog):
  require_once __DIR__ . "/../utils/getAllBlogSlugs.php";
  require_once __DIR__ . "/../utils/getCurrentBlogSlug.php";
  require_once __DIR__ . "/../utils/getBlogPath.php";

  $blogSlugs = getAllBlogSlugs();
  $currentBlogSlug = getCurrentBlogSlug();

  for ($i = 0; $i < count($blogSlugs); $i++) {
    $blogSlug = $blogSlugs[$i];
    if ($blogSlug === $currentBlogSlug) {
      $previousBlogSlug = $blogSlugs[$i - 1];
      $nextBlogSlug = $blogSlugs[$i + 1];

      $previous = empty($previousBlogSlug) ? "/" : getBlogPath($previousBlogSlug);
      $next = empty($nextBlogSlug) ? "/" : getBlogPath($nextBlogSlug);
      break;
    }
  }
?>
  <div id="hot-links">
    <a href="<?= $previous ?>">Previous</a>
    <a href="/">Home</a>
    <a href="<?= $next ?>">Next</a>
  </div>
<?php endif; ?>

</main>
</body>

</html>