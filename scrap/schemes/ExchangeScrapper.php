<?php
require_once ('ForexScrapper.php');
require_once (dirname(__FILE__) . '/../../libs/log4php/Logger.php');
Logger::configure ( 'logs/config.xml' );
class ExchangeScrapper extends ForexScrapper {
	var $log;
	public function ExchangeScrapper() {
		parent::__construct ();
		$this->log = Logger::getLogger ( 'Scrappers' );
		$this->url = 'http://www.exchangerates.org.uk/usd-pkr-exchange-rate-history.html';
		$this->fetch ();
	}
	public function parse() {
		preg_match_all ( "|highest:[\s]+([0-9]+\.[0-9]+)|is", $this->rawData, $matches );
		if (isset ( $matches [1] [0] )) {
			$this->dollarRate = $matches [1] [0];
		} else {
			$this->log->warn ( __CLASS__ . ' - Unable to scrap rates.' );
			$this->dollarRate = null;
		}
	}
	public function getParsedData() {
		$this->parse ();
		return parent::getParsedData ();
	}
	public function fetch() {
		$this->browser->get ( $this->url );
		$this->rawData = $this->browser->getContentAsText ();
	}
}
?>