<?php
/*
	Plugin Name: Couch Beard APIs
	Plugin URI: 
	Description: Manage API keys for applications
	Author: Mads Lundt
	Version: 1.0
	Author URI: 
*/

global $wpdb;
global $table_name;
$table_name = $wpdb->prefix . 'apis';

function loadStyle() {
	wp_register_style( 'style', plugin_dir_url( __FILE__ ) . 'couch-beard-api.css');
	wp_enqueue_style( 'style');
}
add_action( 'admin_enqueue_scripts', 'loadStyle' );
function couchbeardapi_activate() {
	global $wpdb;
	global $table_name;

	$apis = array("Couchpotato", "Sickbeard", "SabNZBD");
	$logins = array("XBMC");
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE " . $table_name . "(
              ID INT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
              name VARCHAR(45) NOT NULL UNIQUE ,
              api VARCHAR(100) NULL ,
              ip VARCHAR(100) NULL ,
              username VARCHAR(45) NULL ,
              password VARCHAR(45) NULL ,
              login TINYINT(1) DEFAULT 0 NOT NULL ,
              PRIMARY KEY (ID) )
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('api_database_version', '1.0');
    }

    foreach ($apis as $a) {
    	$wpdb->insert( $table_name, array('name' => $a));
	}

	foreach ($logins as $l) {
		$wpdb->insert( $table_name, array('name' => $l, 'login' => 1));
	}
}
register_activation_hook( __FILE__, 'couchbeardapi_activate' );

function couchbeardapi_admin_actions() {
	$page_title = "Couch Beard APIs";
	$menu_title = "Couch Beard APIs";
	$capability = "manage_options";

	add_options_page( $page_title, $menu_title, $capability, __FILE__, 'couchbeardapi_admin' );
}
add_action('admin_menu', 'couchbeardapi_admin_actions');

function couchbeardapi_admin() {
	global $wpdb;
	global $table_name;
	$list = $wpdb->get_results(
		"
		SELECT *
		FROM $table_name
		"
	);

	if (isset($_POST['apisave'])) 
	{
		foreach ($list as $a) {

			// API
			if ($a->login == 0) {
				if (strlen($_POST[$a->name . 'api']) > 2 && strlen($_POST[$a->name . 'ip']) > 2) {
						$wpdb->query($wpdb->prepare(
							"
								UPDATE $table_name
								SET api = %s, ip = %s
								WHERE name = %s
							",
							array(
								$_POST[$a->name . 'api'],
								$_POST[$a->name . 'ip'],
								$a->name
							)
						));
				} else {
					$_POST[$a->name . 'api'] = "";
					$_POST[$a->name . 'ip'] = "";
					printf(__('Error in %s', 'wpbootstrap'), $a->name);
					echo '<br />';
				}
			} else {
				if (strlen($_POST[$a->name . 'user']) > 2 && strlen($_POST[$a->name . 'pw']) > 2 && strlen($_POST[$a->name . 'ip']) > 2) {
						$wpdb->query($wpdb->prepare(
							"
								UPDATE $table_name
								SET ip = %s, username = %s, password = %s
								WHERE name = %s
							",
							array(
								$_POST[$a->name . 'ip'],
								$_POST[$a->name . 'user'],
								$_POST[$a->name . 'pw'],
								$a->name
							)
						));
				} else {
					$_POST[$a->name . 'user'] = "";
					$_POST[$a->name . 'pw'] = "";
					$_POST[$a->name . 'ip'] = "";
					printf(__('Error in %s', 'wpbootstrap'), $a->name);
					echo '<br />';
				}				
			}
		}
	}
?>
	<div class="wrap">
	<h2>Manage Couch Beard APIs</h2>
	</div>
	<form action="" method="POST" id="content">
		<table class="widefat" id="table">
			<thead>
			<tr>
				<th> Application </th>
				<th> Key </th>
				<th> IP:Port </th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th> Application </th>
				<th> Key </th>
				<th> IP:Port </th>
			</tr>
			</tfoot>
			<tbody>
			<?php
			foreach ($list as $a)
			{
			?>
				<tr>
					<td id="name"> <strong> <?php echo $a->name; ?> </strong> </td>
			<?php	
					if ($a->login == 0) {
			?>
						<td> <input type="text" name="<?php echo $a->name; ?>api" value="<?php echo (isset($a->api) ? $a->api : $_POST[$a->name . 'api']); ?>" placeholder="API key"> </td>
						<td> <input type="text" name="<?php echo $a->name; ?>ip" value="<?php echo (isset($a->ip) ? $a->ip : $_POST[$a->name . 'ip']); ?>" placeholder="IP:Port"> </td>
			<?php 
					} else {
			?>
						<td> 
							<input type="text" name="<?php echo $a->name; ?>user" value="<?php echo (isset($a->username) ? $a->username : $_POST[$a->name . 'user']); ?>" placeholder="Username"> 
							<input type="password" name="<?php echo $a->name; ?>pw" value="<?php echo (isset($a->password) ? $a->password : $_POST[$a->name . 'pw']); ?>" placeholder="Password"> 
						</td>
						<td> <input type="text" name="<?php echo $a->name; ?>ip" value="<?php echo (isset($a->ip) ? $a->ip : $_POST[$a->name . 'ip']); ?>" placeholder="IP:Port"> </td>					
			<?php
					}
			?>
				</tr>
			<?php
			}
			?>	
			</tbody>
		</table>
		<input type="submit" name="apisave" id="sub" value="Save changes" class="button-primary" />
	</form>

<?php
} // END FUNCTION






