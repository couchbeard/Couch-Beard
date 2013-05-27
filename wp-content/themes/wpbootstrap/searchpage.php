<?php
/*
Template Name: Search Page
*/
?>
<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
<?php get_header(); ?>
<?php
if (isset($_GET['id'])) {	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (!is_plugin_active('couch-beard-api/couch-beard-api.php')) {
		printf(__('Could not find %s plugin. You need to activate %s ', 'wpbootstrap'), 'couch-beard-api', 'couch-beard-api');
	} else {
		get_template_part( 'template_parts/view-movie-info' );
	}
}
get_footer(); ?>