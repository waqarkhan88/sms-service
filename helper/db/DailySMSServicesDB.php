<?php
/*
	****					Modification History								****
	********************************************************************************
	********************************************************************************
	****		Name: 					Haseeb Ahmed							****
	****		Date: 					November 03,2012						****	
	****		Description:			Connection to database					****	
	********************************************************************************
	********************************************************************************
*/
require_once 'SafeSQL.class.php';
require_once 'DB.class.php';
require_once 'DbHelper.class.php';
class DailySMSServicesDB
{
	private $username = 'root';
	private $password = '';
	private $server = 'localhost';
	private $dbName = 'DailySMSServices';
	private $log;
	private $safeSQL;
	private $db;
		
	public function DailySMSServicesDB()
	{
		$this->db = new DB($this->dbName, $this->server, $this->username, $this->password);
		$this->safeSQL = new SafeSQL_MySQL();
	}
	
	Public function GetAPIAccounts()
	{
		$result = array();
		$query = $this->safeSQL->query("Select pk_accountId as accountId, APIKey, DailyLimit - ConsumedSMS as remainingSMS From api_accounts Where Active = 1 And DailyLimit - ConsumedSMS > 0 And APIKey is not null", array());
		$source = $this->db->query($query);
		while($row = $this->db->fetchNextObject($source))
			$result[] = $row;
	
		return $result;
	}
	
	Public function GetRecipients($groupId)
	{
		$result = array();
		$query = $this->safeSQL->query("Select R.pk_RecpId as recpId, R.phone_number, ST.name as SMSTypeCode From recipients R inner join package P ON(R.fk_PackageId = p.package_id) inner join sms_types ST ON(R.fk_SMSTypeId = ST.pk_SMSTypeId) Where R.Active = 1 And R.ConsumedSMS < P.daily_limit And (ifnull((R.LastSent + interval P.sms_interval minute), now()) <= now()) And R.fk_GroupId = %i", array($groupId));
		$source = $this->db->query($query);
		while($row = $this->db->fetchNextObject($source))
			$result[] = $row;
	
		return $result;
	}
	Public function UpdateAPISmsCount(array $accountsCount)
	{
		if(count($accountsCount) > 0)
		{
			$query = "Update api_accounts Set ConsumedSMS = Case pk_accountId ";
			foreach($accountsCount as $account)
			{
				$query .= "When " . $account["accountId"] . " Then ConsumedSMS + " . $account["count"] . " ";
			}
			$query .= "Else ConsumedSMS End";
			
			$query = $this->safeSQL->query($query, array());
			$this->db->execute($query);
		}
	}
	Public function UpdateRecpConsumedSMS(array $recipients)
	{
		if(count($recipients) > 0)
		{
			$query = "Update recipients Set ConsumedSMS = Case pk_RecpId ";
			foreach($recipients as $recp)
			{
				$query .= "When " . $recp["recpId"] . " Then ConsumedSMS + 1 ";
			}
			$query .= "Else ConsumedSMS End, LastSent = case pk_RecpId ";
			
			foreach($recipients as $recp)
			{
				$query .= "When " . $recp["recpId"] . " Then '" . $recp['lastSent'] . "' ";
			}
			$query .= "Else LastSent End";
			
			
			$query = $this->safeSQL->query($query, array());
			$this->db->execute($query);
		}
	}
	
	public static function UpdateDailyAPIAccounts()
	{
		$query = "Update api_accounts Set ConsumedSMS = 0, LastRefreshed = '" . date('Y-m-d H:i:s') . "'";
		
		$query = $this->safeSQL->query($query, array());
		$this->db->execute($query);
	}
	
	public static function UpdateDailyRecipients()
	{
		$query = "Update recipients R inner join payments P on(R.pk_RecpId = P.fk_RecpId) Set 		R.Active = 0 Where P.ExpiryDate <= '" . date('Y-m-d H:i:s') . "'";
		
		$query = $this->safeSQL->query($query, array());
		$this->db->execute($query);
	}
}
?>