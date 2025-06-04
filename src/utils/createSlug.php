<?php

require_once __DIR__ . "/formatDateSlug.php";

function createSlug($rawTitle)
{
  $title = ucwords($rawTitle);
  $dateSlug = date("Y-m-d");
  $titleSlug = strtolower(str_replace(" ", "-", $title));
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
