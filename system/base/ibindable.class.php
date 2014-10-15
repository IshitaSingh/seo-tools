<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Provides the interface for bindable objects
	 * 
	 * @package			PHPRum
	 * @subpackage		Mail
	 * @author			Darnell Shinbine
	 */
	interface IBindable extends \Countable, \ArrayAccess
	{
		/**
		 * refreshes data from source
		 *
		 * @return void
		 */
		public function refresh();

		/**
		 * writes data to source
		 *
		 * @return void
		 */
		public function save();

		/**
		 * converts object into array
		 *
		 * @return array
		 */
		public function toArray();

		/**
		 * returns fields as array
		 *
		 * @return array
		 */
		public function fields();
	}
?>