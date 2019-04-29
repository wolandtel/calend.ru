<?php
	require(__DIR__ . '/Mail.php');
	
	class Vevent
	{
		private $uid, $date, $title;
		
		public function end ($str)
		{
			if (strcmp($str, 'END:VEVENT') != 0)
				return false;
			
			if ($this->uid && $this->date && $this->title)
				$this->save();
			
			return true;
		}
		
		public function uid ($str)
		{
			if (strncmp($str, 'COMMENT:', 8) != 0)
				return false;
			
			$this->uid = str_replace('https://www.calend.ru', '', substr($str, 8));
			return true;
		}
		
		public function date ($str)
		{
			if (strncmp($str, 'DTSTART;VALUE=DATE:', 19) != 0)
				return false;
			
			$this->date = substr($str, 19);
			return true;
		}
		
		public function title ($str)
		{
			if (strncmp($str, 'SUMMARY:', 8) != 0)
				return false;
			
			$this->title = substr($str, 8);
			return true;
		}
		
		private function save ()
		{
			if (strcmp($this->date, date('Ymd')) === 0)
				new Mail('[calend.ru date] ' . $this->title, date('d.m') . ': ' . $this->title);
		}
	}
