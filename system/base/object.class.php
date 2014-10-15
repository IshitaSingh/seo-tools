<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * This class represents a core object from which child objects can be constructed
	 * this partial class implimentation provides basic event and runtime assignment support
	 *
	 * @property		EventCollection		$events Collection of events
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class Object
	{
		/**
		 * Collection of events
		 * @var EventCollection
		 */
		private $_events;

		/**
		 * Collection of runtime methods
		 * @var array
		 */
		private $_methods = array();

		/**
		 * factory
		 * allocates a new object
		 */
		static public function create() {
			$class = get_called_class();
			return new $class();
		}

		/**
		 * attach an method at runtime
		 * 
		 * @param string $name
		 * @param function $function
		 * @param string $scope
		 */
		public function attachFunction( $name, $function, $scope = null ) {
			$this->_methods[$name] = \Closure::bind( $function, $this, $scope?$scope:get_class($this));
		}

		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'events' ) {
				return $this->_getEvents();
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}

		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @param  string	$value		value of field
		 * @return void
		 * @ignore
		 */
		public function __set( $field, $value ) {
			throw new \System\Base\BadMemberCallException("call to undefined property {$field} in ".get_class($this));
		}

		/**
		 * invokes a dynamic runtime method
		 *
		 * @param  string   $function   name of the method
		 * @param  array	$args	   array of arguments
		 * @return mixed
		 * @ignore
		 */
		public function __call( $function, $args ) {
			if(is_callable($this->_methods[$function])) {
				return call_user_func_array($this->_methods[$function], $args);
			}
			else {
				throw new \System\Base\BadMethodCallException("call to undefined method {$function} in ".get_class($this));
			}
		}

		/**
		 * convert object into string
		 * @return string
		 */
		public function __toString() {
			return serialize($this);
		}

		/**
		 * get events
		 *
		 * @return  EventCollection
		 */
		private function _getEvents() {
			if(!$this->_events) {
				$this->_events = new \System\Base\EventCollection();
			}
			return $this->_events;
		}
	}
?>