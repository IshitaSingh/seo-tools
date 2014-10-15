<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Represents an application state
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class AppState
	{
		private $flags;

		private function __construct($flags)
		{
			$this->flags = (int)$flags;
		}

		/**
		 * Specifies the application state as On
		 * @return AppState
		 */
		public static function On() {return new AppState(1);}

		/**
		 * Specifies the application state as Debug
		 * @return AppState
		 */
		public static function Debug() {return new AppState(2);}
	}
?>