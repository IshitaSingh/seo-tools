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
	class MatchValidator extends CompareValidator
	{
		/**
		 * MatchValidator
		 *
		 * @param  InputBase $controlToMatch control to match
		 * @param  string $errorMessage error message
		 * @return void
		 */
		public function __construct(\System\Web\WebControls\InputBase &$controlToMatch, $errorMessage = '' )
		{
			trigger_error("MatchValidator is deprecated, use CompareValidator instead", E_USER_DEPRECATED);
			parent::__construct($controlToMatch->dataField, '==', $errorMessage);
		}
	}
?>