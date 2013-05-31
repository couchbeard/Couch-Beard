<?php

// AJAX calls
function addMovieFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    echo (bool) cp_addMovie($_POST['id']);
    exit();
}
add_action('wp_ajax_addMovie', 'addMovieFunction');  // Only logged in users

function xbmc_sendNotificationFunction() {
    check_ajax_referer( 'keyy', 'security' );
    echo (bool) xbmc_sendNotification('Couch Beard message', $_POST['message']);
    exit();
}
add_action('wp_ajax_xbmcSendNotification', 'xbmc_sendNotificationFunction');  // Only logged in users

function addTVFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    echo (bool) sb_addShow($_POST['id']);
    exit();
}
add_action('wp_ajax_addTV', 'addTVFunction');  // Only logged in users

function movieInfoFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    echo getMovieData($_POST['imdb'], 1);
    exit();
}
add_action('wp_ajax_movieInfo', 'movieInfoFunction');  // Only logged in users

function movieXbmcInfoFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    echo json_encode(xbmc_getMovieDetails($_POST['movieid']));
    exit();
}
add_action('wp_ajax_movieXbmcInfo', 'movieXbmcInfoFunction');  // Only logged in users

function xbmcPlayMovieFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    echo json_encode(xbmc_play($_POST['movieid']));
    exit();
}
add_action('wp_ajax_xbmcPlayMovie', 'xbmcPlayMovieFunction');  // Only logged in users

function xbmcPlayPauseVideoFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    echo xbmc_playPauseVideo();
    exit();
}
add_action('wp_ajax_xbmcPlayPauseVideo', 'xbmcPlayPauseVideoFunction');  // Only logged in users

function currentPlayingFunction() 
{
    check_ajax_referer( 'keyy', 'security' );
    echo json_encode(xbmc_getCurrentPlaying());
    exit();
}
add_action('wp_ajax_currentPlaying', 'currentPlayingFunction');  // Only logged in users

function currentDownloadingFunction() 
{
    check_ajax_referer( 'keyy', 'security' );
    echo json_encode(sab_getQueue());
    exit();
}
add_action('wp_ajax_currentDownloading', 'currentDownloadingFunction');  // Only logged in users


// 

function timezone() {
    date_default_timezone_set(get_option('timezone_string'));
}
add_action('init', 'timezone');

function init_sessions() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'init_sessions');


// LOGIN SCREEN 
function custom_login_css() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri().'/Styles/login-style.css" />';
}
add_action('login_head', 'custom_login_css');

function custom_fonts() {
    echo '<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700" rel="stylesheet" type="text/css">';
}
add_action('login_head', 'custom_fonts');

function untame_fadein() {
    echo '<script type="text/javascript">// <![CDATA[
    jQuery(document).ready(function() { jQuery("#loginform,#nav,#backtoblog").css("display", "none");          jQuery("#loginform,#nav,#backtoblog").fadeIn(3500);     
    });
    // ]]></script>';
}
add_action( 'login_head', 'untame_fadein',30);

function custom_login_header_url($url) {
    return get_home_url();
}
add_filter( 'login_headerurl', 'custom_login_header_url' );


// SEARCH
function myprefix_autocomplete_init() {  
    // Register our jQuery UI style and our custom javascript file  
    wp_register_style('myprefix-jquery-ui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
    wp_register_script( 'my_acsearch', get_template_directory_uri() . '/Scripts/movie-search.js', array('jquery', 'jquery-ui-autocomplete'),null,true);  
  
    wp_localize_script( 'my_acsearch', 'MyAcSearch', array('url' => admin_url( 'admin-ajax.php' )));  
    // Function to fire whenever search form is displayed  
    add_action( 'get_search_form', 'myprefix_autocomplete_search_form' );  
  
    // Functions to deal with the AJAX request - one for logged in users, the other for non-logged in users.  
    add_action( 'wp_ajax_myprefix_autocompletesearch', 'myprefix_autocomplete_suggestions' ); 
    add_action( 'wp_ajax_{action}', 'my_hooked_function' ); 
    //add_action( 'wp_ajax_nopriv_myprefix_autocompletesearch', 'myprefix_autocomplete_suggestions' );  
}
add_action( 'init', 'myprefix_autocomplete_init' );    

function myprefix_autocomplete_search_form(){  
    wp_enqueue_script( 'my_acsearch' );  
    wp_enqueue_style( 'myprefix-jquery-ui' );  
}

function resizePoster($filename) {
    $path = "./cache/".md5($filename).".png";
    if (file_exists($path))
        return $path;

    // Content type
    header('Content-Type: image/png');

    // Get new sizes
    list($width, $height) = getimagesize($filename);

    $std_width = 175;
    $std_height = 250;
     
    $newwidth = (isset($_GET['w']) ? $_GET['w'] : $std_width);
    $newheight = (isset($_GET['h']) ? $_GET['h'] : $std_height);

    // Load
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    if (exif_imagetype($filename) == IMAGETYPE_JPEG)
    {
        $source = imagecreatefromjpeg($filename);
    }
    else if (exif_imagetype($filename) == IMAGETYPE_PNG)
    {
        $source = imagecreatefrompng($filename);
    }
    else if (exif_imagetype($filename) == IMAGETYPE_GIF)
    {
        $source = imagecreatefromgif($filename);
    }

    // Resize
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    // Output
    imagepng($thumb, $path);

    return $path;
} 

/**
 * Download website
 * @param  string $Url Download URL
 * @return $json      Website
 */
function curl($Url, $headers = null){
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }

    $ch = curl_init();
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
    // Set a referer
    //curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");
 
    // User agent
    curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);

    // Set header
    if (!empty($headers))
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}

