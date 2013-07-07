<?php
require_once ('libs/log4php/Logger.php');
Logger::configure ( 'logs/config.xml' );
class MessageGenerator {
	private $rates;
	private $log;
	public function MessageGenerator($rates) {
		$this->rates = $rates;
	}
	public function getMessage($messageType) {
		switch ($messageType) {
			case 'G' :
				return 'G=' . (isset ( $this->rates ['Gold'] ) ? $this->rates ['Gold'] : 'n/a');
				break;
			
			case 'S' :
				return 'S=' . (isset ( $this->rates ['Silver'] ) ? $this->rates ['Silver'] : 'n/a');
				break;
			
			case 'D' :
				return '$=' . (isset ( $this->rates ['Dollar'] ) ? $this->rates ['Dollar'] : 'n/a');
				break;
			
			case 'GS' :
				return 'G=' . (isset ( $this->rates ['Gold'] ) ? $this->rates ['Gold'] : 'n/a') . '\n' .
				'S=' . (isset ( $this->rates ['Silver'] ) ? $this->rates ['Silver'] : 'n/a');
				break;
			
			case 'GSD' :
				return 'G=' . (isset ( $this->rates ['Gold'] ) ? $this->rates ['Gold'] : 'n/a') . '\n' 
				. 'S=' . (isset ( $this->rates ['Silver'] ) ? $this->rates ['Silver'] : 'n/a') . '\n' 
				. '$=' . (isset ( $this->rates ['Dollar'] ) ? $this->rates ['Dollar'] : 'n/a') . '\n'
				. 'Time: ' . date('H:i');
				break;
			case 'GSDLocal' :
				return 'G: ' . (isset( $this->rates ['Gold'] ) ? $this->calculateLocalRates($this->rates ['Gold']) : 'n/a') . '\n' 
				. 'S: ' . (isset ( $this->rates ['Silver'] ) ? $this->calculateLocalRates($this->rates ['Silver']) : 'n/a') . '\n' 
				. '$: ' . (isset ( $this->rates ['Dollar'] ) ? $this->rates ['Dollar'] : 'n/a') . '\n'
				. 'Time: ' . date('H:i');			
				break;
			
		}
	}
	
	private  function calculateLocalRates($rate)
	{
		if(isset( $this->rates ['Dollar']))
		{
			return  round(($rate * $this->rates ['Dollar'] * 0.375006308),0);
		}
		else 
			return 'n/a';				
	}
}
?>
