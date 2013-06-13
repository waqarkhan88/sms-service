<?php
require_once (dirname(__FILE__) . '/../libs/simpletest/browser.php');
abstract class Scrapper {
	protected $browser;
	protected $url;
	protected $parsedData;
	protected $rawData;
	public function Scrapper() {
	}
	public function getParsedData() {
	}
	public function fetch() {
	}
	public function parse() {
	}
}
?>