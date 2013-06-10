<?php
/*
	****					Modification History								****
	********************************************************************************
	********************************************************************************
	****		Name: 					Haseeb Ahmed							****
	****		Date: 					November 03,2012						****	
	****		Description:			Send SMS to users						****	
	********************************************************************************
	********************************************************************************
*/
require_once('apis/sendsmsdotpk.php');
require_once('libs/log4php/Logger.php');
Logger::configure('logs/config.xml');

class SMSCentre
{
	private $log;
	private $apiKey;
	private $SMSAPI;

	public function SMSCentre($APIKey)
	{
		$this->apiKey = $APIKey;
		$this->SMSAPI = new sendsmsdotpk($this->apiKey);
		$this->log = Logger::getLogger(__CLASS__);
	}
	public function Send(array $recipients, $message)
	{				
		if($this->SMSAPI->isValid())
		{
			foreach($recipients as $recp)
			{
				if($this->SMSAPI->sendsms($recp, $message, 0))
				{
					// Message sent
					$this->log->info("Message sent to " . $recp);
				}	
				else
				{
					//Message not sent
					$this->log->warn("Message not sent to " . $recp);
				}
			}			
		}
		else
		{
			//Invalid API
			$this->log->error("Invalid API Key '" . $this->apiKey . "'");
		}
	}	
}
?>