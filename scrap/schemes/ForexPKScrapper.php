<?php
/*
	****					Modification History								****
	********************************************************************************
	********************************************************************************
	****		Name: 					Haseeb Ahmed							****
	****		Date: 					November 06,2012						****	
	****		Description:			Fetch data from internet				****	
	********************************************************************************
	********************************************************************************
*/
require_once('ForexScrapper.php');
require_once('/../../libs/log4php/Logger.php');
Logger::configure('logs/config.xml');

class ForexPKScrapper extends ForexScrapper
{	
	private $log;
	public function ForexPKScrapper()
	{
		parent::__construct();
		$log = Logger::getLogger('Scrappers');
		$this->browser->addHeader('Referer: http://www.forex.pk/currency-converter.php');		
		$this->url='http://www.forex.pk/currency-converter.php';
		$this->Fetch();
	}
	public function Parse()
	{
		preg_match_all("|1.00[\s]+USD[\s]+=[\s]+([0-9]+\.[0-9]+)[\s]+PKR|is",$this->rawData,$matches);
		if(isset($matches[1][0]))
		{
			$this->dollarRate = $matches[1][0];
		}
		else
		{
			$log->warn(__CLASS__ . ' - Unable to scrap rates.');
			$this->dollarRate = null;
		}
	}
	public function GetParsedData()
	{
		$this->Parse();
		return parent::GetParsedData();
		
		
	}
	public function Fetch()
	{
		$this->browser->post($this->url, 
			array(
				'send' => 'Y',
				'type' => 'OPEN',
				'Amount' => '1',
				'currid1' => '66', //pakistani rupee code
				'currid2' => '23', // us dollar code
				'cache' => date("Ymdhis"),
				'day' => date("d"),
				'month' => date("m"),
				'year' => date("Y")
				));
		$this->rawData = $this->browser->getContentAsText();
	}
	
}
?>