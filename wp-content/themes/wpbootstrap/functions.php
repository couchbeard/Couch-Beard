<?php

// AJAX calls
function addMovieFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    echo cp_addMovie($_POST['id']);
    exit();
}
add_action('wp_ajax_addMovie', 'addMovieFunction');  // Only logged in users

function addTVFunction()
{
    check_ajax_referer( 'keyy', 'security' );
    //echo sb_addShow($_POST['id']);
    echo 1;
    exit();
}
add_action('wp_ajax_addTV', 'addTVFunction');  // Only logged in users

function timezone() {
    date_default_timezone_set(get_option('timezone_string'));
}
add_action('init', 'timezone');

/*function init_sessions() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'init_sessions');*/


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
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}
add_action( 'thesis_hook', 'curl');

/**
 * Get movie data by ID
 * @param  string $id IMDB id
 * @return array     Movie data
 */
function getMovieData($id) {
    $url = "http://www.omdbapi.com/?i=" . $_GET['id'] . "&plot=full";
    $json = curl_download($url);

    $data = json_decode($json);

    return $data;
}

function myprefix_autocomplete_suggestions() {
    $url = "http://www.omdbapi.com/?s=" . urlencode($_REQUEST['term']);

    $imdb = curl($url);

    if(!$_SERVER["HTTP_X_REQUESTED_WITH"] || !$_GET['term']){
        _e('error', 'wpbootstrap');
        exit();
    }

    $json = json_decode($imdb)->Search;

    $suggestions = array();
    $suggestion = array();    

    if (!isset($json->Error)) {
        foreach ($json as $data) {
            $new_url = "http://www.omdbapi.com/?i=" . $data->imdbID;
            $new_json = curl($new_url);
            $movie = json_decode($new_json);

            $string = (strlen($movie->Title) > 50) ? substr($movie->Title, 0, 45).'...' : $movie->Title;
            $searchpage = get_page_by_title( 'Search' );
            $suggestion['searchpageid'] =  $searchpage->ID;
            $suggestion['imdbid'] = (string) $movie->imdbID;
            $suggestion['label'] = $movie->Title;
            $suggestion['title'] = $string;
            $suggestion['year'] = $movie->Year;
            $suggestion['type'] = $movie->Type;
            $suggestion['image'] = ($movie->Poster == 'N/A') ? IMAGES . '/no_cover.png' : $movie->Poster;
            $suggestions[]= $suggestion;
        }
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
    wp_register_script('jnotify', get_template_directory_uri() . '/Scripts/jquery.notify.js', array('jquery', 'jquery-ui-autocomplete'));
    wp_enqueue_script('jnotify');
 
}
add_action('wp_enqueue_scripts', 'custom_scripts');

function custom_styles() 
{
    //wp_register_style( 'Ranchoeffect', 'http://fonts.googleapis.com/css?family=Rancho&effect=shadow-multiple');
    //wp_enqueue_style( 'Ranchoeffect');
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
?>