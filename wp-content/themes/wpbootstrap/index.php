<?php get_header(); ?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<h1><?php the_title(); ?></h1>	
		<?php the_content(); ?>
	<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>

	<div class="row">
        <div class="span4">
          	<div class="front-footer-1">
          		<h2>Test</h2>
          		<?php get_sidebar('') ?>
          	</div> <!-- end front footer 1 -->
        </div>
        <div class="span4">
          	<div class="front-footer-2">
          		<h2>Test</h2>
          	</div> <!-- end front footer 2 -->
       	</div>
        <div class="span4">
          	<div class="front-footer-3">
          		<h2>Test</h2>
          	</div> <!-- end front footer 3 -->
        </div>
  	</div>
  	
<?php get_footer(); ?>