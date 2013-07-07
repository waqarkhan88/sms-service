<?php
require_once 'SafeSQL.class.php';
require_once 'DB.class.php';
require_once 'DbHelper.class.php';
class DailySMSServicesDB {
	private $username = 'b13_13302111';
	private $password = 'haseeb';
	private $server = 'sql207.byethost13.com';
	private $dbName = 'b13_13302111_sms_api_services';
	private $log;
	private $safeSQL;
	private $db;
	public function DailySMSServicesDB() {
		$this->db = new DB ( $this->dbName, $this->server, $this->username, $this->password );
		$this->safeSQL = new SafeSQL_MySQL ();
	}
	public function getAPIAccounts() {
		$result = array ();
		$query = $this->safeSQL->query ( "Select account_id as accountId, api_key as apiKey, daily_limit - consumed_sms as remainingSMS From api_accounts Where active = 1 And daily_limit - consumed_sms > 0 And api_key is not null", array () );
		$source = $this->db->query ( $query );
		while ( $row = $this->db->fetchNextObject ( $source ) )
			$result [] = $row;
		
		return $result;
	}
	public function getRecipients($groupId) {
		$result = array ();
		$query = $this->safeSQL->query ( "Select r.recipient_id as recipientId, r.phone_number as phoneNumber, st.code as smsTypeCode From recipients r inner join package p ON(r.package_id = p.package_id) inner join sms_types st ON(r.sms_type_id = st.sms_type_id) Where r.active = 1 And r.consumed_sms < p.daily_limit And (ifnull((r.last_sent + interval p.sms_interval minute), %n) <= %n) And r.group_id = %i", array (
				date ( 'Y-m-d H:i:s' ),
				date ( 'Y-m-d H:i:s' ),
				$groupId 
		) );
		$source = $this->db->query ( $query );
		while ( $row = $this->db->fetchNextObject ( $source ) )
			$result [] = $row;
		
		return $result;
	}
	public function updateAPISmsCount(array $accounts) {
		if (count ( $accounts ) > 0) {
			$query = "Update api_accounts Set consumed_sms = Case account_id ";
			foreach ( $accounts as $account ) {
				$query .= " When " . $account ["accountId"] . " Then consumed_sms + " . $account ["count"] . " ";
			}
			$query .= "Else consumed_sms End";
			
			$query = $this->safeSQL->query ( $query, array () );
			$this->db->execute ( $query );
		}
	}
	public function updateRecipientsConsumedSMS(array $recipients) {
		if (count ( $recipients ) > 0) {
			$query = "Update recipients Set consumed_sms = Case recipient_id ";
			
			foreach ( $recipients as $recp ) {
				$query .= "When " . $recp ["recipientId"] . " Then consumed_sms + 1 ";
			}
			
			$query .= "Else consumed_sms End, last_sent = case recipient_id ";
			
			foreach ( $recipients as $recp ) {
				$query .= "When " . $recp ["recipientId"] . " Then '" . $recp ['lastSent'] . "' ";
			}
			
			$query .= "Else last_sent End";
			
			$query = $this->safeSQL->query ( $query, array () );
			$this->db->execute ( $query );
		}
	}
	public function updateDailyAPIAccounts() {
		$query = "Update api_accounts Set consumed_sms = 0, last_refreshed = %n";
		
		$query = $this->safeSQL->query ( $query, array (date ( 'Y-m-d H:i:s' )) );
		$this->db->execute ( $query );
	}
	public function updateDailyRecipients() {
		$query = "Update recipients r inner join payments p on(r.recipient_id = p.recipient_id) Set r.active = 0 Where p.expiry_date <= %n";
		
		$query = $this->safeSQL->query ( $query, array (date ( 'Y-m-d H:i:s' )) );
		$this->db->execute ( $query );
		
		$query = "Update recipients Set consumed_sms = 0 Where  active = 1";
		
		$query = $this->safeSQL->query ( $query, array () );
		$this->db->execute ( $query );
	}
}
?>