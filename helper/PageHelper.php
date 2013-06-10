<?php


class PageHelper {
	
	public static function isPageSubmitted() {
		if (strtoupper ( $_SERVER ['REQUEST_METHOD'] ) == 'POST' || strtoupper ( $_SERVER ['REQUEST_METHOD'] ) == 'GET')
			return true;
		else
			return false;
	}
	
	public static function sanitizeInput($input, $type, $options = null) {
		if ($options == null) {
			$defaultVal = 0;
			if ($type == FILTER_SANITIZE_NUMBER_INT)
				$defaultVal = 0;
			
			if ($type == FILTER_SANITIZE_NUMBER_FLOAT)
				$defaultVal = 0.0;
			
			if ($type == FILTER_SANITIZE_STRING)
				$defaultVal = "";
			
			$options = array (
					'options' => array (
							'default' => $defaultVal  // value to return if the filter fails
					 
			));
		}
		
		return filter_var ( $input, $type, $options );
	}
	
	public static function sanitizeInputArray($array, $type, $options = null) {
		$temp_array = array ();
		if ($options == null) {
			$defaultVal = 0;
			if ($type == FILTER_SANITIZE_NUMBER_INT)
				$defaultVal = 0;
			
			if ($type == FILTER_SANITIZE_NUMBER_FLOAT)
				$defaultVal = 0.0;
			
			if ($type == FILTER_SANITIZE_STRING)
				$defaultVal = "";
			
			$options = array (
					'options' => array (
							'default' => $defaultVal  // value to return if the filter fails
					 
			));
		}
		foreach ( $array as $item ) {
			$temp_array [] = filter_var ( $item, $type, $options );
		}
		return $temp_array;
	}
	
	public static function sanitizeCommaSeperatedInt($input) {
		return implode ( ",", array_map ( "intval", explode ( ",", $input ) ) );
	}
	
	public static function GetTimeSpan($time) {
		date_default_timezone_set ( 'Asia/Karachi' );
		$time = time () - strtotime ( $time );
		
		$tokens = array (
				31536000 => 'year',
				2592000 => 'month',
				604800 => 'week',
				86400 => 'day',
				3600 => 'hour',
				60 => 'minute',
				1 => 'second',
				0 => 'just now' 
		);
		
		foreach ( $tokens as $unit => $text ) {
			if ($time < $unit)
				continue;
			$numberOfUnits = floor ( $time / $unit );
			return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
		}
	
	}	
	
	public static function getHttpPutData()
	{
		$data = "";
		$dataStream = fopen('php://input', 'r');
		while ($dataChunk = fread($dataStream, 1024))
		{
			$data .= $dataChunk;
		}
		fclose($dataStream);
		return $data;
	}
}
?>