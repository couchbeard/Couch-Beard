	<?php
	$movies = cp_getMovies();
	if (empty($movies)) {
		_e('No movies wanted', 'wpbootstrap');
	} else {
?>
	<div class="row">
		<div class="span12">
<?php
	foreach ($movies as $val) { 
		$movie = $val->library->info;
	?>
			<div id="wantedSearchCover">
				<a class="nolink" href="<?php echo get_permalink(getSearchpageID()) . '&id=' . $movie->imdb; ?>">
					<div id="wantedCoverOverlay">
						<p class="nolink"><?php echo $movie->original_title . ' (' . $movie->year . ')'; ?></p>
					</div>
					<img id="wantedSearchpageCover" src="<?php print IMAGES; ?>/no_cover.png" data-original="<?php echo $movie->images->poster[0]; ?>" class="lazy"/>
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
</div>
<?php } ?>
<script>
	$(function() {
		$("img.lazy").lazyload({
			event : "sporty"
		});
	});
	$(window).bind("load", function() { 
	    var timeout = setTimeout(function() {$("img.lazy").trigger("sporty")}, 2000);
	}); 
</script>