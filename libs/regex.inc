<?php
	// +----------------------------------------------------------------------+
	// | PHP version 5.1.x                                                    |
	// +----------------------------------------------------------------------+
	// | Desc:  Common Regular Expression Patterns                            |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	// | Version: 1.0.2                                                       |
	// | Modified: Dec 19, 2005                                               |
	// +----------------------------------------------------------------------+
	// | Author(s): Darnell Shinbine <gasolene@gmail.com>                     |
	// |                                                                      |
	// +----------------------------------------------------------------------+
	//
	// $Id$

	/* version history
	 * 1.0.2 - fixed bug - missing delimeter
	 * 1.0.3 - added password validation
	 */

	// Pattern Syntax -- Describes PCRE Syntax

	// domain (example: google.ca)
	define( '__REGEX_DOMAIN__',		'^[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,6}^' );

	// url (example: http://www.google.ca)
	define( '__REGEX_URL__',		'`(ftp|http)+(s)?://[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,6}`' );

	// hex value (example: #FFFFFF)
	define( '__REGEX_HEX__',		'#[0-9a-fA-F]{6}$^' );

	// email (example: T5T 5T5)
	define( '__REGEX_POSTAL__',		'^[a-zA-Z][0-9][a-zA-Z]( )?[0-9][a-zA-Z][0-9]$^' );

	// email (example: 90210)
	define( '__REGEX_ZIP__',		'^[0-9]{5,9}$^' );

	// email (example: jdoe@gmail.com)
	define( '__REGEX_EMAIL__',		'^[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,6}$^' );

	// phone number (example: 1-800-555-5555)
	define( '__REGEX_PHONE__',		'^[1]?[- ]?[(]?[2-9]{1}[0-9]{2}[) -]{0,2}' . '[0-9]{3}[- ]?' . '[0-9]{4}[ ]?' . '((x|ext)[.]?[ ]?[0-9]{1,5})?$^' );

	// password (password must be between 6 and 32 characters and contain at least one numeric digit.)
	define( '__REGEX_PASSWD__',		'^(?=.*\d).{6,32}$^' );
?>