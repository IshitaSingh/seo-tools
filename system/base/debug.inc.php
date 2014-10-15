<?php
	/**
	 * debug tools
	 *
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */


	/**
	 * Clear all output buffers
	 *
	 * @return  void
	 */
	function clr() {
		ob_clean();
	}

	/**
	 * Set breakpoint
	 *
	 * @return  void
	 */
	function breakpoint() {
		callstack();
		exit;
	}

	/**
	 * Return callstack string as html
	 *
	 * @return string callstack formatted as html
	 */
	function callstack() {
		$callstack = debug_backtrace();

		$table  = "<table border=\"1\">";
		$table .= "<caption>Call Stack</caption>";
		$table .= "<thead>";
		$table .= "<tr>";
		$table .= "<th>Function</th>";
		$table .= "<th>Args</th>";
		$table .= "<th>Location</th>";
		$table .= "</tr>";
		$table .= "</thead>";
		$table .= "<tbody>";

		foreach( $callstack as $call ) {
			$table .= "<tr>";

			$table .= "<td>";
			if( isset( $call['class'] )) {
				$table .= $call['class'];
				$table .= $call['type'];
			}
			$table .= $call['function'];
			$table .= "</td>";
			$table .= "<td>";
			$table .= "(";
			if( isset( $call['args'] )) {
				foreach( $call['args'] as $arg ) {

					if( is_object( $arg )) {
						$table .= ( "<span style=\"color:#0000FF\">Object</span>(<span onclick=\"getElementById('trace_{$call['line']}_{$i}_{$ii}').style.display='inline';\" style=\"text-decoration:underline;cursor:pointer;color:#000000\">".get_class($arg)."</span>)" );
					}
					elseif( is_array( $arg )) {
						$table .= ( "<span style=\"text-decoration:underline;cursor:pointer;color:#0000FF\" onclick=\"getElementById('trace_{$call['line']}_{$i}_{$ii}').style.display='inline';\">Array</span>" );
					}
					elseif( is_string( $arg )) {
						$table .= ( "<span style=\"color:#0000FF\">string</span>(<span style=\"color:#FF0000\">\"{$arg}\"</span>)" );
					}
					elseif( is_scalar( $arg )) {
						$table .= ( "<span style=\"color:#0000FF\">".gettype($arg)."</span>(<span style=\"color:#FF0000\">".$arg."</span>)" );
					}
					else {
						$table .= ( "<span style=\"color:#0000FF\">".gettype($arg)."</span>(<span style=\"color:#FF0000\">__PHP_Incomplete_Class</span>)" );
					}
				}
			}
			$table .= ")";
			$table .= "</td>";

			$table .= "<td>";
			$table .= 'in ' . $call['file'] . ' on line ' . $call['line'];
			$table .= "</td>";

			$table .= "</tr>";
		}

		$table .= "</tbody>";
		$table .= "</table>";

		return $table;
	}

	/**
	 * begin trapping memory
	 *
	 * @return void
	 */
	function trap() {
		$GLOBALS['debug_mode_memory'] = memory_get_usage( TRUE );
	}

	/**
	 * Print memory usage
	 *
	 * @return void
	 */
	function alloc() {
		$memory = memory_get_usage( TRUE );

		if( isset( $GLOBALS['debug_mode_memory'] )) {
			print( '<pre>trapped allocated memory: ' . (( $memory - $GLOBALS['debug_mode_memory'] ) / 1048576 ));
			print( " MB\ntotal allocated memory:   " . $memory / 1048576 );
			print( ' MB</pre>' );
			unset( $GLOBALS['debug_mode_memory'] );
		}
		else {
			print( "<pre>allocated memory: " . $memory );
			print( ' bytes</pre>' );
		}
		flush();
	}

	/**
	 * Begin timer
	 *
	 * @return void
	 */
	function strt() {
		$mtime = explode( ' ', microtime() );
		$timer = (real)$mtime[1] + (real)$mtime[0];
		$GLOBALS['debug_mode_timer'] = $timer;
	}

	/**
	 * Stop timer and print results
	 *
	 * @return void
	 */
	function stp() {
		if( isset( $GLOBALS['debug_mode_timer'] )) {
			$timer = $GLOBALS['debug_mode_timer'];
			$mtime = explode(' ', microtime());
			print( '<pre>timer: ' . number_format(((real)$mtime[1]+(real)$mtime[0])-$timer, 8 ));
			print( 's</pre>' );
		}
	}

	/**
	 * Print variable to screen
	 *
	 * @param mixed $var Variable to print
	 * @param bool $halt Specifies whether to halt application
	 *
	 * @return void
	 */
	function dmp($var, $halt=true) {
		print( "<pre>\n\r" );
		print_r( $var );
		print( "\n\r</pre>" );
		if($halt)exit;
	}

	/**
	 * Trace
	 *
	 * @param mixed $var Variable to trace
	 *
	 * @return void
	 */
	function trace($string) {
		die($string);
	}
?>