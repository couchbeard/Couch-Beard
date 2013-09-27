<?php
$data = getMovieData($_GET['id']);
	if (!isset($data->Error)) {
?>
<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
	<legend><?php echo $data->Title; ?></legend>
	<div class="row">
		<div class="span3">
			<img id="wantedOverlay" src="<?php print IMAGES; ?>/download_logo_square.png" />
			<img id="checkOverlay" src="<?php print IMAGES; ?>/check.png" />
			<div id="searchCover">
				<div id="coverOverlay">
					<center>
						<div class="opensans">
							<?php echo $data->imdbRating; ?>
						</div>
						<br /><br /><br /><br />
						<div class="josefinslab">
							<?php echo $data->imdbVotes; ?>
						</div>
					</center>
				</div>
				<img id="searchpageCover" src="<?php echo ($data->Poster == 'N/A') ? IMAGES . '/no_cover.png' : $data->Poster; ?>" class="img-rounded"/>
			</div>
			<div class="rating" data-average="<?php echo floatval($data->imdbRating); ?>" data-id="1" data-toggle="tooltip" data-placement="bottom" title="<?php echo $data->imdbRating . ' / ' . $data->imdbVotes; ?>"></div>
			<div class="ratingtext"><center><p class="lead"><?php echo $data->imdbRating; ?></p></center></div>	
		</div>
		<div class="span9">
			<div class="row">
				<div class="span8 pull-left">
					<p class="lead pline"><?php echo $data->Genre; ?></p>
				</div>
				<div class="span1">
					<p class="lead"><?php echo $data->Year; ?></p>
				</div>
			</div>
			<div class="row">
				<div class="span8 pull-left">
					<p class="lead pline"><?php //$data->country; ?></p>
				</div>
				<div class="span1">
					<?php
						$time = explode('h', $data->Runtime);
						if (strpos($data->Runtime, 'h')) {
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
					<p class="lead pline"><?php echo $data->Actors; ?></p>
				</div>
			</div>
			<br />
			<div class="row">
				<div class="span8 pull-left">
					<p class="lead pline"><?php echo $data->Writer; ?></p>
				</div>
			</div>
			<br />			
			<div class="row">
				<div class="span9 pull-left">
					<p><?php echo $data->Plot; ?></p>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span2 pull-right">
			<?php
			
			if($data->Type == 'movie') {
					if (xbmc_movieOwned($data->imdbID))
					{ ?>
						<button class="btn btn-inverse pull-right disabled" disabled="disabled"><i><?php _e('Movie owned', 'couchbeard'); ?></i></button>
						<script>
							$('#checkOverlay').css("visibility", "visible");
						</script>
					<?php }
					else if (cp_movieWanted($data->imdbID))
					{ ?>
						<button class="btn btn-inverse pull-right disabled" disabled="disabled"><i><?php _e('Movie added', 'couchbeard'); ?></i></button>
						<script>
							$('#wantedOverlay').css("visibility", "visible");
						</script>					
					<?php }
					else
					{
					?>
						<button class="btn btn-inverse pull-right" id="addMovie"><?php _e('Add movie', 'couchbeard'); ?></button>
			<?php
					}
			} else if ($data->Type == 'series') {
				$show = sb_showAdded($data->imdbID);
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
				<?php if (xbmc_showOwned($data->imdbID)) { ?>
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
			<a href="http://www.imdb.com/title/<?php echo $data->imdbID; ?>" target="_blank"><img id="imdblogo" alt="IMDB" src="<?php print IMAGES; ?>/imdb-logo.png" /></a>
		</div>	
	</div>
<?php
	} else {
		printf(__('No movie found with ID: <strong>%s</strong>', 'couchbeard'), $_GET['id']);
	}
?>

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
var imdbID = "<?php echo $data->imdbID; ?>";
console.log(imdbID);
var tv_title = "<?php _e('TV show added', 'couchbeard'); ?>";
var movie_title = "<?php _e('Movie added', 'couchbeard'); ?>";
var tv_msg = "<?php printf(__('%s was added', 'couchbeard'), $data->Title); ?>";
var err_title = "<?php _e('Not implemented', 'couchbeard'); ?>";
var err_msg = "<?php printf(__('<strong>%s</strong> was not added', 'couchbeard'), $data->Title); ?>";
var movie_error = "<?php _e('There was an error adding the movie. The movie was not added.', 'couchbeard'); ?>";
</script>