<?php
	$cfg = require(__DIR__ . '/config.php');
	
	class Mail
	{
		public function __construct ($subject, $content)
		{
			global $cfg;
			
			$fds = [
				['pipe', 'r'], // stdin
				['pipe', 'w'], // stdout
				['pipe', 'w'], // stderr
			];
			
			$pcs = proc_open('mail -r ' . escapeshellarg($cfg->frommail) .
									' -s ' . escapeshellarg($subject) .
									' ' . escapeshellarg($cfg->recipient),
								$fds,
								$pipes,
								null,
								null);
			if ($pcs)
			{
				fwrite($pipes[0], $content);
				foreach ($pipes as $pipe)
					fclose($pipe);
				$r = proc_close($pcs);
			}
		}
	}
