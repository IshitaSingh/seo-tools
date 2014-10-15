<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Migrate;


	/**
	 * Provides base functionality for database migration
	 *
	 * @package			PHPRum
	 * @subpackage		Migrate
	 * @author			Darnell Shinbine
	 */
	abstract class MigrationBase
	{
		/**
		 * database
		 * @var SQLDataAdapter
		 */
		protected $db;

		/**
		 * version
		 * @var int
		 */
		public $version;


		/**
		 * Constructor
		 */
		final public function  __construct()
		{
			$this->db = \System\Base\ApplicationBase::getInstance()->dataAdapter;
		}

		/**
		 * up migration
		 * @return SQLStatement
		 */
		abstract public function up();

		/**
		 * down migration
		 * @return SQLStatement
		 */
		abstract public function down();
	}
?>