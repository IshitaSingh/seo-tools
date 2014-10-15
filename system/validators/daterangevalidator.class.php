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
	class DateRangeValidator extends ValidatorBase
	{
		/**
		 * min
		 * @var double
		 */
		private $minDate;

		/**
		 * max
		 * @var double
		 */
		private $maxDate;


		/**
		 * RangeValidator
		 *
		 * @param  double   $minDate		min value
		 * @param  double   $maxDate		max value
		 * @param  string $errorMessage error message
		 * @return void
		 */
		public function __construct( $minDate = null, $maxDate = null, $errorMessage = '' )
		{
			parent::__construct($errorMessage);

			$this->minDate = $minDate;
			$this->maxDate = $maxDate;
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.
					\str_replace('%x', ($this->minDate? "after ".$this->minDate : ""), 
					\str_replace('%y', ($this->maxDate? ($this->minDate? " and ":"")."before ".$this->maxDate : ""), 
					\System\Base\ApplicationBase::getInstance()->translator->get('must_be_within_the_range_of_x_and_y')));
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			if($this->controlToValidate)
			{
				$valid_date = strtotime($this->controlToValidate->value)!== false;
				$min = $this->minDate? (strtotime($this->controlToValidate->value) >= strtotime($this->minDate)) : true;
				$max = $this->maxDate? (strtotime($this->controlToValidate->value) <= strtotime($this->maxDate)) : true;
				return !$this->controlToValidate->value || ( $valid_date && $min && $max );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("no control to validate");
			}
		}
	}
?>