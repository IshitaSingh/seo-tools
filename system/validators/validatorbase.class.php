<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Validators;


	/**
	 * Provides basic validation for web controls
	 *
	 * @property string $field field name to validate
	 * @property string $label field label
	 * @property string $errorMessage error message
	 *
	 * @package			PHPRum
	 * @subpackage		Validators
	 * @author			Darnell Shinbine
	 */
	abstract class ValidatorBase
	{
		/**
		 * field name to validate
		 * @var string
		 */
		protected $field;

		/**
		 * field label
		 * @var string
		 */
		protected $label;

		/**
		 * error message
		 * @var string
		 */
		protected $errorMessage;


		/**
		 * ValidatorBase
		 *
		 * @param  string $errorMessage error message
		 * @return void
		 */
		public function __construct($errorMessage = '')
		{
			$this->errorMessage = (string)$errorMessage;
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
			if( $field === 'field' ) {
				return $this->field;
			}
			elseif( $field === 'errorMessage' ) {
				return $this->errorMessage;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * __set
		 *
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return mixed
		 * @ignore
		 */
		public function __set( $field, $value ) {
			if( $field === 'field' ) {
				$this->field = (string)$value;
			}
			elseif( $field === 'errorMessage' ) {
				$this->errorMessage = (string)$value;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * called when all controls are loaded
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function load()
		{
			// onLoad event
			$this->label = $this->label?$this->label:ucwords(str_replace('_',' ',$this->field));
			$this->onLoad();
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad() {}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		abstract public function validate($value);
	}
?>