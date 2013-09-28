    <?php get_header(); ?>
    <?php $ajax_nonce = wp_create_nonce("keyy"); ?>


<?php

try {
    $xbmc = new xbmc();
    $mov = json_decode($xbmc->getRecentlyAddedMovies());
    $tv = json_decode($xbmc->getRecentlyAddedEpisodes());
} catch(Exception $e) {}

?>
<div class="row">
    <div class="span3">
        <p class="lead"><?php _e('Recently added movies', 'couchbeard'); ?></p>
        <?php 
        if (!empty($mov)):
        ?>
        <div class="row">
            <div class="span3">
                <div id='movieCarousel' class='carousel slide movie'>
                    <div class='carousel-inner' id='movie-carousel'>
                        <?php foreach ($mov->result->movies as $index => $movie): ?>

                            <div class="item <?php echo ($index == 0 ? ' active' : ''); ?>">
                                <img class="frontpagemoviecover minfullwidth" src="<?php echo urldecode(substr($movie->thumbnail, 8, -1)); ?>" alt="">
                                <div class="carousel-caption">
                                    <p><?php echo $movie->label . ' (' . $movie->year . ')'; ?></p><a class="pull-right pointer" data-toggle="modal" href="#myMovie" id="movieopen" data-id="<?php echo $movie->imdbnumber; ?>"><i class="icon-info-sign icon-white"></i></a>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a class='left carousel-control' href='#movieCarousel' data-slide='prev'>&lsaquo;</a>
                    <a class='right carousel-control' href='#movieCarousel' data-slide='next'>&rsaquo;</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="span4">
        <p class="lead"><?php _e('Downloads', 'couchbeard'); ?></p>
        <div class="row">
            <div class="span4">
                <ul class="unstyled" id="downloads">

                </ul>
            </div>
        </div>
    </div>
    <div class="span5 pull-right">
        <p class="lead"><?php _e('Recently added TV show episodes', 'couchbeard'); ?></p>
        <?php 
        if (!empty($tv)): 
        ?>
        <div class="row">
            <div class="span5">
                <div id='showCarousel' class='carousel slide tv'>
                    <div class='carousel-inner'>
                        <?php
                        foreach ($tv->result->episodes as $index => $episode): ?>
                            <div class="item <?php echo ($index == 0 ? ' active' : ''); ?>">
                                <img class="minfullwidth frontpagemoviecover" src="<?php echo urldecode(substr($episode->thumbnail, 8, -1)); ?>" alt="">
                                <div class="carousel-caption">
                                    <p><?php echo $episode->showtitle . ' [' . $episode->season . 'x' . $episode->episode . '] '.$episode->title; ?></p><a class="pull-right pointer" data-toggle="modal" href="#myMovie" id="movieopen" data-id="<?php echo $episode->imdb; ?>"><i class="icon-info-sign icon-white"></i></a>
                                </div>
                            </div>
                        <?php
                        endforeach;
                        ?>
                    </div>
                    <a class='left carousel-control' href='#showCarousel' data-slide='prev'>&lsaquo;</a>
                    <a class='right carousel-control' href='#showCarousel' data-slide='next'>&rsaquo;</a>
                </div>
            </div>
        </div>
        <?php
        endif;
        ?>
    </div>
</div>

<?php get_template_part( 'template_parts/my_movie_modal' ); ?>

<?php get_footer(); ?>