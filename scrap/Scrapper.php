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
require_once('/../libs/simpletest/browser.php');
Abstract class Scrapper
{
	protected $browser;
	protected $url;
	protected $parsedData;
	protected $rawData;
	
	public function Scrapper()
	{
		
	}
	public function GetParsedData()
	{
		
	}
	public function Fetch()	{}
	public function Parse()	{}

}
?>