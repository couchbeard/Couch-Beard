<?php
include "header.php";

$url = "http://".$sb_host."/api/".$sb_api."/?cmd=shows";

$json = file_get_contents($url);

$res = json_decode($json)->data;

$data = array();

foreach ($res as $key => $val)
{
	$data[] = $res->$key->show_name;
}

asort($data);

foreach ($data as $series)
{
	echo $series."<br />";
}

include "footer.php";
?>