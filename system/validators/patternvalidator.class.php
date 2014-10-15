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
	class PatternValidator extends ValidatorBase
	{
		/**
		 * pattern
		 * @var string
		 */
		private $pattern;


		/**
		 * PatternValidator
		 *
		 * @param  string	$pattern		pattern
		 * @param  string	$errorMessage	error message
		 * @return void
		 */
		public function __construct( $pattern, $errorMessage = '' )
		{
			parent::__construct($errorMessage);

			$this->pattern = (string) $pattern;
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.str_replace('%n', $this->pattern, \System\Base\ApplicationBase::getInstance()->translator->get('must_match_the_pattern'));
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			return !$value || (0 !== preg_match($this->pattern, $value));
		}
	}
?>