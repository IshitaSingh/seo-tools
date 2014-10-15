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
	 * @property string $fieldToCompare field to compare
	 * @property string $operator operator
	 *
	 * @package			PHPRum
	 * @subpackage		Validators
	 * @author			Darnell Shinbine
	 */
	class CompareValidator extends ValidatorBase
	{
		/**
		 * field to compare
		 * @var InputBase
		 */
		protected $fieldToCompare;

		/**
		 * compare operator
		 * @var string
		 */
		protected $operator = '==';


		/**
		 * CompareValidator
		 *
		 * @param  string $fieldToCompare field to compare
		 * @param  string $operator operator
		 * @param  string $errorMessage error message
		 * @return void
		 */
		public function __construct($fieldToCompare, $operator = '==', $errorMessage = '')
		{
			parent::__construct($errorMessage);

			$this->fieldToCompare = $fieldToCompare;
			$this->operator = $operator;
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
			if( $field === 'fieldToCompare' ) {
				return $this->fieldToCompare;
			}
			else {
				return parent::__get($field);
			}
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * compares the passed values
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function compare($value1, $value2)
		{
			$this->setErrMsg($value2);

			if($this->operator=='==' || $this->operator=='=')
			{
				return ($value1 == $value2);
			}
			elseif($this->operator=='>')
			{
				return ($value1 > $value1);
			}
			elseif($this->operator=='<')
			{
				return ($value1 < $value1);
			}
			elseif($this->operator=='>=')
			{
				return ($value1 >= $value1);
			}
			elseif($this->operator=='<=')
			{
				return ($value1 <= $value1);
			}
			elseif($this->operator=='<>' || $this->operator=='!=')
			{
				return ($value1 <> $value1);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("CompareValidator operator `{$this->operator}` is not supported");
			}
		}


		/**
		 * sets the default error message
		 *
		 * @return void
		 */
		private function setErrMsg($valueToCompare)
		{
			if(!$this->errorMessage)
			{
				if($this->operator=='==' || $this->operator=='=')
				{
					$this->errorMessage = $this->label . ' ' . str_replace('%n', $valueToCompare, \System\Base\ApplicationBase::getInstance()->translator->get('must_be_equal_to', 'must be equal to %n'));
				}
				elseif($this->operator=='>')
				{
					$this->errorMessage = $this->label . ' ' . str_replace('%n', $valueToCompare, \System\Base\ApplicationBase::getInstance()->translator->get('must_be_greater_than', 'must be greater than %n'));
				}
				elseif($this->operator=='<')
				{
					$this->errorMessage = $this->label . ' ' . str_replace('%n', $valueToCompare, \System\Base\ApplicationBase::getInstance()->translator->get('must_be_less_than', 'must be less than %n'));
				}
				elseif($this->operator=='>=')
				{
					$this->errorMessage = $this->label . ' ' . str_replace('%n', $valueToCompare, \System\Base\ApplicationBase::getInstance()->translator->get('must_be_greater_than_or_equal_to', 'must be greater than or equal to %n'));
				}
				elseif($this->operator=='<=')
				{
					$this->errorMessage = $this->label . ' ' . str_replace('%n', $valueToCompare, \System\Base\ApplicationBase::getInstance()->translator->get('must_be_less_than_or_equal_to', 'must be less than or equal to %n'));
				}
				elseif($this->operator=='<>' || $this->operator=='!=')
				{
					$this->errorMessage = $this->label . ' ' . str_replace('%n', $valueToCompare, \System\Base\ApplicationBase::getInstance()->translator->get('must_be_not_equal_to', 'must be not equal to %n'));
				}
			}
		}
	}
?>