<?php

function formatDateSlug($dateSlug)
{
  $dateObj = DateTime::createFromFormat("Y-m-d", $dateSlug);
  $date = $dateObj->format("d/m/y");
  return $date;
}
