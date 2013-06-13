<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);
date_default_timezone_set("Asia/Karachi");
require_once('helper/db/DailySMSServicesDB.php');

DailySMSServicesDB::updateDailyAPIAccounts();
DailySMSServicesDB::updateDailyRecipients();

?>