////////////////////////////////////////////////////////////////////////////////////
// Private call functions 																  
////////////////////////////////////////////////////////////////////////////////////

/**
 * Get API key
 * @param  string $name name of the application
 * @return string       API key
 */
function getAPI($name) {
	global $wpdb;
	global $table_name;
	$api = $wpdb->get_var($wpdb->prepare(
		"
		SELECT api
		FROM $table_name
		WHERE name = %s
		", 
		$name
	));
	if (empty($api))
		throw new Exception('No API');

	return $api;
}

/**
 * Get login
 * @param  string $name name of the application
 * @return array       username and password
 */
function getLogin($name) {
global $wpdb;
	global $table_name;
	$user = $wpdb->get_row($wpdb->prepare(
		"
		SELECT username, password
		FROM $table_name
		WHERE name = %s
		", 
		$name
	));
	if (empty($user->username))
		throw new Exception('No user');

	return $user;
}

/**
 * Download website
 * @param  string $Url Download URL
 * @return $json      Website
 */
function curl_download($Url, $headers = null){
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }

    $ch = curl_init();
 
    // Now set some options (most are optional)
 
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

////////////////////////////////////////////////////////////////////////////////////
// Public call functions 																  
////////////////////////////////////////////////////////////////////////////////////

///////////////
// Couchpotato
///////////////
/**
 * Get couchpotato url
 * @return string url
 */
function cp_getURL() {
	global $wpdb;
	global $table_name;
	$ip = $wpdb->get_var($wpdb->prepare(
		"
		SELECT ip
		FROM $table_name
		WHERE name = %s
		", 
		'Couchpotato'
	));
	if (empty($ip))
		throw new Exception('No IP');

	$url = "http://" . $ip . "/api/" . getAPI('Couchpotato');
	return $url;
}

/**
 * Get version of Couchpotato
 * @return string Version
 */
