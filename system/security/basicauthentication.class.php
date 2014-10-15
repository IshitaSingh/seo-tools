<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Security;


	/**
	 * Provides application wide authentication using Basic HTTP Headers
	 *
	 * @package			PHPRum
	 * @subpackage		Security
	 * @author			Darnell Shinbine
	 */
	class BasicAuthentication extends Authentication
	{
		/**
		 * returns true if user authenticated
		 *
		 * @return  bool
		 */
		public static function authenticated()
		{
			if( BasicAuthentication::isAuthUserSet() )
			{
				if( Authentication::authenticate( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] )->authenticated() )
				{
					if( Authentication::authorize( $_SERVER['PHP_AUTH_USER'] ))
					{
						Authentication::$identity = BasicAuthentication::getAuthUser();
						return true;
					}
				}
			}
			return false;
		}


		/**
		 * send auth http headers
		 * 
		 * @return  void
		 */
		public static function sendAuthHeaders()
		{
			if( \Rum::config()->authenticationRequireSSL && \Rum::config()->protocol <> 'https' && !isset($GLOBALS["__DISABLE_HEADER_REDIRECTS__"]))
			{
				// redirect to secure server (forward session)
				$url = 'https://' . __NAME__ . (__SSL_PORT__<>443?':'.__SSL_PORT__:'') . \System\Web\WebApplicationBase::getInstance()->getPageURI('', array( \System\Web\WebApplicationBase::getInstance()->session->sessionName => \System\Web\WebApplicationBase::getInstance()->session->sessionId ));

				// write and close session
				\System\Web\WebApplicationBase::getInstance()->session->close();
				\System\Web\HTTPResponse::redirect($url);
			}

			\System\Web\HTTPResponse::addHeader('WWW-Authenticate: Basic realm="' . \System\Base\ApplicationBase::getInstance()->config->authenticationBasicRealm . '"');
			\System\Web\WebApplicationBase::getInstance()->sendHTTPError( 401 );
		}


		/**
		 * gets auth user
		 * 
		 * @return  string
		 */
		public static function getAuthUser()
		{
			if( isset( $_SERVER['PHP_AUTH_USER'] ))
			{
				return $_SERVER['PHP_AUTH_USER'];
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Auth user not set, call BasicAuthentication::isAuthUserSet()");
			}
		}


		/**
		 * returns true if auth is set
		 * 
		 * @return  bool
		 */
		public static function isAuthUserSet()
		{
			return ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ));
		}


		/**
		 * perform sign out
		 *
		 * @return  void
		 */
		public static function signout()
		{
			\System\Web\HTTPResponse::addHeader('WWW-Authenticate: Basic realm="' . \System\Base\ApplicationBase::getInstance()->config->authenticationBasicRealm . '"');
			\System\Web\WebApplicationBase::getInstance()->sendHTTPError( 401 );
		}
	}
?>