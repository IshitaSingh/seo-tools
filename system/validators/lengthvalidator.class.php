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
	class LengthValidator extends ValidatorBase
	{
		/**
		 * min
		 * @var double
		 */
		private $min;

		/**
		 * max
		 * @var double
		 */
		private $max;


		/**
		 * LengthValidator
		 *
		 * @param  double   $min		min value
		 * @param  double   $max		max value
		 * @param  string $errorMessage error message
		 * @return void
		 */
		public function __construct( $min, $max, $errorMessage = '' )
		{
			parent::__construct($errorMessage);

			$this->min = (double) $min;
			$this->max = (double) $max;
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.str_replace('%x', $this->min, str_replace('%y', $this->max, \System\Base\ApplicationBase::getInstance()->translator->get('must_be_between_x_to_y_characters')));
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			return !$value || (strlen($value) >= $this->min && \strlen($value) <= $this->max);
		}
	}
?>