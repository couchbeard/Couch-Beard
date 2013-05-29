<div class="span12">
	<?php
	$page = isset($_GET['page']) ? absint($_GET['page']) : 1;
	$limit = 14;
	$offset = ($page - 1) * $limit;
	$shows = xbmc_getShows($offset, $limit);
	if (empty($shows)) {
		_e('No TV shows owned', 'wpbootstrap');
	} else {
	foreach ($shows as $show) { ?>
			<div id="wantedSearchCover">
				<a class="nolink" href="<?php echo get_permalink(getSearchpageID()) . '&id=' . $show->label; ?>">
					<div id="wantedCoverOverlay">
						<p class="nolink"><?php echo $show->label . ' (' . $show->year . ')'; ?></p>
					</div>
					<img class="lazy" id="wantedSearchpageCover" src="<?php print IMAGES; ?>/no_cover.png" data-original="<?php echo urldecode(substr($show->thumbnail, 8, -1)); ?>"/>
				</a>
			</div>	
		<?php } ?>

		</div>
	</div>
<div class="row">
	<div class="span12">
		<ul class="pager">
			<li class="previous <?php echo ($page > 1) ? '' : 'disabled'; ?>">
		    	<a onClick="<?php echo ($page > 1) ? '' : 'return false'; ?>" href="<?php echo get_permalink() . '&page=' . (($page > 1) ? $page - 1 : 1); ?>">Previous</a>
	  		</li>
		  	<li class="next">
		    	<a href="<?php echo get_permalink() . '&page=' . ($page + 1); ?>">Next</a>
		  	</li>
		</ul>
<?php } ?>
</div>
<script>
	$(function() {
		$("img.lazy").lazyload({
			event : "sporty"
		});
	});
	$(window).bind("load", function() { 
	    var timeout = setTimeout(function() {$("img.lazy").trigger("sporty")}, 500);
	}); 
</script>