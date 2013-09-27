<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
<div class="span12">
	<?php
	$page = isset($_GET['page']) ? absint($_GET['page']) : 1;
	$limit = 21;
	$pages = ceil(sizeof(xbmc_getMovies())/$limit);
	$offset = ($page - 1) * $limit;
	$movies = xbmc_getMovies($offset, $limit);
	if (empty($movies)) {
		_e('No movies owned', 'wpbootstrap');
	} else {
		foreach ($movies as $movie) { ?>
			<div id="wantedSearchCover">
				<a class="nolink" data-toggle="modal" href="#myMovie" id="movieopen_owned" data-id="<?php echo $movie->movieid; ?>">
					<div id="wantedCoverOverlay">
						<p class="nolink"><?php echo $movie->label . ' (' . $movie->year . ')'; ?></p>
					</div>
					<img class="lazysporty" id="wantedSearchpageCover" src="<?php print IMAGES; ?>/no_cover.png" data-original="<?php echo urldecode(substr($movie->thumbnail, 8, -1)); ?>"/>
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
	<div class="row">
		<div class="span12">
			<ul class="pager">
				<?php if ($page > 1) { ?>
				<li class="previous">
			    	<a onClick="<?php echo ($page > 1) ? '' : 'return false'; ?>" href="<?php echo get_permalink() . '&page=' . ($page - 1); ?>">Previous</a>
		  		</li>
		  		<?php
		  		}
		  		if ($page < $pages) { ?>
			  	<li class="next">
			    	<a href="<?php echo get_permalink() . '&page=' . ($page + 1); ?>">Next</a>
			  	</li>
			  	<?php } ?>
			</ul>
		</div>
	</div>
<?php } ?>
</div>

<?php get_template_part( 'template_parts/movie_info_modal' ); ?>