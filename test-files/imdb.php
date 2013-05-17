<?php

$url = "http://imdbapi.org/?q=".$_GET['q']."&episode=0&limit=10";

$json = file_get_contents($url);

$res = json_decode($json);


foreach ($res as $data)
{
	foreach ($data as $key => $val)
	{
		if ($key == "type" && $val == "TVS")
		{
			echo "<strong>TVDB</strong>: ".imdb_to_tvdb($data->imdb_id)."<br />";
		}
		if (is_array($val) && count($val) < 10)
		{
			echo "<strong>".$key."</strong>: ".implode(", ", $val)."<br />";
		}
		else if ($key == "poster")
		{
			echo "<img src='".$val."' /><br />";
		}
		else
		{
			echo "<strong>".$key."</strong>: ".$val."<br />";
		}
	}
	echo "<br /><br />";
}

function imdb_to_tvdb($imdb)
{
	$xml = simplexml_load_string(file_get_contents("http://thetvdb.com/api/GetSeriesByRemoteID.php?imdbid=".$imdb));
	return (string) $xml->Series->children()->seriesid;
}

?>