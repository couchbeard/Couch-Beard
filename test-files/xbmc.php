<?php

$json = "{\"jsonrpc\": \"2.0\", \"method\": \"VideoLibrary.GetMovies\", \"params\": { \"properties\" : [\"art\", \"rating\", \"playcount\", \"year\", \"imdbnumber\"], \"sort\": { \"order\": \"ascending\", \"method\": \"label\", \"ignorearticle\": true } }, \"id\": \"libMovies\"}";
$json = urlencode($json);
$url = "http://".$xbmc_host."/jsonrpc?request=".$json;

$opts = array('http' =>
  array(
    'header'  => "Content-Type: application/json\r\n".
    "Authorization: Basic ".base64_encode("$xbmc_user:$xbmc_pass")."",
    'timeout' => 60
  )
);
                        
$context  = stream_context_create($opts);
$result = file_get_contents($url, true, $context);

$data = json_decode($result);

$arr = $data->result->movies;

//echo "<strong>".count($arr)." movies found</strong><br />";
$count = 1;
foreach ($arr as $v)
{
	//print_r($v);
	//echo "<img src='".urldecode(substr($v->art->poster, 8, -1))."' height='100px' /> ".utf8_decode($v->label)."<br />";
	echo $count++.") <a href='info.php?id=".$v->imdbnumber."'>".utf8_decode($v->label)."</a> (".$v->year.")<br />";
}

?>