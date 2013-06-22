<?php
// ini_set('display_errors', '0');
error_reporting ( E_ALL );
date_default_timezone_set ( "Asia/Karachi" );
require_once ('helper/db/DailySMSServicesDB.php');
require_once ('helper/PageHelper.php');
require_once ('MessageGenerator.php');
require_once ('SMSCentre.php');
require_once ('libs/log4php/Logger.php');

Logger::configure ( 'logs/config.xml' );

// test

$metalScrappers = array (
		"KitcoScrapper",
		"UpniwebScrapper" 
);
$forexScrappers = array (
		"ForexPKScrapper",
		"ExchangeScrapper" 
);

$metalRates = null;
$forexRates = null;

$log = Logger::getLogger ( 'Main' );
$timeLog = Logger::getLogger ( 'ExecutionTime' );

$groupId = isset ( $_REQUEST ['group_id'] ) ? PageHelper::sanitizeInput ( $_REQUEST ['group_id'], FILTER_SANITIZE_NUMBER_INT ) : null;

if ($groupId) {
	$log->info ( 'Scrapping started for groupId: ' . $groupId );
	$time = "02:00:00";
	if (intval ( date ( 'H' ) ) >= 6 || date ( 'H:i:s' ) <= $time) 	// limit b/w 6am to 2am
	{
		$scrapTime1 = microtime ( true );
		foreach ( $metalScrappers as $scrapper ) {
			require_once ('scrap/schemes/' . $scrapper . '.php');
			$scrapper = new $scrapper ();
			$metalRates = $scrapper->getParsedData ();
			if ($metalRates) {
				break;
			}
		}
		
		foreach ( $forexScrappers as $scrapper ) {
			require_once ('scrap/schemes/' . $scrapper . '.php');
			$scrapper = new $scrapper ();
			$forexRates = $scrapper->getParsedData ();
			if ($forexRates) {
				break;
			}
		}
		
		if ($metalRates || $forexRates) {
			$scrapTime2 = microtime ( true );
			$timeLog->info ( round ( $scrapTime2 - $scrapTime1, 2 ) );
			
			$db = new DailySMSServicesDB ();
			$recipients = $db->getRecipients ( $groupId );
			if (count ( $recipients ) > 0) {
				$APIAccounts = $db->getAPIAccounts ();
				if (count ( $APIAccounts ) > 0) {
					$messageGenerator = new MessageGenerator ( array_merge ( $metalRates, $forexRates ) );
					$isRecpEnd = false;
					$recpCount = 0;
					$updateAPIAccount = array ();
					$updateRecp = array ();
					foreach ( $APIAccounts as $APIAccount ) {
						$APICount = 0;
						$smsCentre = new SMSCentre ( $APIAccount->apiKey );
						while ( $APIAccount->remainingSMS - $recpCount > 0 ) {
							if (count ( $recipients ) > $recpCount) {
								$message = $messageGenerator->getMessage ( $recipients [$recpCount]->smsTypeCode );
								$smsCentre->send ( array (
										$recipients [$recpCount]->phoneNumber 
								), $message );
								$updateRecp [] = array (
										'recipientId' => $recipients [$recpCount]->recipientId,
										'lastSent' => date ( 'Y-m-d H:i:s' ) 
								);
								$recpCount ++;
							} else {
								$isRecpEnd = true;
								break;
							}
						}
						$updateAPIAccount [] = array (
								'accountId' => $APIAccount->accountId,
								'count' => $recpCount - $APICount 
						);
						$APICount = $recpCount;
						if ($isRecpEnd) {
							break;
						}
					}
					$db->updateAPISmsCount ( $updateAPIAccount );
					$db->updateRecipientsConsumedSMS ( $updateRecp );
				} else {
					$log->warn ( 'API accounts sms finished | recipients count: ' . count ( $recipients ) );
				}
			}
		}
	} else {
		$log->info ( 'Time range exceeds: ' . date ( 'H:i:s' ) );
	}
} else {
	$log->error ( 'GroupId not specified' );
}
?>