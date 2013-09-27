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
		    try
		    {
		        $url = $this->getURL() . 'version';
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->version;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}

		/**
		 * Get sabnzbd downloads
		 * @return array downloads
		 */
		public function getCurrentDownloads()
		{
		    try
		    {
		        $url = $this->getURL() . 'qstatus';
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->jobs;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}

		public function getHistory($start = 0, $limit = 5)
		{
		    try
		    {
		        $url = $this->getURL() . 'history&start=' . $start . '&limit=' . $limit;
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->history->slots;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}

		public function getQueue() 
		{
		    try
		    {
		        $url = $this->getURL() . 'qstatus';
		        $json = curl_download($url);
		        if (!$json)
		            return false;

		        $data = json_decode($json);
		        return $data->jobs;
		    }
		    catch (Exception $e)
		    {
		        return false;
		    }
		}
	}
?>