<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace Rum\Migrate;
	use System\Console\ConsoleApplicationBase;


	/**
	 * deplomyent script
	 *
	 * @package			PHPRum
	 * @subpackage		Migrate
	 * @author			Darnell Shinbine
	 */
	final class Test extends ConsoleApplicationBase
	{
		/**
		 * execute the application
		 *
		 * @param	int			$argc		Number of command line arguments
		 * @param	array		$argv		Array of command line arguments
		 *
		 * @return  void
		 */
		protected function execute()
		{
			global $argc, $argv;

			if($argc > 1)
			{
				if($argv[1]=="run_all")
				{
					$this->runAllTestCases($this->_getTestCaseReporter());
					exit;
				}
				elseif($argv[1]=="run_unit_test")
				{
					if(isset($argv[2]))
					{
						$this->runUnitTestCase($argv[2], $this->_getTestCaseReporter());
						exit;
					}
					else
					{
						print("You must specify the unit testcase parameter");
					}
				}
				elseif($argv[1]=="run_functional_test")
				{
					if(isset($argv[2]))
					{
						$this->runFunctionalTestCase($argv[2], $this->_getTestCaseReporter());
						exit;
					}
					else
					{
						print("You must specify the controller testcase parameter");
					}
				}
			}
		}


		/**
		 * event triggered by an uncaught Exception thrown in the application, can be overridden to provide error handling.
		 *
		 * @param  \Exception	$e
		 *
		 * @return void
		 */
		protected function handleException(\Exception $e) {die($e->getMessage().PHP_EOL);}


		/**
		 * event triggered by an error in the application, can be overridden to provide error handling.
		 *
		 * @param  string	$errno		error code
		 * @param  string	$errstr		error description
		 * @param  string	$errfile	file
		 * @param  string	$errline	line no.
		 *
		 * @return void
		 */
		protected function handleError($errno, $errstr, $errfile, $errline) {die("{$errstr} in {$errfile} on line {$errline}".PHP_EOL);}
	}
?>