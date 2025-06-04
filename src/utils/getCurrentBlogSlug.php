<?php

function getCurrentBlogSlug()
{
  $uri = $_SERVER['REQUEST_URI'];
  $basename = basename(parse_url($uri, PHP_URL_PATH));
  return $basename;
}
