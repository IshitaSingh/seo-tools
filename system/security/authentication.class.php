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
	class Authentication
	{
		/**
		 * authenticated id
		 * @var string
		 */
		static public $identity;

		/**
		 * sets the log level, default HighLevelEvents
		 * @var int
		 */
		static public $logLevel = 2;

		/**
		 * specifies whether the current controller is protected
		 * @var bool
		 */
		static protected $isProtected = null;


		/**
		 * Constructor
		 *
		 * @return void
		 */
		private function __construct() {}


		/**
		 * authenticate user/password based on credentials (does not set cookie)
		 *
		 * @param   string	$username	specifies username
		 * @param   string	$password	specifies password
		 * @return  AuthenticationStatus		returns AuthenticationStatus representing attempt status
		 */
		public static function authenticate( $username, $password ) {

			// Check IP Address
			if( Authentication::checkIPAddress() ) {

				$status = self::getAuthStatus($username, $password);

				if( $status->authenticated() )
				{
					Authentication::$identity = $username;

					if(self::$logLevel>=AuthenticationLogLevel::AllEvents())
					{
						\Rum::log("User `{$username}` logged in from IP {$_SERVER["REMOTE_ADDR"]}", 'security');
					}
				}
				else
				{
					if(self::$logLevel>=AuthenticationLogLevel::HighLevelEvents())
					{
						if( $status->invalidCredentials() )
						{
							\Rum::log("Failed login attempt for user `{$username}` from IP {$_SERVER["REMOTE_ADDR"]}", 'security');
						}
						elseif( $status->disabled() )
						{
							\Rum::log("Blocked login for suspended user `{$username}` from IP {$_SERVER["REMOTE_ADDR"]}", 'security');
						}
						elseif( $status->lockedOut() )
						{
							\Rum::log("Blocked login due to too many failed login attempts for user `{$username}` from IP {$_SERVER["REMOTE_ADDR"]}", 'security');
						}
					}
				}

				return $status;
			}
			else
			{
				\Rum::log("Blocked login from restricted IP for user `{$username}` from IP {$_SERVER["REMOTE_ADDR"]}", 'security');
			}

			// Invalid credentials
			return new AuthenticationStatus(true);
		}


		/**
		 * authenticate user based on credentials (does not set cookie)
		 *
		 * @param   string	$username	specifies username
		 * @return  bool
		 */
		public static function authorize( $username ) {

			// Authenticate using credentials users
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsUsers as $credential ) {
				$credential = new UserCredential($credential);

				if( $credential->authorize( $username ) ) {
					return true;
				}
			}

			// Authenticate using credentials tables
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsTables as $credential ) {
				$credential = new TableCredential($credential);

				if( $credential->authorize( $username ) ) {
					return true;
				}
			}

			// Authenticate using credentials tables
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsLDAP as $credential ) {
				$credential = new LDAPCredential($credential);

				if( $credential->authorize( $username ) ) {
					return true;
				}
			}

			// Authenticate using custom objects
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsCustom as $credential ) {
				$credentialObject = new $credential["class"]($credential);

				if( $credentialObject->authorize( $username ) ) {
					return true;
				}
			}

			// Invalid credentials
			return false;
		}


		/**
		 * returns true if user authenticated
		 *
		 * @return  bool
		 */
		public static function authenticated()
		{
			// Basic Authentication
			if( \System\Security\Authentication::getAuthMethod() === 'basic' )
			{
				$authenticated = \System\Security\BasicAuthentication::authenticated();
			}
			// Forms Authentication
			elseif( \System\Security\Authentication::getAuthMethod() === 'forms' )
			{
				$authenticated = \System\Security\FormsAuthentication::authenticated();
			}
			// No Authentication
			else
			{
				$authenticated = true;
			}

			// Authenticated
			if( $authenticated )
			{
				return true;
			}
			// Not Authenticated (Guest)
			else
			{
				if( \System\Security\Authentication::isProtected( \System\Web\WebApplicationBase::getInstance()->requestHandler->controllerId ))
				{
					// Not Authenticated
					return false;
				}
				else
				{
					// No Authentication Needed
					return true;
				}
			}
		}


		/**
		 * returns true if user authenticated
		 *
		 * @return  bool
		 */
		public static function authorized()
		{
			if( \System\Security\Authentication::isProtected( \System\Web\WebApplicationBase::getInstance()->requestHandler->controllerId ))
			{
				$denyRoles = array();
//				$allowRoles = array();

				if(\System\Web\WebApplicationBase::getInstance()->requestHandler->denyRoles)
				{
					$denyRoles = \System\Web\WebApplicationBase::getInstance()->requestHandler->denyRoles;
				}
				else
				{
					if(isset(\Rum::config()->authorizationPages[\System\Web\WebApplicationBase::getInstance()->requestHandler->controllerId]["deny"]))
					{
						$denyRoles = \Rum::config()->authorizationPages[\System\Web\WebApplicationBase::getInstance()->requestHandler->controllerId]["deny"];
					}
					else
					{
						$denyRoles = \Rum::config()->authorizationDeny;
					}
				}

				$allowRoles = \System\Web\WebApplicationBase::getInstance()->requestHandler->getRoles();
				if(!$allowRoles)
				{
					if(isset(\Rum::config()->authorizationPages[\System\Web\WebApplicationBase::getInstance()->requestHandler->controllerId]["allow"]))
					{
						$allowRoles = \Rum::config()->authorizationPages[\System\Web\WebApplicationBase::getInstance()->requestHandler->controllerId]["allow"];
					}
					else
					{
						$allowRoles = \Rum::config()->authorizationAllow;
					}
				}

				// Check if roles have been defined
				if( $denyRoles )
				{
					// Grant access to these roles
					foreach($denyRoles as $role)
					{
						if(Roles::isUserInRole($role))
						{
							// Not Authorized
							return false;
						}
					}
				}
				if( $allowRoles )
				{
					// Grant access to these roles
					foreach($allowRoles as $role)
					{
						if(Roles::isUserInRole($role))
						{
							// Authorized
							return true;
						}
					}

					// Not Authorized
					return false;
				}

				// No Roles Defined
				return true;
			}
			else
			{
				// No Authorization Needed
				return true;
			}
		}


		/**
		 * redirct user to login
		 *
		 * @return  void
		 */
		public static function redirectToLogin()
		{
			// Basic Authentication
			if( \System\Security\Authentication::getAuthMethod() === 'basic' )
			{
				// Send HTTP authenticate headers
				\System\Security\BasicAuthentication::sendAuthHeaders();
			}
			// Forms Authentication
			elseif( \System\Security\Authentication::getAuthMethod() === 'forms' )
			{
				// Redirect to login page
				\System\Security\FormsAuthentication::redirectToLoginPage();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("No authentication method has been defined");
			}
		}


		/**
		 * returns authentication method
		 *
		 * @return  string
		 */
		public static function getAuthMethod() {
			return strtolower( \System\Base\ApplicationBase::getInstance()->config->authenticationMethod );
		}


		/**
		 * returns true if controller is protected (requires authenctication to process)
		 *
		 * @param   string		id of controller
		 * @return  bool
		 */
		public static function isProtected( $controllerId )
		{
			if(is_null(Authentication::$isProtected))
			{
				Authentication::$isProtected = Authentication::checkProtected($controllerId);
			}

			return Authentication::$isProtected;
		}


		/**
		 * generates a hash based on the password
		 *
		 * @param   string	$passwordFormat			password format
		 * @param   string	$passwordToEncrypt		password to encrypt
		 * @param   string	$salt					salt
		 * @return  string
		 */
		final public static function generateHash( $passwordFormat, $passwordToEncrypt, $salt )
		{
			if( $passwordFormat === 'md5' )
			{
				return md5( (string) $passwordToEncrypt . $salt );
			}
			elseif( $passwordFormat === 'sha1' )
			{
				return sha1( (string) $passwordToEncrypt . $salt );
			}
			elseif( $passwordFormat === 'crypt' )
			{
				return crypt( (string) $passwordToEncrypt, $salt );
			}
			else
			{
				return $passwordToEncrypt;
			}
		}


		/**
		 * generates a salt
		 *
		 * @param   string	$len		length of salt
		 * @return  string
		 */
		final public static function generateSalt( $len = 15 )
		{
			$characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$i = 0;
			$salt = '';
			do {
				$salt .= $characterList{mt_rand(0,strlen($characterList)-1)};
				$i++;
			} while ($i < $len);
			return $salt;
		}


		/**
		 * perform sign out (does not end session)
		 *
		 * @return  void
		 */
		public static function signout()
		{
			if(Authentication::$identity)
			{
				\Rum::log("User `".Authentication::$identity."` logged out from IP {$_SERVER["REMOTE_ADDR"]}", 'security');
			}

			// Basic Authentication
			if( \System\Security\Authentication::getAuthMethod() === 'basic' )
			{
				BasicAuthentication::signout();
			}
			// Forms Authentication
			elseif( \System\Security\Authentication::getAuthMethod() === 'forms' )
			{
				FormsAuthentication::signout();
			}
		}


		/**
		 * returns true if controller is protected (requires authenctication to process)
		 *
		 * @param   string		id of controller
		 * @return  bool
		 */
		private static function checkProtected( $controllerId )
		{
			if(\System\Base\ApplicationBase::getInstance()->requestHandler instanceof \System\Web\Services\WebServiceBase) return false;

			// If page === loginpage && authMethod === forms
			if( strtolower( $controllerId ) === strtolower( \System\Base\ApplicationBase::getInstance()->config->authenticationFormsLoginPage ) &&
				\System\Base\ApplicationBase::getInstance()->config->authenticationMethod === 'forms' ) {
				return false;
			}

			$deny  = \System\Base\ApplicationBase::getInstance()->config->authenticationDeny;
			$allow = \System\Base\ApplicationBase::getInstance()->config->authenticationAllow;

			// If deny list does not contain 'all|*' and
			// deny list does not contain page
			if( !in_array( 'all', $deny ) &&
				!in_array( '*', $deny ) &&
				!in_array( strtolower( $controllerId ), $deny )) {
				return false;
			}

			// If allow list contains 'all|*' or
			// allow list contains page
			if( in_array( 'all', $allow ) ||
				in_array( '*', $allow ) ||
				in_array( strtolower( $controllerId ), $allow )) {
				return false;
			}

			// If page is error page
			foreach( \System\Base\ApplicationBase::getInstance()->config->errors as $err => $page ) {
				if( strtolower( $page ) === strtolower( $controllerId )) {
					return false;
				}
			}

			return true;
		}


		/**
		 * returns true if remote client IP address is valid
		 *
		 * @return  bool
		 */
		private static function checkIPAddress()
		{
			$ip_array = explode(',', \System\Base\ApplicationBase::getInstance()->config->authenticationRestrict);

			if( count( $ip_array ) > 0 && !empty( $ip_array[0] ))
			{
				foreach( $ip_array as $ip_address )
				{
					if( strpos( $_SERVER["REMOTE_ADDR"], trim( $ip_address )) === 0 || $_SERVER["REMOTE_ADDR"]=="::1" )
					{
						return true;
					}
				}

				return false;
			}

			return true;
		}


		/**
		 * return authentication status
		 *
		 * @param   string	$username	specifies username
		 * @param   string	$password	specifies password
		 * @return  AuthenticationStatus		returns AuthenticationStatus representing attempt status
		 */
		private static function getAuthStatus( $username, $password ) {

			// Authenticate using user credentials
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsUsers as $credential ) {
				$credential = new UserCredential($credential);

				$status = $credential->authenticate( $username, $password );
				if( !$status->invalidCredentials() ) {
					return $status;
				}
			}

			// Authenticate using credentials tables
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsTables as $credential ) {
				$credential = new TableCredential($credential);

				$status = $credential->authenticate( $username, $password );
				if( !$status->invalidCredentials() ) {
					return $status;
				}
			}

			// Authenticate using LDAP
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsLDAP as $credential ) {
				$credential = new LDAPCredential($credential);

				$status = $credential->authenticate( $username, $password );
				if( !$status->invalidCredentials() ) {
					return $status;
				}
			}

			// Authenticate using custom objects
			foreach( \System\Base\ApplicationBase::getInstance()->config->authenticationCredentialsCustom as $credential ) {
				
				$credentialObject = new $credential["class"]($credential);

				$status = $credentialObject->authenticate( $username, $password );
				if( !$status->invalidCredentials() ) {
					return $status;
				}
			}

			// Invalid credentials
			return new AuthenticationStatus(true);
		}
	}
?>