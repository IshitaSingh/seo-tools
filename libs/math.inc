<?php
	// +----------------------------------------------------------------------+
	// | PHP version 5.1.x                                                    |
	// +----------------------------------------------------------------------+
	// | Desc:  Math Functions                                                |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	// | Version: 1.0.2                                                       |
	// | Modified: Jun 29, 2006                                               |
	// +----------------------------------------------------------------------+
	// | Author(s): Darnell Shinbine <gasolene@gmail.com>                     |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	//
	// $Id$


	function avg( $array ) {
	    $sum   = array_sum($array);
	    $count = count($array);
	    return $sum / $count;
	}

	function std_deviation( $array ) {
	   
	    $avg = avg( $array );
	    foreach( $array as $value ) {
	        $variance[] = pow( $value - $avg, 2 );
	    }
	    $deviation = sqrt( avg( $variance ));
	    return $deviation;
	}


	/**
	 * to_bytes
	 *
	 * convert int to bytes
	 *
	 	b  = bits        B  = Bytes
		Kb = Kilobits    KB = Kilobytes
		Mb = Megabits    MB = Megabytes
		Gb = Gigabits    GB = Gigabytes
	 * @author       <unknown>
	 * @version     1.0.0
	 * @param       char 		$value	 		value to convert
	 				int			$unit			unit of conversion
	 * @return      int							value in new units
	 */
	function to_bytes($value, $unit)
	{
	  $k = 1024;
	  $m = 1048576;
	  $g = 1073741824;

	  switch (trim($unit))
	  {
		case "b": //bytes
		  return ($value / 8);
		case "Kb": //Kilobits
		  return (($value * $k) / 8);
		case "Mb": // Megabits
		  return (($value * $m) / 8);
		case "Gb": // Gigabits
		  return (($value * $g) / 8);
		case "B": // Bytes
		  return $value;
		case "KB": // Kilobytes
		  return ($value * $k);
		case "MB": // Megabytes
		  return ($value * $m);
		case "GB": // Gigabytes
		  return ($value * $g);
		default: return 0;
	  }
	}

	/**
	 * bytes_to
	 *
	 * convert bytes to value
	 *
	 	b  = bits        B  = Bytes
		Kb = Kilobits    KB = Kilobytes
		Mb = Megabits    MB = Megabytes
		Gb = Gigabits    GB = Gigabytes
	 * @author       <unknown>
	 * @version     1.0.0
	 * @param       char 		$value	 		value to convert
	 				int			$unit			unit of conversion
	 * @return      int							value in new units
	 */
	function bytes_to($value, $unit)
	{
	  $k = 1024;
	  $m = 1048576;
	  $g = 1073741824;

	  switch (trim($unit))
	  {
		case "b": //bytes
		  return ($value * 8);
		case "Kb": //Kilobits
		  return (($value * 8) / $k);
		case "Mb": // Megabits
		  return (($value * 8) / $m);
		case "Gb": // Gigabits
		  return (($value * 8) / $g);
		case "B": // Bytes
		  return $value;
		case "KB": // Kilobytes
		  return ($value / $k);
		  //return bcdiv($value, $k, 4);
		case "MB": // Megabytes
		  return ($value / $m);
		case "GB": // Gigabytes
		  return ($value / $g);
		default: return 0;
	  }
	}

	/**
	 * sum_digits
	 *
	 * some digits in a number
	 * eg: 123 => 1 + 2 + 3 = 6
	 *
	 * @author      <unknown>
	 * @version     1.0.0
	 * @param       int 		$x		 		number to some values of
	 * @return      int							sum of values
	 */
	function sum_digits( $x ) {
		$y = 0;
		while ($x) {
			$y+= $x % 10;
			$x/= 10;
		}
		return $y;
	}
?>