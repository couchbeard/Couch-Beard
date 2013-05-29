<?php
/*
Template Name: Wanted TV shows
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

<legend>
	<?php the_title(); ?>
	<div class="pull-right">
		<div class="btn-group">
			<a class="btn btn-inverse" href="<?php echo get_permalink(); ?>&shows=wanted">Wanted</a>
			<a class="btn btn-inverse" href="<?php echo get_permalink(); ?>">Owned</a>
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
	<div class="row">
   <?php
   		if ($_GET['shows'] == 'wanted') {
   			get_template_part( 'template_parts/view_wanted_tv' );
   		} else {
   			get_template_part( 'template_parts/view_owned_tv' );
   		}
   ?> 	
	</div>
<?php get_footer(); ?>