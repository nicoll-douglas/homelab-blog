<?php

function getAllBlogSlugs()
{
  $blogsDir = __DIR__ . "/../../public/blog";
  $blogSlugs = array_values(array_diff(scandir($blogsDir), [".", ".."]));
  return $blogSlugs;
}
