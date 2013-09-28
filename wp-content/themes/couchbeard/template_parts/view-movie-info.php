<?php
try {
	$imdb = new imdbAPI($_GET['id']);
} catch (Exception $e) {
	printf(__('No movie found with ID: <strong>%s</strong>', 'couchbeard'), $_GET['id']);
	return;
}

try {
	$xbmc = new xbmc();
} catch (Exception $e) {
}

try {
	$cp = new couchpotato();
} catch (Exception $e) {

}

try {
	$sb = new sickbeard();
} catch (Exception $e) {

}
?>
<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
	<legend><?php echo $imdb->title(); ?></legend>
	<div class="row">
		<div class="span3">
			<img id="wantedOverlay" src="<?php print IMAGES; ?>/download_logo_square.png" />
			<img id="checkOverlay" src="<?php print IMAGES; ?>/check.png" />
			<div id="searchCover">
				<div id="coverOverlay">
					<center>
						<div class="opensans">
							<?php echo $imdb->imdbRating(); ?>
						</div>
						<br /><br /><br /><br />
						<div class="josefinslab">
							<?php echo $imdb->imdbVotes(); ?>
						</div>
					</center>
				</div>
				<img id="searchpageCover" src="<?php echo ($imdb->poster() == 'N/A') ? IMAGES . '/no_cover.png' : $imdb->poster(); ?>" class="img-rounded"/>
			</div>
			<div class="rating" data-average="<?php echo floatval($imdb->imdbRating()); ?>" data-id="1" data-toggle="tooltip" data-placement="bottom" title="<?php echo $imdb->imdbRating() . ' / ' . $imdb->imdbVotes(); ?>"></div>
			<div class="ratingtext"><center><p class="lead"><?php echo $imdb->imdbRating(); ?></p></center></div>	
		</div>
		<div class="span9">
			<div class="row">
				<div class="span8 pull-left">
					<p class="lead pline"><?php echo $imdb->genre(); ?></p>
				</div>
				<div class="span1">
					<p class="lead"><?php echo $imdb->year(); ?></p>
				</div>
			</div>
			<div class="row">
				<div class="span8 pull-left">
					<p class="lead pline"><?php //$imdb->country(); ?></p>
				</div>
				<div class="span1">
					<?php
						$time = explode('h', $imdb->runtime());
						if (strpos($imdb->runtime(), 'h')) {
							$h = trim($time[0]);
							$m = explode('min', $time[1]);
							$m = trim($m[0]);
						} else {
							$h = 0;
							$m = explode('min', $time[0]);
							$m = trim($m[0]);
						}
					?>
					<p class="lead"><?php echo $h . ':' . ((strlen($m) < 2) ? '0' . $m : $m); ?></p>
				</div>				
			</div>
			<div class="row">
				<div class="span8 pull-left">
					<p class="lead pline"><?php echo $imdb->actors(); ?></p>
				</div>
			</div>
			<br />
			<div class="row">
				<div class="span8 pull-left">
					<p class="lead pline"><?php echo $imdb->writer(); ?></p>
				</div>
			</div>
			<br />			
			<div class="row">
				<div class="span9 pull-left">
					<p><?php echo $imdb->plot(); ?></p>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span2 pull-right">
			<?php
			
			if($imdb->type() == 'movie') {
					if (isset($xbmc) && $xbmc->movieOwned($imdb->getID()))
					{ ?>
						<button class="btn btn-inverse pull-right disabled" disabled="disabled"><i><?php _e('Movie owned', 'couchbeard'); ?></i></button>
						<script>
							$('#checkOverlay').css("visibility", "visible");
						</script>
					<?php }
					else if (isset($cp) && $cp->movieWanted($imdb->getID()))
					{ ?>
						<button class="btn btn-inverse pull-right disabled" disabled="disabled"><i><?php _e('Movie added', 'couchbeard'); ?></i></button>
						<script>
							$('#wantedOverlay').css("visibility", "visible");
						</script>					
					<?php }
					else if (isset($cp))
					{
					?>
						<button class="btn btn-inverse pull-right" id="addMovie"><?php _e('Add movie', 'couchbeard'); ?></button>
			<?php
					}
			} else if ($imdb->type() == 'series') {
				$show = isset($sb) ? $sb->showAdded($imdb->getID()) : false;
				if ($show) { 
				?>
					<script>
						$('#wantedOverlay').css('visibility', 'visible');
					</script>
					<?php if ($show->status == 'Continuing') { ?>
						<button class="btn btn-inverse pull-right disabled" disabled="disabled"><i><?php _e('TV show continuing', 'couchbeard'); ?></i></button>
					<?php } else { ?>
						<button class="btn btn-inverse pull-right disabled" disabled="disabled"><i><?php _e('TV show ended', 'couchbeard'); ?></i></button>
					<?php } ?>
				<?php } else { ?>
					<button class="btn btn-inverse pull-right" id="addTV"><?php _e('Add TV show', 'couchbeard'); ?></button>
				<?php } ?>
				<?php if (isset($xbmc) && xbmc_showOwned($imdb->getID())) { ?>
					<script>
						if ($('#wantedOverlay').show()) {
							$('#wantedOverlay').css('margin-top', '60px');
						}
						$('#checkOverlay').css('visibility', 'visible');
					</script>
				<?php }
			} ?>
		</div>
		<div class="span1 pull-left">
			<a href="http://www.imdb.com/title/<?php echo $imdb->getID(); ?>/" target="_blank"><img id="imdblogo" alt="IMDB" src="<?php print IMAGES; ?>/imdb-logo.png" /></a>
		</div>	
	</div>

<div id="notification">
	<div id="default">
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>

	<div id="withIcon">
		<a class="ui-notify-close ui-notify-cross" href="#">x</a>
		<div style="float:left;margin:0 10px 0 0"><img src="<?php print IMAGES; ?>/alert.png" alt="warning" /></div>
		<h1>#{title}</h1>
		<p>#{text}</p>
	</div>
</div>

<script>
var imdbID = "<?php echo $imdb->getID(); ?>";
var tv_title = "<?php _e('TV show added', 'couchbeard'); ?>";
var movie_title = "<?php _e('Movie added', 'couchbeard'); ?>";
var tv_msg = "<?php printf(__('%s was added', 'couchbeard'), $imdb->Title); ?>";
var err_title = "<?php _e('Not implemented', 'couchbeard'); ?>";
var err_msg = "<?php printf(__('<strong>%s</strong> was not added', 'couchbeard'), $imdb->Title); ?>";
var movie_error = "<?php _e('There was an error adding the movie. The movie was not added.', 'couchbeard'); ?>";
</script>