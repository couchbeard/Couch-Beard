<?php

	class sickbeard extends couchbeard 
	{

		protected function setApp() 
		{
			$this->app = 'sickbeard';
			$this->api = getAPI($this->app);
		}

		public function __construct() 
		{
			parent::__construct();
		}

		/**
		 * Get version of Sick Beard
		 * @return string Version
		 */
		public function version()
		{
	        $url = $this->getURL() . '/?cmd=sb';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->data->version;
		}

		/**
		 * Add TV show to sickbeard
		 * @param  string $id TVDB id
		 * @return bool     Success
		 */
		public function addShow($id)
		{
	        $url = $this->getURL() . '/?cmd=show.addnew&tvdbid=' . imdb_to_tvdb($id);
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return ($data->result != 'failure');
		}

		/**
		 * Get all TV shows in Sickbeard
		 * @return array TV shows
		 */
		public function getShows()
		{
	        $url = $this->getURL() . '/?cmd=shows';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->data;
		}

		/**
		 * Get a specific show info
		 * @param  string $id TVDB id
		 * @return array     TV show data
		 */
		public function getShow($id)
		{
	        $url = $this->getURL() . '/?cmd=show&tvdbid=' . $id;
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->data;
		}

		/**
		 * Check if series is in Sick Beard
		 * @param  string $id TVDB id
		 * @return bool     Success
		 */
		public function showAdded($id)
		{
		    $res = (array) $this->getShows();
		    return (in_array(imdb_to_tvdb($id), array_keys($res)) ? $this->getShow(imdb_to_tvdb($id)) : false);
		}

		/**
		 * Returns future starting shows
		 * @return array future shows
		 */
		public function getFuture()
		{
	        $url = $this->getURL() . '/?cmd=future&sort=date';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->data;
		}
	}

?>