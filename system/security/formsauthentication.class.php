<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Security;


	/**
	 * Provides application wide authentication using Forms
	 *
	 * @package			PHPRum
	 * @subpackage		Security
	 * @author			Darnell Shinbine
	 */
	class FormsAuthentication extends Authentication
	{
		/**
		 * returns true if user authenticated
		 *
		 * @return  bool
		 */
		public static function authenticated()
		{
			if( FormsAuthentication::isAuthCookieSet() )
			{
				if( FormsAuthentication::authenticateSecret( FormsAuthentication::getAuthCookie(), FormsAuthentication::getAuthSecret() ))
				{
					if( Authentication::authorize(FormsAuthentication::getAuthCookie() ))
					{
						Authentication::$identity = FormsAuthentication::getAuthCookie();
						return true;
					}
				}
			}
			return false;
		}


		/**
		 * sets auth cookie and redirects to original requested resource
		 *
		 * @param   string	$uid		unique value representing user
		 * @param   string	$permanent	specifies whether cookie is permanent
		 * @return  void
		 */
		public static function redirectFromLoginPage( $uid, $permanent = false )
		{
			FormsAuthentication::setAuthCookie( $uid, $permanent );
			\System\Web\WebApplicationBase::getInstance()->saveApplicationState();
			\System\Web\WebApplicationBase::getInstance()->session->write();

			if( isset( \System\Web\HTTPRequest::$request['returnUrl'] ))
			{
				\System\Web\HTTPResponse::redirect( \System\Web\HTTPRequest::$request['returnUrl'] );
			}
			else
			{
				\System\Web\HTTPResponse::redirect( \System\Web\WebApplicationBase::getInstance()->getPageURI( \System\Base\ApplicationBase::getInstance()->config->defaultController ));
			}
		}


		/**
		 * redirects to login page
		 *
		 * @return  void
		 */
		public static function redirectToLoginPage()
		{
			// redirect to secure server (forward session)
			if( \Rum::config()->authenticationRequireSSL && \Rum::config()->protocol <> 'https' && !isset($GLOBALS["__DISABLE_HEADER_REDIRECTS__"]))
			{
				$url = 'https://' . __NAME__ . (__SSL_PORT__<>443?':'.__SSL_PORT__:'') . \System\Web\WebApplicationBase::getInstance()->getPageURI( \System\Base\ApplicationBase::getInstance()->config->authenticationFormsLoginPage, array( 'returnUrl' => $_SERVER["REQUEST_URI"], \System\Web\WebApplicationBase::getInstance()->session->sessionName => \System\Web\WebApplicationBase::getInstance()->session->sessionId ));
			}
			else
			{
				$url = \System\Web\WebApplicationBase::getInstance()->getPageURI( \System\Base\ApplicationBase::getInstance()->config->authenticationFormsLoginPage, array( 'returnUrl' => $_SERVER["REQUEST_URI"] ));
			}

			// write and close session
			\System\Web\WebApplicationBase::getInstance()->session->close();
			\System\Web\HTTPResponse::redirect( $url );
		}


		/**
		 * authenticate secret
		 * 
		 * @param   string	$uid		unique value representing user
		 * @param   string	$secret		secret
		 * @return void
		 */
		protected static function authenticateSecret($uid, $secret)
		{
			$timestamp = substr($secret, 40);
			$hash = substr($secret, 0, 40);
			$salt = $_SERVER["REMOTE_ADDR"].$uid.$timestamp;
			return( $hash === Authentication::generateHash('sha1', \Rum::config()->authenticationFormsSecret, $salt ));
		}


		/**
		 * sets auth cookie
		 *
		 * @param   string	$uid		unique value representing user
		 * @param   string	$permanent	specifies whether cookie is permanent
		 * @param   int		$expires	specifies time in seconds for cookie to expire, default 1 year
		 *
		 * @return  void
		 */
		public static function setAuthCookie( $uid, $permanent = false, $expires = 31536000, $ssl = false )
		{
			$timestamp = time();
			$salt = $_SERVER["REMOTE_ADDR"].$uid.$timestamp;
			$secret = Authentication::generateHash('sha1', \Rum::config()->authenticationFormsSecret, $salt) . $timestamp;

			if( $permanent )
			{
				\System\Web\HTTPResponse::setCookie(\Rum::config()->authenticationFormsCookieName, $uid, time() + $expires, \Rum::config()->uri, null, $ssl );
				\System\Web\HTTPResponse::setCookie(\Rum::config()->authenticationFormsCookieName.'_secret', $secret, time() + $expires, \Rum::config()->uri, null, $ssl );
			}
			else
			{
				// set session cookie
				\System\Base\ApplicationBase::getInstance()->session[\Rum::config()->authenticationFormsCookieName] = $uid;
				\System\Base\ApplicationBase::getInstance()->session[\Rum::config()->authenticationFormsCookieName.'_secret'] = $secret;
			}
		}


		/**
		 * gets auth cookie
		 *
		 * @return  string
		 */
		public static function getAuthCookie()
		{
			if( isset( \System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName] ))
			{
				return \System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName];
			}
			elseif( isset( $_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName] ))
			{
				return $_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName];
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Auth cookie does not exist, call FormsAuthentication::isAuthCookieSet()");
			}
		}


		/**
		 * gets auth secret
		 *
		 * @return  string
		 */
		public static function getAuthSecret()
		{
			if( isset( \System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_secret'] ))
			{
				return \System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_secret'];
			}
			elseif( isset( $_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_secret'] ))
			{
				return $_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_secret'];
			}
			else
			{
				return '';
			}
		}


		/**
		 * returns true if auth cookie is set
		 *
		 * @return  bool
		 */
		public static function isAuthCookieSet()
		{
			if( isset( \System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName] ))
			{
				if(\System\Base\ApplicationBase::getInstance()->config->authenticationFormsExpires > 0)
				{
					if(isset(\System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_last_activity']) && (time() - \System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_last_activity'] > \System\Base\ApplicationBase::getInstance()->config->authenticationFormsExpires)) {
						
						FormsAuthentication::signout();
						return false;
					}
				}
				\System\Base\ApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_last_activity'] = time();
				return true;
			}
			elseif( isset( $_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName] ))
			{
				if(\System\Base\ApplicationBase::getInstance()->config->authenticationFormsExpires > 0)
				{
					if(isset($_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_last_activity']) && (time() - $_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_last_activity'] > \System\Base\ApplicationBase::getInstance()->config->authenticationFormsExpires)) {
						FormsAuthentication::signout();
						return false;
					}
				}
				$_COOKIE[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_last_activity'] = time();
				return true;
			}
			else
			{
				return false;
			}
		}


		/**
		 * perform sign out (does not end session)
		 *
		 * @return  void
		 */
		public static function signout()
		{
			if( isset( \System\Web\WebApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName] ))
			{
				unset( \System\Web\WebApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName] );
				unset( \System\Web\WebApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_secret'] );
				unset( \System\Web\WebApplicationBase::getInstance()->session[\System\Base\ApplicationBase::getInstance()->config->authenticationFormsCookieName.'_last_activity'] );
			}

			\System\Web\HTTPResponse::setCookie(\Rum::config()->authenticationFormsCookieName, '', -1, \Rum::config()->uri, null );
			\System\Web\HTTPResponse::setCookie(\Rum::config()->authenticationFormsCookieName.'_secret', '', -1, \Rum::config()->uri, null );
			\System\Web\HTTPResponse::setCookie(\Rum::config()->authenticationFormsCookieName.'_last_activity', '', -1, \Rum::config()->uri, null );

			\System\Web\WebApplicationBase::getInstance()->setForwardPage();
		}
	}
?>