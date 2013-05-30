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
    </div>

<?php
$mov = json_decode(xbmc_getRecentlyAddedMovies());
$tv = json_decode(xbmc_getRecentlyAddedEpisodes());
if (!empty($mov) && !empty($tv))
{
    ?>
<table>
    <tr>
        <td>     
<div id='movieCarousel' class='carousel slide movie'>
    <h3><?php _e('Recently added movies', 'wpbootstrap'); ?></h3>
    <div class='carousel-inner' id='movie-carousel'>
        <?php
        foreach ($mov->result->movies as $index => $movie) {
        echo '<div class="item'.($index == 0 ? ' active' : '').'">
          <img src="' . urldecode(substr($movie->thumbnail, 8, -1)) . '" alt="">
          <div class="carousel-caption">
            <h4>' . $movie->label . ' (' . $movie->year . ')</h4>
          </div>
        </div>';
        }
        ?>
    </div>
    <a class='left carousel-control' href='#movieCarousel' data-slide='prev'>&lsaquo;</a>
    <a class='right carousel-control' href='#movieCarousel' data-slide='next'>&rsaquo;</a>
</div>
        </td>
        <td>
            
<div id='showCarousel' class='carousel slide tv'>
    <h3><?php _e('Recently added TV show episodes', 'wpbootstrap'); ?></h3>
<div class='carousel-inner'>
    <?php
    
    foreach ($tv->result->episodes as $index => $episode) {
    echo '<div class="item'.($index == 0 ? ' active' : '').'">
      <img src="' . urldecode(substr($episode->thumbnail, 8, -1)) . '" alt="">
      <div class="carousel-caption">
        <h4>' . $episode->showtitle . ' [' . $episode->season . 'x' . $episode->episode . '] '.$episode->title.'</h4>
      </div>
    </div>';
    }
    ?>
</div>
<a class='left carousel-control' href='#showCarousel' data-slide='prev'>&lsaquo;</a>
<a class='right carousel-control' href='#showCarousel' data-slide='next'>&rsaquo;</a>
</div>
        </td>
    </tr>
</table>
<?php
}
try
{
    $sab_history = sab_getHistory();
    if (!empty($sab_history))
        foreach ($sab_history as $slot)
        {
            echo '['.$slot->status.'] '.$slot->name.(empty($slot->completed) ? '' : ' <small><em>'.date('d-m-Y H:i', $slot->completed).'</em></small>').'<br />';
        }
} catch (Exception $e) {}

?>


<?php get_footer(); ?>
<script>
$(function() {
    $('#movieCarousel').carousel({
        interval: Math.floor(Math.random() * 4000) + 2500
    });

    $('#showCarousel').carousel({
        interval: Math.floor(Math.random() * 4000) + 2500
    });
    $("img.lazy").lazyload({
    });
});
</script>