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
require_once('MetalScrapper.php');
require_once('/../../libs/log4php/Logger.php');
Logger::configure('logs/config.xml');

class KitcoScrapper extends MetalScrapper
{	
	private $log;
	public function KitcoScrapper()
	{
		parent::__construct();
		$log = Logger::getLogger('Scrappers');
		$this->url='http://www.kitco.com/market/';		
		$this->Fetch();
	}
	public function Parse()
	{
		$rates = null;
		preg_match("/SPOT[\s]+MARKET[\s]+IS[\s]+OPEN/", $this->rawData, $matches);
		if(count($matches ) > 0)
		{
			$rates = $this->GetKitcoRates(true);			
		}
		else
		{
			preg_match("/MARKET[\s]+IS[\s]+OPEN/", $this->rawData, $matches);
			if(count($matches) > 0)
			{
				$rates = $this->GetKitcoRates(false);
			}
		}
		if($rates != null && $rates['Gold'] != null && $rates['Silver'] != null)
		{
			$this->goldRate = $rates['Gold'];
			$this->silverRate = $rates['Silver'];
		}
		else
		{
			$log->warn(__CLASS__ . ' - Unable to scrap rates.');
			$this->goldRate = null;
			$this->silverRate = null;
		}		
	}
	public function GetParsedData()
	{
		$this->Parse();
		return parent::GetParsedData();
	}
	public function Fetch()
	{
		$this->browser->get($this->url);
		$this->rawData = $this->browser->getContentAsText();
	}
	private function GetKitcoRates($international)
	{
		preg_match_all("/Gold.{1,3}Charts.{1,3}GOLD[\s]+([0-9]+\/[0-9]+\/[0-9]+)[\s]+([0-9]+:[0-9]+)[\s]+([0-9]+\.[0-9]+)[\s]+([0-9]+\.[0-9]+)/is",$this->rawData,$matches);
		// $nyGoldDate=$matches[1][0];
		// $nyGoldTime=$matches[2][0];
		// $nyGoldBid=$matches[3][0];
		// $intGoldDate=$matches[1][1];
		// $intGoldTime=$matches[2][1];
		//$intGoldBid=$matches[3][1];
		$nyGoldAsk = isset($matches[4][0]) ? $matches[4][0] : null;
		$intGoldAsk = isset($matches[4][1]) ? $matches[4][1] : null;
		preg_match_all("/Silver.{1,3}Charts.{1,3}SILVER[\s]+([0-9]+\/[0-9]+\/[0-9]+)[\s]+([0-9]+:[0-9]+)[\s]+([0-9]+\.[0-9]+)[\s]+([0-9]+\.[0-9]+)/is",$this->rawData,$matches);
		// $nySilverDate=$matches[1][0];
		// $nySilverTime=$matches[2][0];
		// $nySilverBid=$matches[3][0];		
		// $intSilverDate=$matches[1][1];
		// $intSilverTime=$matches[2][1];
		// $intSilverBid=$matches[3][1];
		$intSilverAsk = isset($matches[4][1]) ? $matches[4][1] : null;
		$nySilverAsk = isset($matches[4][0]) ? $matches[4][0] : null;

		if($international)
		{
			$result['Gold'] = $intGoldAsk ? $intGoldAsk : null;
			$result['Silver'] = $intSilverAsk ? $intSilverAsk : null;			
			return $result;
			
		}
		else
		{
			$result['Gold'] = $nyGoldAsk ? $nyGoldAsk : null;
			$result['Silver'] = $nytSilverAsk ? $nytSilverAsk : null;
			return $result;
		}
	}
	
}
?>