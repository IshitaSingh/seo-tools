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
	 * @author			Darnell Shinbine
	 */
	class URLValidator extends PatternValidator
	{
		/**
		 * URLValidator
		 *
		 * @param  string	$errorMessage	error message
		 * @return void
		 */
		public function __construct( $errorMessage = '' )
		{
			parent::__construct('`http+(s)?://[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,6}`', $errorMessage);
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.\System\Base\ApplicationBase::getInstance()->translator->get('must_be_a_valid_url');
		}
	}
?>