<?php

require_once __DIR__ . "/formatDateSlug.php";

function parseSlug($slug)
{
  if (preg_match('/^(\d{4}-\d{2}-\d{2})-(.+)$/', $slug, $matches)) {
    $dateSlug = $matches[1];
    $titleSlug = $matches[2];

    $title = str_replace("-", " ", $titleSlug);

    $date = formatDateSlug($dateSlug);

    return [
      "dateSlug" => $dateSlug,
      "titleSlug" => $titleSlug,
      "date" => $date,
      "title" => $title,
    ];
  }

  return [
    "dateSlug" => null,
    "titleSlug" => null,
    "date" => null,
    "title" => null
  ];
}
