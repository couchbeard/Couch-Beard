<?php

	$name = "Arrested Development";
  $xml = simplexml_load_string(file_get_contents("http://thetvdb.com/api/GetSeries.php?seriesname=".urlencode($name)));
  echo (string) $xml->Series->children()->IMDB_ID;

?>