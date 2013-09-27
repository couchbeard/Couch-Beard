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
		    try
		    {
		        $url = $this->getURL() . '/?cmd=sb';
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->data->version;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}

		/**
		 * Add TV show to sickbeard
		 * @param  string $id TVDB id
		 * @return bool     Success
		 */
		public function addShow($id)
		{
		    try
		    {
		        $url = $this->getURL() . '/?cmd=show.addnew&tvdbid=' . imdb_to_tvdb($id);
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return ($data->result != 'failure');
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}

		/**
		 * Get all TV shows in Sickbeard
		 * @return array TV shows
		 */
		public function getShows()
		{
		    try
		    {
		        $url = $this->getURL() . '/?cmd=shows';
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->data;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}

		/**
		 * Get a specific show info
		 * @param  string $id TVDB id
		 * @return array     TV show data
		 */
		public function getShow($id)
		{
		    try
		    {
		        $url = $this->getURL() . '/?cmd=show&tvdbid=' . $id;
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->data;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}

		/**
		 * Check if series is in Sick Beard
		 * @param  string $id TVDB id
		 * @return bool     Success
		 */
		public function showAdded($id)
		{
		    $res = (array) getShows();
		    return (in_array(imdb_to_tvdb($id), array_keys($res)) ? getShow(imdb_to_tvdb($id)) : false);
		}

		public function getFuture()
		{
		    try
		    {
		        $url = $this->getURL() . '/?cmd=future&sort=date';
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->data;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}
	}

?>