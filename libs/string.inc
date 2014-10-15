<?php
	// +----------------------------------------------------------------------+
	// | PHP version 4.3.8                                                    |
	// +----------------------------------------------------------------------+
	// | Desc:  String Functions                                    |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	// | Version: 1.0.4                                                       |
	// | Modified: Jul 21, 2008                                               |
	// +----------------------------------------------------------------------+
	//
	// $Id$


	/**
	 * ordinal
	 *
	 * Returns the ordinal suffix of a numnber
	 *
	 * @param		int		The number to add the ordinal suffix to
	 * @return		string	formatted value
	 * @author		Kris Kroker
	 */
	function ordinal($number) {
	
		// when fed a number, adds the English ordinal suffix. Works for any
		// number, even negatives
	
		if ($number % 100 > 10 && $number % 100 < 14)
			: $suffix = "th";
		else
			: switch ($number % 10) {
	
				case 0 :
					$suffix = "th";
					break;
	
				case 1 :
					$suffix = "st";
					break;
	
				case 2 :
					$suffix = "nd";
					break;
	
				case 3 :
					$suffix = "rd";
					break;
	
				default :
					$suffix = "th";
					break;
			}
	
		endif;
	
		return $suffix;
	}


	/**
	 * formatOrdinal
	 *
	 * Returns a string of the format INT . Suffix (e.g. 23<sup>rd</sup>)
	 *
	 * @param		int		The number to add the ordinal suffix to
	 * @return		string	formatted value
	 * @author		Kris Kroker
	 */
	function formatOrdinal($number) {
		return "${number}<sup>" . ordinal($number) . "</sup>";
	}


	/**
	 * leading_zero
	 *
	 * Returns a numeric value with leading zeros
	 *
	 * @param		float		$aNumber		number
	 * @param		int			$intPart		places before decimal
	 * @param		int			$floatPart		places after decimal
	 * @param		int			$dec_point		decimal point
	 * @param		int			$thousands_sep	thousands seperator
	 * @return		string						numeric value
	 */
	function leading_zero($aNumber, $intPart, $floatPart = null, $dec_point = null, $thousands_sep = null) { //Note: The $thousands_sep has no real function because it will be "disturbed" by plain leading zeros -> the main goal of the function
		$formattedNumber = $aNumber;
		if (!is_null($floatPart)) { //without 3rd parameters the "float part" of the float shouldn't be touched
			$formattedNumber = number_format($formattedNumber, $floatPart, $dec_point, $thousands_sep);
		}
		//if ($intPart > floor(log10($formattedNumber)))
		$formattedNumber = str_repeat("0", ($intPart + -1 - floor(log10($formattedNumber)))) . $formattedNumber;
		return $formattedNumber;
	}


	/**
	 * right_align
	 *
	 * Returns a string aligned to the right
	 *
	 * @param		string		$str			string
	 * @param		int			$length			length
	 * @param		char		$leading		leading character
	 * @return		string						formatted string
	 */
	function right_align($str, $length, $leading = ' ') {
		$result = '';
		$len = strlen($str);
		if ($length > $len) {
			for ($i = 0; $i < $length - $len; $i++) {
				$result .= $leading;
			}
		}
	
		return $result . $str;
	}


	/**
	 * Returns a string of the exact length specified
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       string 		$_str 		string to adjust
	 				int			$_length 	length of string
					char		$_spChr		fill character
					int			$_spChrLen	length of fill character
	 * @return      string	    			re-formatted string
	 */
	function adjust_length($_str, $_len, $_spChr = '.', $_spChrLen = 3) {
		if (strlen($_str) > $_len) {
			$_str = substr($_str, 0, $_len - $_spChrLen);
	
			while (strlen($_str) < $_len) {
				$_str .= substr($_spChr, 0, 1);
			}
		} else {
			while (strlen($_str) < $_len) {
				$_str .= ' ';
			}
		}
		return $_str;
	}


	/**
	 * extract_segment_from_chunk
	 *
	 * Returns a portion of a string between two segments
	 *
	 * @param		string		$string			string to search
	 * @param		string		$beg			begining segment
	 * @param		string		$end			ending segment
	 * @return		string						string, FALSE if not found
	 */
	function extract_segment_from_chunk($string, $beg, $end) {
		if( stripos( $string, $beg )) {
			$start = stripos( $string, $beg ) + strlen( $beg );
			$len = stripos( $string, $end, $start ) - $start;
			return substr($string, $start, $len);
		}
		return '';
	}


	/**
	 * Generates string of random chars
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       int		$_len	length of string
	 * @return      string			Returns random string
	 */
	function generate_random_string($_len = 8, $_salt = 'wLpqK5Mfst3naBgvE76rHj84Uch29XYz0') {
		srand((double) microtime() * 1000000);
		$_i = 0;
		$_str = '';
		while ($_i <= ($_len -1)) {
			$_num = rand() % 33;
			$_tmp = substr($_salt, $_num, 1);
			$_str .= $_tmp;
			$_i++;
		}
		return $_str;
	}


	/**
	 * This function takes a string and returns an array with words (delimited by spaces), 
	 * also taking into account quotes, doublequotes, backticks and backslashes (for escaping stuff).
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       string		$string		the string to split
	 * @return      array		the word array
	 */	
	function parse_cli($string) {
	    $state = 'space';
	    $previous = '';     // stores current state when encountering a backslash (which changes $state to 'escaped', but has to fall back into the previous $state afterwards)
	    $out = array();     // the return value
	    $word = '';
	    $type = '';         // type of character
	    // array[states][chartypes] => actions
	    $chart = array(
	        'space'        => array('space'=>'',   'quote'=>'q',  'doublequote'=>'d',  'backtick'=>'b',  'backslash'=>'ue', 'other'=>'ua'),
	        'unquoted'     => array('space'=>'w ', 'quote'=>'a',  'doublequote'=>'a',  'backtick'=>'a',  'backslash'=>'e',  'other'=>'a'),
	        'quoted'       => array('space'=>'a',  'quote'=>'w ', 'doublequote'=>'a',  'backtick'=>'a',  'backslash'=>'e',  'other'=>'a'),
	        'doublequoted' => array('space'=>'a',  'quote'=>'a',  'doublequote'=>'w ', 'backtick'=>'a',  'backslash'=>'e',  'other'=>'a'),
	        'backticked'   => array('space'=>'a',  'quote'=>'a',  'doublequote'=>'a',  'backtick'=>'w ', 'backslash'=>'e',  'other'=>'a'),
	        'escaped'      => array('space'=>'ap', 'quote'=>'ap', 'doublequote'=>'ap', 'backtick'=>'ap', 'backslash'=>'ap', 'other'=>'ap'));
	    for ($i=0; $i<=strlen($string); $i++) {
	        $char = substr($string, $i, 1);
	        $type = array_search($char, array('space'=>' ', 'quote'=>'\'', 'doublequote'=>'"', 'backtick'=>'`', 'backslash'=>'\\'));
	        if (! $type) $type = 'other';
	        if ($type == 'other') {
	            // grabs all characters that are also 'other' following the current one in one go
	            preg_match("/[ \'\"\`\\\]/", $string, $matches, PREG_OFFSET_CAPTURE, $i);
	            if ($matches) {
	                $matches = $matches[0];
	                $char = substr($string, $i, $matches[1]-$i); // yep, $char length can be > 1
	                $i = $matches[1] - 1;
	            }else{
	                // no more match on special characters, that must mean this is the last word!
	                // the .= hereunder is because we *might* be in the middle of a word that just contained special chars
	                $word .= substr($string, $i);
	                break; // jumps out of the for() loop
	            }
	        }
	        $actions = $chart[$state][$type];
	        for($j=0; $j<strlen($actions); $j++) {
	            $act = substr($actions, $j, 1);
	            if ($act == ' ') $state = 'space';
	            if ($act == 'u') $state = 'unquoted';
	            if ($act == 'q') $state = 'quoted';
	            if ($act == 'd') $state = 'doublequoted';
	            if ($act == 'b') $state = 'backticked';
	            if ($act == 'e') { $previous = $state; $state = 'escaped'; }
	            if ($act == 'a') $word .= $char;
	            if ($act == 'w') { $out[] = $word; $word = ''; }
	            if ($act == 'p') $state = $previous;
	        }
	    }
	    if (strlen($word)) $out[] = $word;
	    return $out;
	}


	/**
	 * This function takes a string and returns everything to the left of the needle search key 
	 *
	 * @author       <>
	 * @version     1.0.0
	 * @param       string		$haystack		the string to search
	 * @param		string		$needle			the search string
	 * @return     	string		everything to the left of $needle
	 */	
	function strleft($haystack, $needle) {
		return substr($haystack, 0, strpos($haystack, $needle));
	}

?>