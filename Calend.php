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
			curl_setopt($this->ch, CURLOPT_URL, "https://www.calend.ru/user/$this->user?ics=1");
			
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
			curl_setopt($this->ch, CURLOPT_URL, "https://www.calend.ru/login/");
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
