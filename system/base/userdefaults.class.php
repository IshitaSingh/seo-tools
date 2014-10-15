<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Provides access to read/write cache objects from a database
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class UserDefaults
	{
		/**
		 * name of table
		 * @var string
		 */
		static $table			= __USERDEFAULTS_TABLENAME__;


		/**
		 * Constructor
		 *
		 * @param string $table table name
		 * @param string $dsn database connection string
		 */
		public function __construct($table = __USERDEFAULTS_TABLENAME__)
		{
			if($table)
			{
				self::$table = $table;
			}
		}


		/**
		 * Retrieves a value from cache with a specified key.
		 *
		 * @param string	$key			the key identifying the value to be cached
		 * @return string				the value stored in cache
		 */
		public static function getObject( $key )
		{
			try
			{
				return (\Rum::db()->queryBuilder()
					->select(self::$table, 'value')
					->from(self::$table)
					->where(self::$table, 'user_id', '=', \System\Security\Authentication::$identity)
					->where(self::$table, 'user_default_id', '=', (string)$key)
					->openDataSet()
					->row["value"]);
			}
			catch(\System\DB\DatabaseException $e)
			{
				\System\Base\ApplicationBase::getInstance()->dataAdapter->addTableSchema(new \System\DB\TableSchema(
					array(
						'name' => self::$table),
					array(),
					array(new \System\DB\ColumnSchema(array(
						'name' => 'user_id',
						'table' => self::$table,
						'type' => 'VARCHAR',
						'primaryKey' => true,
						'length' => 255,
						'notNull' => true,
						'string' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'user_default_id',
						'table' => self::$table,
						'type' => 'VARCHAR',
						'primaryKey' => true,
						'length' => 255,
						'notNull' => true,
						'string' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'value',
						'table' => self::$table,
						'type' => 'MEDIUMBLOB',
						'length' => 255,
						'notNull' => true,
						'blob' => true)))));

				// Try one more time (no trapping exceptions)
				return (\Rum::db()->queryBuilder()
					->select(self::$table, 'value')
					->from(self::$table)
					->where(self::$table, 'user_id', '=', \System\Security\Authentication::$identity)
					->where(self::$table, 'user_default_id', '=', (string)$key)
					->openDataSet()
					->row["value"]);
			}

			return null;
		}


		/**
		 * Stores a value identified by a key into user defaults.
		 *
		 * If the cache already contains such a key, the existing value and
		 * expiration time will be replaced with the new ones.
		 *
		 * @param string	$key	the key identifying the value
		 * @param string 	$object	the object to be stored
		 * @return void
		 */
		public static function setObject( $key, $object )
		{
			self::clear($key);

			\Rum::db()->queryBuilder()
				->insertInto(self::$table, array('user_id', 'user_default_id', 'value'))
				->values(array(\System\Security\Authentication::$identity, (string)$key, (string)$object))
				->execute();
		}


		/**
		 * Removes a value with the specified key from user defaults
		 *
		 * @param  string	$id		the key identifying the value to be cached
		 * @return void
		 */
		public static function remove( $key )
		{
			\Rum::db()->queryBuilder()
				->delete()
				->from(self::$table)
				->where(self::$table, 'user_id', '=', \System\Security\Authentication::$identity)
				->where(self::$table, 'user_default_id', '=', (string)$key)
				->execute();
		}


		/**
		 * Removes all user data from user defaults
		 *
		 * @return void
		 */
		public static function flush()
		{
			\Rum::db()->queryBuilder()
				->delete()
				->from(self::$table)
				->where(self::$table, 'user_id', '=', \System\Security\Authentication::$identity)
				->execute();
		}
	}
?>