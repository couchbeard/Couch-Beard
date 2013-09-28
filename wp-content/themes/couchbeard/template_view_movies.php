<?php
/*
Template Name: Wanted Movies
 */
?>

<?php
	$cache = new Cache();
    $cache->start();
    mt_srand(time());
    $cache->cacheTime = 3600 * 24; // 1 day
    $cache->end();
?>

<?php get_header(); ?>
<?php
	try {
		$xbmc = new xbmc();
		$genres = $xbmc->getGenres('movie');
	} catch (Exception $e) {
		
	}
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
		<button class="btn btn-link nolink" id="search" style="cursor: pointer;"><strong><?php _e('Search', 'couchbeard'); ?></strong></button>
	</div>
<div class="row">
		<div class="span12" id="searchBar">
			<div class="pull-right" id="exitSearch">
				<button type="button" class="close" id="close">&times;</button>
			</div>
			<form class="form-search">
				<div class="row">
					<center><input type="text" id="searchInput" class="span5" placeholder="<?php _e('Search', 'couchbeard'); ?>" /></center>
				</div>
				<div class="row">
					<div class="center" data-toggle="buttons-checkbox">
						<?php
						if (!empty($genres)) 
							foreach ($genres as $g) { ?>
					  			<button type="button" class="btn btn-inverse span2" id="genreButtons" value="<?php echo $g->genreid; ?>"><?php echo $g->label; ?></button>
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
		printf(__('Could not find %s plugin. You need to activate %s ', 'couchbeard'), 'couch-beard-api', 'couch-beard-api');
		exit();
	}
	?>
	<div class="row">
   	<?php if ($_GET['movies'] == 'wanted') {
		get_template_part( 'template_parts/view_wanted_movies' );
	} else {
		get_template_part( 'template_parts/view_owned_movies' );
	}
   	?>
   </div>
<?php get_footer(); ?>

<?php wp_enqueue_script('jrating'); ?>