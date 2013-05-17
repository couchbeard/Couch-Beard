<?php

include "header.php";

	$url = "http://imdbapi.org/?id=".$_GET['id']."&plot=full&episode=0";

	$json = file_get_contents($url);

	$res = json_decode($json);

	foreach ($res as $key => $val)
	{
		if ($key == "type" && $val == "TVS")
			{
				echo "<strong>TVDB</strong>: ".imdb_to_tvdb($res->imdb_id)."<br />";
			}
			if (is_array($val) && count($val) < 10)
			{
				echo "<strong>".ucwords($key)."</strong>: ".implode(", ", $val)."<br />";
			}
			else if ($key == "poster")
			{
				echo "<img src='".$val."' /><br />";
			}
			else
			{
				echo "<strong>".ucwords($key)."</strong>: ".$val."<br />";
		}
	}


function imdb_to_tvdb($imdb)
{
	$xml = simplexml_load_string(file_get_contents("http://thetvdb.com/api/GetSeriesByRemoteID.php?imdbid=".$imdb));
	return (string) $xml->Series->children()->seriesid;
}

include "footer.php";
?>