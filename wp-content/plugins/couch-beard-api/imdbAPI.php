<?php

	/**
	 * Currently using omdbapi
	 */
	class imdbAPI 
	{
		private $id;
		private $url;
		private $data;

		public function __construct($id) 
		{
			$this->id = $id;
			$this->url = "http://www.omdbapi.com/?i=" . $id . "&plot=full";
			$this->data = json_decode(curl_download($this->url));
		}

		public function getMovieTitle() 
		{
			return $this->data->Title;
		}

		public function getData() 
		{
			return $this->data;
		}

	}
?>