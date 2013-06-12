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

	$('.action').on('click', function () {
		$.post('<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
		{
			action: 'xbmcInputAction',
			security: '<?php echo $ajax_nonce; ?>',
			input: $(this).data('action')
		});
	});

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
	var running = false;
	$(function() {

		$(document).on('keydown', function(e) {
			if ($('#ctrl').prop('checked'))
			{
				var cmd;
				switch(e.keyCode)
				{
					case 37:
						cmd = 'left';
						break;
					case 38:
						cmd = 'up';
						break;
					case 39:
						cmd = 'right';
						break;
					case 40:
						cmd = 'down';
						break;
					case 13:
						cmd = 'select';
						break;
					case 8:
						cmd = 'back';
						break;
					default:
						return;
				}
				e.preventDefault();
				$.post('<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
				{
					action: 'xbmcInputAction',
					security: '<?php echo $ajax_nonce; ?>',
					input: cmd
				});
			}
		});

		$('#notificationbutton').click(function () {
			var e = jQuery.Event("keypress");
			e.which = 13;
			$('#notificationfield').trigger(e);
		});

		$('#notificationfield').on('keypress', function(e) {
	        if(e.which == 13 && $(this).val()) {
	            $.post('<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
				{
					action: 'xbmcSendNotification',
					security: '<?php echo $ajax_nonce; ?>',
					message: $('#notificationfield').val()
				})
				.done(function(data)
				{
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
				})
				.fail(function(data)
				{
					alert("<?php _e('There was an error adding the movie. The movie was not added.', 'wpbootstrap'); ?>");
				});
	        }         
	    });

		function currentPlayingTimer() {
			$.post('<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
			{
				action: 'xbmcPlayerProps',
				security: '<?php echo $ajax_nonce; ?>'
			}, null, 'json')
			.done(function(data)
			{
				var now = data.result.time.milliseconds + (data.result.time.seconds * 1000) + (data.result.time.minutes * 60 * 1000) + (data.result.time.hours * 60 * 60 * 1000);
            	var total = data.result.totaltime.milliseconds + (data.result.totaltime.seconds * 1000) + (data.result.totaltime.minutes * 60 * 1000) + (data.result.totaltime.hours * 60 * 60 * 1000);
            	var left = total - now;
            	$('#playingRuntime').text('-' + msToTime(left));
            	$('#playingProgress').css('width', Math.round(data.result.percentage) + '%');
			});
		}

		function msToTime(s) {
  			var ms = s % 1000;
			s = (s - ms) / 1000;
			var secs = s % 60;
			s = (s - secs) / 60;
			var mins = s % 60;
			var hrs = (s - mins) / 60;
			return hrs + ':' + (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + Math.round(secs);
		}
		
		function currentPlaying() {
			$.post('<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
			{
				action: 'currentPlaying',
				security: '<?php echo $ajax_nonce; ?>'
			}, null, 'json')
			.done(function(data)
			{
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
		            	if (!running && $('#xbmc_menu_box_mini').css('display') == 'none') {
							running = true;
		            		$("#playpause").html('<i class="icon-pause icon-white"></i>');
		            		$('#xbmc_menu_box_mini').slideDown(500);
		            	}
		            	currentPlayingTimer();	            		
	            	} else {
	            		if (running) {
		            		running = false;
		            		$('#playpause').html('<i class="icon-play icon-white"></i>');
		            		$('#xbmc_menu_box').slideUp(500);
		            		$('#xbmc_menu_box_mini').slideUp(500);
		            		$('#playingProgress').hide();
			            	clearInterval(timer);
            				timer = setInterval(currentPlaying, 5000);
			            }
			            if ($('#playingTitle').text() == "" || $('#playingTitle').text() == null) {
			            	$('#xbmc_menu_box').slideUp(500);
			            } 
	            	}
			})
			.fail(function(data)
			{
				if (running) {
	            		running = false;
	            		$("#playpause").html('<i class="icon-play icon-white"></i>');
	            		$('#xbmc_menu_box').slideUp(500);
	            		$('#xbmc_menu_box_mini').slideUp(500);
	            		$('#playingProgress').hide();
		            	clearInterval(timer);
            			timer = setInterval(currentPlaying, 5000);   
		            }
		            $('#xbmcConnection').fadeIn(500);
		            if ($('#playingTitle').text() == "" || $('#playingTitle').text() == null) {
		            	$('#xbmc_menu_box').slideUp(500);
		            }
			});
		}
		currentPlaying();
		var timer = setInterval(currentPlaying, 5000);

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


	$('#playpause').on("click", function () {
		$.post('<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
		{
			action: 'xbmcPlayPause',
			security: '<?php echo $ajax_nonce; ?>',
			player: 1
		}, null, 'json')
		.done(function(data)
		{
			if (data.result.speed)
            {
            	$("#playpause").html('<i class="icon-pause icon-white"></i>');
            }
            else
            {
            	$("#playpause").html('<i class="icon-play icon-white"></i>');
            }
		});
    });	
		
	$('#stop').on('click', function () {
		$.post('<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>',
		{
				action: 'xbmcInputAction',
				security: '<?php echo $ajax_nonce; ?>',
				input: 'stop'
		})
		.done(function()
		{
			$('#playpause').html('<i class="icon-play icon-white"></i>');
		});
	});




	</script>
<noscript>
	
		<?php 
			_e('You need javascript to access the rest of the homepage.', 'wpbootstrap');
		?>
		<meta http-equiv="refresh" content="3; URL=<?php echo home_url() . '/404' ?>">
</noscript>
<div class="container">