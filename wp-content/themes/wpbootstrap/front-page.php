<?php get_header(); ?>
	
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<div class="hero-unit">
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</div>
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
  	</div>
<?php get_footer(); ?>