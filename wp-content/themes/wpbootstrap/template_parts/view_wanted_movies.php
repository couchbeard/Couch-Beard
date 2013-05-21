<?php
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (!is_plugin_active('couch-beard-api/couch-beard-api.php')) {
		printf(__('Could not find %s plugin. You need to activate %s ', 'wpbootstrap'), 'couch-beard-api', 'couch-beard-api');
		exit();
	}
	?>
	<div class="span12">	
	<?php foreach (cp_getMovies() as $val) { 
		$movie = $val->library->info;
	?>
			<div id="wantedSearchCover">
				<a class="nolink" href="<?php echo get_permalink(getSearchpageID()) . '&id=' . $movie->imdb; ?>">
					<div id="wantedCoverOverlay">
						<p class="nolink"><?php echo $movie->original_title; ?></p>
					</div>
					<img id="wantedSearchpageCover" src="<?php echo $movie->images->poster[0]; ?>" class="img-rounded"/>
				</a>
			</div>		
		<!--<div class="row">
			<div class="span3">
				<img src="<?php echo $movie->images->poster_original[0]; ?>" />
			</div>
			<div id="movieInfo" class="span8">
				<div class="row">
					<div class="span7">
						<p class="lead"><?php echo $movie->original_title; ?></p>
					</div>
					<div class="span1 pull-right">
						<p class="lead"><?php echo $movie->year; ?></p>
					</div>
				</div>

			</div>
		</div>
		<br /> -->
		<?php } ?>
	</div>