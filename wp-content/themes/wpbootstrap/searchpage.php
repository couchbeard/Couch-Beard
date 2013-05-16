<?php
/*
Template Name: Search Page
*/
?>
<?php get_header(); ?>
<?php
if (isset($_GET['id'])) {
	$url = "http://imdbapi.org/?id=".$_GET['id']."&episode=0&limit=1";

	$json = file_get_contents($url);

	$data = json_decode($json);
?>
	<div class="row-fluid">
		<div class="span12">
    		<legend><?php echo $data->title; ?></legend>
    		<div class="row-fluid">
      			<div class="span3">
      				<div class="coverSearch">
	        			<img src="<?php echo $data->poster; ?>" class="img-rounded"/>
	        			<center><p class="lead"><?php echo $data->rating; ?></p></center>
	        		</div>
  				</div>
				<div class="span5">
  					<p class="lead"><?php echo implode(', ', $data->genres); ?></p>
  				</div>
  				<div class="span1 pull-right">
					<p class="lead"><?php echo $data->country[0]; ?></p>
  				</div>
  				<div class="span1 pull-right">
					<p class="lead"><?php echo $data->year; ?></p>
  				</div>
  				<div class="row-fluid">
  					<div class="span1"></div>
  					<div class="span3">
						<p class="lead"><?php echo implode(', ', $data->language); ?></p>
					</div>
					<div class="span3">
						<p class="lead"><?php echo date('g:i', strtotime('today ' . (string) $data->runtime[0])); ?></p>
					</div>
	    		</div>
    		</div>
    		<div class="span1 pull-right">
				<?php if($data->type == 'M') { ?>
					Couchpotato
				<?php } else { ?>
					Sickbeard
				<?php } ?>
    		</div>
    		<div class="span1 pull-right">
				<a href="<?php echo $data->imdb_url; ?>">IMDB</a>
    		</div>
  		</div>
	</div>
<?php
}

function imdb_to_tvdb($imdb)
{
	$xml = simplexml_load_string(file_get_contents("http://thetvdb.com/api/GetSeriesByRemoteID.php?imdbid=".$imdb));
	return (string) $xml->Series->children()->seriesid;
}

?>
<?php get_footer(); ?>