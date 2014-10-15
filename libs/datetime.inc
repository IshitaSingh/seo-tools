<?php
	// +----------------------------------------------------------------------+
	// | PHP version 5.1.x                                                    |
	// +----------------------------------------------------------------------+
	// | Desc:  Date/Time Functions                                           |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	// | Version: 1.1.0                                                       |
	// | Modified: Nov 24, 2006                                              |
	// +----------------------------------------------------------------------+
	// | Author(s): Darnell Shinbine <gasolene@gmail.com>                     |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	//
	// $Id$


	function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}


	/**
	* get_daylight_saving()
	* return timestamp with daylight saving factor
	*
	* @param $timezone
	* the timestamp
	* @return
	* timestamp
	*/
	function get_daylight_saving( $timestamp ) {
		if( daylight_saving( $timestamp )) {
			$timestamp+=3600;
		}
		return $timestamp;
	}


	/**
	* Determine if a date is in Daylight Savings Time (AEST - NSW).
	* By Jaza, 2005-01-03 (birthday function).
	*
	* @param $timestamp
	* the exact date on which to make the calculation, as a UNIX timestamp (should already be set to GMT+10:00).
	* @return
	* boolean value of TRUE for DST dates, and FALSE for non-DST dates.
	*/
	function daylight_saving($timestamp) {
	  $daylight_saving = FALSE;
	  $current_month = gmdate('n', $timestamp);
	  $current_day = gmdate('d', $timestamp);
	  $current_weekday = gmdate('w', $timestamp);
	  // Daylight savings is between October and March
	  if($current_month >= 10 || $current_month <= 3) {
		$daylight_saving = TRUE;
		if($current_month == 10 || $current_month == 3) {
		  // It starts on the last Sunday of October, and ends on the last Sunday of March.
		  if($current_day >= 25) {
			if($current_day - $current_weekday >= 25) {
			  if($current_weekday == 0) {
				// Starts at 2am in the morning.
				if(gmdate('G', $timestamp) >= 2) {
				  $daylight_saving = $current_month == 10 ? TRUE : FALSE;
				} else {
				  $daylight_saving = $current_month == 10 ? FALSE : TRUE;
				}
			  } else {
				$daylight_saving = $current_month == 10 ? TRUE : FALSE;
			  }
			} else {
			  $daylight_saving = $current_month == 10 ? FALSE : TRUE;
			}
		  } else {
			$daylight_saving = $current_month == 10 ? FALSE : TRUE;
		  }
		}
	  }
	  return !$daylight_saving;
	}


	/**
	 * get_human_date
	 *
	 * return a human friendly date/time string
	 *
	 * @author      <>
	 * @version     1.0.0
	 * @param       timestamp 		$timestamp 		event timestamp
	 * @param       timestamp 		$now	 		current timestamp, defaults to time()
	 * @return      string							date string
	 */
	function get_human_date( $timestamp, $now = 0 ) {
		// get current time
		$now = $now?$now:time();

		if( $now < $timestamp + 3600 ) { // less than 60 minutes ago
			$minutes = (int)( $now - $timestamp ) / 60;
			return $minutes>1?$minutes.' minutes ago':'1 minute ago';
		}
		elseif( $now < $timestamp + 86400 ) { // less than 24 hours ago
			$hours = (int)( $now - $timestamp ) / 3600;
			return $hours>1?$hours.' hours ago':'1 hour ago';
		}
		elseif( $now < $timestamp + 604800 ) { // less than 7 days ago
			$days = (int)( $now - $timestamp ) / 86400;
			return $days>1?$days.' days ago':'1 day ago';
		}
		else {
			return gmdate( 'F j, Y', $timestamp ); // 'more than 7 days'
		}
	}


	/**
	 * Adds to timestamp
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       char 		$_interval 		character representing interval
	 				int			$_number		number to increment
					int			$_date			date as timestamp
	 * @return      int							new date as timestamp
	 */
	function date_add( $_interval, $_number, $_date )
	{
		$_date_time_array  = getdate($_date);

		$_hours =  $_date_time_array["hours"];
		$_minutes = $_date_time_array["minutes"];
		$_seconds = $_date_time_array["seconds"];
		$_month = $_date_time_array["mon"];
		$_day = $_date_time_array["mday"];
		$_year = $_date_time_array["year"];

		switch (strtolower($_interval))
		{
			case "y":
				$_year += $_number;
				break;
			case "q":
				$_year += ( $_number * 3 );
				break;
			case "m":
				$_month += $_number;
				break;
			case "d":
				 $_day += $_number;
				break;
			case "w":
				 $_day += ( $_number * 7 );
				break;
			case "h":
				 $_hours += $_number;
				break;
			case "n":
				 $_minutes += $_number;
				break;
			case "s":
				 $_seconds += $_number;
				break;
		}
		return mktime( $_hours, $_minutes, $_seconds, $_month ,$_day, $_year );
	}


	/**
	 * Returns difference between two timestamps
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       char 		$_interval 		character representing interval
	 				int			$_date1 		date1 as timestamp
					int			$_date2			date2 as timestamp
	 * @return      int							difference between dates as timestamp
	 */
	function date_diff( $_interval, $_date1, $_date2 )
	{
		$_timedifference = $_date2 - $_date1;

		switch ($_interval)
		{
			case "m":
				$_retval = ($_timedifference / 2678400);
				break;
			case "w":
				$_retval = ($_timedifference / 604800);
				break;
			case "d":
				$_retval = ($_timedifference / 86400);
				break;
			case "h":
				$_retval = ($_timedifference / 3600);
				break;
			case "n":
				$_retval = ($_timedifference / 60);
				break;
			case "s":
				$_retval = $_timedifference;
				break;
		}
		return $_retval;
	}


	/**
	 * Returns number of days in feburary
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       int		$_year	year of month
	 * @return      int	    		number of days in month
	 */
	function days_in_february( $_year )
	{
		if( is_numeric( $_year ))
		{
			return (int)((($_year % 4 == 0) && ( (!($_year % 100 == 0)) || ($_year % 400 == 0))) ? 29 : 28 );
		}
		return false;
	}



	/**
	 * Returns number of days in month
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       int		$_month		numerical month
	 * @return      int	    			number of days in month
	 */
	function days_in_month( $_month, $_year = null )
	{
		if( !$_year )
		{
			$_year = (int)date( 'Y', time() );
		}

		if( is_numeric( $_month ) && $_month > 0 && $_month < 13 )
		{
			if( $_month == 4 || $_month == 6 || $_month == 9 || $_month == 11 ) return 30;
			if( $_month == 2 ) return days_in_february( $_year );
			return 31;
		}
		return false;
	}
?>