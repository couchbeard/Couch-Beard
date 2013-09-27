<?php
/**
 * Abstract class for remaining classes.
 */
abstract class couchbeard 
{
	
	protected $app;
	abstract protected function setApp();

	protected $url;
	protected $login;
	protected $api;

	public function __construct() 
	{
		$this->setApp();
		if (!$this->isAlive())
			throw new Exception($this->app . " is not alive.");

		$this->url = getURL($this->app);
	}

	protected function getURL()
	{
	    return $this->url;
	}

	protected function getLogin()
	{
		return $this->login;
	}

	protected function getAPI()
	{
		return $this->api;
	}


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

	function isAnyAlive() 
	{
	    global $wpdb;
	    global $table_name;
	    $app = $wpdb->get_col($wpdb->prepare(
	        "
	        SELECT name
	        FROM $table_name
	        "
	    ));
	    $notAlive = array();
	    foreach ($app as $a) {
	        if (!isAlive($a))
	            array_push($notAlive, $a);
	    }
	    return $notAlive;
	}

	function getAPI($app)
	{
		global $wpdb;
    	global $table_name;
	    $api = $wpdb->get_var($wpdb->prepare(
	        "
			SELECT api
			FROM $table_name
			WHERE name = %s
			", $app
	    ));
	    if (empty($api))
	        throw new Exception('No API');

	    return $api;
	}

	function getLogin($app)
	{
		global $wpdb;
    	global $table_name;
	    $user = $wpdb->get_row($wpdb->prepare(
	        "
			SELECT username, password
			FROM $table_name
			WHERE name = %s
			", $app
	    ));
	    if (empty($user->username))
	        throw new Exception('No user');

	    return $user;
	}

	function getURL($app)
	{
	    global $wpdb;
	    global $table_name;
	    $ip = $wpdb->get_var($wpdb->prepare(
	        "
			SELECT ip
			FROM $table_name
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