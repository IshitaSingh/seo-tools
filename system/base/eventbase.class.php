<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Provides basic event handling
	 *
	 * @property string $name event name
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class EventBase
	{
		/**
		 * callback
		 * @var string
		 */
		private $name;


		/**
		 * Constructor
		 *
		 * @param  string	$name			event name
		 * @return void
		 */
		protected function __construct($name)
		{
			$this->name = (string)$name;
		}


		/**
		 * __get
		 *
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'name' ) {
				return $this->name;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}
	}
?>