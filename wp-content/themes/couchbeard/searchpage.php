<?php
/*
Template Name: Search Page
*/
?>
<?php get_header(); ?>
<?php
if (isset($_GET['id'])) {	
		get_template_part( 'template_parts/view-movie-info' );
}
get_footer(); ?>

<?php
	wp_enqueue_script('jrating');
	wp_enqueue_script('searchpage'); 
?>
