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
				<a class="nolink" data-toggle="modal" href="#myMovie" id="movieopen" data-id="<?php echo $movie->movieid; ?>">
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

<div id="myMovie" class="modal hide fade inverseback" tabindex="-1" role="dialog" aria-labelledby="myMovieLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="title"></h3>
		</div>
		<div class="modal-body">
			<div class="row-fluid">
				<div class="span3">
					<div id="searchCover">
						<div id="coverOverlay">
							<center>
								<div class="opensans">
									<div id="rating"></div>
								</div>
								<br /><br /><br /><br />
								<div class="josefinslab">
									<div id="votes"></div>
								</div>
							</center>
						</div>
						<img id="poster" src="<?php echo IMAGES . '/no_cover.png'; ?>" class="img-rounded"/>
					</div>
				</div>
				<div class="span9">
					<div class="row-fluid">
						<div class="span8 pull-left">
							<p class="lead pline" id="genres"></p>
						</div>
						<div class="span1">
							<p class="lead" id="year"></p>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span8 pull-left">
							<p class="lead pline"><?php //$data->country; ?></p>
						</div>
						<div class="span3">
							<p class="lead" id="runtime"></p>
						</div>				
					</div>
					<div class="row-fluid">
						<div class="span8 pull-left">
							<p class="lead pline" id="actors"></p>
						</div>
					</div>
					<br />
					<div class="row-fluid">
						<div class="span8 pull-left">
							<p class="lead pline" id="writers"></p>
						</div>
					</div>
					<br />			
					<div class="row-fluid">
						<div class="span9 pull-left">
							<p id="plot"></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" id="play" data-loading-text="Playing" data-id=""><?php _e('Play', 'wpbootstrap'); ?></button>
		    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true"><?php _e('Close', 'wpbootstrap'); ?></a>
		</div>
	</div>
</div>

<script>
	$(document).on("click", "#movieopen", function () {
	    var id = $(this).data('id');
				$("#myMovie #title").text( '' );
            	$("#myMovie #rating").text( '' );
            	$("#myMovie #votes").text( '' );
				$("#myMovie #genres").text( '' );
            	$("#myMovie #year").text( '' );            	
				$("#myMovie #runtime").text( '' );
            	$("#myMovie #actors").text( '' );            	
				$("#myMovie #writers").text( '' );
            	$("#myMovie #plot").text( '' );            	
            	$("#myMovie #poster").attr("src", '');	
            	$("#myMovie #play").data("id", '');    
            	$("#play").button('reset');
            	$("#play").removeAttr("disabled");
		jQuery.ajax({ 
            type: 'POST',
            cache: false,  
            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
            data: {  
                action: 'movieXbmcInfo',
                security: '<?php echo $ajax_nonce; ?>',
                movieid: id
            },
            dataType:'json',
            success: function(data, textStatus, XMLHttpRequest) {
            	$("#myMovie #title").text( data.label );
            	$("#myMovie #rating").text( data.rating.toFixed(1) );
				$("#myMovie #genres").text( data.genre );
            	$("#myMovie #year").text( data.year );            	
				$("#myMovie #runtime").text( formatSeconds(data.runtime) );
            	$("#myMovie #plot").text( data.plot );            	
            	$("#myMovie #poster").attr("src", decodeURIComponent(data.thumbnail.replace('image://', '').replace('.jpg/', '.jpg')));
            	$("#myMovie #play").data("id", data.movieid);
            },  
            error: function(MLHttpRequest, textStatus, errorThrown) {
               	$("#myMovie #title").text("<?php _e('Couldn\'t find the movie', 'wpbootstrap'); ?>");  
            }  
        });
	});

	function formatSeconds(sec) {
	    var hour = Math.floor(sec / 3600);
	    sec -= hour * 3600;
	    var min = Math.floor(sec / 60);
	    sec -= min * 60;
	    return hour + ":" + (min < 10 ? '0' + min : min) + ":" + (sec < 10 ? '0' + sec : sec);
	}

		$(document).on("click", "#play", function () {
	    var id = $(this).data('id');  
		jQuery.ajax({ 
            type: 'POST',
            cache: false,  
            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
            data: {  
                action: 'xbmcPlayMovie',
                security: '<?php echo $ajax_nonce; ?>',
                movieid: id
            },
            dataType:'json',
            success: function(data, textStatus, XMLHttpRequest) {
            	if (data.result == 'OK')
            	{
	            	$("#play").button('loading');
	            	$("#play").attr("disabled", "disabled");
            	}
            }
        });
	});

	$(function() {
		$("img.lazy").lazyload({
			event : "sporty"
		});
	});
	$(window).bind("load", function() { 
	    var timeout = setTimeout(function() {$("img.lazy").trigger("sporty")}, 2000);
	}); 
</script>