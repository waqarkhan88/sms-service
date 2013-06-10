<?php
/*
	****					Modification History								****
	********************************************************************************
	********************************************************************************
	****		Name: 					Haseeb Ahmed							****
	****		Date: 					November 03,2012						****	
	****		Description:			Generate all types of SMS				****	
	********************************************************************************
	********************************************************************************
*/
require_once('libs/log4php/Logger.php');
Logger::configure('logs/config.xml');

class MessageGenerator
{
	private $rates;
	private $log;
	public function MessageGenerator($rates)
	{
		$this->rates = $rates;
	}
	
	public function GetMessage($messageType)
	{
		switch($messageType)
		{
			case 'G':
					return 'G=' . (isset($this->rates['Gold']) ? $this->rates['Gold'] : 'n/a');
			break;
			
			case 'S':
					return 'S=' . (isset($this->rates['Silver']) ? $this->rates['Silver'] : 'n/a');
			break;
			
			case 'D':
					return '$=' . (isset($this->rates['Dollar']) ? $this->rates['Dollar'] : 'n/a');
			break;
			
			case 'GS':
					return 'G=' . (isset($this->rates['Gold']) ? $this->rates['Gold'] : 'n/a') .'\n' .
							'S=' . (isset($this->rates['Silver']) ? $this->rates['Silver'] : 'n/a');
			break;
			
			case 'GSD':
					return 'G=' . (isset($this->rates['Gold']) ? $this->rates['Gold'] : 'n/a') .'\n' .
							'S=' . (isset($this->rates['Silver']) ? $this->rates['Silver'] : 'n/a') . '\n'.
							'$=' . (isset($this->rates['Dollar']) ? $this->rates['Dollar'] : 'n/a');
							
			break;
		}
	}
}
?>