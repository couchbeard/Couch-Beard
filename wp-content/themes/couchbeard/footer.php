		<hr>    
		<footer>
			<script>var ajax_nonce = '<?php echo $ajax_nonce; ?>';</script>
			<div class="row">
				<div class="span2">
		   			 <p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?></p>
		   			 <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/deed"><img alt="Creative Commons licens" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-sa/3.0/80x15.png" /></a>
		    	</div>
		    	<?php wp_footer(); ?>
			</div>
		</footer>
			<div id="statusConnection" data-toggle="tooltip">
				<span class="label label-warning">
					<p class="pull-right">Connection status</p>
					<p class="pull-left"><i class="icon-minus-sign icon-white"></i></p>
				</span>
			</div>
		</div> <!-- /container -->
	</body>
</html>