<?php
	class xbmc extends couchbeard 
	{

		protected function setApp() 
		{
			$this->app = 'xbmc';
			$this->login = getLogin($this->app);
		}

		public function __construct() 
		{
			parent::__construct();
		}

		/**
		 * Returns xbmc $this->api 
		 * @param array $json data
		 */
		private function API($json)
		{
		    if (empty($json))
		    {
		        return false;
		    }
	        $xbmc = $this->getLogin('XBMC');
	        $json = urlencode($json);
	        $url = $this->getURL() . '/jsonrpc?request=' . $json;

	        $header = array(
	            'Content-Type: application/json',
	            'Authorization: Basic ' . base64_encode(($xbmc->password ? $xbmc->username. ':' . $xbmc->password : $xbmc->username))
	        );

	        $result = curl_download($url, $header);

	        return $result;
		}

		/**
		 * Get all XBMC movies
		 * @return array all XBMC movies
		 */
		public function getMovies($start = '', $end = '')
		{
	        if (empty($start) && empty($end))
	            $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params": { "properties" : ["art", "thumbnail", "rating", "playcount", "year", "imdbnumber"], "sort": { "order": "ascending", "method": "sorttitle", "ignorearticle": true } }, "id": "libMovies"}';
	        else
	            $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params": { "limits": { "start" : ' . intval($start) . ', "end" : ' . intval($start + $end) . ' }, "properties" : ["art", "thumbnail", "rating", "playcount", "year", "imdbnumber"], "sort": { "order": "ascending", "method": "sorttitle", "ignorearticle": true} }, "id": "libMovies"}';

	        $data = json_decode($this->API($json));

	        return $data->result->movies;
		}

		/**
		 * Check if movie is in XBMC
		 * @param  string $imdb_id IMDb movie ID
		 * @return bool     Success
		 */
		public function movieOwned($imdb_id)
		{
		    $movies = $this->getMovies();
		    if (empty($movies))
		        return false;

		    foreach ($movies as $movie)
		    {
		        if ($movie->imdbnumber == $imdb_id)
		        {
		            return true;
		        }
		    }
		    return false;
		}

		/**
		 * Return all tv-shows in xbmc
		 * @param  string $start start index
		 * @param  string $end   end limit
		 * @return array        result
		 */
		public function getShows($start = '', $end = '')
		{
	        if (empty($start) || empty($end))
	            $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows", "params": { "properties" : ["art", "thumbnail", "rating", "playcount", "year", "imdbnumber"], "sort": { "order": "ascending", "method": "label", "ignorearticle": true } }, "id": "libTvShows"}';
	        else
	            $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows", "params": { "limits": { "start" : ' . intval($start) . ', "end" : ' . intval($start + $end) . ' }, "properties" : ["art", "thumbnail", "rating", "playcount", "year", "imdbnumber"], "sort": { "order": "ascending", "method": "label", "ignorearticle": true} }, "id": "libTvShows"}';
	        
	        $data = json_decode($this->API($json));
	        return $data->result->tvshows;
		}

		/**
		 * Checks if show is already in xbmc
		 * @param  string $id show id
		 * @return boolean     is show in xbmc
		 */
		public function showOwned($id)
		{
		    $shows = getShows();
		    if (empty($shows))
		        return false;
		    $showID = $id;
		    foreach ($shows as $show)
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
		public function sendNotification($title, $message)
		{
		    $json = '{"jsonrpc": "2.0", "method": "GUI.ShowNotification", "params": {"title" : "' . $title . '", "message" : "' . $message . '" }, "id": "1"}';
		    $data = json_decode($this->API($json));
		    return ($data->result == 'OK');
		}

		/**
		 * Check if tv show is in XBMC and not in Sick Beard
		 * @param  string $imdb_id TVDB movie ID
		 * @return bool     Success
		 */
		public function not_sb($tvdb_id)
		{
		    return (showOwned($tvdb_id) && !sb_showAdded($tvdb_id));
		}

		/**
		 * Return what xbmc is currently playing
		 * @return array movie array
		 */
		public function getCurrentPlaying() {
		    $json = '{"jsonrpc": "2.0", "method": "Player.GetActivePlayers", "id": 1}';
		    $data = json_decode($this->API($json));
		    if (empty($data))
		    {
		        return false;
		    }
		    if ($data->result[0]->type == 'video')
		    {
		        return getCurrentMoviePlaying();
		    }
		    else if ($data->result[0]->type == 'audio')
		    {
		        return getCurrentAudioPlaying();
		    }
		    return false;
		}

		public function isPlaying() {
		    return (getCurrentPlaying() != false);
		}


		public function getCurrentMoviePlaying()
		{
		    $json = '{"jsonrpc": "2.0", "method": "Player.GetItem", "params": { "properties": ["title", "album", "artist", "season", "episode", "duration", "showtitle", "tvshowid", "thumbnail", "file", "fanart", "streamdetails"], "playerid": 1 }, "id": "VideoGetItem"}';
		    $data = json_decode($this->API($json));
		    return $data->result->item;
		}

		public function getCurrentAudioPlaying()
		{
		    $json = '{"jsonrpc": "2.0", "method": "Player.GetItem", "params": { "properties": ["title", "album", "artist", "duration", "thumbnail", "file", "fanart", "streamdetails"], "playerid": 0 }, "id": "AudioGetItem"}';
		    $data = $this->API($json);
		    return $data->result->item;
		}


		public function getRecentlyAddedMovies()
		{
		    $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetRecentlyAddedMovies", "params": { "properties" : ["thumbnail", "year", "imdbnumber"], "sort": { "order": "descending", "method": "dateadded" } }, "id": "libMovies"}';
		    $data = $this->API($json);
		    return $data;
		}

		public function getRecentlyAddedEpisodes()
		{
		    $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetRecentlyAddedEpisodes", "params": { "properties" : ["thumbnail", "showtitle", "season", "episode", "title"], "sort": { "order": "descending", "method": "dateadded" } }, "id": "libMovies"}';
		    $data = $this->API($json);
		    return $data;
		}


		public function getMovieDetails($movieid)
		{
		    $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovieDetails", "params": { "properties": ["title", "genre", "year", "rating", "plot", "runtime", "imdbnumber", "thumbnail", "art"], "movieid": ' . $movieid . ' }, "id": "VideoGetItem"}';
		    $data = json_decode($this->API($json))->result->moviedetails;
		    return $data;
		}

		public function play($libraryid)
		{
		    $json = '{"jsonrpc": "2.0", "method": "Player.Open", "params": { "item": { "movieid": '.$libraryid.' } }, "id": 1}';
		    $data = $this->API($json);
		    return $data;
		}

		public function playPause($player) // 1 = video, 0 = audio
		{
		    $json = '{"jsonrpc": "2.0", "method": "Player.PlayPause", "params": { "playerid": ' .$player. ' }, "id": "PlayPause"}';
		    $data = $this->API($json);
		    return $data;
		}

		public function getGenres($type) //type = movie || tvshow || musicvideo
		{
		    $json = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetGenres", "params": {"type" : "'.$type.'", "sort": { "order": "ascending", "method": "label" } }, "id": "1"}';
		    $data = $this->API($json);
		    return json_decode($data)->result->genres;
		}

		public function getPlayerProperties($player = 1) // 1 = video, 0 = audio
		{
		    $json = '{"jsonrpc": "2.0", "method": "Player.GetProperties", "params": { "properties": [ "time", "percentage", "totaltime" ], "playerid": ' .$player. ' }, "id": "PlayerProperties"}';
		    $data = $this->API($json);
		    return $data;
		}


		public function inputAction($action)
		{
		    $json = '{"jsonrpc": "2.0", "method": "Input.ExecuteAction", "params" : {"action" : "'.$action.'"}, "id": 1}';
		    $data = $this->API($json);
		    return $data;
		}

		public function ejectDrive($action)
		{
		    $json = '{"jsonrpc": "2.0", "method": "System.EjectOpticalDrive", "id": 1}';
		    $data = $this->API($json);
		    return $data;
		}
	}
?>