<?php

	/**
	 * Currently using omdbapi
	 */
	class imdbAPI 
	{
		private $id;
		private $url;
		private $data;

		public function __construct($id = false) 
		{
			if (empty($id))
			{
				$this->id = $id;
				$this->getData($id);
			}
		}

		private function getData($id)
		{
			$this->url = "http://www.omdbapi.com/?i=" . $id . "&plot=full";
			$this->data = json_decode(curl_download($this->url));
			if ($this->data->Response == 'False' || isset($this->data->Error))
				throw new Exception('No movie found');
		}

		public function setID($id)
		{
			getData($id);
			$this->id = $id;
		}

		public function getID()
		{
			return $this->id;
		}

		public function title() 
		{
			return $this->data->Title;
		}

		public function imdbRating() 
		{
			return $this->data->imdbRating;
		}

		public function imdbVotes()
		{
			return $this->data->imdbVotes;
		}

		public function poster()
		{
			return $this->data->Poster;
		}

		public function genre()
		{
			return $this->data->Genre;
		}

		public function year()
		{
			return $this->data->Year;
		}

		public function country()
		{
			return $this->data->Country;
		}

		public function actors()
		{
			return $this->data->Actors;
		}

		public function writer()
		{
			return $this->data->Writer;
		}

		public function plot()
		{
			return $this->data->Plot;
		}

		public function runtime()
		{
			return $this->data->Runtime;
		}

		public function type()
		{
			return $this->data->Type;
		}

	}
?>