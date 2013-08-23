<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title( '|',1,'right' ); bloginfo( 'name' ); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo( 'description' ); ?>">
    <meta name="author" content="">
	<meta name='robots' content='noindex,nofollow' />

    <!-- Le styles -->
    <link href="<?php bloginfo( 'stylesheet_url' ); ?>" rel="stylesheet" />

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
	<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
    <script>
		var no_movie_found = "<?php _e('Couldn\'t find the movie', 'wpbootstrap'); ?>";
		var ajax_url = "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>";
		var ajax_nonce = '<?php echo $ajax_nonce; ?>';
		var notification_error = "<?php _e('There was an error adding the movie. The movie was not added.', 'wpbootstrap'); ?>";
		var downloads_error = "<?php _e('Couldn\'t find any downloads', 'wpbootstrap'); ?>";
		var images_src = '<?php print IMAGES; ?>';
	</script>

	<?php wp_enqueue_script("jquery"); ?>
	<?php wp_head(); ?>
  </head>

  <body>
  	<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" alt="<?php bloginfo( 'description' ); ?>" href="<?php echo site_url(); ?>"><?php bloginfo( 'name' ); ?></a>
          <div class="nav-collapse collapse">
            <ul class="nav" style="z-index: 10;">
				<?php
				$menu = 'guest';
				if (is_user_logged_in()) {
					$menu = 'user';					
				}
				$args = array(
					'theme_location' => 'top-bar',
					'depth'		 => 0,
					'container'	 => false,
					'menu_class'	 => 'nav',
					'theme_location' => $menu,
					'walker'	 => new BootstrapNavMenuWalker()
				);
				wp_nav_menu($args);				
				?>
			</ul>
			<ul class="nav pull-right">
				<?php if (is_user_logged_in()) { ?>
				<div class="navbar-search span2" id="searchform">
					<input type="text" id="movieName" name="query" class="input-large search-query" placeholder="<?php _e('Search', 'wpbootstrap'); ?>	(Alt + S)" />
				</div>
				<div class="span1 offset1">
					<div class="btn-group">
					  <a class="btn" href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><i class="icon-user"></i><?php echo $current_user->user_login; ?></a>
					  <button class="btn dropdown-toggle" data-toggle="dropdown">
					  	<i class="icon-chevron-down"></i>
					  </button>
					  <ul class="dropdown-menu pull-right">
					  		<li><a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><i class="icon-user"></i><?php echo $current_user->user_login; ?></a></li>
					  		<li class="divider"></li>
					  		<li><a href="<?php echo wp_logout_url(get_option('siteurl')); ?>"><?php _e('Log out','wpbootstrap'); ?></a></li>
					  </ul>
					</div>
				</div>
				<?php } ?>
			</ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
      <div class="currentPlayingBox">
      	<div id="xbmc_menu_box_mini">
			<div class="row-fluid">
				<div class="span5 pull-left">
					<p class="lead" id="playingTitle"></p>
					<div class="span8">
						<div class="input-append">
							<input id="notificationfield" type="text" placeholder="<?php _e('Send notification', 'wpbootstrap'); ?>">
						  	<button class="btn" id="notificationbutton" type="button"><i class="icon-comment"></i></button>
						</div>
						<div class="span2 pull-right">
							<span class="label label-success displaynone" ><i class="icon-ok icon-white"></i> Success</span>  
	                    	<span class="label label-important displaynone"><i class="icon-remove icon-white"></i> Failed</span>
				    	</div>
				    </div>
				</div>
				<div class="span1 pull-right">
					<p class="lead" id="playingRuntime"></p>
				</div>
				<div class="span3 pull-right">
					<div class="progress">
						<div class="bar" id="playingProgress" style="width: 0%;"></div>
					</div>
				</div>
				<div class="span3 pull-right" id="playButtons">
					<a class="btn btn-mini btn-inverse action" data-action="bigstepback"><i class="icon-step-backward icon-white"></i></a>
					<a class="btn btn-mini btn-inverse action" data-action="stepback"><i class="icon-fast-backward icon-white"></i></a>
					<a class="btn btn-mini btn-inverse" id="playpause"><i class="icon-<?php echo (xbmc_getCurrentPlaying() ? "pause" : "play"); ?> icon-white"></i></a>
					<a class="btn btn-mini btn-inverse" id="stop"><i class="icon-stop icon-white"></i></a>
					<a class="btn btn-mini btn-inverse action" data-action="stepforward"><i class="icon-fast-forward icon-white"></i></a>
					<a class="btn btn-mini btn-inverse action" data-action="bigstepforward"><i class="icon-step-forward icon-white"></i></a>
					<a class="btn btn-mini btn-inverse" id="eject"><i class="icon-eject icon-white"></i></a>
				</div>
				<div class="span3 pull-right">
					<label class="checkbox">
     					<input type="checkbox" id="ctrl"> <abbr title="<?php _e('Use arrow keys, backspace and enter buttons to control XBMC', 'wpbootstrap'); ?>"><?php _e('Control XBMC?' , 'wpbootstrap'); ?></abbr>
    				</label>
				</div>
			</div>
		</div>
        <div id="xbmc_menu_box">
			<legend></legend>
			<img id="playingCover" src="<?php print IMAGES; ?>/no_cover.png"/>
		</div>
		<div id="xbmc_menu_button" class="muted"><?php _e('Current playing', 'wpbootstrap'); ?></div>
	</div>
    </div>

	<script>
// 	jQuery(document).ready(function ($) {
//     // use this hash to cache search results
//   window.query_cache = {};
//   $('#movieName').typeahead({
//       source:function(query,process) {
//           // if in cache use cached value, if don't wanto use cache remove this if statement
//           if(query_cache[query]){
//               process(query_cache[query]);
//               return;
//           }
//           if( typeof searching != "undefined") {
//               clearTimeout(searching);
//               process([]);
//           }
//           searching = setTimeout(function() {
//               return $.ajax({
//                 type: 'POST',
//                 url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",
//                 data: {  
//                     action: 'getMovies',
//                     //security: '<?php echo $ajax_nonce; ?>',
//                     q: query
//                 },
//                 success: function(data) {
//                 	console.log(data);
//                 // save result to cache, remove next line if you don't want to use cache
//                   query_cache[query] = data;
//                   // only search if stop typing for 300ms aka fast typers
//                   return process(data);
//                 }
//                 });
//           }, 300); // 300 ms
//       }
//   });
// });
	</script>
	<?php wp_enqueue_script('header'); ?>
<noscript>
	
		<?php 
			_e('You need javascript to access the rest of the homepage.', 'wpbootstrap');
		?>
		<meta http-equiv="refresh" content="3; URL=<?php echo home_url() . '/404' ?>">
</noscript>
<div class="container">