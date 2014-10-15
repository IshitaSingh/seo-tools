<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Test;
	use \TextReporter;

	/**
	 * include simpletest framework
	 */
	require_once __LIB_PATH__ . '/simpletest/reporter.php';


	/**
	 * Provides basic reporting for test cases
	 *
	 * @package			PHPRum
	 * @subpackage		TestCase
	 * @author			Darnell Shinbine
	 */
	class TestReporter extends TextReporter
	{
		/**
		 * elapsed
		 * @var int
		 */
		private $_elapsed = 0;


		/**
		 * set elapsed time
		 *
		 * @param  float	$elapsed	time in s
		 * @return void
		 */
		public function setElapsedTime( $elapsed ) {
			$this->_elapsed = $elapsed;
		}
	}
?>