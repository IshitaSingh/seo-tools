<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web;


	/**
	 * Represents an application forward method
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class ForwardMethodType
	{
		private $flags;

		private function __construct($flags)
		{
			$this->flags = (int)$flags;
		}

		/**
		 * Specifies the application forward method as URI
		 * @return ForwardMethodType
		 */
		public static function URI() {return new ForwardMethodType(1);}

		/**
		 * Specifies the application forward method as Request
		 * @return ForwardMethodType
		 */
		public static function Request() {return new ForwardMethodType(2);}
	}
?>