<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Security;


	/**
	 * Provides application wide authentication
	 *
	 * @package			PHPRum
	 * @subpackage		Security
	 * @author			Darnell Shinbine
	 */
	abstract class CredentialBase
	{
		/**
		 * credential data
		 * @var array
		 */
		protected $credential = array();

		/**
		 * Constructor
		 * 
		 * @param  array	$credential		credential data
		 */
		final public function __construct($credential)
		{
			$this->credential = $credential;
		}


		/**
		 * authenticates password based on the credential
		 *
		 * @param   string	$username	specifies username
		 * @param   string	$password	specifies password
		 * @return  AuthenticationStatus
		 */
		abstract public function authenticate( $username, $password );
	}
?>