<?php get_header(); ?>
    <?php $ajax_nonce = wp_create_nonce("keyy"); ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
	<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>
	<div class="row">
        <div class="span4">
          	<?php get_sidebar( 'front-footer-1' ); ?>
        </div>
        <div class="span4">
          	<?php get_sidebar( 'front-footer-2' ); ?>
       	</div>
        <div class="span4">
          	<?php get_sidebar( 'front-footer-3' ); ?>
        </div>
        
        <button class="btn" id="sendnoti" data-toggle="collapse" data-target="#message">XBMC</button>
        <div id="message" class="collapse out" style="margin: 10px;">
                    <input id='notificationfield' type='text' placeholder='Notification' />
                    <span class="label label-success" style="display: none;"><i class="icon-ok icon-white"></i> Success</span>  
                    <span class="label label-important" style="display: none;"><i class="icon-remove icon-white"></i> Failed</span>
        </div>

    </div>
<?php get_footer(); ?>

<script>
$(function() {
    $('#sendnoti').on('click', function() {
        $("#notificationfield").focus();
    });

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
});
</script>