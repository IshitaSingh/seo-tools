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
	 * @property string $event event
	 * @property string $callback callback
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class EventHandlerBase
	{
		/**
		 * event
		 * @var string
		 */
		private $event;


		/**
		 * callback
		 * @var string
		 */
		private $callback;


		/**
		 * Constructor
		 *
		 * @param  string $event event
		 * @param  string $callback call back
		 * @return void
		 */
		protected function __construct($event, $callback)
		{
			$this->event = (string)$event;
			$this->callback = (string)$callback;
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
			if( $field === 'callback' ) {
				return $this->callback;
			}
			elseif( $field === 'event' ) {
				return $this->event;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * raise event
		 *
		 * @param  object	$sender		Sender object
		 * @param  array	$args		optional args
		 * @return void
		 */
		final public function raise(&$sender, array $args = array())
		{
			if($this->callback)
			{
				eval($this->callback.'($sender, new \System\Base\EventArgs($args));');
			}
		}
	}
?>