function cp_version(){
	try {
		$url = cp_getURL() . '/app.version';
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->version;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Get connection status to Couchpotato
 * @return bool Connection status
 */
function cp_available(){
	try {
		$url = cp_getURL() . '/app.available';
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->success;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Add movie to Couchpotato
 * @param  string $id IMDB movie id
 * @return bool     Adding status
 */
function cp_addMovie($id){
	try {
		$url = cp_getURL() . '/movie.add/?identifier=' . $id;
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->added;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Remove movie from wanted list in Couchpotato
 * @param  int $id Couchpotato id
 * @return bool     Success
 */
function cp_removeMovie($id){
	try {
		$url = cp_getURL() . '/movie.delete/?id=' . $id . '&delete_from=wanted';
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->success;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Get all wanted movies in Couchpotato
 * @return array Movies
 */
function cp_getMovies(){
	try {
		$url = cp_getURL() . '/movie.list/?status=active';
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->movies;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Refresh a movie in Couchpotato
 * @param  int $id Couchpotato id
 * @return bool     Success
 */
function cp_refreshMovie($id){
	try {
		$url = cp_getURL() . '/movie.list/?id=' . $id;
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->success;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Looking for updates to Couchpotato
 * @return bool update available
 */
function cp_update() {
	try {
		$url = cp_getURL() . '/updater.check';
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->update_available;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Looking for a specific movie in CouchPotato
 * @param  int $imdb_id IMDb movie ID
 * @return bool movie found in CouchPotato
 */
function cp_movieWanted($imdb_id)
{
	try {
		$url = cp_getURL() . '/movie.get/?id=' . $imdb_id;	
		$json = curl_download($url);

		$res = json_decode($json);
		if ($res->success)
		{
			if (count($res->movie->releases))
			{
				return false;
			}
			return true;
		}
		return false;
	} catch (Exception $e) {
		return false;
	}
}




//////////////////
// Sickbeard (sb)
//////////////////

/**
 * Get version of Sick Beard
 * @return string Version
 */
function sb_version() {
	try {
		$url = sb_getURL() . '/?cmd=sb';
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->data->sb_version;
 	} catch (Exception $e) {
		return false;
	}
}

/**
 * Get sickbeard url
 * @return string url
 */
function sb_getURL() {
	global $wpdb;
	global $table_name;
	$ip = $wpdb->get_var($wpdb->prepare(
		"
		SELECT ip
		FROM $table_name
		WHERE name = %s
		", 
		'Sickbeard'
	));
	if (empty($ip))
		throw new Exception('Ip empty');

	$url = "http://" . $ip . "/api/" . getAPI('Sickbeard');
	return $url;
}

/**
 * Add TV show to sickbeard
 * @param  string $id TVDB id
 * @return bool     Success
 */
function sb_addShow($id) {
	try {
		$url = sb_getURL() . '/?cmd=show.addnew&tvdbid=' . imdb_to_tvdb($id);
		$json = curl_download($url);

		$data = json_decode($json);
		return ($data->result != 'failure');
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Get all TV shows in Sickbeard
 * @return array TV shows
 */
function sb_getShows() {
	try {
		$url = sb_getURL() . '/?cmd=shows';
		$json = curl_download($url);

		$data = json_decode($json);
		return $data->data;
	} catch (Exception $e) {
		return false;
	}
}

/**
 * Get a specific show info
 * @param  string $id IMDB id
 * @return array     TV show data
 */
function sb_getShow($id) {
	try {
		$url = sb_getURL() . '/?cmd=show&tvdbid=' . imdb_to_tvdb($id);
		$json = curl_download($url);

		$data = json_decode($json);
		return $data->data;
	} catch (Exception $e) {
		return false;
	}
}


/**
 * Check if series is in Sick Beard
 * @param  string $id IMDb id
 * @return bool     Success
 */
function sb_showAdded($id) {
	$res = (array) sb_getShows();
	return (in_array(imdb_to_tvdb($id), array_keys($res)) ? sb_getShow($id) : false);
}

function sb_getFuture() {
	try {
		$url = sb_getURL() . '/?cmd=future&sort=date';
		$json = curl_download($url);

		$data = json_decode($json);
		return $data->data;
	} catch (Exception $e) {
		return false;
	}
}




/////////////////
// SabNZBD (sab)
/////////////////

/**
 * Get version of SABnzbd+
 * @return string Version
 */
function sab_version(){
	try {
		$url = sab_getURL() . 'version';
		$json = curl_download($url);

		$data = json_decode($json);
	 	return $data->version;
 	} catch (Exception $e) {
		return false;
	}
}

/**
 * Get sabnzbd url
 * @return string url
 */
function sab_getURL() {
	global $wpdb;
	global $table_name;
	$ip = $wpdb->get_var($wpdb->prepare(
		"
		SELECT ip
		FROM $table_name
		WHERE name = %s
		", 
		'SabNZBD'
	));
	if (empty($ip))
		throw new Exception('No IP');

	$url = "http://" . $ip . "/api?apikey=" . getAPI('SabNZBD') . "&output=json&mode=";
	return $url;
}

/**
 * Get sabnzbd downloads
 * @return array downloads
 */
function sab_getCurrentDownloads() {
	try {
		$url = sab_getURL() . "qstatus";
		$json = curl_download($url);

		$data = json_decode($json);
		return $data->jobs;
	} catch (Exception $e) {
		return false;
	}
}




///////////////
// XBMC (xbmc)
///////////////

/**
 * Get XBMC url
 * @return string url
 */
function xbmc_getURL() {
	global $wpdb;
	global $table_name;
	$ip = $wpdb->get_var($wpdb->prepare(
		"
		SELECT ip
		FROM $table_name
		WHERE name = %s
		", 
		'XBMC'
	));
	if (empty($ip))
		throw new Exception('No IP');
	
	$url = "http://" . $ip;
	return $url;
}

/**
 * Get all XBMC movies
 * @return array all XBMC movies
 */
function xbmc_getMovies() {
	try {
		$xbmc = getLogin('XBMC');

		$json = "{\"jsonrpc\": \"2.0\", \"method\": \"VideoLibrary.GetMovies\", \"params\": { \"properties\" : [\"art\", \"rating\", \"playcount\", \"year\", \"imdbnumber\"], \"sort\": { \"order\": \"ascending\", \"method\": \"label\", \"ignorearticle\": true } }, \"id\": \"libMovies\"}";
		$json = urlencode($json);
		$url = xbmc_getURL() . "/jsonrpc?request=" . $json;

		$header = array(
			"Content-Type: application/json",
	        "Authorization: Basic " . base64_encode($xbmc->username . ":" . $xbmc->password)
	    ); 

		$result = curl_download($url, $header);
		$data = json_decode($result);
		return $data->result->movies;
	} catch (Exception $e) {
		return false;
	}

}

/**
 * Check if movie is in XBMC
 * @param  string $imdb_id IMDb movie ID
 * @return bool     Success
 */
function xbmc_movieOwned($imdb_id)
{
	$movies = xbmc_getMovies();
	if (empty($movies))
		return false;

	foreach($movies as $movie)
	{
		if ($movie->imdbnumber == $imdb_id)
		{
			return true;
		}
	}
	return false;
}


function xbmc_getShows() {
	try {
		$xbmc = getLogin('XBMC');

		$json = "{\"jsonrpc\": \"2.0\", \"method\": \"VideoLibrary.GetTVShows\", \"params\": { \"properties\" : [\"art\", \"rating\", \"playcount\", \"year\", \"imdbnumber\"], \"sort\": { \"order\": \"ascending\", \"method\": \"label\", \"ignorearticle\": true } }, \"id\": \"libShows\"}";
		$json = urlencode($json);
		$url = xbmc_getURL() . "/jsonrpc?request=" . $json;

		$header = array(
			"Content-Type: application/json",
	        "Authorization: Basic " . base64_encode($xbmc->username . ":" . $xbmc->password)
	    ); 

		$result = curl_download($url, $header);
		$data = json_decode($result);
		return $data->result->tvshows;
	} catch (Exception $e) {
		return false;
	}
}

function xbmc_showOwned($id)
{
	$shows = xbmc_getShows();
	if (empty($shows))
		return false;
	$showID = imdb_to_tvdb($id);
	foreach($shows as $show)
	{
		if ($show->imdbnumber == $showID)
		{
			return true;
		}
	}
	return false;
}

/**
 * Send a notification to XBMC
 * @param  string $title title
 * @param  string $message message
 * @return bool     Success
 */
function xbmc_sendNotification($title, $message)
{
	$xbmc = getLogin('XBMC');

	$json = "{\"jsonrpc\": \"2.0\", \"method\": \"GUI.ShowNotification\", \"params\": {\"title\" : \"".$title."\", \"message\" : \"".$message."\" }, \"id\": \"1\"}";
	$json = urlencode($json);
	$url = xbmc_getURL() . "/jsonrpc?request=" . $json;

	$header = array(
		"Content-Type: application/json",
        "Authorization: Basic " . base64_encode($xbmc->username . ":" . $xbmc->password)
    ); 

	$result = curl_download($url, $header);
	$data = json_decode($result);

	return ($data->result == "OK");
}


/**
 * Check if movie is in XBMC and not in Sick Beard
 * @param  string $imdb_id IMDb movie ID
 * @return bool     Success
 */
function xbmc_not_sb($imdb_id)
{
	return (xbmc_movieOwned($imdb_id) && !sb_showAdded($imdb_id));
}




/**
 * Converts IMDb ID to TVDB ID
 * @param  string $imdb_id IMDb ID
 * @return string     TVDB ID
 */
function imdb_to_tvdb($imdb)
{
	$xml = simplexml_load_string(curl_download("http://thetvdb.com/api/GetSeriesByRemoteID.php?imdbid=".$imdb));
	return (string) $xml->Series->children()->seriesid;
}

?>