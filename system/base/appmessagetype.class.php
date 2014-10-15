<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Represents an application message type
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class AppMessageType
	{
		private $flags;

		private function __construct($flags)
		{
			$this->flags = (int)$flags;
		}

		/**
		 * Specifies the message type as Notice
		 * @return AppMessageType
		 */
		public static function Info() {return new AppMessageType(0);}

		/**
		 * Specifies the message type as Success
		 * @return AppMessageType
		 */
		public static function Success() {return new AppMessageType(1);}

		/**
		 * Specifies the message type as Fail
		 * @return AppMessageType
		 */
		public static function Fail() {return new AppMessageType(2);}

		/**
		 * Specifies the message type as Notice
		 * @return AppMessageType
		 */
		public static function Warning() {return new AppMessageType(4);}


		/**
		 * Returns a string representing the type
		 * @return string
		 */
		final public function __toString()
		{
			switch($this->flags)
			{
				case 0: return "Info"; break;
				case 1: return "Success"; break;
				case 2: return "Fail"; break;
				case 4: return "Warning"; break;
			}
		}
	}
?>