/**
 * Get movie data by ID
 * @param  string $id IMDB id
 * @return array     Movie data
 */
function getMovieData($id, $jsonret = 0) {
    $url = "http://www.omdbapi.com/?i=" . $id . "&plot=full";
    $json = curl($url);
    if (!empty($jsonret))
        return $json;

    $data = json_decode($json);

    return $data;
}

function login_redirect() {
    if (!is_user_logged_in())
        auth_redirect();
}
add_action( 'wp', 'login_redirect' );

function getSearchpageID() {
    return get_page_by_title( 'Search' )->ID;
}

function myprefix_autocomplete_suggestions() {
    //$url = "http://www.omdbapi.com/?s=" . urlencode($_REQUEST['term']);
    //$url = "http://imdbapi.org/?q=" . $_REQUEST['term'] . "&episode=0&limit=10";
    $search = str_replace(array(" ", "(", ")"), array("_", "", ""), $_REQUEST['term']); //format search term
    $firstchar = substr($search,0,1); //get first character
    $url = "http://sg.media-imdb.com/suggests/${firstchar}/${search}.json"; //format IMDb suggest URL
    $imdb = curl($url);
    preg_match('/^imdb\$.*?\((.*?)\)$/ms', $imdb, $matches); //convert JSONP to JSON

    if(!$_SERVER["HTTP_X_REQUESTED_WITH"] || !$_GET['term']) {
        _e('error', 'wpbootstrap');
        exit();
    }

    $json = $matches[1];
    $arr = json_decode($json, true);

    $suggestions = array();  

    if(isset($arr['d'])) {
        foreach ($arr['d'] as $data) {
            if ($data['q'] == "feature" || $data['q'] == "TV series") {
                $suggestion = array();
                $img = preg_replace('/_V1_.*?.jpg/ms', "_V1._SY50.jpg", $data['i'][0]);
                $string = (strlen($data['l']) > 50) ? substr($data['l'], 0, 45).'...' : $data['l'];
                $searchpage = get_page_by_title( 'Search' );
                $suggestion['searchpageid'] = getSearchpageID();
                $suggestion['imdbid'] = (string) $data['id'];
                $suggestion['label'] = $data['l'];
                $suggestion['title'] = $string;
                $suggestion['year'] = $data['y'];
                $suggestion['type'] = $data['q'];
                $suggestion['image'] = (empty($img)) ? IMAGES . '/no_cover.png' : $img;
                $suggestions[] = $suggestion;
            }
        }
    } else {
        $suggestion = array();
        $suggestion['imdbid'] = -1;
        $suggestion['title'] = __('No results', 'wpbootstrap');
        $suggestions[] = $suggestion;
    }

    echo $_GET["callback"] . "(" . json_encode($suggestions) . ")";
    exit();
}  


function disableTopToolBar()
{
	show_admin_bar(false);
}
add_action('init', 'disableTopToolBar', 9);

