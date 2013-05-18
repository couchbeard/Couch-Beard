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

function couchbeardapi_activate() {
	global $wpdb;
	global $table_name;

	$apis = array("Couchpotato", "Sickbeard", "SabNZBD");
    if ($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name) 
    {
        $sql = "CREATE TABLE " . $table_name . "(
              ID INT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
              name VARCHAR(45) NOT NULL UNIQUE ,
              api VARCHAR(100) NULL ,
              ip VARCHAR(100) NULL ,
              PRIMARY KEY (ID) )
            ENGINE = InnoDB;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta( $sql );

        add_option('api_database_version', '1.0');
    }

    foreach ($apis as $a) {
    	$wpdb->insert( $table_name, array('name' => $a));
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
?>
	<div class="wrap">
	<h2>Manage Couch Beard APIs</h2>
	</div>
	<form action="" method="POST">
		<table class="widefat">
			<thead>
			<tr>
				<th> Application </th>
				<th> API key </th>
				<th> IP (:Port) </th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th> Application </th>
				<th> API key </th>
				<th> IP (:Port) </th>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$adb = $wpdb->get_results(
				"
				SELECT *
				FROM $table_name
				"
			);
			?>
			<?php
			foreach ($adb as $a)
			{
			?>
				<tr>
					<td> <?php echo $a->name; ?> </td>
					<td> <input type="text" name="<?php echo $a->name; ?>api" value="<?php echo $a->api; ?>"> </td>
					<td> <input type="text" name="<?php echo $a->name; ?>ip" value="<?php echo $a->ip; ?>"> </td>
				</tr>
			<?php 
			}
			?>
			</tbody>
		</table>
		<input type="submit" name="apisave" value="Save" class="button-primary" style="float: right;" />
	</form>
<?php
	if (isset($_POST['apisave'])) 
	{
		foreach ($adb as $a) {
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
				echo 'FAIL <br />';
			}
		}
	}

} // End function

// Get API
function getAPI($name) {
	global $wpdb;
	global $table_name;
	return $wpdb->get_var($wpdb->prepare(
		"
		SELECT api
		FROM $table_name
		WHERE name = %d
		", 
		$name
	));
}

// Get URL with API
function cp_getURL($name = 'Couchpotato') {
	global $wpdb;
	global $table_name;
	$ip = $wpdb->get_var($wpdb->prepare(
		"
		SELECT ip
		FROM $table_name
		WHERE name = %d
		", 
		$name
	));
	$url = "http://" . $ip . "/api/" . getAPI($name);
	return $url;
}

// Couchpotato

/**
 * Get version of Couchpotato
 * @return string Version
 */
function cp_version(){
	$url = cp_getURL('Couchpotato') . '/app.version';
	$json = file_get_contents($url);
	$data = json_decode($json);
 	return $data->version;
}
add_action( 'thesis_hook', 'cp_version');

/**
 * Get connection status to Couchpotato
 * @return bool Connection status
 */
function cp_available(){
	$url = cp_getURL('Couchpotato') . '/app.available';
	$json = file_get_contents($url);
	$data = json_decode($json);
 	return $data->success;
}
add_action( 'thesis_hook', 'cp_available');

/**
 * Add movie to Couchpotato
 * @param  string $id IMDB movie id
 * @return bool     Adding status
 */
function cp_addMovie($id){
	$url = cp_getURL('Couchpotato') . '/movie.add/?identifier=' . $id;
	$json = file_get_contents($url);
	$data = json_decode($json);
 	return $data->added;
}
add_action( 'thesis_hook', 'cp_addMovie');

/**
 * Remove movie from wanted list in Couchpotato
 * @param  int $id Couchpotato id
 * @return bool     Success
 */
function cp_removeMovie($id){
	$url = cp_getURL('Couchpotato') . '/movie.delete?id=' . $id . '&delete_from=wanted';
	$json = file_get_contents($url);
	$data = json_decode($json);
 	return $data->success;
}
add_action( 'thesis_hook', 'cp_removeMovie');

/**
 * Get all wanted movies in Couchpotato
 * @return array Movies
 */
function cp_getMovies(){
	$url = cp_getURL('Couchpotato') . '/movie.list?status=active';
	$json = file_get_contents($url);
	$data = json_decode($json);
 	return $data;
}
add_action( 'thesis_hook', 'cp_getMovies');

/**
 * Refresh a movie in Couchpotato
 * @param  int $id Couchpotato id
 * @return bool     Success
 */
function cp_refreshMovie($id){
	$url = cp_getURL('Couchpotato') . '/movie.list?id=' . $id;
	$json = file_get_contents($url);
	$data = json_decode($json);
 	return $data->success;
}
add_action( 'thesis_hook', 'cp_refreshMovie');

/**
 * Looking for updates to Couchpotato
 * @return bool update available
 */
function cp_update() {
	$url = cp_getURL('Couchpotato') . '/updater.check';
	$json = file_get_contents($url);
	$data = json_decode($json);
 	return $data->update_available;
}
add_action( 'thesis_hook', 'cp_update');

// Sickbeard (sb)
function sb_getURL($name = 'Sickbeard') {
	global $wpdb;
	global $table_name;
	$ip = $wpdb->get_var($wpdb->prepare(
		"
		SELECT ip
		FROM $table_name
		WHERE name = %d
		", 
		$name
	));
	$url = "http://" . $ip . "/api/" . getAPI($name);
	return $url;
}

function sb_addShow($id) {
	$url = sb_getURL() . '/?cmd_show.addnew&tvdbid=' . $id;
}

// SabNZBD (sab)

?>