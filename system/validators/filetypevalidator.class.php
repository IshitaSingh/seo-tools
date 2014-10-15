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
	class FileTypeValidator extends ValidatorBase
	{
		/**
		 * array of file types, leave empty for all types
		 * @var array
		 */
		private $types			= array();


		/**
		 * FileTypeValidator
		 *
		 * @param  array	$types array of types
		 * @param  string	$errorMessage error message
		 * @return void
		 */
		public function __construct( array $types = array(), $errorMessage = '' )
		{
			parent::__construct($errorMessage);

			$this->types = $types;
		}


		/**
		 * adds a file type to the list of allowed types
		 *
		 * @param  string		$type			file type
		 * @return void
		 */
		public function addType( $type )
		{
			$this->types[] = (string)$type;
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.\System\Base\ApplicationBase::getInstance()->translator->get('is_not_a_valid_file_type');
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			if( array_search( $value['type'], $this->types ) === false )
			{
				return false;
			}
			return true;
		}
	}
?>