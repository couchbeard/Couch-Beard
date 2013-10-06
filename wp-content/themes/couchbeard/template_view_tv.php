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

<?php wp_enqueue_script('jrating'); ?>