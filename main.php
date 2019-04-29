<?php
	require(__DIR__ . '/Calend.php');
	require(__DIR__ . '/Vevent.php');
	
	$calend = new Calend($cfg->username, $cfg->password);
	$vevent = null;
	
	$ics = $calend->get();
	if (is_string($ics))
	{
		$ics = strtr($ics, ["\n " => '']);
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
	}
	else
		new Mail('[calend.ru ERROR] ', "Cann't retreive iCalendar\n");
