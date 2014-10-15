<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Security;


	/**
	 * Provides application wide authentication for Web Services using stateless tokens
	 *
	 * @package			PHPRum
	 * @subpackage		Security
	 * @author			Darnell Shinbine
	 */
	class WebServiceAuthentication extends Authentication
	{
		/**
		 * specifies the auth secret
		 * @var string
		 */
		static protected $authSecret = null;

		/**
		 * returns true if user authenticated
		 *
		 * @return  bool
		 */
		public static function authenticated()
		{
			if( WebServiceAuthentication::isAuthUserSet() )
			{
				if( WebServiceAuthentication::authenticateSecret( WebServiceAuthentication::getAuthUser(), WebServiceAuthentication::getAuthSecret() ))
				{
					return Authentication::authorize(WebServiceAuthentication::getAuthUser());
				}
			}
			return false;
		}


		/**
		 * authenticate secret
		 * 
		 * @param   string	$uid		unique value representing user
		 * @param   string	$secret		secret
		 * @param   int     $expires	time in seconds until token expires
		 * @return void
		 */
		public static function authenticateSecret($uid, $secret, $expires = 0)
		{
			$timestamp = substr($secret, 40);
			$hash = substr($secret, 0, 40);
			$salt = $_SERVER["REMOTE_ADDR"].$uid.$timestamp;
			if( 0 === $expires || $timestamp + $expires > time() )
			{
				if( $hash === Authentication::generateHash('sha1', \Rum::config()->authenticationFormsSecret, $salt ))
				{
					Authentication::$identity = $uid;
					self::$authSecret = $secret;
					return true;
				}
			}
			return false;
		}


		/**
		 * sets auth user
		 * 
		 * @param  string $uid authenticated user
		 * @return void
		 */
		public static function setAuthUser( $uid )
		{
			//Authentication::$identity = $uid;
			$timestamp = time();
			$salt = $_SERVER["REMOTE_ADDR"].$uid.$timestamp;
			$secret = Authentication::generateHash('sha1', \Rum::config()->authenticationFormsSecret, $salt) . $timestamp;

			Authentication::$identity = $uid;
			self::$authSecret = $secret;
		}


		/**
		 * gets auth user
		 * 
		 * @return  string
		 */
		public static function getAuthUser()
		{
			return Authentication::$identity;
		}


		/**
		 * gets auth secret
		 *
		 * @return  string
		 */
		public static function getAuthSecret()
		{
			return self::$authSecret;
		}


		/**
		 * returns true if auth is set
		 * 
		 * @return  bool
		 */
		public static function isAuthUserSet()
		{
			return (bool)Authentication::$identity;
		}


		/**
		 * perform sign out (does not end session)
		 *
		 * @return  void
		 */
		public static function signout()
		{
			Authentication::$identity = null;
		}
	}
?>