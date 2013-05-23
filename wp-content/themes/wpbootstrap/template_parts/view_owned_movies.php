	<?php
	$page = isset($_GET['page']) ? absint($_GET['page']) : 1;
	$limit = 14;
	$offset = ($page - 1) * $limit;
	$movies = xbmc_getMovies($offset, $limit);
	if (empty($movies)) {
		_e('No movies owned', 'wpbootstrap');
	} else {
	?>
	<div class="row">
		<div class="span12">
	<?php
	foreach ($movies as $movie) { ?>
			<div id="wantedSearchCover">
				<a class="nolink" href="<?php echo get_permalink(getSearchpageID()) . '&id=' . $movie->imdbnumber; ?>">
					<div id="wantedCoverOverlay">
						<p class="nolink"><?php echo $movie->label . ' (' . $movie->year . ')'; ?></p>
					</div>
					<img class="lazy" id="wantedSearchpageCover" src="<?php print IMAGES; ?>/no_cover.png" data-original="<?php echo urldecode(substr($movie->thumbnail, 8, -1)); ?>"/>
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
<div class="row">
	<div class="span12">
		<ul class="pager">
			<li class="previous <?php echo ($page > 1) ? '' : 'disabled'; ?>">
		    	<a href="<?php echo get_permalink() . '&page=' . (($page > 1) ? $page - 1 : 1); ?>">Previous</a>
	  		</li>
		  	<li class="next">
		    	<a href="<?php echo get_permalink() . '&page=' . ($page + 1); ?>">Next</a>
		  	</li>
		</ul>
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