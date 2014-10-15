<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Provides access to the Application Configuration
	 *
	 * @property AppState $state specifies app state
	 * @property string $lang specifies default language
	 * @property string $charset specifies default character set
	 * @property AppSettingsCollection $appsettings collection of app settings
	 * @property string $defaultTheme specifies the default theme
	 * @property bool $viewStateEnabled specifies whether viewstate is enabled
	 * @property string $viewStateMethod specifies the viewstate method
	 * @property int $viewStateExpires specifies the viewstate expiration in seconds form the current time
	 * @property string $themesPath path to themes folder
	 * @property string $themesURI uri to themes folder
	 * @property string $defaultController specifies default controller
	 * @property string $requestParameter specifies page request parameter
	 * @property bool $rewriteURIS specifies whether to rewrite URI's
	 * @property bool $cookielessSession specifies whether cookieless session is enabled
	 * @property int $sessionTimeout specifies the session timeout
	 * @property bool $cacheEnabled specifies if cache is enabled
	 * @property int $cacheExpires specifies cache expiration in seconds
	 * @property string $authenticationMethod specifies authentication method
	 * @property string $authenticationDeny specifies pages to deny
	 * @property string $authenticationAllow specifies pages to allow
	 * @property string $authenticationRestrict specifies allowed ip address
	 * @property int $authenticationMaxInvalidAttempts specifies max allowed invalid attempts
	 * @property int $authenticationAttemtpWindow specifies attempt window in seconds
	 * @property bool $authenticationRequireSSL specifies whether to require SSL for authentication
	 * @property string $authenticationBasicRealm specifies realm for basic authentication
	 * @property string $authenticationFormsLoginPage specifies authentication login page
	 * @property string $authenticationFormsCookieName specifies session cookie name
	 * @property string $authenticationFormsSecret specifies secret used when checking auth cookies
	 * @property string $authenticationFormsExpires specifies the inactive time before session expires in seconds
	 * @property array $authenticationCredentialsUsers array of authentication users
	 * @property array $authenticationCredentialsTables array of authentication tables
	 * @property array $authenticationCredentialsLDAP array of authentication LDAP connections
	 * @property array $authenticationCredentialsCustom array of custom credential parameters
	 * @property array $authenticationMemberships array of memberships
	 * @property array $authenticationMembershipsTables array of membership tables
	 * @property array $authorizationDeny specifies the roles that are denied access by default
	 * @property array $authorizationAllow specifies the roles that are granted access by default
	 * @property bool $authorizationRequireSSL specifies whether to require SSL for authentication
	 * @property array $authorizationPages contains the page authorization configuration
	 * @property string $dsn dsn connecting string
	 * @property string $db_username dsn username
	 * @property string $db_password dsn password
	 * @property array $errors array of errors
	 * @property string $root path to root folder
	 * @property string $htdocs path to htdocs folder
	 * @property string $protocol server protocol
	 * @property string $host server host
	 * @property string $uri base application URI
	 * @property string $url base application URL
	 * @property string $assets base URI to the assets folder
	 * @property string $controllers path to controllers folder
	 * @property string $views path to views folder
	 * @property string $templates path to templates folder
	 * @property string $functionaltests path to funcitonal test folder
	 * @property string $unittests path to unit test folder
	 * @property string $fixtures path to fixtures folder
	 * @property string $system path to system folder
	 * @property string $cache path to cache folder
	 * @property string $tmp path to tmp folder
	 * @property string $logs path to logs folder
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class AppConfiguration
	{
		/**
		 * application state
		 * @var AppState
		 */
		private $state;

		/**
		 * default language
		 * @var string
		 */
		private $lang							= 'en';

		/**
		 * default character set
		 * @var string
		 */
		private $charset						= 'utf-8';

		/**
		 * specifies custom application settings
		 * @var AppSettingsCollection
		 */
		private $appsettings;

		/**
		 * specifies the default theme
		 * @var string
		 */
		private $defaultTheme					= 'default';

		/**
		 * specifies the URI to the themes folder
		 * @var string
		 */
		private $themes							= '/themes';

		/**
		 * specifies whether viewstate is enabled
		 * @var bool
		 */
		private $viewStateEnabled				= false;

		/**
		 * specifies the viewstate method
		 * @var string
		 */
		private $viewStateMethod				= 'session';

		/**
		 * specifies the viewstate expiration in seconds form the current time
		 * @var int
		 */
		private $viewStateExpires				= 0;

		/**
		 * specifies the default controller
		 * @var string
		 */
		private $defaultController				= 'Index';

		/**
		 * specifies the default controller parameter
		 * @var string
		 */
		private $requestParameter				= __PATH_REQUEST_PARAMETER__;

		/**
		 * specifies the re-write URI status
		 * @var string
		 */
		private $rewriteURIS					= true;

		/**
		 * specifies the session behaviour sessions (cookieless=sessionId passed in URL and POST headers)
		 * @var bool
		 */
		private $cookielessSession				= false;

		/**
		 * specifies the session timeout (in minutes)
		 * @var int
		 */
		private $sessionTimeout					= 0;

		/**
		 * specifies if caching is enabled
		 * @var bool
		 */
		private $cacheEnabled					= true;

		/**
		 * specifies if caching is enabled
		 * @var int
		 */
		private $cacheExpires					= 0;

		/**
		 * specifies authentication method
		 * @var string
		 */
		private $authenticationMethod			= 'none';

		/**
		 * specifies what pages are protected
		 * @var string
		 */
		private $authenticationDeny				= array();

		/**
		 * specifies what pages are not protected
		 * @var string
		 */
		private $authenticationAllow			= array();

		/**
		 * specifies what ip's to restrict the protected pages to
		 * @var string
		 */
		private $authenticationRestrict			= '';

		/**
		 * specifies max allowed invalid attempts
		 * @var int
		 */
		private $authenticationMaxInvalidAttempts	= 0;

		/**
		 * specifies whether to require SSL for authentication
		 * @var int
		 */
		private $authenticationAttemtpWindow	= 300;

		/**
		 * specifies whether to require SSL
		 * @var bool
		 */
		private $authenticationRequireSSL		= false;

		/**
		 * specifies the name of the authentication cookie
		 * @var string
		 */
		private $authenticationFormsCookieName	= '__AUTHCOOKIE';

		/**
		 * specifies secret used when checking auth cookies
		 * @var string
		 */
		private $authenticationFormsSecret		= 'secret';

		/**
		 * specifies the inactive time before session expires in seconds
		 * @var string
		 */
		private $authenticationFormsExpires		= 0;

		/**
		 * specifies the password format for http authentication
		 * @var string
		 */
		private $authenticationBasicRealm		= 'My Realm';

		/**
		 * specifies the name of the forms login page
		 * @var string
		 */
		private $authenticationFormsLoginPage	= 'login';

		/**
		 * contains an array of user credentials
		 * @var array
		 */
		private $authenticationCredentialsUsers		= array();

		/**
		 * contains an array of user credential tables
		 * @var array
		 */
		private $authenticationCredentialsTables	= array();

		/**
		 * contains an array of user credential LDAP connections
		 * @var array
		 */
		private $authenticationCredentialsLDAP	= array();

		/**
		 * contains an array of custom credential parameters
		 * @var array
		 */
		private $authenticationCredentialsCustom	= array();

		/**
		 * contains an array of user memberships
		 * @var array
		 */
		private $authenticationMemberships			= array();

		/**
		 * contains an array of user membership tables
		 * @var array
		 */
		private $authenticationMembershipsTables	= array();

		/**
		 * specifies the roles that are denied access by default
		 * @var array
		 */
		private $authorizationDeny					= array();

		/**
		 * specifies the roles that are granted access by default
		 * @var array
		 */
		private $authorizationAllow					= array();

		/**
		 * specifies whether to require SSL for authentication
		 * @var bool
		 */
		private $authorizationRequireSQL			= false;

		/**
		 * contains the page authorization configuration
		 * @var array
		 */
		private $authorizationPages					= array();

		/**
		 * specifies the dsn connection string for the data-source
		 * @var string
		 */
		private $dsn							= '';

		/**
		 * specifies the data-source username
		 * @var string
		 */
		private $db_username						= '';

		/**
		 * specifies the data-source password
		 * @var string
		 */
		private $db_password						= '';

		/**
		 * contains an array of error handling pages
		 * @var array
		 */
		private $errors							= array();


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'state' ) {
				return $this->state;
			}
			elseif( $field === 'lang' ) {
				return $this->lang;
			}
			elseif( $field === 'charset' ) {
				return $this->charset;
			}
			elseif( $field === 'appsettings' ) {
				return new AppSettingsCollection( $this->appsettings );
			}
			elseif( $field === 'defaultTheme' ) {
				return $this->defaultTheme;
			}
			elseif( $field === 'themesPath' ) {
				return __HTDOCS_PATH__ . $this->themes;
			}
			elseif( $field === 'themesURI' ) {
				return __APP_URI__ . $this->themes;
			}
			elseif( $field === 'viewStateEnabled' ) {
				return $this->viewStateEnabled;
			}
			elseif( $field === 'viewStateMethod' ) {
				return $this->viewStateMethod;
			}
			elseif( $field === 'viewStateExpires' ) {
				return $this->viewStateExpires;
			}
			elseif( $field === 'defaultController' ) {
				return $this->defaultController;
			}
			elseif( $field === 'requestParameter' ) {
				return $this->requestParameter;
			}
			elseif( $field === 'rewriteURIS' ) {
				return $this->rewriteURIS;
			}
			elseif( $field === 'cookielessSession' ) {
				return $this->cookielessSession;
			}
			elseif( $field === 'sessionTimeout' ) {
				return $this->sessionTimeout;
			}
			elseif( $field === 'cacheEnabled' ) {
				return $this->cacheEnabled;
			}
			elseif( $field === 'cacheExpires' ) {
				return $this->cacheExpires;
			}
			elseif( $field === 'authenticationMethod' ) {
				return $this->authenticationMethod;
			}
			elseif( $field === 'authenticationDeny' ) {
				return $this->authenticationDeny;
			}
			elseif( $field === 'authenticationAllow' ) {
				return $this->authenticationAllow;
			}
			elseif( $field === 'authenticationRestrict' ) {
				return $this->authenticationRestrict;
			}
			elseif( $field === 'authenticationMaxInvalidAttempts' ) {
				return $this->authenticationMaxInvalidAttempts;
			}
			elseif( $field === 'authenticationAttemtpWindow' ) {
				return $this->authenticationAttemtpWindow;
			}
			elseif( $field === 'authenticationRequireSSL' ) {
				return $this->authenticationRequireSSL;
			}
			elseif( $field === 'authenticationBasicRealm' ) {
				return $this->authenticationBasicRealm;
			}
			elseif( $field === 'authenticationFormsLoginPage' ) {
				return $this->authenticationFormsLoginPage;
			}
			elseif( $field === 'authenticationFormsCookieName' ) {
				return $this->authenticationFormsCookieName;
			}
			elseif( $field === 'authenticationFormsSecret' ) {
				return $this->authenticationFormsSecret;
			}
			elseif( $field === 'authenticationFormsExpires' ) {
				return $this->authenticationFormsExpires;
			}
			elseif( $field === 'authenticationCredentialsUsers' ) {
				return $this->authenticationCredentialsUsers;
			}
			elseif( $field === 'authenticationCredentialsTables' ) {
				return $this->authenticationCredentialsTables;
			}
			elseif( $field === 'authenticationCredentialsLDAP' ) {
				return $this->authenticationCredentialsLDAP;
			}
			elseif( $field === 'authenticationCredentialsCustom' ) {
				return $this->authenticationCredentialsCustom;
			}
			elseif( $field === 'authenticationMemberships' ) {
				return $this->authenticationMemberships;
			}
			elseif( $field === 'authenticationMembershipsTables' ) {
				return $this->authenticationMembershipsTables;
			}
			elseif( $field === 'authorizationDeny' ) {
				return $this->authorizationDeny;
			}
			elseif( $field === 'authorizationAllow' ) {
				return $this->authorizationAllow;
			}
			elseif( $field === 'authorizationRequireSQL' ) {
				return $this->authorizationRequireSQL;
			}
			elseif( $field === 'authorizationPages' ) {
				return $this->authorizationPages;
			}
			elseif( $field === 'dsn' ) {
				return $this->dsn;
			}
			elseif( $field === 'db_username' ) {
				return $this->db_username;
			}
			elseif( $field === 'db_password' ) {
				return $this->db_password;
			}
			elseif( $field === 'errors' ) {
				return $this->errors;
			}
			// readonly properties
			elseif( $field === 'root' ) {
				return __ROOT__;
			}
			elseif( $field === 'htdocs' ) {
				return __HTDOCS_PATH__;
			}
			elseif( $field === 'protocol' ) {
				return __PROTOCOL__;
			}
			elseif( $field === 'host' ) {
				return __HOST__;
			}
			elseif( $field === 'uri' ) {
				return __APP_URI__;
			}
			elseif( $field === 'url' ) {
				return __PROTOCOL__ . '://' . __HOST__ . __APP_URI__;
			}
			elseif( $field === 'assets' ) {
				return __ASSETS_URI__;
			}
			elseif( $field === 'controllers' ) {
				return __CONTROLLERS_PATH__;
			}
			elseif( $field === 'views' ) {
				return __VIEWS_PATH__;
			}
			elseif( $field === 'templates' ) {
				return __TEMPLATES_PATH__;
			}
			elseif( $field === 'functionaltests' ) {
				return __FUNCTIONAL_TESTS_PATH__;
			}
			elseif( $field === 'unittests' ) {
				return __UNIT_TESTS_PATH__;
			}
			elseif( $field === 'fixtures' ) {
				return __FIXTURES_PATH__;
			}
			elseif( $field === 'system' ) {
				return __SYSTEM_PATH__;
			}
			elseif( $field === 'cache' ) {
				return __CACHE_PATH__;
			}
			elseif( $field === 'logs' ) {
				return __LOG_PATH__;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * Creates an instance of the controller and sets default action map.
		 *
		 * @return  void
		 */
		public function __construct()
		{
			$this->state = \System\Base\AppState::On();
			$this->authenticationDeny = array('all');
			$this->authenticationAllow = array('none');
		}


		/**
		 * loads config data from an xml file
		 *
		 * @param   string	$file	path to config file
		 * @return  void
		 */
		public function loadAppConfig( $file )
		{
			// create xml parser resource
			$xml_parser = xml_parser_create();

			if( file_exists( $file ))
			{
				// open template
				$fp = fopen( $file, "r" );
				if(!($fp)) {
					throw new \System\Base\InvalidOperationException( 'could not open xml document' );
				}

				// validate document
				\libxml_use_internal_errors(true);
				$dom = new \DOMDocument;
				$dom->load($file);
				if(!$dom->schemaValidate(__SYSTEM_PATH__.'/base/application.xsd')) {
					$errors = \libxml_get_errors();
					throw new \System\Base\InvalidOperationException( 'xml document does not validate: ' . $errors[0]->message . ' in ' . $errors[0]->file . ' on line ' . $errors[0]->line );
				}

				// read xml data into array
				$array = array();
				while( $data = fread( $fp, filesize( $file ))) {
					$array = array();
					if( !xml_parse_into_struct( $xml_parser, $data, $array )) {
						throw new \System\Base\InvalidOperationException( sprintf(
							"Configuration XML Parse Error: %s at line %d",
							xml_error_string(xml_get_error_code( $xml_parser )),
							xml_get_current_line_number( $xml_parser )
							));
					}
				}

				// free resources
				xml_parser_free($xml_parser);

				// parse node tree
				$index=0;
				$this->_getRootNode( $array, $index );
			}
		}


		/**
		 * get root node
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getRootNode( &$nodes, &$index )
		{
			$node_data = $nodes[$index++];

			if( $node_data['tag'] === 'APPLICATION' &&
				$node_data['type'] != 'cdata' &&
				isset( $node_data['attributes'] )) {

				if( $node_data['attributes']['STATE'] === 'on' ) {
					$this->state = \System\Base\AppState::On();
				}
				elseif( $node_data['attributes']['STATE'] === 'debug' ) {
					$this->state = \System\Base\AppState::Debug();
				}
				if( isset( $node_data['attributes']['LANG'] )) {
					$this->lang = $node_data['attributes']['LANG'];
				}
				if( isset( $node_data['attributes']['CHARSET'] )) {
					$this->charset = $node_data['attributes']['CHARSET'];
				}

				if( $node_data['type'] === 'open' )
				{
					$this->_getNodes( $nodes, $index );
				}
			}

			return;
		}


		/**
		 * get all nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getNodes( &$nodes, &$index )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				// app-settings
				elseif( $node_data['tag'] === 'APP-SETTINGS' && $node_data['type'] != 'cdata' )
				{
					if( $node_data['type'] === 'open' )
					{
						$this->_getAppSettingNodes( $nodes, $index );
					}
				}
				// pages
				elseif( $node_data['tag'] === 'PAGES' && $node_data['type'] != 'cdata' && isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['THEME'] )) {
						// TODO: Deprecated
						$this->defaultTheme = $node_data['attributes']['THEME'];
					}
					if( isset( $node_data['attributes']['DEFAULT-THEME'] )) {
						$this->defaultTheme = $node_data['attributes']['DEFAULT-THEME'];
					}

					$this->_closeNode( $nodes, $index );
				}
				// viewstate
				elseif( $node_data['tag'] === 'VIEWSTATE' && $node_data['type'] != 'cdata' && isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['ENABLED'] )) {
						if( strtolower( $node_data['attributes']['ENABLED'] === 'true' )) {
							$this->viewStateEnabled = true;
						}
						else {
							$this->viewStateEnabled = false;
						}
					}
					if( isset( $node_data['attributes']['METHOD'] )) {
						$this->viewStateMethod = $node_data['attributes']['METHOD'];
					}
					if( isset( $node_data['attributes']['EXPIRES'] )) {
						if( is_numeric( $node_data['attributes']['EXPIRES'] )) {
							$this->viewStateExpires = (int) $node_data['attributes']['EXPIRES'];
						}
					}

					$this->_closeNode( $nodes, $index );
				}
				// request
				elseif( $node_data['tag'] === 'REQUEST' && $node_data['type'] != 'cdata' && isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['DEFAULT'] )) {
						$this->defaultController = $node_data['attributes']['DEFAULT'];
					}
					if( isset( $node_data['attributes']['PARAM'] )) {
						$this->requestParameter = $node_data['attributes']['PARAM'];
					}
					if( isset( $node_data['attributes']['FRIENDLY-URIS'] )) {
						if( strtolower( $node_data['attributes']['FRIENDLY-URIS'] === 'true' )) {
							$this->rewriteURIS = true;
						}
						else {
							$this->rewriteURIS = false;
						}
					}

					$this->_closeNode( $nodes, $index );
				}
				// authentication
				elseif( $node_data['tag'] === 'AUTHENTICATION' && $node_data['type'] != 'cdata' )
				{
					if( isset( $node_data['attributes'] )) {
						// authenticationMethod
						if( isset( $node_data['attributes']['METHOD'] )) {
							$this->authenticationMethod = strtolower( $node_data['attributes']['METHOD'] );
						}
						// authenticationDeny
						if( isset( $node_data['attributes']['DENY'] )) {
							$this->authenticationDeny = array_map('trim', explode( ',', strtolower( $node_data['attributes']['DENY'] )));
						}
						// authenticationAllow
						if( isset( $node_data['attributes']['ALLOW'] )) {
							$this->authenticationAllow = array_map('trim', explode( ',', strtolower( $node_data['attributes']['ALLOW'] )));
						}
						// authenticationRestrict
						if( isset( $node_data['attributes']['RESTRICT'] )) {
							$this->authenticationRestrict = $node_data['attributes']['RESTRICT'];
						}
						// authenticationMaxInvalidAttempts
						if( isset( $node_data['attributes']['MAXINVALIDATTEMPTS'] )) {
							$this->authenticationMaxInvalidAttempts = (int)$node_data['attributes']['MAXINVALIDATTEMPTS'];
						}
						// authenticationAttemtpWindow
						if( isset( $node_data['attributes']['ATTEMPTWINDOW'] )) {
							$this->authenticationAttemtpWindow = (int) $node_data['attributes']['ATTEMPTWINDOW'];
						}
						// authenticationRequireSSL
						if( isset( $node_data['attributes']['REQUIRESSL'] )) {
							if( strtolower( $node_data['attributes']['REQUIRESSL'] === 'true' )) {
								$this->authenticationRequireSSL = true;
							}
							else {
								$this->authenticationRequireSSL = false;
							}
						}
					}

					if( $node_data['type'] === 'open' )
					{
						$this->_getAuthenticationNodes( $nodes, $index );
					}
				}
				// authorization
				elseif( $node_data['tag'] === 'AUTHORIZATION' && $node_data['type'] != 'cdata' )
				{
					if( isset( $node_data['attributes'] )) {
						// deny
						if( isset( $node_data['attributes']['DENY'] )) {
							$this->authorizationDeny = array();
							$roles = explode( ',', $node_data['attributes']['DENY'] );
							foreach($roles as $role)
							{
								if(trim($role)) $this->authorizationDeny[] = trim($role);
							}
						}
						// allow
						if( isset( $node_data['attributes']['ALLOW'] )) {
							$this->authorizationAllow = array();
							$roles = explode( ',', $node_data['attributes']['ALLOW'] );
							foreach($roles as $role)
							{
								if(trim($role)) $this->authorizationAllow[] = trim($role);
							}
						}
						// restrict
						if( isset( $node_data['attributes']['RESTRICT'] )) {
							$this->authorizationRestrict = (int)$node_data['attributes']['RESTRICT'];
						}
						// requireSSL
						if( isset( $node_data['attributes']['REQUIRESSL'] )) {
							if( strtolower( $node_data['attributes']['REQUIRESSL'] === 'true' )) {
								$this->authorizationRequireSSL = true;
							}
							else {
								$this->authorizationRequireSSL = false;
							}
						}
					}

					if( $node_data['type'] === 'open' )
					{
						$this->_getAuthorizationNodes( $nodes, $index );
					}
				}
				// session
				elseif( $node_data['tag'] === 'SESSION' && $node_data['type'] != 'cdata' && isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['COOKIELESS'] )) {
						if( strtolower( $node_data['attributes']['COOKIELESS'] === 'true' )) {
							$this->cookielessSession = true;
						}
						else {
							$this->cookielessSession = false;
						}
					}
					if( isset( $node_data['attributes']['TIMEOUT'] )) {
						if( is_numeric( $node_data['attributes']['TIMEOUT'] )) {
							$this->sessionTimeout = (int) $node_data['attributes']['TIMEOUT'];
						}
					}

					$this->_closeNode( $nodes, $index );
				}
				// cache
				elseif( $node_data['tag'] === 'CACHE' && $node_data['type'] != 'cdata' && isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['ENABLED'] )) {
						if( strtolower( $node_data['attributes']['ENABLED'] === 'true' )) {
							$this->cacheEnabled = true;
						}
						else {
							$this->cacheEnabled = false;
						}
					}
					if( isset( $node_data['attributes']['EXPIRES'] )) {
						if( is_numeric( $node_data['attributes']['EXPIRES'] )) {
							$this->cacheExpires = (int) $node_data['attributes']['EXPIRES'];
						}
					}

					$this->_closeNode( $nodes, $index );
				}
				// data-source
				elseif( $node_data['tag'] === 'DATA-SOURCE' && $node_data['type'] != 'cdata' && isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['DSN'] )) {
						$this->dsn = $node_data['attributes']['DSN'];
					}
					if( isset( $node_data['attributes']['USERNAME'] )) {
						$this->db_username = $node_data['attributes']['USERNAME'];
					}
					if( isset( $node_data['attributes']['PASSWORD'] )) {
						$this->db_password = $node_data['attributes']['PASSWORD'];
					}

					$this->_closeNode( $nodes, $index );
				}
				// errors
				elseif( $node_data['tag'] === 'ERRORS' && $node_data['type'] != 'cdata' )
				{
					if( $node_data['type'] === 'open' )
					{
						$this->_getErrorHandlingNodes( $nodes, $index );
					}
				}
			}
		}


		/**
		 * get app-setting nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getAppSettingNodes( &$nodes, &$index )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				elseif( $node_data['tag'] === 'ADD' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					// error page
					if( isset( $node_data['attributes']['KEY'] ) &&
						isset( $node_data['attributes']['VALUE'] ))
					{
						$this->appsettings[$node_data['attributes']['KEY']] = $node_data['attributes']['VALUE'];
					}

					$this->_closeNode( $nodes, $index );
				}
			}
		}


		/**
		 * get authentication Nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getAuthenticationNodes( &$nodes, &$index )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				// basic
				elseif( $node_data['tag'] === 'BASIC' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					// authenticationBasicRealm
					if( isset( $node_data['attributes']['REALM'] )) {
						$this->authenticationBasicRealm = $node_data['attributes']['REALM'];
					}

					$this->_closeNode( $nodes, $index );
				}
				// forms
				elseif( $node_data['tag'] === 'FORMS' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					// authenticationFormsLoginPage
					if( isset( $node_data['attributes']['LOGINPAGE'] )) {
						$this->authenticationFormsLoginPage = $node_data['attributes']['LOGINPAGE'];
					}
					// authenticationFormsCookieName
					if( isset( $node_data['attributes']['COOKIENAME'] )) {
						$this->authenticationFormsCookieName = $node_data['attributes']['COOKIENAME'];
					}
					// authenticationFormsSecret
					if( isset( $node_data['attributes']['SECRET'] )) {
						$this->authenticationFormsSecret = $node_data['attributes']['SECRET'];
					}
					// authenticationFormsExpires
					if( isset( $node_data['attributes']['EXPIRES'] )) {
						$this->authenticationFormsExpires = (int)$node_data['attributes']['EXPIRES'];
					}

					$this->_closeNode( $nodes, $index );
				}
				// credentials
				elseif( $node_data['tag'] === 'CREDENTIALS' &&
						$node_data['type'] != 'cdata' )
				{
					if( $node_data['type'] === 'open' )
					{
						$this->_getCredentialsNodes( $nodes, $index );
					}
				}
				// memberships
				elseif( $node_data['tag'] === 'MEMBERSHIPS' &&
						$node_data['type'] != 'cdata' )
				{
					if( $node_data['type'] === 'open' )
					{
						$this->_getMembershipsNodes( $nodes, $index );
					}
				}
			}
		}


		/**
		 * get authorization nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getAuthorizationNodes( &$nodes, &$index )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				elseif( $node_data['tag'] === 'PAGE' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['PATH'] ))
					{
						$page = array();
						// deny
						if( isset( $node_data['attributes']['DENY'] )) {
							$roles = explode( ',', $node_data['attributes']['DENY'] );
							foreach($roles as $role)
							{
								$page["deny"][] = trim($role);
							}
						}
						// allow
						if( isset( $node_data['attributes']['ALLOW'] )) {
							$roles = explode( ',', $node_data['attributes']['ALLOW'] );
							foreach($roles as $role)
							{
								$page["allow"][] = trim($role);
							}
						}
						// restrict
						if( isset( $node_data['attributes']['RESTRICT'] )) {
							$page["restrict"] = $node_data['attributes']['RESTRICT'];
						}
						// requireSSL
						if( isset( $node_data['attributes']['REQUIRESSL'] )) {
							if( strtolower( $node_data['attributes']['REQUIRESSL'] === 'true' )) {
								$page["requireSSL"] = true;
							}
							else {
								$page["requireSSL"] = false;
							}
						}
						else {
							$page["requireSSL"] = true;
						}

						$this->authorizationPages[$node_data['attributes']['PATH']] = $page;
					}

					$this->_closeNode( $nodes, $index );
				}
			}
		}


		/**
		 * get credentials nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getCredentialsNodes( &$nodes, &$index )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				// user
				elseif( $node_data['tag'] === 'USER' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['USERNAME'] ) &&
						isset( $node_data['attributes']['PASSWORD'] ))
					{
						$user = array();
						$user['username'] = $node_data['attributes']['USERNAME'];
						$user['password'] = $node_data['attributes']['PASSWORD'];
						if( isset( $node_data['attributes']['ACTIVE'] )) {
							if( $node_data['attributes']['ACTIVE'] === 'true' ) {
								$user['active'] = true;
							}
							else {
								$user['active'] = false;
							}
						}
						else {
							$user['active'] = true;
						}
						if( isset( $node_data['attributes']['SALT'] )) {
							$user['salt'] = $node_data['attributes']['SALT'];
						}
						if( isset( $node_data['attributes']['PASSWORD-FORMAT'] )) {
							$user['password-format'] = $node_data['attributes']['PASSWORD-FORMAT'];
						}

						$this->authenticationCredentialsUsers[] = $user;
					}

					$this->_closeNode( $nodes, $index );
				}
				// table
				elseif( $node_data['tag'] === 'TABLE' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['SOURCE'] ) &&
						isset( $node_data['attributes']['USERNAME-FIELD'] ) &&
						isset( $node_data['attributes']['PASSWORD-FIELD'] ))
					{
						$table = array();
						if( isset( $node_data['attributes']['DSN'] )) {
							$table['dsn']		= $node_data['attributes']['DSN'];
						}
						$table['source']		 = $node_data['attributes']['SOURCE'];
						$table['username-field'] = $node_data['attributes']['USERNAME-FIELD'];
						$table['password-field'] = $node_data['attributes']['PASSWORD-FIELD'];
						if( isset( $node_data['attributes']['EMAILADDRESS-FIELD'] )) {
							$table['emailaddress-field'] = $node_data['attributes']['EMAILADDRESS-FIELD'];
						}
						if( isset( $node_data['attributes']['ACTIVE-FIELD'] )) {
							$table['active-field'] = $node_data['attributes']['ACTIVE-FIELD'];
						}
						if( isset( $node_data['attributes']['FAILEDATTEMPTCOUNT-FIELD'] )) {
							$table['failedattemptcount-field'] = $node_data['attributes']['FAILEDATTEMPTCOUNT-FIELD'];
						}
						if( isset( $node_data['attributes']['ATTEMPTWINDOWEXPIRES-FIELD'] )) {
							$table['attemptwindowexpires-field'] = $node_data['attributes']['ATTEMPTWINDOWEXPIRES-FIELD'];
						}
						if( isset( $node_data['attributes']['SALT-FIELD'] )) {
							$table['salt-field'] = $node_data['attributes']['SALT-FIELD'];
						}
						if( isset( $node_data['attributes']['SALT'] )) {
							$table['salt'] = $node_data['attributes']['SALT'];
						}
						if( isset( $node_data['attributes']['PASSWORD-FORMAT'] )) {
							$table['password-format'] = $node_data['attributes']['PASSWORD-FORMAT'];
						}

						$this->authenticationCredentialsTables[] = $table;
					}

					$this->_closeNode( $nodes, $index );
				}
				// ldap
				elseif( $node_data['tag'] === 'LDAP' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['HOST'] ))
					{
						$ldap = array();
						$ldap['host']			 = $node_data['attributes']['HOST'];

						if( isset( $node_data['attributes']['DOMAIN'] )) {
							$ldap['domain'] = $node_data['attributes']['DOMAIN'];
						}
						if( isset( $node_data['attributes']['USE-START-TLS'] )) {
							$ldap['use-start-tls'] = (bool)$node_data['attributes']['USE-START-TLS'];
						}
						if( isset( $node_data['attributes']['ACCOUNT-CANONICAL-FORM'] )) {
							$ldap['account-canonical-form'] = (int)$node_data['attributes']['ACCOUNT-CANONICAL-FORM'];
						}
						if( isset( $node_data['attributes']['BASE-DN'] )) {
							$ldap['base-dn'] = (int)$node_data['attributes']['BASE-DN'];
						}
						if( isset( $node_data['attributes']['LDAP-USER'] )) {
							$ldap['ldap_user'] = $node_data['attributes']['LDAP-USER'];
						}
						if( isset( $node_data['attributes']['LDAP-PASSWORD'] )) {
							$ldap['ldap_password'] = $node_data['attributes']['LDAP-PASSWORD'];
						}
						if( isset( $node_data['attributes']['ATTRIBUTES'] )) {
							$ldap['attributes'] = $node_data['attributes']['ATTRIBUTES'];
						}
						if( isset( $node_data['attributes']['TIMELIMIT'] )) {
							$ldap['timelimit'] = $node_data['attributes']['TIMELIMIT'];
						}

						$this->authenticationCredentialsLDAP[] = $ldap;
					}

					$this->_closeNode( $nodes, $index );
				}
				// custom
				elseif( $node_data['tag'] === 'CUSTOM' )
				{
					if( $node_data['type'] === 'open' )
					{
						$custom = array('name'=>'','class'=>'');
						$this->_getCustomParametersNodes( $nodes, $index, $custom );

						if( $node_data['type'] != 'cdata' &&
							isset( $node_data['attributes'] ))
						{
							if( isset( $node_data['attributes']['CLASS'] ))
							{
								$custom['name'] = $node_data['attributes']['NAME'];
								$custom['class'] = $node_data['attributes']['CLASS'];
							}
						}

						$this->authenticationCredentialsCustom[] = $custom;
					}

					$this->_closeNode( $nodes, $index );
				}
			}
		}


		/**
		 * get app-setting nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getCustomParametersNodes( &$nodes, &$index, &$array )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				elseif( $node_data['tag'] === 'ADD' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					// error page
					if( isset( $node_data['attributes']['KEY'] ) &&
						isset( $node_data['attributes']['VALUE'] ))
					{
						$array[$node_data['attributes']['KEY']] = $node_data['attributes']['VALUE'];
					}

					$this->_closeNode( $nodes, $index );
				}
			}
		}


		/**
		 * get memberships nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getMembershipsNodes( &$nodes, &$index )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				// user
				elseif( $node_data['tag'] === 'MEMBERSHIP' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['USERNAME'] ) &&
						isset( $node_data['attributes']['ROLE'] ))
					{
						$membership = array();
						$membership['username'] = $node_data['attributes']['USERNAME'];
						$membership['role'] = $node_data['attributes']['ROLE'];

						$this->authenticationMemberships[] = $membership;
					}

					$this->_closeNode( $nodes, $index );
				}
				// table
				elseif( $node_data['tag'] === 'TABLE' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					if( isset( $node_data['attributes']['SOURCE'] ) &&
						isset( $node_data['attributes']['USERNAME-FIELD'] ) &&
						isset( $node_data['attributes']['ROLE-FIELD'] ))
					{
						$table = array();
						if( isset( $node_data['attributes']['DSN'] )) {
							$table['dsn']		 = $node_data['attributes']['DSN'];
						}
						$table['source']		 = $node_data['attributes']['SOURCE'];
						$table['username-field'] = $node_data['attributes']['USERNAME-FIELD'];
						$table['role-field']	 = $node_data['attributes']['ROLE-FIELD'];

						$this->authenticationMembershipsTables[] = $table;
					}

					$this->_closeNode( $nodes, $index );
				}
			}
		}


		/**
		 * get error handling nodes
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _getErrorHandlingNodes( &$nodes, &$index )
		{
			while( $index < sizeof( $nodes ))
			{
				$node_data = $nodes[$index++];

				if( $node_data['type'] === 'close' )
				{
					return;
				}
				elseif( $node_data['tag'] === 'WHEN' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					// error page
					if( isset( $node_data['attributes']['ERROR'] ) &&
						isset( $node_data['attributes']['PAGE'] ))
					{
						$this->errors[$node_data['attributes']['ERROR']] = $node_data['attributes']['PAGE'];
					}

					$this->_closeNode( $nodes, $index );
				}
				elseif( $node_data['tag'] === 'OTHERWISE' &&
						$node_data['type'] != 'cdata' &&
						isset( $node_data['attributes'] ))
				{
					// default error page
					if( isset( $node_data['attributes']['PAGE'] ))
					{
						$this->errors['Default'] = $node_data['attributes']['PAGE'];
					}

					$this->_closeNode( $nodes, $index );
				}
			}
		}


		/**
		 * close node
		 *
		 * @param   array	&$nodes	array of xml nodes
		 * @param   int		&$index	array index
		 * @return  void
		 */
		private function _closeNode( &$nodes, &$index )
		{
			$node = $nodes[$index-1];

			// if node is open, close
			if( $node['type'] === 'open' ) {
				while( $index < sizeof( $nodes )) {
					$node_data = $nodes[$index++];
					if( $node_data['type'] === 'close' ) return;
				}
			}
			return;
		}
	}
?>