<?php get_header(); ?>
	<div class="row">
		<div class="span8">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<legend><?php the_title(); ?></legend>
				<?php the_content(); ?>
			<?php endwhile; else: ?>
				<p><?php _e('Sorry, this page does not exist.'); ?></p>
			<?php endif; ?>
		</div>
		<div class="span4">
			<?php get_sidebar(); ?>	
		</div>
	</div>	
<?php get_footer(); ?>