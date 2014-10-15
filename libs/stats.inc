<?php
	// +----------------------------------------------------------------------+
	// | PHP version 5.1.x                                                    |
	// +----------------------------------------------------------------------+
	// | Desc:  Stats Functions                                               |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	// | Version: 1.0.2                                                       |
	// | Modified: Jun 29, 2006                                               |
	// +----------------------------------------------------------------------+
	// | Author(s): Craih Matthews <craig1972@nonononospamspamspam.x-mail.net>|
	// |                                                                      |
	// +----------------------------------------------------------------------+
	//
	// $Id$


	function stat_mean ($data) {
	// calculates mean
	return (array_sum($data) / count($data));
	}
	
	function stat_median ($data) {
	// calculates median
	sort ($data);
	$elements = count ($data);
	if (($elements % 2) == 0) {
	$i = $elements / 2;
	return (($data[$i - 1] + $data[$i]) / 2);
	} else {
	$i = ($elements - 1) / 2;
	return $data[$i];
	}
	}
	
	function stat_range ($data) {
	// calculates range
	return (max($data) - min($data));
	}
	
	function stat_var ($data) {
	// calculates sample variance
	$n = count ($data);
	$mean = stat_mean ($data);
	$sum = 0;
	foreach ($data as $element) {
	$sum += pow (($element - $mean), 2);
	}
	return ($sum / ($n - 1));
	}
	
	function stat_varp ($data) {
	// calculates population variance
	$n = count ($data);
	$mean = stat_mean ($data);
	$sum = 0;
	foreach ($data as $element) {
	$sum += pow (($element - $mean), 2);
	}
	return ($sum / $n);
	}
	
	function stat_stdev ($data) {
	// calculates sample standard deviation
	return sqrt (stat_var($data));
	}
	
	function stat_stdevp ($data) {
	// calculates population standard deviation
	return sqrt (stat_varp($data));
	}
	
	function stat_simple_regression ($x, $y) {
	// runs a simple linear regression on $x and $y
	// returns an associative array containing the following fields:
	// a - intercept
	// b - slope
	// s - standard error of estimate
	// r - correlation coefficient
	// r2 - coefficient of determination (r-squared)
	// cov - covariation
	// t - t-statistic
	$output = array();
	$output['a'] = 0;
	$n = min (count($x), count($y));
	$mean_x = stat_mean ($x);
	$mean_y = stat_mean ($y);
	$SS_x = 0;
	foreach ($x as $element) {
	$SS_x += pow (($element - $mean_x), 2);
	}
	$SS_y = 0;
	foreach ($y as $element) {
	$SS_y += pow (($element - $mean_y), 2);
	}
	$SS_xy = 0;
	for ($i = 0; $i < $n; $i++) {
	$SS_xy += ($x[$i] - $mean_x) * ($y[$i] - $mean_y);
	}
	$output['b'] = $SS_xy / $SS_x;
	$output['a'] = $mean_y - $output['b'] * $mean_x;
	$output['s'] = sqrt (($SS_y - $output['b'] * $SS_xy)/ ($n - 2));
	$output['r'] = $SS_xy / sqrt ($SS_x * $SS_y);
	$output['r2'] = pow ($output['r'], 2);
	$output['cov'] = $SS_xy / ($n - 1);
	$output['t'] = $output['r'] / sqrt ((1 - $output['r2']) / ($n - 2));
	
	return $output;
	}
?>