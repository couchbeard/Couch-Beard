		<hr>    
		<footer>
			<div class="row">
				<div class="span2">
		   			 <p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?></p>
		   			 <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed"><img alt="Creative Commons licens" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png" /></a>
		    	</div>
		    	<div class="span1 pull-right">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" class="pull-right">
					<input type="hidden" name="cmd" value="_donations">
					<input type="hidden" name="business" value="madssx@hotmail.com">
					<input type="hidden" name="lc" value="US">
					<input type="hidden" name="item_name" value="Couch beard">
					<input type="hidden" name="no_note" value="0">
					<input type="hidden" name="currency_code" value="EUR">
					<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/da_DK/i/scr/pixel.gif" width="1" height="1">
					</form>
				</div>
			</div>
		</footer>
		<div id="statusConnection" data-toggle="tooltip">
			<span class="label label-warning">
				<p class="pull-right">Connection status</p>
				<p class="pull-left"><i class="icon-minus-sign icon-white"></i></p>
			</span>
		</div>
		</div> <!-- /container -->
		<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
		<script>
		$(function() {
			$('#statusConnection').on('click', function() {
				$(this).fadeOut(500);
				clearInterval(timer);
			});

			function getConnections() {
				jQuery.ajax({  
		            type: 'POST',
		            cache: false,  
		            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",
		            dataType:'json',  
		            data: {  
		                action: 'connectionStatus',
		                security: '<?php echo $ajax_nonce; ?>'
		            },
		            success: function(data, textStatus, XMLHttpRequest) {
		            	if (data.length > 0)
		            	{
		            		if ($('#statusConnection').css('display') == 'none') {
		            			$('#statusConnection').fadeIn(500, function() {
		            				$('#statusConnection').show();
		            			});
		            		}

							$('#statusConnection').attr('title', 'Connection failed to ' + data);
							$('#statusConnection').tooltip({
								placement: 'left'
							});

							$('#statusConnection').tooltip('hide').tooltip('fixTitle');
		            	} 
		            	else 
		            	{	
		            		if ($('#statusConnection').css('display') != 'none') {
		            			$('#statusConnection').fadeOut(500, function() {
		            				$('#statusConnection').hide();
		            			});
		            		}

		            	}
		            },
		            error: function(MLHttpRequest, textStatus, errorThrown) {
		            	console.log("error");
		            }
	        	});
			}
			getConnections();
	    	var timer = setInterval(getConnections, 10000);
	    });
		</script>

		<?php wp_footer(); ?>
	</body>
</html>