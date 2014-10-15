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
	abstract class MigrationsTestCaseBase extends TestCaseBase {

		/**
		 * states
		 * @var array
		 */
		protected $state = array();


		/**
		 * setup test module
		 *
		 * @return  void
		 */
		public function setUp() {
			parent::setUp();
		}


		/**
		 * clean test module
		 *
		 * @return  void
		 */
		public function tearDown() {
			parent::tearDown();
		}


		/**
		 * returns the response as XMLEntity object
		 *
		 * @return  XMLEntity
		 */
		protected function xxx()
		{
			$migrate = new \Rum\Migrate\Migrations();
			$migrations = $migrate->getMigrations();

			// step to bottom
			$migrate->to($migrate->getLatestVersion());
			$this->storeState(\System\Base\ApplicationBase::getInstance()->dataAdapter->getTables());
			$migrate->to(0);
			$migrate->to($migrate->getLatestVersion());
			$this->compareState(\System\Base\ApplicationBase::getInstance()->dataAdapter->getTables());
		}


		/**
		 * store state
		 *
		 * @return void
		 */
		private function storeState(&$tables)
		{
			foreach($tables->rows as $table)
			{
				$tablename = \array_values($table);
				$ds = $tables->dataAdapter->openDataSet($tablename[0]);

				$this->state[$tablename[0]]['fields'] = \serialize($ds->fields);
			}
		}


		/**
		 * compare state
		 *
		 * @return void
		 */
		private function compareState(&$tables)
		{
			foreach($tables->rows as $table)
			{
				$tablename = \array_values($table);
				$ds = $tables->dataAdapter->openDataSet($tablename[0]);

				$this->assertEqual($tablename[0], $this->state[$tablename[0]]['fields'] != \serialize($this->state[$tablename[0]]['fields']), $tablename[0].'not valid');
			}
		}
	}
?>