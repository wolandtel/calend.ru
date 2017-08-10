<?php
	class Calend
	{
		private $ch, $user, $password;
		
		public function __construct ($user, $password)
		{
			$this->user = $user;
			$this->password = $password;
			
			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 15);
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');
		}
		
		public function __destruct ()
		{
			curl_close($this->ch);
		}
		
		public function get ()
		{
			if (!$this->login())
				return false;
			return $this->ics();
		}
		
		private function ics ()
		{
			$exp_type = 'text/calendar';
			
			curl_setopt($this->ch, CURLOPT_HTTPGET, true);
			curl_setopt($this->ch, CURLOPT_URL, "http://www.calend.ru/user/$this->user?ics=1");
			
			$response = curl_exec($this->ch);
			
			$code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
			$type = curl_getinfo($this->ch, CURLINFO_CONTENT_TYPE);
			
			if ($code != 200)
				return false;
			
			if (strncmp($type, $exp_type, strlen($exp_type)))
				return false;
			
			return $response;
		}
		
		private function login ()
		{
			curl_setopt($this->ch, CURLOPT_POST, true);
			curl_setopt($this->ch, CURLOPT_URL, "http://www.calend.ru/login/");
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, "login=$this->user&password=$this->password&remember=1");
			
			curl_exec($this->ch);
			$code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
			
			if (!in_array($code, [200, 301, 302]))
				return false;
			
			foreach (curl_getinfo($this->ch, CURLINFO_COOKIELIST) as $cookie)
				if (strpos($cookie, 'password_hash'))
					return true;
			
			return false;
		}
	}
	
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
			
			$this->uid = str_replace('http://www.calend.ru/', '', substr($str, 8));
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
			echo "$this->date $this->uid\t$this->title\n";
		}
	}
	
	$calend = new Calend('wolandtel', 'warpDr1ve');
	$vevent = null;
	
	$ics = $calend->get();
	if (is_string($ics))
		foreach (explode("\n", $ics) as $str)
		{
			if (strcmp($str, 'BEGIN:VEVENT') === 0)
				$vevent = new Vevent;
			elseif ($vevent)
			{
				if ($vevent->end($str))
					$vevent = null;
				elseif ($vevent->uid($str));
				elseif ($vevent->date($str));
				else
					$vevent->title($str);
			}
		}
