<?php
include "header.php";
echo "<div class='row'>";

$json = "{\"jsonrpc\": \"2.0\", \"method\": \"VideoLibrary.GetTVShows\", \"params\": { \"properties\": [\"art\", \"genre\", \"plot\", \"title\", \"originaltitle\", \"year\", \"rating\", \"thumbnail\", \"playcount\", \"file\", \"fanart\"], \"sort\": { \"order\": \"ascending\", \"method\": \"label\" } }, \"id\": \"libTvShows\"}";
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

$arr = $data->result->tvshows;

$count = 0;

echo "<table class='table'>";
foreach ($arr as $v)
{
	if ($count % 5 == 0)
	{
		echo "<tr>";
	}
	echo "<td><div class='span2'>
          <p><a href='info.php?id=".tvdb_to_imdb($v->label)."'><img src='".urldecode(substr($v->art->poster, 8, -1))."' style='height:150px;' /></p>
          <p>".$v->label." (".$v->year.") [".round($v->rating, 1)."]</a></p>
        </div></td>";
    if ($count++ % 5 == 4)
	{
		echo "</tr>";
	}
}
echo "</table>";

echo "</div>";

function tvdb_to_imdb($name)
{
  $xml = simplexml_load_string(file_get_contents("http://thetvdb.com/api/GetSeries.php?seriesname=".urlencode($name)));
  return (string) $xml->Series->children()->IMDB_ID;
}


include "footer.php";
?>