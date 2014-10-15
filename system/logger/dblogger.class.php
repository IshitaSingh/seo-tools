<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Logger;


	/**
	 * Provides access to read/write log messages
	 *
	 * @property   string $path path to log file
	 * @property   string $file name of log file
	 * @property   int $maxFileSize max size of log file in KB, 0 for infinite
	 * 
	 * @package			PHPRum
	 * @subpackage		Logger
	 * @author			Darnell Shinbine
	 */
	class DBLogger extends LoggerBase
	{
		/**
		 * data adapter
		 * @var DataAdapter
		 */
		private $db;

		/**
		 * name of table
		 * @var string
		 */
		private $table			= __LOGS_TABLENAME__;


		/**
		 * Constructor
		 *
		 * @param string $table table name
		 * @param string $dsn database connection string
		 */
		public function __construct($table = __LOGS_TABLENAME__, $dsn = '')
		{
			$this->table = $table;

			if($dsn)
			{
				$this->db = \System\DB\DataAdapter::create($dsn);
			}
			else
			{
				$this->db =& \System\Base\ApplicationBase::getInstance()->dataAdapter;
			}
		}


		/**
		 * This method writes a log message to memory
		 *
		 * @param  string	$message		message to log
		 * @param  string	$category		message category
		 * @return void
		 */
		public function log($message, $category)
		{
			try
			{
				$this->db->queryBuilder()
					->insertInto($this->table, array('datetime', 'message', 'category'))
					->values(array(date('Y-m-d g:ia'), $message, $category))
					->execute();
			}
			catch(\System\DB\DatabaseException $e)
			{
				\System\Base\ApplicationBase::getInstance()->dataAdapter->addTableSchema(new \System\DB\TableSchema(
					array(
						'name' => $this->table),
					array(),
					array(new \System\DB\ColumnSchema(array(
						'name' => 'datetime',
						'table' => $this->table,
						'type' => 'DATETIME',
						'length' => 30,
						'notNull' => true,
						'datetime' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'message',
						'table' => $this->table,
						'type' => 'VARCHAR',
						'length' => 255,
						'notNull' => true,
						'string' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'category',
						'table' => $this->table,
						'type' => 'VARCHAR',
						'length' => 20,
						'notNull' => true,
						'string' => true)))));

				$this->db->queryBuilder()
					->insertInto($this->table, array('datetime', 'message', 'category'))
					->values(array(date('Y-m-d g:ia'), $message, $category))
					->execute();
			}
		}


		/**
		 * This method retrieves all log messages
		 *
		 * @param  string	$category		message category
		 * @return array
		 */
		public function logs($category)
		{
			return $this->db->queryBuilder()
				->select()
				->from($this->table)
				->where($this->table, 'category', '=', $category)
				->openDataSet()->rows;
		}


		/**
		 * This method flushes all messages
		 *
		 * @param  string	$category		message category
		 * @return void
		 */
		public function flush($category)
		{
			return $this->db->queryBuilder()
				->delete()
				->from($this->table)
				->where($this->table, 'category', '=', $category)
				->execute();
		}
	}
?>