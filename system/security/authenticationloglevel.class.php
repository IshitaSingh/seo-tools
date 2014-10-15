<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Security;


	/**
	 * Represents an AuthenticationLogLevel type
	 *
	 * @package			PHPRum
	 * @subpackage		Security
	 * @author			Darnell Shinbine
	 */
	final class AuthenticationLogLevel
	{
		private $flags;

		private function __construct($flags)
		{
			$this->flags = (int)$flags;
		}

		/**
		 * Specifies the LogLevel is NoLogging
		 * @return AuthenticationLogLevel
		 */
		public static function NoLogging() {return 1;}

		/**
		 * Specifies the LogLevel is HighLevelEvents
		 * @return AuthenticationLogLevel
		 */
		public static function HighLevelEvents() {return 2;}

		/**
		 * Specifies the LogLevel is NoLogging
		 * @return AuthenticationLogLevel
		 */
		public static function AllEvents() {return 4;}
	}
?>