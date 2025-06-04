<?php

require_once __DIR__ . "/../utils/getCurrentBlogSlug.php";
require_once __DIR__ . "/../utils/parseSlug.php";

if (!isset($isBlog)) {
  $isBlog = true;
}

if ($isBlog) {
  $currentBlogSlug = getCurrentBlogSlug();
  [
    "title" => $title,
    "date" => $date
  ] = parseSlug($currentBlogSlug);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=block" rel="stylesheet">
  <link rel="stylesheet" href="/assets/styles.css">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?>
  </title>
</head>

<body>
  <main>
    <?php if ($isBlog): ?>
      <h1><?= $title ?></h1>
      <p><?= $date ?></p>
    <?php endif;
