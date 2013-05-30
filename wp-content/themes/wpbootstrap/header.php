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
						  	<button class="btn" type="button"><i class="icon-comment"></i></button>
						</div>
						<div class="span2 pull-right">
							<span class="label label-success displaynone" ><i class="icon-ok icon-white"></i> Success</span>  
	                    	<span class="label label-important displaynone"><i class="icon-remove icon-white"></i> Failed</span>
				    	</div>
				    </div>
				</div>
				<div class="span1 pull-right">
					<p class="lead" id="playingRuntime">12:01</p>
				</div>
				<div class="span3 pull-right">
					<div class="progressbar playingProgress" id="playingProgress">
						<div class="bar" style="width: 50%;"></div>
					</div>
				</div>
				<div class="span3 pull-right" id="playButtons">
					<a class="btn btn-mini btn-inverse"><i class="icon-step-backward icon-white"></i></a>
					<a class="btn btn-mini btn-inverse"><i class="icon-fast-backward icon-white"></i></a>
					<a class="btn btn-mini btn-inverse"><i class="icon-stop icon-white"></i></a>
			      	<a class="btn btn-mini btn-inverse"><i class="icon-play icon-white"></i></a>
					<a class="btn btn-mini btn-inverse"><i class="icon-pause icon-white"></i></a>
					<a class="btn btn-mini btn-inverse"><i class="icon-fast-forward icon-white"></i></a>
					<a class="btn btn-mini btn-inverse"><i class="icon-step-forward icon-white"></i></a>
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
	var running;
	$(function() {

		$('#notificationfield').on('keypress', function(e) {
	        if(e.which == 13) {
	            jQuery.ajax({  
	                type: 'POST',
	                cache: false,  
	                url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
	                data: {  
	                    action: 'xbmcSendNotification',
	                    security: '<?php echo $ajax_nonce; ?>',
	                    message: $('#notificationfield').val()
	                },
	                success: function(data, textStatus, XMLHttpRequest) {
	                    if (data == 1) {
	                        $('.label-success').show();
	                        setTimeout(function() {
	                            $("#message").collapse('hide');
	                            $('#notificationfield').val('');
	                            $('.label-success').fadeOut(500);
	                        }, 2000);

	                        
	                    } else {
	                        $('.label-important').show();
	                        setTimeout(function() {
	                            $('.label-important').fadeOut(500);
	                        }, 2000);
	                        
	                    }
	                },  
	                error: function(MLHttpRequest, textStatus, errorThrown) {
	                    alert("<?php _e('There was an error adding the movie. The movie was not added.', 'wpbootstrap'); ?>");  
	                }  
	            });
	        }         
	    });
		
		function currentPlaying() {
			jQuery.ajax({  
	            type: 'POST',
	            cache: false,  
	            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",
	            dataType:'json',  
	            data: {  
	                action: 'currentPlaying',
	                security: '<?php echo $ajax_nonce; ?>'
	            },
	            success: function(data, textStatus, XMLHttpRequest) {
	            	if (data != "" && data != null) {
	            		// Not needed to be updated
	            		var title = data.label;
	            		if (data.type == 'episode')
	            		{
	            			title = data.showtitle + ' [' + data.season + 'x' + data.episode + '] - ' + data.title;
	            		}
	            		if ($('#playingTitle').text() != title) {
		            		$('#playingTitle').text(title);
		            		$('#playingCover').attr('src', function() {
		            			return decodeURIComponent(data.thumbnail.replace('image://', '').replace('.jpg/', '.jpg'));
		            		});
		            		clearInterval(timer);
		            		timer = setInterval(currentPlaying, 1000);
		            	}
		            	if (!running && $('#xbmc_menu_box_mini').css('display') == 'none')
		            		$('#xbmc_menu_box_mini').slideDown(500);
	            		running = true;
	            	}
	            },  
	            error: function(MLHttpRequest, textStatus, errorThrown) {
	            	if (running) {
	            		$('#xbmc_menu_box').slideUp(500);
	            		$('#xbmc_menu_box_mini').slideUp(500);
	            		$('#playingProgress').hide();
		            	running = false;
		            	clearInterval(timer);
            			timer = setInterval(currentPlaying, 5000);   
		            }
		            if ($('#playingTitle').text() == "" || $('#playingTitle').text() == null) {
		            	$('#xbmc_menu_box').slideUp(500);
		            } 
	            }  
	        }); 
		}
		currentPlaying();
		var timer = setInterval(currentPlaying, 1000);

		$("img.lazy").lazyload({
		});
	});
	
	$('.icon-play').click(function() {
		$(this).toggleClass('icon-play icon-white');
	});

		$('#xbmc_menu_button').click(function() {
			if (running == true) {
				$('#xbmc_menu_box').animate({
				    bottom: '+=10',
				    height: 'toggle'
				  }, 500, function() {

				  });
			} else {
				$('#xbmc_menu_box_mini').animate({
				    bottom: '+=10',
				    height: 'toggle'
				  }, 500, function() {

				  });
			}
		});

	$(document).keydown(function(e) {
	    if(e.which == 83 && e.altKey) {
	    	e.preventDefault();
	        $('#movieName').focus();
	    }
	});

	function writeCookie(name,value,days) {
	    var date, expires;
	    if (days) {
	        date = new Date();
	        date.setTime(date.getTime()+(days*24*60*60*1000));
	        expires = "; expires=" + date.toGMTString();
	            }else{
	        expires = "";
	    }
	    document.cookie = name + "=" + value + expires + "; path=/";
	}

	function readCookie(name) {
	    var i, c, ca, nameEQ = name + "=";
	    ca = document.cookie.split(';');
	    for(i=0;i < ca.length;i++) {
	        c = ca[i];
	        while (c.charAt(0)==' ') {
	            c = c.substring(1,c.length);
	        }
	        if (c.indexOf(nameEQ) == 0) {
	            return c.substring(nameEQ.length,c.length);
	        }
	    }
	    return '';
	}
	</script>
<noscript>
	
		<?php 
			_e('You need javascript to access the rest of the homepage.', 'wpbootstrap');
		?>
		<meta http-equiv="refresh" content="3; URL=<?php echo home_url() . '/404' ?>">
</noscript>
<div class="container">