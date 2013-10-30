<?php
	class couchpotato extends couchbeard 
	{

		protected function setApp() 
		{
			$this->app = 'couchpotato';
			$this->api = getAPI($this->app);
		}

		public function __construct() {
			parent::__construct();
		}

		/**
		 * Get version of Couchpotato
		 * @return string Version
		 */
		public function version()
		{
	        $url = $this->getURL() . '/app.version';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->version;
		}

		/**
		 * Get connection status to Couchpotato
		 * @return bool Connection status
		 */
		public function available()
		{		        
			$url = $this->getURL() . '/app.available';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->success;
		}

		/**
		 * Add movie to Couchpotato
		 * @param  string $id IMDB movie id
		 * @return bool     Adding status
		 */
		public function addMovie($id)
		{
	        $url = $this->getURL() . '/movie.add/?identifier=' . $id;
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return 1;
	        return $data->added;
		}

		/**
		 * Remove movie from wanted list in Couchpotato
		 * @param  int $id Couchpotato id
		 * @return bool     Success
		 */
		public function removeMovie($id)
		{
	        $url = $this->getURL() . '/movie.delete/?id=' . $id . '&delete_from=wanted';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->success;
		}

		/**
		 * Get all wanted movies in Couchpotato
		 * @return array Movies
		 */
		public function getMovies()
		{
	        $url = $this->getURL() . '/movie.list/?status=active';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->movies;
		}

		/**
		 * Refresh a movie in Couchpotato
		 * @param  int $id Couchpotato id
		 * @return bool     Success
		 */
		public function refreshMovie($id)
		{
	        $url = $this->getURL() . '/movie.list/?id=' . $id;
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->success;
		}

		/**
		 * Looking for updates to Couchpotato
		 * @return bool update available
		 */
		public function update()
		{
	        $url = $this->getURL() . '/updater.check';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->update_available;
		}

		/**
		 * Looking for a specific movie in CouchPotato
		 * @param  int $imdb_id IMDb movie ID
		 * @return bool movie found in CouchPotato
		 */
		public function movieWanted($imdb_id)
		{
	        $url = $this->getURL() . '/movie.get/?id=' . $imdb_id;
	        $json = curl_download($url);
	        if (!$json)
	            return false;

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
		}
	}
?>