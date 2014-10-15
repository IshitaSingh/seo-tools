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
	 * @property string $controlId Control Id
	 *
	 * @package			PHPRum
	 * @subpackage		Validators
	 * @author			Darnell Shinbine
	 */
	class UniqueValidator extends ValidatorBase
	{
		/**
		 * previous value
		 * @var string
		 */
		private $prevValue;

		/**
		 * data source to validate against
		 * @var DataSet
		 */
		private $dataSource;


		/**
		 * UniqueValidator
		 *
		 * @param  InputBase $controlToValidate control to validate
		 * @param  string $errorMessage error message
		 * @return void
		 */
		public function __construct($errorMessage = '')
		{
			parent::__construct($errorMessage);
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
			if( $field === 'dataSource' ) {
				return $this->dataSource;
			}
			else {
				return parent::__get($field);
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
			if( $field === 'dataSource' ) {
				if($value instanceof \System\DB\DataSet) {
					$this->dataSource = $value;
				}
				else {
					throw new \System\Base\BadMemberCallException("dataSource must be type DataSet");
				}
			}
			else {
				parent::__set($field, $value);
			}
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.$this->field.' '.\System\Base\ApplicationBase::getInstance()->translator->get('must_be_unique');
			$this->prevValue = $this->dataSource[$this->field];
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			if($value === $this->prevValue) return true;

			if($this->dataSource)
			{
				foreach($this->dataSource->rows as $row)
				{
					if($row[$this->field] == $value) return false;
				}
			}
			return true;
		}
	}
?>