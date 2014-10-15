<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Test;


	/**
	 * Provides base functionality for the UnitTestCase
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class UnitTestCaseBase extends TestCaseBase {


		/**
		 * Constructor
		 *
		 * @param   string			$testCase		Name of test case
		 *
		 * @return  void
		 */
		public function __construct( $testCase ) {
			parent::__construct( ucwords( $testCase ) . '_UnitTestCase' );
		}


		/**
		 * setup test module
		 *
		 * @return  void
		 */
		final public function setUp() {
			parent::setUp();
		}


		/**
		 * clean test module
		 *
		 * @return  void
		 */
		final public function tearDown() {
			parent::tearDown();
		}
	}
?>