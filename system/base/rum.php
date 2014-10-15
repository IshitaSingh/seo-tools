<?php
	/**
	 * Startup script
	 *
	 * This script will detect the following environmental variables
	 *
	 * __ROOT__						absolute path to the root folder
	 * __HTDOCS_PATH__				absolute path to the web folder
	 * __APP_URI__					root relative URI to the web folder
	 * __PROTOCOL__					protocol prefix (http|https)://
	 * __HOST__						host name
	 * __PORT__						server port
	 *
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;

	// php directives
	error_reporting( E_ALL | E_STRICT );

	/**
	 * specifies the default timezone
	 */
	date_default_timezone_set(date_default_timezone_get());

	/*
	 * auto detected env variables
	 * these env vars are auto detected, and may be overridden
	 */

	/**
	 * specifies the public web folder path
	 */
	if( !defined( '__HTDOCS_PATH__' )) {
		define( '__HTDOCS_PATH__', substr( $_SERVER['SCRIPT_FILENAME'], 0, strrpos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), '/' )));
	}

	// auto detect root path
	if( !defined( '__ROOT__' )) {
		define( '__ROOT__', substr( __FILE__, 0, strlen( __FILE__ ) - 20 ));
	}

	$protocol = 'http';
	if( isset( $_SERVER['HTTPS'] )) {
		if( $_SERVER['HTTPS'] && strtolower( $_SERVER['HTTPS'] ) != 'off' ) {
			$protocol = 'https';
		}
	}

	/**
	 * specifies the transfer protocol
	 */
	if( !defined( '__PROTOCOL__' ))						define( '__PROTOCOL__',						$protocol );

	/**
	 * specifies the server name
	 */
	if( !defined( '__NAME__' ))							define( '__NAME__',							isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'' );

	/**
	 * specifies the server port
	 */
	if( !defined( '__PORT__' ))							define( '__PORT__',							isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:'' );

	/**
	 * specifies the host name
	 */
	if( !defined( '__HOST__' ))							define( '__HOST__',							isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'' );

	/**
	 * specifies the application uri
	 */
	if( !defined( '__APP_URI__' ))						define( '__APP_URI__',						str_replace(" ", "%20", substr( $_SERVER['SCRIPT_NAME'], 0, strrpos( $_SERVER['SCRIPT_NAME'], '/' ))));

	/**
	 * specifies the URI to the assets folder
	 */
	if( !defined( '__ASSETS_URI__' ))					define( '__ASSETS_URI__',					__APP_URI__ . '/assets' );

	/*
	 * global defined constants
	 * These constants are defaults, and may be overridden
	 */

	/**
	 * specifies the db_schema_version table name
	 */
	if( !defined( '__DB_SCHEMA_VERSION_TABLENAME__' ))	define( '__DB_SCHEMA_VERSION_TABLENAME__',	'db_schema_version' );

	/**
	 * specifies the user defaults table name
	 */
	if( !defined( '__USERDEFAULTS_TABLENAME__' ))		define( '__USERDEFAULTS_TABLENAME__',		'user_defaults' );

	/**
	 * specifies the cache table name
	 */
	if( !defined( '__CACHE_TABLENAME__' ))				define( '__CACHE_TABLENAME__',				'cache' );

	/**
	 * specifies the logs table name
	 */
	if( !defined( '__LOGS_TABLENAME__' ))				define( '__LOGS_TABLENAME__',				'logs' );

	/**
	 * specifies the langs table name
	 */
	if( !defined( '__LANGS_TABLENAME__' ))				define( '__LANGS_TABLENAME__',				'langs' );

	/**
	 * specifies the environment parameter
	 */
	if( !defined( '__APP_CONF_FILENAME__' ))			define( '__APP_CONF_FILENAME__',			'/application.xml' );

	/**
	 * specifies the environment parameter
	 */
	if( !defined( '__ENV_PARAMETER__' ))				define( '__ENV_PARAMETER__',				'APP_ENV' );

	/**
	 * specifies the models namespace
	 */
	if( !defined( '__CONTROLLERS_NAMESPACE__' ))		define( '__CONTROLLERS_NAMESPACE__',		'Controllers' );

	/**
	 * specifies the models namespace
	 */
	if( !defined( '__MODELS_NAMESPACE__' ))				define( '__MODELS_NAMESPACE__',				'Models' );

	/**
	 * specifies the page request parameter
	 */
	if( !defined( '__PATH_REQUEST_PARAMETER__' ))		define( '__PATH_REQUEST_PARAMETER__',		'path' );

	/**
	 * specifies the async request parameter
	 */
	if( !defined( '__ASYNC_REQUEST_PARAMETER__' ))		define( '__ASYNC_REQUEST_PARAMETER__',		'async' );

	/**
	 * specifies the module request parameter
	 */
	if( !defined( '__MODULE_REQUEST_PARAMETER__' ))		define( '__MODULE_REQUEST_PARAMETER__',		'modules' );

	/**
	 * specifies the dev environment name
	 */
	if( !defined( '__DEV_ENV__' ))						define( '__DEV_ENV__',						'dev' );

	/**
	 * specifies the test environment name
	 */
	if( !defined( '__TEST_ENV__' ))						define( '__TEST_ENV__',						'test' );

	/**
	 * specifies the SSL port
	 */
	if( !defined( '__SSL_PORT__' ))						define( '__SSL_PORT__',						443 );

	/**
	 * specifies the request page extension
	 */
	if( !defined( '__PAGE_EXTENSION__' ))				define( '__PAGE_EXTENSION__',				'/' );

	/**
	 * specifies the class extension
	 */
	if( !defined( '__CLASS_EXTENSION__' ))				define( '__CLASS_EXTENSION__',				'.class.php' );

	/**
	 * specifies the controller extension
	 */
	if( !defined( '__CONTROLLER_EXTENSION__' ))			define( '__CONTROLLER_EXTENSION__',			'.php' );

	/**
	 * specifies the template extension
	 */
	if( !defined( '__TEMPLATE_EXTENSION__' ))			define( '__TEMPLATE_EXTENSION__',			'.tpl' );

	/**
	 * specifies the controller extension
	 */
	if( !defined( '__DEPLOYMENT_EXTENSION__' ))			define( '__DEPLOYMENT_EXTENSION__',			'.php' );

	/**
	 * specifies the testcase suffix
	 */
	if( !defined( '__TESTCASE_SUFFIX__' ))				define( '__TESTCASE_SUFFIX__',				'TestCase' );

	/**
	 * specifies the controller testcase suffix
	 */
	if( !defined( '__CONTROLLER_TESTCASE_SUFFIX__' ))	define( '__CONTROLLER_TESTCASE_SUFFIX__',   'ControllerTestCase' );

	/**
	 * specifies the asyncronous validation timeout
	 */
	if( !defined( '__VALIDATION_TIMEOUT__' ))			define( '__VALIDATION_TIMEOUT__',			'3000' );

	/**
	 * specifies the asyncronous flash message timeout
	 */
	if( !defined( '__FLASH_MSG_TIMEOUT__' ))			define( '__FLASH_MSG_TIMEOUT__',			'3000' );

	/**
	 * specifies the number of warnings to dump
	 */
	if( !defined( '__ERROR_LIMIT__' ))					define( '__ERROR_LIMIT__',					10 );

	/**
	 * specifies the quote style when escaping
	 */
	if( !defined( '__QUOTE_STYLE__' ))					define( '__QUOTE_STYLE__',					ENT_COMPAT );

	/**
	 * specifies whether to show deprecated notices
	 */
	//if( !defined( '__SHOW_DEPRECATED_NOTICES__' ))		define( '__SHOW_DEPRECATED_NOTICES__',		TRUE );

	/**
	 * specifies whether to show deprecated notices
	 */
	//if( !defined( '__BACKWARDS_COMPATIBILITY_MODE__' ))	define( '__BACKWARDS_COMPATIBILITY_MODE__',	FALSE );

	/**
	 * specifies the number of warnings to dump
	 */
	if( !defined( '__ACTIVERECORD_AUTO_MAP__' ))		define( '__ACTIVERECORD_AUTO_MAP__',		FALSE );

	/*
	 * default paths
	 * These constants are defaults, and may be overridden
	 */

	/**
	 * specifies the root relative path to the cache folder
	 */
	if( !defined( '__BUILD_PATH__' ))					define( '__BUILD_PATH__',					__ROOT__ . '/.build' );

	/**
	 * specifies the root relative path to the cache folder
	 */
	if( !defined( '__CACHE_PATH__' ))					define( '__CACHE_PATH__',					__ROOT__ . '/.cache' );

	/**
	 * specifies the root relative path to the application folder
	 */
	if( !defined( '__APP_PATH__' ))						define( '__APP_PATH__',						__ROOT__ . '/app' );

	/**
	 * specifies the root relative path to the configuration folder
	 */
	if( !defined( '__CONFIG_PATH__' ))					define( '__CONFIG_PATH__',					__ROOT__ . '/app/config' );

	/**
	 * specifies the root relative path to the configuration folder
	 */
	if( !defined( '__ENV_PATH__' ))						define( '__ENV_PATH__',						__CONFIG_PATH__ . '/environments' );

	/**
	 * specifies the root relative path to the deployment folder
	 */
	if( !defined( '__DEPLOY_PATH__' ))					define( '__DEPLOY_PATH__',					__CONFIG_PATH__ . '/deploy' );

	/**
	 * specifies the path to the langs file
	 */
	if( !defined( '__LANGS_FILE__' ))					define( '__LANGS_FILE__',					__CONFIG_PATH__ . '/langs.xml' );

	/**
	 * specifies the root relative path to the migrations folder
	 */
	if( !defined( '__MIGRATIONS_PATH__' ))				define( '__MIGRATIONS_PATH__',				__CONFIG_PATH__ . '/migrations' );

	/**
	 * specifies the default controller path
	 */
	if( !defined( '__CONTROLLERS_PATH__' ))				define( '__CONTROLLERS_PATH__',				__ROOT__ . '/app/controllers' );

	/**
	 * specifies the default model path
	 */
	if( !defined( '__MODELS_PATH__' ))              	define( '__MODELS_PATH__',      			__ROOT__ . '/app/models' );

	/**
	 * specifies the root relative path to the plugins folder
	 */
	if( !defined( '__PLUGINS_PATH__' ))					define( '__PLUGINS_PATH__',					__ROOT__ . '/app/plugins' );

	/**
	 * specifies the default template path
	 */
	if( !defined( '__TEMPLATES_PATH__' ))				define( '__TEMPLATES_PATH__',				__ROOT__ . '/app/layouts' );

	/**
	 * specifies the default fixtures path
	 */
	if( !defined( '__FIXTURES_PATH__' ))				define( '__FIXTURES_PATH__',				__ROOT__ . '/app/tests/fixtures' );

	/**
	 * specifies the default functional test path
	 */
	if( !defined( '__FUNCTIONAL_TESTS_PATH__' ))		define( '__FUNCTIONAL_TESTS_PATH__',		__ROOT__ . '/app/tests/functional' );

	/**
	 * specifies the default unit test path
	 */
	if( !defined( '__UNIT_TESTS_PATH__' ))				define( '__UNIT_TESTS_PATH__',				__ROOT__ . '/app/tests/unit' );

	/**
	 * specifies the default view path
	 */
	if( !defined( '__VIEWS_PATH__' ))					define( '__VIEWS_PATH__',					__ROOT__ . '/app/views' );

	/**
	 * specifies the root relative path to the log folder
	 */
	if( !defined( '__LOG_PATH__' ))						define( '__LOG_PATH__',						__ROOT__ . '/logs' );

	/**
	 * specifies the root relative path to the system folder
	 */
	if( !defined( '__SYSTEM_PATH__' ))					define( '__SYSTEM_PATH__',					__ROOT__ . '/system' );

	/**
	 * specifies the root relative path to the libs folder
	 */
	if( !defined( '__LIB_PATH__' ))						define( '__LIB_PATH__',						__SYSTEM_PATH__ . '/libs' );

	/*
	 * framework constants
	 * These constants are defaults, and may be overridden
	 */

	/**
	 * no of spaces to indent xml elements
	 * @var int
	 * @ignore
	 */
	const INDENT = 0;

	/**
	 * characters used to denote carriage return
	 * @var string
	 * @ignore
	 */
	const CARAGERETURN = '';

	/**
	 * include prefix
	 * @var string
	 * @ignore
	 */
	const INCLUDEPREFIX = 'PRE';

	// set env
	$_SERVER[__ENV_PARAMETER__] = isset($_SERVER[__ENV_PARAMETER__])?$_SERVER[__ENV_PARAMETER__]:__DEV_ENV__;

	// include required scripts
	require __SYSTEM_PATH__ . '/base/object.class.php';
	require __SYSTEM_PATH__ . '/base/applicationbase.class.php';
	require __SYSTEM_PATH__ . '/base/framework.info.file';
	require __SYSTEM_PATH__ . '/base/classloader.inc.php';
	require __SYSTEM_PATH__ . '/base/build.class.php';
	require __SYSTEM_PATH__ . '/rum.class.php';

	if(__HOST__=='localhost' && isset($_GET[__PATH_REQUEST_PARAMETER__]) && isset($_GET["id"]) && $_GET[__PATH_REQUEST_PARAMETER__]=='dev' && ($_GET["id"]=='clean' || $_GET["id"]=='build'))
	{
		ob_start();
		Build::$verbose = true;
		Build::clean();
		Build::$verbose = false;
	}

	// set autoloader
	\spl_autoload_register('\System\Base\__autoload');
?>