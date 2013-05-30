<?php get_header(); ?>
    <?php $ajax_nonce = wp_create_nonce("keyy"); ?>

<?php
$mov = json_decode(xbmc_getRecentlyAddedMovies());
$tv = json_decode(xbmc_getRecentlyAddedEpisodes());
if (!empty($mov) && !empty($tv))
{
    ?>
<div class="row">
    <div class="span3">
        <div id='movieCarousel' class='carousel slide movie'>
            <p class="lead"><?php _e('Recently added movies', 'wpbootstrap'); ?></p>
                    <div class='carousel-inner' id='movie-carousel'>
                        <?php
                        foreach ($mov->result->movies as $index => $movie) { ?>
                            <div class="item <?php echo ($index == 0 ? ' active' : ''); ?>">
                                <img class="frontpagemoviecover minfullwidth" src="<?php echo urldecode(substr($movie->thumbnail, 8, -1)); ?>" alt="">
                                <div class="carousel-caption">
                                    <p><?php echo $movie->label . ' (' . $movie->year . ')'; ?></p><i class="icon-info-sign icon-white pull-right pointer"></i>

                                </div>
                            </div>
                        <?php 
                        }
                        ?>
                    </div>
                    <a class='left carousel-control' href='#movieCarousel' data-slide='prev'>&lsaquo;</a>
                    <a class='right carousel-control' href='#movieCarousel' data-slide='next'>&rsaquo;</a>
                </div>
            </div>
    <div class="span5 pull-right">    
        <div id='showCarousel' class='carousel slide tv'>
            <p class="lead"><?php _e('Recently added TV show episodes', 'wpbootstrap'); ?></p>
            <div class='carousel-inner'>
                <?php
                foreach ($tv->result->episodes as $index => $episode) { ?>
                    <div class="item <?php echo ($index == 0 ? ' active' : ''); ?>">
                        <img class="minfullwidth frontpagemoviecover" src="<?php echo urldecode(substr($episode->thumbnail, 8, -1)); ?>" alt="">
                        <div class="carousel-caption">
                            <p><?php echo $episode->showtitle . ' [' . $episode->season . 'x' . $episode->episode . '] '.$episode->title; ?></p><i class="icon-info-sign icon-white pull-right pointer"></i>
                        </div>
                    </div>
                <?php
                }
                ?>
        </div>
        <a class='left carousel-control' href='#showCarousel' data-slide='prev'>&lsaquo;</a>
        <a class='right carousel-control' href='#showCarousel' data-slide='next'>&rsaquo;</a>
        </div>
    </div>
</div>

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