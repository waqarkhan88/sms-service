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

require_once('/../Scrapper.php');
class ForexScrapper extends Scrapper
{	
	protected $dollarRate;
	public function ForexScrapper()
	{
		$this->browser = new SimpleBrowser();
		$this->browser->addHeader('User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
		$this->parsedData = null;
	}	
	public function GetParsedData()
	{
		if($this->dollarRate != null)
		{
			$this->parsedData = array("Dollar" => $this->dollarRate);
			return $this->parsedData;
		}
		else 
		{
			return null;
		}
	}
}
?>