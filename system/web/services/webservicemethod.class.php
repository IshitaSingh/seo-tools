<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\Services;


	/**
	 * This class represents a remote procedure call
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class WebServiceMethod
	{
		private $function;
		private $parameters;

		/**
		 * Constructor
		 *
		 * @param   string		$function	function callback
		 * @return  void
		 */
		public function __construct($function)
		{
			$this->setFunction($function);
		}

		/**
		 * set function
		 *
		 * @param   string		$function	function callback
		 * @return  void
		 */
		public function setFunction($function)
		{
			$this->function = $function;
		}

		/**
		 * set parameters
		 *
		 * @param   string		$name		parameter name
		 * @param   string		$type		parameter type
		 * @return  void
		 */
		public function setParameters($name, $type)
		{
			$this->parameters[$name] = $type;
		}

		/**
		 * return the function callback
		 *
		 * @return  array
		 */
		public function getFunction()
		{
			return $this->function;
		}

		/**
		 * return all parameters
		 *
		 * @return  array
		 */
		public function getParameters()
		{
			return $this->parameters;
		}
	}
?>