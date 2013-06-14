<?php
require_once ('MetalScrapper.php');
require_once (dirname(__FILE__) . '/../../libs/log4php/Logger.php');
class UpniwebScrapper extends MetalScrapper {
	public $log;
	public function UpniwebScrapper() {
		parent::__construct ();
		$this->log = Logger::getLogger ( 'Scrappers' );
		$this->url = 'http://upniweb.com/SpotGold.aspx';
		$this->fetch ();
	}
	public function fetch() {
		$this->browser->get ( $this->url );
		$this->rawData = $this->browser->getContent ();
		//echo $this->rawData;
	}
	public function parse() {
		$rates = null;
		preg_match ( '/lbl1ounceUSD[^>]*>(.*?)</', $this->rawData, $matches );
		$rates ['Gold'] = isset ( $matches [1] ) ? $matches [1] : null;
		preg_match ( '/lbl10GrmPKR4[^>]*>(.*?)</', $this->rawData, $matches );
		$rates ['Silver'] = isset ( $matches [1] ) ? $matches [1] : null;
		if ($rates != null && $rates ['Gold'] != null && $rates ['Silver'] != null) {
			$this->goldRate = $rates ['Gold'];
			$this->silverRate = $rates ['Silver'];
		} else {
			$this->log->warn ( __CLASS__ . ' - Unable to scrap rates.' );
			$this->goldRate = null;
			$this->silverRate = null;
		}
	}
	public function getParsedData() {
		$this->parse ();
		return parent::getParsedData ();
	}
}
?>