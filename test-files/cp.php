<?php
include "header.php";

$url = "http://".$cp_host."/api/".$cp_api."/movie.list/";

$json = file_get_contents($url);

$res = json_decode($json);

$count = 1;
foreach ($res->movies as $v)
{
	echo $count++.") <a href='info.php?id=".$v->library->info->imdb."'>".$v->library->titles[0]->title."</a> (".$v->library->year.")<br />";
}

include "footer.php";
?>