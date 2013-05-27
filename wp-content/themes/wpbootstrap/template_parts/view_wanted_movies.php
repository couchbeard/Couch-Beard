<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
<div class="span12">	
	<?php
	$movies = cp_getMovies();
	if (empty($movies)) {
		_e('No movies wanted', 'wpbootstrap');
	} else {
		foreach ($movies as $val) { 
			$movie = $val->library->info;
	?>
			<div id="wantedSearchCover">
				<a class="nolink" data-toggle="modal" href="#myMovie" id="movieopen" data-id="<?php echo $movie->imdb; ?>">
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
<?php } ?>
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
			<br />
			<div class="row-fluid">
				<div class="span12">
					<p>Couchpotato info and options</p>
				</div>
			</div>
		</div>
		<div class="modal-footer">
		    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Close</a>
		</div>
	</div>
</div>

<script>
	$(document).on("click", "#movieopen", function () {
	     var imdb = $(this).data('id');
		jQuery.ajax({ 
            type: 'POST',
            cache: false,  
            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
            data: {  
                action: 'movieInfo',
                security: '<?php echo $ajax_nonce; ?>',
                imdb: imdb
            },
            dataType:'json',
            success: function(data, textStatus, XMLHttpRequest) {
            	$("#myMovie #title").text( data.Title );
            	$("#myMovie #rating").text( data.imdbRating );
            	$("#myMovie #votes").text( data.imdbVotes );
				$("#myMovie #genres").text( data.Genre );
            	$("#myMovie #year").text( data.Year );            	
				$("#myMovie #runtime").text( data.Runtime );
            	$("#myMovie #actors").text( data.Actors );            	
				$("#myMovie #writers").text( data.Writer );
            	$("#myMovie #plot").text( data.Plot );            	
            	$("#myMovie #poster").attr("src", data.Poster);
            },  
            error: function(MLHttpRequest, textStatus, errorThrown) {
               	$("#myMovie #title").text("<?php _e('Couldn\'t find the movie', 'wpbootstrap'); ?>");  
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