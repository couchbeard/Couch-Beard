<?php get_header(); ?>
    <?php $ajax_nonce = wp_create_nonce("keyy"); ?>

<?php
$mov = json_decode(xbmc_getRecentlyAddedMovies());
$tv = json_decode(xbmc_getRecentlyAddedEpisodes());
?>
<div class="row">
    <div class="span3">
        <?php 
        if (!empty($mov))
        {
        ?>
        <p class="lead"><?php _e('Recently added movies', 'wpbootstrap'); ?></p>
        <div class="row">
            <div class="span3">
                <div id='movieCarousel' class='carousel slide movie'>
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
        </div>
        <?php
        }
        ?>
    </div>
    <div class="span4">
        <p class="lead"><?php _e('Downloads', 'wpbootstrap'); ?></p>
        <div class="row">
            <div class="span4">
                <ul class="unstyled" id="downloads">

                </ul>
            </div>
        </div>
    </div>
    <div class="span5 pull-right">
        <?php 
        if (!empty($tv))
        {
        ?>
        <p class="lead"><?php _e('Recently added TV show episodes', 'wpbootstrap'); ?></p>
        <div class="row">
            <div class="span5">
                <div id='showCarousel' class='carousel slide tv'>
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
        ?>
    </div>
</div>

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
    var running;
    function currentDownloading() {
        jQuery.ajax({  
            type: 'POST',
            cache: false,  
            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",
            dataType:'json',  
            data: {  
                action: 'currentDownloading',
                security: '<?php echo $ajax_nonce; ?>'
            },
            success: function(data, textStatus, XMLHttpRequest) {
                if (data != "" && data != null) {
                    if (!running) {
                        clearInterval(timer);
                        timer = setInterval(currentDownloading, 2000);
                        running = true;
                    }
                    var length = data.length - 1;
                    $('#downloads').html('');
                    for (var i = length; i >= 0; i--) {
                        var listItem = document.createElement("li");
                        listItem.innerHTML = data[i].filename;
                        $('#downloads').append(listItem);
                    }
                } else {
                    $('#downloads').html("Couldn't find any downloads");
                }
            },  
            error: function(MLHttpRequest, textStatus, errorThrown) {
                if (running) {
                    running = false;
                    clearInterval(timer);
                    timer = setInterval(currentDownloading, 5000);
                    $('#downloads').html("Couldn't find any downloads");
                }
            }  
        }); 
    }
    
    

    currentDownloading();
    var timer = setInterval(currentDownloading, 1000);
});
</script>