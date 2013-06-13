<?php
require_once ('/../Scrapper.php');
class MetalScrapper extends Scrapper {
	protected $goldRate;
	protected $silverRate;
	public function MetalScrapper() {
		$this->browser = new SimpleBrowser ();
		$this->browser->addHeader ( 'User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1' );
		$this->parsedData = null;
	}
	public function getParsedData() {
		if ($this->goldRate != null && $this->silverRate != null) {
			$this->parsedData = array (
					"Gold" => $this->goldRate,
					"Silver" => $this->silverRate 
			);
			return $this->parsedData;
		} else {
			return null;
		}
	}
}
?>