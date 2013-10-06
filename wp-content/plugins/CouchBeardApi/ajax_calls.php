<?php
// AJAX calls
function addMovieFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$cp = new couchpotato();
    } catch (Exception $e) {
    	exit();
    }
    
    echo (bool) $cp->addMovie($_POST['id']);
    exit();
}
add_action('wp_ajax_addMovie', 'addMovieFunction');  // Only logged in users

function xbmc_sendNotificationFunction() {
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo (bool) $xbmc->sendNotification('Couch Beard message', $_POST['message']);
    exit();
}
add_action('wp_ajax_xbmcSendNotification', 'xbmc_sendNotificationFunction');  // Only logged in users

function addTVFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$sb = new sickbeard();
    } catch (Exception $e) {
    	exit();
    }
    
    echo (bool) $sb->addShow($_POST['id']);
    exit();
}
add_action('wp_ajax_addTV', 'addTVFunction');  // Only logged in users

function movieInfoFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    $movie = new imdbAPI($_POST['imdb']);
    echo json_encode($movie->getData());
    exit();
}
add_action('wp_ajax_movieInfo', 'movieInfoFunction');  // Only logged in users

function movieXbmcInfoFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo json_encode($xbmc->getMovieDetails($_POST['movieid']));
    exit();
}
add_action('wp_ajax_movieXbmcInfo', 'movieXbmcInfoFunction');  // Only logged in users

function xbmcPlayMovieFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo $xbmc->play($_POST['movieid']);
    exit();
}
add_action('wp_ajax_xbmcPlayMovie', 'xbmcPlayMovieFunction');  // Only logged in users

function xbmcPlayPauseFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo $xbmc->playPause($_POST['player']);
    exit();
}
add_action('wp_ajax_xbmcPlayPause', 'xbmcPlayPauseFunction');  // Only logged in users

function currentPlayingFunction() 
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo json_encode($xbmc->getCurrentPlaying());
    exit();
}
add_action('wp_ajax_currentPlaying', 'currentPlayingFunction');  // Only logged in users

function xbmcPlayerPropsFunction() 
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo $xbmc->getPlayerProperties();
    exit();
}
add_action('wp_ajax_xbmcPlayerProps', 'xbmcPlayerPropsFunction');  // Only logged in users

function currentDownloadingFunction() 
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$sab = new sabnzbd();
    } catch (Exception $e) {
    	exit();
    }
    
    echo json_encode($sab->getQueue());
    exit();
}
add_action('wp_ajax_currentDownloading', 'currentDownloadingFunction');  // Only logged in users

function connectionStatusFunction()
{
    //check_ajax_referer( 'keyy', 'security' );
    echo json_encode(isAnyAlive());
    exit();
}
add_action('wp_ajax_connectionStatus', 'connectionStatusFunction'); // Only logged in users

function xbmcInputActionFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo $xbmc->inputAction($_POST['input']);
    exit();
}
add_action('wp_ajax_xbmcInputAction', 'xbmcInputActionFunction'); // Only logged in users

function xbmcEjectDriveFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    try {
    	$xbmc = new xbmc();
    } catch (Exception $e) {
    	exit();
    }
    
    echo $xbmc->ejectDrive();
    exit();
}
add_action('wp_ajax_xbmcEjectDrive', 'xbmcEjectDriveFunction'); // Only logged in users
?>