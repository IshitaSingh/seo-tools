<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\MySQL;
	use \System\DB\DataAdapter;


	/**
	 * Represents an open connection to a MySQL database
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class MySQLDataAdapter extends DataAdapter
	{
		/**
		 * Handle to the open connection to the datasource
		 * @var resource
		 */
		private $link;

		/**
		 * Specifies the character set
		 * @var string
		 */
		protected $charset				= 'utf8';


		/**
		 * opens a connection to a mysql database
		 * @return bool						TRUE if successfull
		 */
		public function open()
		{
			\trigger_error("Use of the MySQLDataAdapter adapter is discouraged. Use the MySQLiDataAdapter or PDODataAdapter adapters instead.", E_USER_DEPRECATED);

			if( !$this->link )
			{
				if( isset( $this->args['server'] ) &&
					isset( $this->args['uid'] ) &&
					isset( $this->args['pwd'] ) &&
					isset( $this->args['database'] ))
				{
					$pconnect = false;
					if( isset( $this->args['pconnect'] ))
					{
						if( $this->args['pconnect'] == 'true' || $this->args['pconnect'] == '1' )
						$pconnect = true;
					}

					if($pconnect)
					{
						$this->link = \mysql_pconnect( $this->args['server'], $this->args['uid'], $this->args['pwd'], true );
					}
					else
					{
						$this->link = \mysql_connect( $this->args['server'], $this->args['uid'], $this->args['pwd'], true );
					}

					\mysql_set_charset( $this->charset, $this->link );
					\mysql_query("SET NAMES {$this->charset};", $this->link );

					if( $this->link )
					{
						if( mysql_select_db( $this->args['database'], $this->link ))
						{
							return true;
						}
						else
						{
							throw new \System\DB\DatabaseException(mysql_error( $this->link ));
						}
					}
					else
					{
						throw new \System\DB\DatabaseException("Could not connect to database " . \mysql_error());
					}
				}
				else
				{
					throw new \System\DB\DataAdapterException("Missing required connection string parameter");
				}
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection already open");
			}
		}


		/**
		 * closes an open connection
		 *
		 * @return bool					true if successfull
		 */
		public function close()
		{
			if( $this->link )
			{
				if( mysql_close( $this->link ))
				{
					$this->link = null;
					return true;
				}
				else
				{
					throw new \System\DB\DataAdapterException("could not close mysql connection");
				}
			}
			else
			{
				throw new \System\DB\DataAdapterException("connection already closed");
			}
		}


		/**
		 * returns true if a connection to a datasource is currently open
		 *
		 * @return bool					true if connection open
		 */
		public function opened()
		{
			return (bool)$this->link;
		}


		/**
		 * prepare an SQL statement
		 * Creates a prepared statement bound to parameters specified by the @symbol
		 * e.g. SELECT * FROM `table` WHERE user=@user
		 *
		 * @param  string	$statement	SQL statement
		 * @param  array	$parameters	array of parameters to bind
		 * @return SQLStatement
		 */
		public function prepare($statement, array $parameters = array())
		{
			$mysqlStatement = new MySQLStatement($this, $this->link);
			$mysqlStatement->prepare($statement, $parameters);
			return $mysqlStatement;
		}


		/**
		 * fills a DataSet object with the current DataAdapter
		 *
		 * @param  DataSet	&$ds		empty DataSet object
		 * @return void
		 */
		public function fill( \System\DB\DataSet &$ds )
		{
			if( $this->link )
			{
				$result = $this->query( $ds->source );

				if( $result )
				{
					$fields = array();
					$fieldMeta = array();
					$fieldCount = \mysql_num_fields($result);
					for($i=0; $i < $fieldCount; $i++)
					{
						$meta = \mysql_fetch_field($result, $i);

						$fields[] = $meta->name;
						$fieldMeta[] = new \System\DB\ColumnSchema(array(
							'name' => (string) $meta->name,
							'table' => (string) $meta->table,
							'type' => (string) $meta->type,
							'length' => \mysql_field_len($result, $i),
							'notNull' => (bool) $meta->not_null,
							'primaryKey' => (bool) $meta->primary_key,
							'foreignKey' => false,
							'unique' => (bool) $meta->unique_key,
							'numeric' => (bool) $meta->numeric,
							'string' => \mysql_field_type($result, $i) === 'string',
							'integer' => \mysql_field_type( $result, $i ) === 'int',
							'real' => \mysql_field_type($result, $i) === 'real',
							'date' => \mysql_field_type($result, $i) === 'date',
							'time' => \mysql_field_type($result, $i) === 'time',
							'datetime' => \mysql_field_type($result, $i) === 'datetime',
							'boolean' => \mysql_field_len($result, $i) === 1 && \mysql_field_type($result, $i) === 'int',
							'autoIncrement' => \strpos( mysql_field_flags($result, $i), 'auto_increment' ) !== false,
							'blob' => \strpos( mysql_field_flags($result, $i), 'binary' ) !== false));
					}

					$rows = array();
					while($row = \mysql_fetch_assoc( $result ))
					{
						$rows[] = $row;
					}

					$ds->setTable(\mysql_field_table($result, 0));
					$ds->setFieldMeta( $fieldMeta );
					$ds->setFields( $fields );
					$ds->setRows( $rows );

					\mysql_free_result( $result );
				}
				else
				{
					throw new \System\DB\DatabaseException(mysql_error($this->link));
				}
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection is closed");
			}
		}


		/**
		 * builds a DataBaseSchema object
		 *
		 * @return DatabaseSchema
		 */
		public function buildSchema()
		{
			$databaseProperties = array();
			$tableSchemas = array();

			$tables = $this->query( "SHOW TABLES" );
			while($table = \mysql_fetch_array($tables, MYSQL_NUM))
			{
				$i=0;
				$tableProperties = array('name'=>$table[0]);
				$foreignKeys = array();
				$columnSchemas = array();

				$columns = $this->query( "SELECT * FROM `{$table[0]}` WHERE 0" );
				while($i < \mysql_num_fields($columns))
				{
					$meta = \mysql_fetch_field($columns, $i);

					if($meta->primary_key)
					{
						$tableProperties['primaryKey'] = $meta->name;
					}

					$columnSchemas[] = new \System\DB\ColumnSchema(array(
						'name' => (string) $meta->name,
						'table' => (string) $meta->table,
						'type' => (string) $meta->type,
						'length' => \mysql_field_len($columns, $i),
						'notNull' => (bool) $meta->not_null,
						'primaryKey' => (bool) $meta->primary_key,
						'foreignKey' => false,
						'unique' => (bool) $meta->unique_key,
						'numeric' => (bool) $meta->numeric,
						'string' => \mysql_field_type($columns, $i) === 'string',
						'integer' => \mysql_field_type($columns, $i ) === 'int',
						'real' => \mysql_field_type($columns, $i) === 'real',
						'date' => \mysql_field_type($columns, $i) === 'date',
						'time' => \mysql_field_type($columns, $i) === 'time',
						'datetime' => \mysql_field_type($columns, $i) === 'datetime',
						'boolean' => \mysql_field_len($columns, $i) === 1 && \mysql_field_type( $columns, $i ) === 'int',
						'autoIncrement' => \strpos( mysql_field_flags($columns, $i), 'auto_increment' ) !== false,
						'blob' => \strpos( mysql_field_flags($columns, $i), 'binary' ) !== false));

					$i++;
				}

				$tableSchemas[] = new \System\DB\TableSchema($tableProperties, $foreignKeys, $columnSchemas);
			}

			return new \System\DB\DatabaseSchema($databaseProperties, $tableSchemas);
		}


		/**
		 * creats a TableSchema object
		 *
		 * @return DatabaseSchema
		 */
		public function addTableSchema( \System\DB\TableSchema &$tableSchema )
		{
			$primaryKeys = array();
			$indexKeys = array();
			$uniqueKeys = array();
			$columns = "";

			foreach($tableSchema->columnSchemas as $columnSchema)
			{
				$type = "";

				if($columnSchema->integer)
				{
					$type = "INT({$columnSchema->length})";
				}
				elseif($columnSchema->real)
				{
					$type = "DOUBLE({$columnSchema->length})";
				}
				elseif($columnSchema->boolean)
				{
					$type = "TINYINT(1)";
				}
				elseif($columnSchema->date)
				{
					$type = "DATE";
				}
				elseif($columnSchema->time)
				{
					$type = "TIME";
				}
				elseif($columnSchema->datetime)
				{
					$type = "DATETIME";
				}
				elseif($columnSchema->blob)
				{
					$type = "MEDIUMBLOB";
				}
				else
				{
					$type = "VARCHAR({$columnSchema->length}) CHARACTER SET {$this->charset} COLLATE {$this->collation}";
				}

				if($columns) $columns .= ",\n	";
				$columns .= "`{$columnSchema->name}` {$type}".($columnSchema->notNull?' NOT NULL':'').($columnSchema->autoIncrement?' AUTO_INCREMENT':'');

				if($columnSchema->primaryKey)
				{
					$primaryKeys[] = $columnSchema->name;
				}

				if($columnSchema->foreignKey)
				{
					$indexKeys[] = $columnSchema->name;
				}

				if($columnSchema->unique)
				{
					$uniqueKeys[] = $columnSchema->name;
				}
			}

			if($primaryKeys)
			{
				$column = "";
				foreach($primaryKeys as $primaryKey)
				{
					if($column) $column .= ", ";
					$column .= "`{$primaryKey}`";
				}

				$columns .= ",\n	PRIMARY KEY ({$column})";
			}

			if($indexKeys)
			{
				$column = "";
				foreach($indexKeys as $indexKey)
				{
					if($column) $column .= ", ";
					$column .= "`{$indexKey}`";
				}

				$columns .= ",\n	INDEX ({$column})";
			}

			if($uniqueKeys)
			{
				$column = "";
				foreach($uniqueKeys as $uniqueKey)
				{
					if($column) $column .= ", ";
					$column .= "`{$uniqueKey}`";
				}

				$columns .= ",\n	UNIQUE ({$column})";
			}

			$this->execute("CREATE TABLE `{$tableSchema->name}` (\n	{$columns}\n);");
		}


		/**
		 * alters a TableSchema object
		 *
		 * @return DatabaseSchema
		 */
		public function alterTableSchema( \System\DB\TableSchema &$tableSchema )
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * drops a TableSchema object
		 *
		 * @return DatabaseSchema
		 */
		public function dropTableSchema( \System\DB\TableSchema &$tableSchema )
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * attempt to insert a record into the datasource
		 *
		 * @param  DataSet	&$ds		empty DataSet object
		 * @return void
		 */
		public function insert( \System\DB\DataSet &$ds )
		{
			if( $this->link )
			{
				$tableSchema = $ds->dataAdapter->getSchema()->seek($ds->table);
				$this->queryBuilder()
					->insertInto($ds->table, $ds->fields)
					->values($ds->row)
					->execute();

				if($tableSchema->primaryKey)
				{
					$ds[$tableSchema->primaryKey] = $this->getLastInsertId();
				}
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection is closed");
			}
		}


		/**
		 * attempt to update a record in the datasource
		 *
		 * @param  DataSet	&$ds		empty DataSet object
		 * @return void
		 */
		public function update( \System\DB\DataSet &$ds )
		{
			if( $this->link )
			{
				$tableSchema = $ds->dataAdapter->getSchema()->seek($ds->table);

				if($tableSchema->primaryKey)
				{
					$this->queryBuilder()
						->update($ds->table)
						->setColumns($ds->table, $ds->fields, $ds->row)
						->where($ds->table, $tableSchema->primaryKey, '=', $ds[$tableSchema->primaryKey])
						->execute();
				}
				else
				{
					throw new \System\DB\DataAdapterException("Cannot update record, no primary key is defined");
				}
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection is closed");
			}
		}


		/**
		 * attempt to delete a record in the datasource
		 *
		 * @param  DataSet	&$ds		empty DataSet object
		 * @return void
		 */
		public function delete( \System\DB\DataSet &$ds )
		{
			if( $this->link )
			{
				$tableSchema = $ds->dataAdapter->getSchema()->seek($ds->table);

				if($tableSchema->primaryKey)
				{
					$this->queryBuilder()
						->delete()
						->from($ds->table)
						->where($ds->table, $tableSchema->primaryKey, '=', $ds[$tableSchema->primaryKey])
						->execute();
				}
				else
				{
					throw new \System\DB\DataAdapterException("Cannot delete record, no primary key is defined");
				}
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection is closed");
			}
		}


		/**
		 * creats a QueryBuilder object
		 *
		 * @return SQLQueryBuilder
		 */
		public function queryBuilder()
		{
			return new MySQLQueryBuilder($this, $this->link);
		}


		/**
		 * creats a Transaction object
		 *
		 * @return MySQLTransaction
		 */
		public function beginTransaction()
		{
			return new MySQLTransaction($this);
		}


		/**
		 * return id of last record inserted
		 *
		 * @return int
		 */
		public function getLastInsertId()
		{
			if( $this->link )
			{
				return mysql_insert_id( $this->link );
			}
			else
			{
				throw new \System\DB\DataAdapterException("MySQL resource is not a valid link identifier");
			}
		}


		/**
		 * return affected rows
		 *
		 * @return int
		 */
		public function getAffectedRows()
		{
			if( $this->link )
			{
				return mysql_affected_rows( $this->link );
			}
			else
			{
				throw new \System\DB\DataAdapterException("MySQL resource is not a valid link identifier");
			}
		}


		/**
		 * Returns escaped string
		 *
		 * @param  string $unescaped_string		String to escape
		 * @return string						Escaped string
		 */
		public function escapeString( $unescaped_string )
		{
			return \mysql_real_escape_string( $unescaped_string, $this->link );
		}
	}
?>