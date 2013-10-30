<?php
	class sabnzbd extends couchbeard 
	{

		protected function setApp() 
		{
			$this->app = 'sabnzbd';
			$this->api = getAPI($this->app);
		}

		public function __construct() 
		{
			parent::__construct();
		}

		/**
		 * Get version of SABnzbd+
		 * @return string Version
		 */
		public function version()
		{
	        $url = $this->getURL() . 'version';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->version;
		}

		/**
		 * Get sabnzbd downloads
		 * @return array downloads
		 */
		public function getCurrentDownloads()
		{
	        $url = $this->getURL() . 'qstatus';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->jobs;
		}

		/**
		 * Returns sabnzbd history (old finished downloads)
		 * @param  integer $start start index
		 * @param  integer $limit end
		 * @return array         history
		 */
		public function getHistory($start = 0, $limit = 5)
		{
	        $url = $this->getURL() . 'history&start=' . $start . '&limit=' . $limit;
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->history->slots;
		}

		/**
		 * Returns sabnzbd download queue
		 * @return array download queue
		 */
		public function getQueue() 
		{
	        $url = $this->getURL() . 'qstatus';
	        $json = curl_download($url);
	        if (!$json)
	            return false;

	        $data = json_decode($json);
	        return $data->jobs;
		}
	}
?>