<?php

require_once __DIR__ . "/formatDateSlug.php";

function createSlug($title)
{
  $dateSlug = date("Y-m-d");
  $titleSlug = rawurlencode(str_replace(" ", "-", $title));
  $slug = $dateSlug . "-" . $titleSlug;
  $date = formatDateSlug($dateSlug);

  return [
    $slug,
    [
      "title" => $title,
      "date" => $date,
      "dateSlug" => $dateSlug,
      "titleSlug" => $titleSlug
    ]
  ];
}
