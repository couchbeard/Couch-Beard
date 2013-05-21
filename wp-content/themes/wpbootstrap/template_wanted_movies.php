<?php
/*
Template Name: Wanted Movies
 */
?>

<?php get_header(); ?>
<?php
	$title = (get_the_title($post->post_parent) == get_the_title()) ? get_the_title() : get_the_title($post->post_parent) . ' > ' . get_the_title();
?>
<legend><a class="nolink" href="<?php echo get_permalink( $post->post_parent ); ?>"><?php echo $title; ?></a></legend>
<div class="row">
   <?php
   		if (isset($_GET['id'])) {
   			// Load specific movie
   		} else {
   			get_template_part( 'template_parts/view_wanted_movies' );
   		}
   ?>
</div>  	
<?php get_footer(); ?>