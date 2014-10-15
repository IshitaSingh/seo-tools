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
	class AuthenticationStatus
	{
		/**
		 * invalid credentials
		 * @var bool
		 */
		private $invalidCredentials = false;

		/**
		 * account disabled
		 * @var bool
		 */
		private $disabled = false;

		/**
		 * account locked out
		 * @var bool
		 */
		private $lockedOut = false;

		/**
		 * ???
		 * @var bool
		 * /
		private $loginAttemptsExceeded = false;


		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct($invalidCredentials = false, $disabled = false, $lockedOut = false)
		{
			$this->invalidCredentials = $invalidCredentials;
			$this->disabled = $disabled;
			$this->lockedOut = $lockedOut;
		}


		/**
		 * returns true if the account is authenticated
		 *
		 * @return  bool
		 */
		public function authenticated()
		{
			return !$this->invalidCredentials && !$this->disabled && !$this->lockedOut;
		}


		/**
		 * returns true if invalid credentials supplied
		 *
		 * @return  bool
		 */
		public function invalidCredentials()
		{
			return $this->invalidCredentials;
		}


		/**
		 * returns true if the account is locked out
		 *
		 * @return  bool
		 */
		public function disabled()
		{
			return $this->disabled;
		}


		/**
		 * returns true if the account is locked out
		 *
		 * @return  bool
		 */
		public function lockedOut()
		{
			return $this->lockedOut;
		}
	}
?>