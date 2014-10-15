<?php
	/**
	 * @package SEOWerx
	 */
	namespace SEOWerx;


	/**
	 * Provides basic validation for web controls
	 *
	 * @property string $controlId Control Id
	 *
	 * @package			PHPRum
	 * @subpackage		Validators
	 * @author			Darnell Shinbine
	 */
	class PartOfDomainValidator extends \System\Validators\ValidatorBase
	{
		/**
		 * domain
		 * @var type string
		 */
		private $domain;


		/**
		 * CompareValidator
		 *
		 * @param  string $fieldToCompare field to compare
		 * @param  string $operator operator
		 * @param  string $errorMessage error message
		 * @return void
		 */
		public function __construct($domain, $errorMessage = '')
		{
			parent::__construct($errorMessage);

			$this->domain = $domain;
		}

		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.\System\Base\ApplicationBase::getInstance()->translator->get('must_be_part_of_domain');
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			return substr($value, strpos($value, '@')+1) == str_replace('www.', '', $this->domain);
		}
	}
?>