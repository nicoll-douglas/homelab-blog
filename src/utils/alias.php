<?php

function alias($alias)
{
  global $aliases;

  foreach ($aliases as $key => $path) {
    if (strpos($alias, $key) === 0) {
      return $path . substr($alias, strlen($key));
    }
  }

  return null;
}