function custom_scripts()
{
    wp_register_script('bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array('jquery'));
    wp_enqueue_script('bootstrap');
    wp_register_script('tablesorter', get_template_directory_uri() . '/Scripts/jquery.tablesorter.min.js', array('jquery'));
    wp_enqueue_script('tablesorter');
    wp_enqueue_script( 'my_acsearch' );
    wp_enqueue_style( 'myprefix-jquery-ui' );
    wp_enqueue_script("jquery-ui-core");
    wp_register_script('jnotify', get_template_directory_uri() . '/Scripts/jquery.notify.js', array('jquery', 'jquery-ui-core', 'jquery-ui-progressbar'));
    wp_enqueue_script('jnotify');
    wp_register_script('lazyload', get_template_directory_uri() . '/Scripts/jquery.lazyload.js', array('jquery'));
    wp_enqueue_script('lazyload');
    wp_register_script('jic', get_template_directory_uri() . '/Scripts/JIC.js', array('jquery'));
    wp_enqueue_script('jic');     
 
}
add_action('wp_enqueue_scripts', 'custom_scripts');

function custom_styles() 
{
    wp_register_style( 'googlefonts', 'http://fonts.googleapis.com/css?family=Noto+Sans:400,700|Patrick+Hand+SC|Josefin+Slab:400,700|Lemon|Love+Ya+Like+A+Sister|Montserrat+Subrayada');
    wp_enqueue_style( 'googlefonts');
}
add_action( 'wp_enqueue_scripts', 'custom_styles' );

function register_my_menus()
{
    register_nav_menus(array(
        'user'   => __('User menu', 'wpbootstrap'),
        'guest'     => __('Guest menu', 'wpbootstrap')
    ));
}
add_action('init', 'register_my_menus');

function getMovies()
{
    $url = "http://imdbapi.org/?q=".$_POST['q']."&episode=0&limit=10";
    $imdb = new IMDb(true, true, 0);
    $imdb = file_get_contents($url);

    if(!$_SERVER["HTTP_X_REQUESTED_WITH"] || !$_POST['q']){
        _e('error', 'wpbootstrap');
        exit;
    }

    $json = json_decode($imdb);
    if (count($json) < 1) {
        return null;
    }
    $suggestions = array();
    foreach($json as $movie){
        $suggestion = array();
        $suggestion['imdbid'] = (string) $movie->imdb_id;
        $suggestion['label'] = $movie->title;
        $suggestion['title'] = chunk_split($movie->title, 20, '<br />') . ' (' . date('Y',strtotime($movie->year)).')';
        $suggestion['type'] = $movie->type;
        $suggestion['image'] = $movie->poster;
        $suggestions[]= $suggestion;
    }
    echo json_encode($suggestions);
    exit;
}

add_action('wp_ajax_getMovies', 'getMovies');  // Only logged in users

function custom_theme_setup()
{
    $lang_dir = get_template_directory() . '/languages';
    load_theme_textdomain('wpbootstrap', $lang_dir);
}
add_action('after_setup_theme', 'custom_theme_setup');

function get_ID_by_slug($page_slug)
{
    $page = get_page_by_path($page_slug);
    if ($page)
    {
        return $page->ID;
    }
    else
    {
        return -1;
    }
}

if (function_exists('register_sidebar'))
{
    register_sidebar(array(
        'before_widget'     => '',
        'after_widget'      => '',
        'before_title'      => '<h3>',
        'after_title'       => '</h3>',
    ));
}

/**
 * Register sidebars
 */

if (function_exists('register_sidebar')) {
    register_sidebar( array (
        'name' => __('Front footer 1', 'wpbootstrap'),
        'id' => 'front-footer-1',
        'description' => 'Widget 1 on frontpage',
        'before_widget' => '<div class="front-footer-1 span4">',
        'after_widget' => '</div> <!-- end front footer 1 -->',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ) ); 

    register_sidebar( array (
        'name' => __('Front footer 2', 'wpbootstrap'),
        'id' => 'front-footer-2',
        'description' => 'Widget 2 on frontpage',
        'before_widget' => '<div class="front-footer-2 span4">',
        'after_widget' => '</div> <!-- end front footer 2 -->',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ) ); 

    register_sidebar( array (
        'name' => __('Front footer 3', 'wpbootstrap'),
        'id' => 'front-footer-3',
        'description' => 'Widget 3 on frontpage',
        'before_widget' => '<div class="front-footer-3 span4">',
        'after_widget' => '</div> <!-- end front footer 3 -->',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ) ); 

    register_sidebar( array (
        'name' => __('Footer', 'wpbootstrap'),
        'id' => 'footer',
        'description' => 'Footer widget',
        'before_widget' => '<div class="footerwidget">',
        'after_widget' => '</div> <!-- end footerwidget -->',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ));

}

define('THEMEROOT', get_stylesheet_directory_uri());
define('IMAGES', THEMEROOT . '/images');

/**
 * Extended Walker class for use with the
 * Twitter Bootstrap toolkit Dropdown menus in Wordpress.
 * Edited to support n-levels submenu.
 * @author johnmegahan https://gist.github.com/1597994, Emanuele 'Tex' Tessore https://gist.github.com/3765640
 */
class BootstrapNavMenuWalker extends Walker_Nav_Menu
{

    function start_lvl(&$output, $depth)
    {

        $indent = str_repeat("\t", $depth);
        $submenu = ($depth > 0) ? ' sub-menu' : '';
        $output .= "\n$indent<ul class=\"dropdown-menu$submenu depth_$depth\">\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {


        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';

        $li_attributes = '';
        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        // managing divider: add divider class to an element to get a divider before it.
        $divider_class_position = array_search('divider', $classes);
        if ($divider_class_position !== false)
        {
            $output .= "<li class=\"divider\"></li>\n";
            unset($classes[$divider_class_position]);
        }

        $classes[] = ($args->has_children) ? 'dropdown' : '';
        $classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';
        $classes[] = 'menu-item-' . $item->ID;
        if ($depth && $args->has_children)
        {
            $classes[] = 'dropdown-submenu';
        }


        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= ($args->has_children) ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= ($depth == 0 && $args->has_children) ? ' <b class="caret"></b></a>' : '</a>';
        $item_output .= $args->after;


        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output)
    {
        //v($element);
        if (!$element)
            return;

        $id_field = $this->db_fields['id'];

        //display this element
        if (is_array($args[0]))
            $args[0]['has_children'] = !empty($children_elements[$element->$id_field]);
        else if (is_object($args[0]))
            $args[0]->has_children = !empty($children_elements[$element->$id_field]);
        $cb_args = array_merge(array(&$output, $element, $depth), $args);
        call_user_func_array(array(&$this, 'start_el'), $cb_args);

        $id = $element->$id_field;

        // descend only when the depth is right and there are childrens for this element
        if (($max_depth == 0 || $max_depth > $depth + 1 ) && isset($children_elements[$id]))
        {

            foreach ($children_elements[$id] as $child)
            {

                if (!isset($newlevel))
                {
                    $newlevel = true;
                    //start the child delimiter
                    $cb_args = array_merge(array(&$output, $depth), $args);
                    call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
                }
                $this->display_element($child, $children_elements, $max_depth, $depth + 1, $args, $output);
            }
            unset($children_elements[$id]);
        }

        if (isset($newlevel) && $newlevel)
        {
            //end the child delimiter
            $cb_args = array_merge(array(&$output, $depth), $args);
            call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
        }

        //end this element
        $cb_args = array_merge(array(&$output, $element, $depth), $args);
        call_user_func_array(array(&$this, 'end_el'), $cb_args);
    }

}

class Cache {
 
        // Things not cache
        var $doNotCache = array();
 
        // General Config Vars
        var $cacheDir = "./cache";
        var $cacheTime = 3600; // Seconds
        var $cacheFile;
        var $cacheFileName;
        var $cacheOverride = "true";
        var $cacheNotice = "\n<!-- Cached by Couch Beard -->";
 
        function __construct()
        {
            if ( !is_dir($this->cacheDir) )
                mkdir($this->cacheDir, 0755);
        }
 
        function override()
        {
            return ( isset($_GET['nocache']) && ( 0 == strcmp(isset($_GET['nocache']), $this->cacheOverride) ) ) ? false : true;
        } // End override
 
        function start()
        {
            if ( ! $this->override() )
            {
                // File setup
                $this->cacheFileName = md5( $_SERVER['REQUEST_URI'] );
                $this->cacheFile = $this->cacheDir.'/'.$this->cacheFileName;
                $request = array_slice ( explode('/',$_SERVER['REQUEST_URI']), 2 );
 
                if ( !in_array($request[0], $this->doNotCache) )
                {
                    // Check cache or create new cache
                    if ( file_exists($this->cacheFile) && ( time() - filemtime($this->cacheFile) ) < $this->cacheTime )
                    {
                        readfile( $this->cacheFile );
                        exit(); // Stop page loading when cache found
                    }
                    else
                        ob_start(); // Start the buffer
                    // End of Magic
                }
            }
        } // End start
 
        function end()
        {
            if ( ! $this->override() )
            {
                if ( false === file_put_contents( $this->cacheFile, ob_get_contents() . $this->cacheNotice ) )
                    echo "Trouble"; // Error message
                ob_clean(); // Close and throw away the buffer
                // Show the contents from the fresh cache.
                readfile( $this->cacheFile );
            }
        } // End end
 
    } // End Class Cache

?>