<?php

require_once __DIR__ . "/../src/utils/createSlug.php";

// prompt for input
$prompt = fn() => readline("Enter a title for the blog: ");
$title = $prompt();
while (empty($title)) {
  echo "Title cannot be empty.";
  $title = $prompt();
}

// create slug
[$slug, $details] = createSlug($title);

// create the blog directory
$blogsDir = __DIR__ . "/../public/blog";
$newBlogDir = $blogsDir . DIRECTORY_SEPARATOR . $slug;
$newBlogFile = $newBlogDir . DIRECTORY_SEPARATOR . "index.php";

echo "Creating blog directory..." . PHP_EOL;
$result = mkdir($newBlogDir, 0775);
if ($result === false) {
  echo "Failed to create blog directory." . PHP_EOL;
  exit;
} else {
  echo "Created blog directory." . PHP_EOL;
}

// create the blog file
$formattedTitle = $details["title"];
$formattedDate = $details["date"];

$template = <<<PHP
<?php
require __DIR__ . "/../../../src/partials/head.php";
?>

<!-- content -->

<?php
require __DIR__ . "/../../../src/partials/tail.php";
PHP;

echo "Creating blog file..." . PHP_EOL;
$result = file_put_contents($newBlogFile, $template);
if ($result === false) {
  echo "Failed to create blog file." . PHP_EOL;
} else {
  echo "Created blog file." . PHP_EOL;
}
