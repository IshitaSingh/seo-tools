<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Test;


	/**
	 * Provides the base funcionality of the application.  This class recieves command line
	 * data and executes the application
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class Tester
	{
		/**
		 * run unit test case
		 *
		 * @param   string		$testCase		name of test
		 * @param   \SimpleReporter	$reporter	instance of a \SimpleReporter
		 * @return  void
		 */
		public function runUnitTestCase( $testCase, \SimpleReporter $reporter ) {
			$this->loadTestConfig();
			$this->getUnitTestCase( $testCase )->run( $reporter );
		}


		/**
		 * run functional test case
		 *
		 * @param   string		$testCase		name of module
		 * @param   \SimpleReporter	$reporter	instance of a \SimpleReporter
		 * @return  void
		 */
		public function runFunctionalTestCase( $testCase, \SimpleReporter $reporter ) {
			$this->loadTestConfig();
			$this->getFunctionalTestCase( $testCase )->run( $reporter );
		}


		/**
		 * run all test cases
		 *
		 * @param   \SimpleReporter	$reporter	instance of a \SimpleReporter
		 * @return  void
		 */
		public function runAllTestCases(\SimpleReporter $reporter) {
			$this->loadTestConfig();

			require_once __LIB_PATH__ . '/simpletest/test_case.php';

			$tests = new \TestSuite( \System\Base\ApplicationBase::getInstance()->applicationId . '_TestSuite' );

			foreach( $this->getAllUnitTestCases() as $testCase ) {
				$tests->addTestCase( $this->getUnitTestCase( $testCase ) );
			}

			foreach( $this->getAllFunctionalTestCases() as $testCase ) {
				$tests->addTestCase( $this->getFunctionalTestCase( $testCase ) );
			}

			$tests->run($reporter);
		}


		/**
		 * return unit test case
		 *
		 * @param   string		$testCase		name of test
		 * @return  UnitTestCaseBase
		 */
		public function getUnitTestCase( $testCase )
		{
			$testPath = \System\Base\ApplicationBase::getInstance()->config->unittests . '/' . $testCase . strtolower( __TESTCASE_SUFFIX__ ) . __CLASS_EXTENSION__;
			$testCaseClass = \System\Base\ApplicationBase::getInstance()->namespace . '\\'.__MODELS_NAMESPACE__.'\\' . ucwords( $testCase ) . __TESTCASE_SUFFIX__;

			if( include_once $testPath )
			{
				if( class_exists( $testCaseClass ))
				{
					return new $testCaseClass( $testCase );
				}
				else
				{
					throw new \System\Base\InvalidOperationException( "class `$testCaseClass` does not exist" );
				}
			}
			else
			{
				throw new \System\Utils\FileNotFoundException( "file `$testPath` does not exist" );
			}
		}


		/**
		 * return functional test case
		 *
		 * @param   string		$module		name of module
		 * @return  ControllerTestCaseBase
		 */
		public function getFunctionalTestCase( $module )
		{
			$controller = str_replace( '-', '_', $module );
			$controllerIncludePath = \System\Base\ApplicationBase::getInstance()->config->controllers . '/' . strtolower( $controller ) . __CONTROLLER_EXTENSION__;

			$testPath = \System\Base\ApplicationBase::getInstance()->config->functionaltests . '/' . strtolower( $module ) . strtolower( __CONTROLLER_TESTCASE_SUFFIX__ ) . __CLASS_EXTENSION__;
			$testCaseClass = \System\Base\ApplicationBase::getInstance()->namespace . "\\".__CONTROLLERS_NAMESPACE__."\\" . ucwords( str_replace('/', '\\', $module )) . __CONTROLLER_TESTCASE_SUFFIX__;

			if( !defined( \System\Base\INCLUDEPREFIX . $controllerIncludePath ))
			{
				define( \System\Base\INCLUDEPREFIX . $controllerIncludePath, true );

				if( !include_once( $controllerIncludePath ))
				{
					throw new \System\Utils\FileNotFoundException( "file `$controllerIncludePath` does not exist" );
				}
			}

			if( !defined( \System\Base\INCLUDEPREFIX . $testPath ))
			{
				define( \System\Base\INCLUDEPREFIX . $testPath, true );

				if( !include_once( $testPath ))
				{
					throw new \System\Utils\FileNotFoundException( "file `$testPath` does not exist" );
				}
			}

			if( class_exists( $testCaseClass ))
			{
				return new $testCaseClass( $controller );
			}
			else
			{
				throw new \System\Base\InvalidOperationException( "class `$testCaseClass` does not exist, make sure you specify the correct namespace" );
			}
		}


		/**
		 * return all unit test cases
		 *
		 * @return  array					array of test modules
		 */
		public function getAllUnitTestCases() {

			$modules = array();
			$dir = dir( \System\Base\ApplicationBase::getInstance()->config->unittests );

			while( false !== ( $file = $dir->read() )) {
				if( stripos( $file, '.php' ) === strlen($file) - 4 ) {
					$modules[] = preg_replace( '^' . strtolower( __TESTCASE_SUFFIX__ . __CLASS_EXTENSION__ ) . '$^', '\\1', $file );
				}
			}

			$dir->close();
			return $modules;
		}


		/**
		 * return all functional test cases
		 *
		 * @param   string		$path		initial path
		 * @return  array					array of test modules
		 */
		public function getAllFunctionalTestCases( $path = '' ) {

			if( !$path ) $path = \System\Base\ApplicationBase::getInstance()->config->functionaltests;

			$modules = array();
			$dir = dir( $path );

			while( false !== ( $file = $dir->read() )) {
				if( $file != '.' && $file != '..' ) {
					if( is_dir( $path . '/' . $file )) {
						$modules = array_merge( $modules, $this->getAllFunctionalTestCases( $path . '/' . $file ));
					}
					else {
						if( stripos( $file, '.php' ) === strlen($file) - 4 ) {
							$module = str_replace( \System\Base\ApplicationBase::getInstance()->config->functionaltests . '/', '', $path . '/' . $file );
							$module = preg_replace( '^' . '(.*)$^', '\\1', $module );
							$module = preg_replace( '^' . strtolower( __CONTROLLER_TESTCASE_SUFFIX__ . __CLASS_EXTENSION__ ) . '$^', '\\1', $module );
							$modules[] = $module;
						}
					}
				}
			}

			$dir->close();
			return $modules;
		}


		/**
		 * run all test cases
		 *
		 * @param   \SimpleReporter	$reporter	instance of a \SimpleReporter
		 * @return  void
		 */
		public function loadTestConfig() {
			restore_error_handler();
			error_reporting( E_ALL & ~E_STRICT );

			// load global app configuration
//			if(file_exists(__CONFIG_PATH__ . __APP_CONF_FILENAME__)) {
//				\System\Base\ApplicationBase::getInstance()->loadAppConfig( __CONFIG_PATH__ . __APP_CONF_FILENAME__ );
//			}

			// load test env app configuration
//			if(file_exists(__ENV_PATH__ . '/' . __TEST_ENV__ . __APP_CONF_FILENAME__)) {
//				\System\Base\ApplicationBase::getInstance()->loadAppConfig( __ENV_PATH__ . '/' . __TEST_ENV__ . __APP_CONF_FILENAME__ );
//			}

//			if(\Rum::config()->dsn)
//			{
//				dmp(\Rum::app()->dataAdapter);
//				\Rum::app()->dataAdapter = \System\DB\DataAdapter::create(\Rum::config()->dsn);
//			}
		}
	}
?>