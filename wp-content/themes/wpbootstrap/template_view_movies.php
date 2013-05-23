<?php
/*
Template Name: Wanted Movies
 */
?>

<?php get_header(); ?>
<?php
	$genres = array(
	"Action",
	"Adventure",
	"Animation",
	"Biography",
	"Comedy",
	"Crime",
	"Documentary",
	"Drama",
	"Family",
	"Fantasy",
	"Film-Noir",
	"Game-Show",
	"History",
	"Horror",
	"Music",
	"Musical",
	"Mystery",
	"News",
	"Reality-TV",
	"Romance",
	"Sci-Fi",
	"Sport",
	"Talk-Show",
	"Thriller",
	"War Western" 
	);
?>
<legend>
	<?php the_title(); ?>
	<div class="pull-right">
		<div class="btn-group">
			<a class="btn btn-inverse" href="<?php echo get_permalink(); ?>&movies=wanted">Wanted</a>
			<a class="btn btn-inverse" href="<?php echo get_permalink(); ?>">Owned</a>
		</div>
	</div>
	<div class="span5 pull-right">
		<button class="btn btn-link nolink" id="search" style="cursor: pointer;"><strong><?php _e('Search', 'wpbootstrap'); ?></strong></button>
	</div>
<div class="row">
		<div class="span12" id="searchBar">
			<form class="form-search">
				<div class="row">
					<center><input type="text" id="searchInput" class="span5" placeholder="<?php _e('Search', 'wpbootstrap'); ?>" /></center>
				</div>
				<div class="row">
					<div class="center" data-toggle="buttons-checkbox">
						<?php foreach ($genres as $g) { ?>
					  		<button type="button" class="btn btn-inverse span2" id="genreButtons" value="<?php echo $g; ?>"><?php echo $g; ?></button>
					  	<?php } ?>
					</div>
				</div>
			</form>
		</div>
	</div>	
</legend>
   	<?php
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (!is_plugin_active('couch-beard-api/couch-beard-api.php')) {
		printf(__('Could not find %s plugin. You need to activate %s ', 'wpbootstrap'), 'couch-beard-api', 'couch-beard-api');
		exit();
	}
	?>
   	<?php if ($_GET['movies'] == 'wanted') {
		get_template_part( 'template_parts/view_wanted_movies' );
	} else {
		get_template_part( 'template_parts/view_owned_movies' );
	}
   	?>
<?php get_footer(); ?>

<script>
	$('#search').click(function() {
		if ($('#searchBar').css('display') == 'none') {
			$('#searchBar').slideDown('slow', function() {
			    // Animation complete.
			});
		} else {
			$('#searchBar').slideUp('slow', function() {
			    // Animation complete.
		    });
		}
	})
</script>