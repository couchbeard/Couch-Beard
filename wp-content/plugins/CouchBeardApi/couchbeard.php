<?php
/**
 * Abstract class for remaining classes.
 */
abstract class couchbeard 
{
	
	/**
	 * To know which class it extends.
	 * @app string
	 */
	protected $app;
	abstract protected function setApp();

	protected $url; 	// url for application
	protected $login; 	// login for application (if necessary)
	protected $api;		// api for application (if necessary)

	/**
	 * Constructor which sets application name and url. Checks if application is online.
	 */
	public function __construct() 
	{
		$this->setApp();
		if (!$this->isAlive())
			throw new Exception($this->app . " is not alive.");

		$this->url = getURL($this->app);
	}

	/**
	 * Returns url for the application
	 * @return string url string
	 */
	protected function getURL()
	{
	    return $this->url;
	}

	/**
	 * Returns login for the application
	 * @return [string, string] String array with name and password
	 */
	protected function getLogin()
	{
		return $this->login;
	}

	/**
	 * Returns api for the application
	 * @return string api string
	 */
	protected function getAPI()
	{
		return $this->api;
	}

	/**
	 * Checks if the application is online
	 * @return boolean online
	 */
	public function isAlive() 
	{
		return isAlive($this->app);
	}

} // abstract class end



	/**
	 * Download website
	 * @param  string $url Download URL
	 * @return $json      Website
	 */
	function curl_download($url, $headers = null)
	{
	    // is cURL installed yet?
	    if (!function_exists('curl_init'))
	    {
	        die('Sorry cURL is not installed!');
	    }

	    $ch = curl_init();

	    // Now set some options (most are optional)
	    // Set URL to download
	    curl_setopt($ch, CURLOPT_URL, $url);

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

	    $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    
	    if ($retcode >= 400)
	        return false;

	    // Close the cURL resource, and free system resources
	    curl_close($ch);

	    return $output;
	}

	/**
	 * Return all application names in database
	 * @return array string array
	 */
	function getAllApps() {
		global $wpdb;
	    $app = $wpdb->get_col($wpdb->prepare(
	        "
	        SELECT name
	        FROM " . CouchBeardApi::$table_name
	    ));

	    return $app;
	}

	/**
	 * Checks every applications in the database if they are online
	 * @return boolean online
	 */
	function isAnyAlive() 
	{
	    $app = getAllApps();
	    $notAlive = array();
	    foreach ($app as $a) {
	        if (!isAlive($a))
	            array_push($notAlive, $a);
	    }
	    return $notAlive;
	}

	/**
	 * Returns api for specific application
	 * @param  string $app application input
	 * @return string      api
	 */
	function getAPI($app)
	{
		global $wpdb;
	    $api = $wpdb->get_var($wpdb->prepare(
	        "
			SELECT api
			FROM " . CouchBeardApi::$table_name . "
			WHERE name = %s
			", $app
	    ));
	    if (empty($api))
	        throw new Exception('No API');

	    return $api;
	}

	/**
	 * Returns login for specific application
	 * @param  string $app application input
	 * @return [string,string]      login
	 */
	function getLogin($app)
	{
		global $wpdb;
	    $user = $wpdb->get_row($wpdb->prepare(
	        "
			SELECT username, password
			FROM " . CouchBeardApi::$table_name . "
			WHERE name = %s
			", $app
	    ));
	    if (empty($user->username))
	        throw new Exception('No user');

	    return $user;
	}

	/**
	 * Returns url for specific application
	 * @param  string $app application input
	 * @return string      url
	 */
	function getURL($app)
	{
	    global $wpdb;
	    $ip = $wpdb->get_var($wpdb->prepare(
	        "
			SELECT ip
			FROM " . CouchBeardApi::$table_name . "
			WHERE name = %s
			", $app
	    ));
	    if (empty($ip))
	        throw new Exception('No IP');

	    if ($app == 'xbmc')
	    	return 'http://' . $ip;
	    else if ($app == 'sabnzbd')
	    	return 'http://' . $ip . '/api?apikey=' . getAPI($app) . '&output=json&mode=';

	    return 'http://' . $ip . '/api/' . getAPI($app);
	}

	/**
	 * Returns online status for specific application
	 * @param  string  $app application input
	 * @return boolean      online status
	 */
	function isAlive($app) 
	{
	    $header = '';
	    switch(strtolower($app))
	    {
	        case 'couchpotato':
	        case 'cp':
	            $url = getURL($app) . '/app.available';
	            break;
	        case 'sickbeard':
	        case 'sb':
	            $url = getURL($app);
	            break;
	        case 'sabnzbd':
	        case 'sab':
	            $url = getURL($app);
	            break;
	        case 'xbmc':
	            $url = getURL($app);
	            $xbmc = getLogin($app);
	            $header = array(
	                'Content-Type: application/json',
	                'Authorization: Basic ' . base64_encode($xbmc->username . ':' . $xbmc->password)
	            );
	            break;
	        default:
	            return false;
	    }
	    
	    if (!(curl_download($url, $header)))
	        return false;

	    return true;
	}

	/**
	 * Converts IMDb ID to TVDB ID
	 * @param  string $imdb_id IMDb ID
	 * @return string     TVDB ID
	 */
	function imdb_to_tvdb($imdb)
	{
	    $xml = simplexml_load_string(curl_download('http://thetvdb.com/api/GetSeriesByRemoteID.php?imdbid=' . $imdb));
	    return (string) $xml->Series->children()->seriesid;
	}

	/**
	 * Converts TVDB ID to IMDb ID
	 * @param  string $name name of show
	 * @return string     IMDb ID
	 */
	function tvdb_to_imdb($name)
	{
	    $xml = simplexml_load_string(file_get_contents('http://thetvdb.com/api/GetSeries.php?seriesname=' . urlencode($name)));
	    return (string) $xml->Series->children()->IMDB_ID;
	}
?>