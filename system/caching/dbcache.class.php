<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Caching;


	/**
	 * Provides access to read/write cache objects from a database
	 *
	 * @package			PHPRum
	 * @subpackage		Caching
	 * @author			Darnell Shinbine
	 */
	class DBCache extends CacheBase
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
		private $table			= __CACHE_TABLENAME__;


		/**
		 * Constructor
		 *
		 * @param string $table table name
		 * @param string $dsn database connection string
		 */
		public function __construct($table = __CACHE_TABLENAME__, $dsn = '')
		{
			if($table)
			{
				$this->table = $table;
			}

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
		 * Retrieves a value from cache with a specified key.
		 *
		 * @param string	$id			the key identifying the value to be cached
		 * @return string				the value stored in cache
		 */
		public function get( $id )
		{
			try
			{
				$value = ($this->db->queryBuilder()
					->select($this->table, 'value')
					->from($this->table)
					->where($this->table, 'cache_id', '=', (string)$id)
					->where($this->table, 'expires', '>', time())
					->openDataSet()
					->row["value"]);

				if($value)
				{
					return \unserialize($value);
				}
			}
			catch(\System\DB\DatabaseException $e)
			{
				\System\Base\ApplicationBase::getInstance()->dataAdapter->addTableSchema(new \System\DB\TableSchema(
					array(
						'name' => $this->table,
						'primaryKey' => 'cache_id'),
					array(),
					array(new \System\DB\ColumnSchema(array(
						'name' => 'cache_id',
						'table' => $this->table,
						'type' => 'VARCHAR',
						'primaryKey' => true,
						'length' => 255,
						'notNull' => true,
						'string' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'value',
						'table' => $this->table,
						'type' => 'MEDIUMBLOB',
						'notNull' => true,
						'blob' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'expires',
						'table' => $this->table,
						'type' => 'INTEGER',
						'notNull' => true,
						'integer' => true)))));
			}

			return null;
		}


		/**
		 * Stores a value identified by a key into cache.
		 *
		 * If the cache already contains such a key, the existing value and
		 * expiration time will be replaced with the new ones.
		 *
		 * @param string	$id		the key identifying the value to be cached
		 * @param string 	$value	the value to be cached
		 * @param integer	$expires	the expiration time of the value in seconds
		 * 								0 means never expire,
		 * @return void
		 */
		public function put( $id, $value, $expires = 0 )
		{
			if($expires) {
				$expires = time() + (int)$expires;
			}
			else {
				$expires = 2147483647;
			}

			$this->clear($id);

			$this->db->queryBuilder()
				->insertInto($this->table, array('cache_id', 'value', 'expires'))
				->values(array((string)$id, \serialize($value), $expires))
				->execute();
		}


		/**
		 * Deletes a value with the specified key from cache
		 *
		 * @param  string	$id		the key identifying the value to be cached
		 * @return void
		 */
		public function clear( $id )
		{
			$this->db->queryBuilder()
				->delete()
				->from($this->table)
				->where($this->table, 'cache_id', '=', (string)$id)
				->execute();
		}


		/**
		 * Deletes all values from cache.
		 *
		 * @return void
		 */
		public function flush()
		{
			$this->db->queryBuilder()
				->truncate($this->table)
				->execute();
		}


		/**
		 * get id array
		 *
		 * @return array
		 */
		public function getIdArray()
		{
			$idArray = array();
			foreach($this->db->queryBuilder()
				->select('*', 'cache_id')
				->from($this->table)
				->openDataSet()->rows as $row)
			{
				$idArray[] = $row["cache_id"];
			}

			return $idArray;
		}
	}
?>