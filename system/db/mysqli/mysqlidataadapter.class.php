<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\MySQLi;
	use \System\DB\DataAdapter;


	/**
	 * Represents an open connection to a MySQL database using the mysqli driver
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class MySQLiDataAdapter extends DataAdapter
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
			if( !$this->link )
			{
				if( isset( $this->args['server'] ) &&
					isset( $this->args['uid'] ) &&
					isset( $this->args['pwd'] ) &&
					isset( $this->args['database'] ))
				{
					$this->link = \mysqli_init();

					if( \mysqli_real_connect( $this->link, $this->args['server'], $this->args['uid'], $this->args['pwd'], $this->args['database'], isset( $this->args['port'] )?$this->args['port']:3306 ) )
					{
						$err = \mysqli_connect_errno();
						if( $err )
						{
							throw new \System\DB\DatabaseException(\mysqli_connect_error());
						}
						else
						{
							\mysqli_set_charset( $this->link, $this->charset );
							return true;
						}
					}
					else
					{
						throw new \System\DB\DatabaseException("Could not connect to database " . \mysqli_connect_error());
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
				if( \mysqli_close( $this->link ))
				{
					$this->link = null;
					return true;
				}
				else
				{
					throw new \System\DB\DataAdapterException("Could not close mysqli connection");
				}
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection already closed");
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
			$mysqlStatement = new MySQLiStatement($this, $this->link);
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
				$result = $this->query( $ds->source, false );

				if( $result )
				{
					$fields = array();
					$fieldMeta = array();
					$fieldCount = \mysqli_num_fields($result);

					for($i=0; $i < $fieldCount; $i++)
					{
						$meta = \mysqli_fetch_field_direct($result, $i);

						$fields[] = $meta->name;
						$fieldMeta[] = $this->getColumnSchema($meta);
					}

					$rows = array();
					while($row = \mysqli_fetch_assoc( $result ))
					{
						$rows[] = $row;
					}

					$ds->setTable($meta->table);
					$ds->setFieldMeta( $fieldMeta );
					$ds->setFields( $fields );
					$ds->setRows( $rows );

					\mysqli_free_result( $result );
				}
				else
				{
					throw new \System\DB\DatabaseException(\mysqli_error($this->link));
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

			while($table = \mysqli_fetch_array($tables, MYSQL_NUM))
			{
				$i=0;
				$tableProperties = array('name'=>$table[0]);
				$foreignKeys = array();
				$columnSchemas = array();

				$columns = $this->query( "SELECT * FROM `{$table[0]}` WHERE 0" );
				while($i < \mysqli_num_fields($columns))
				{
					$meta = \mysqli_fetch_field_direct($columns, $i);

					if($meta->flags & 2)
					{
						$tableProperties['primaryKey'] = $meta->name;
					}

					$columnSchemas[] = $this->getColumnSchema($meta);
					$i++;
				}

				\mysqli_free_result($columns);

				$tableSchemas[] = new \System\DB\TableSchema($tableProperties, $foreignKeys, $columnSchemas);
			}

			\mysqli_free_result($tables);

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
			return new MySQLiQueryBuilder($this, $this->link);
		}


		/**
		 * creats a Transaction object
		 *
		 * @return MySQLTransaction
		 */
		public function beginTransaction()
		{
			return new MySQLiTransaction($this);
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
				return \mysqli_insert_id( $this->link );
			}
			else
			{
				throw new \System\DB\DataAdapterException("MySQLi resource is not a valid link identifier");
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
				return mysqli_affected_rows( $this->link );
			}
			else
			{
				throw new \System\DB\DataAdapterException("MySQLi resource is not a valid link identifier");
			}
		}


		/**
		 * returns a populated ColumnSchema object
		 * @param object $meta
		 * @return \System\DB\ColumnSchema 
		 */
		private function getColumnSchema($meta)
		{
			return new \System\DB\ColumnSchema(array(
				'name' => $meta->name,
				'table' => $meta->table,
				'type' => $meta->type,
				'length' => $meta->length,
				'notNull' => $meta->flags & 1,
				'primaryKey' => $meta->flags & 2,
				'foreignKey' => FALSE,
				'unique' => $meta->flags & 4,
				'numeric' => $meta->type === MYSQLI_TYPE_INT24 || $meta->type === MYSQLI_TYPE_LONG || $meta->type === MYSQLI_TYPE_LONGLONG || $meta->type === MYSQLI_TYPE_BIT || $meta->type === MYSQLI_TYPE_TINY || $meta->type === MYSQLI_TYPE_DECIMAL || $meta->type === MYSQLI_TYPE_DOUBLE || $meta->type === MYSQLI_TYPE_FLOAT || $meta->type === MYSQLI_TYPE_NEWDECIMAL || $meta->type === MYSQLI_TYPE_YEAR,
				'string' => $meta->type === MYSQLI_TYPE_STRING || $meta->type === MYSQLI_TYPE_VAR_STRING,
				'integer' => $meta->type === MYSQLI_TYPE_INT24 || $meta->type === MYSQLI_TYPE_LONG || $meta->type === MYSQLI_TYPE_LONGLONG || $meta->type === MYSQLI_TYPE_BIT || $meta->type === MYSQLI_TYPE_TINY || $meta->type === MYSQLI_TYPE_YEAR,
				'real' => $meta->type === MYSQLI_TYPE_DECIMAL || $meta->type === MYSQLI_TYPE_DOUBLE || $meta->type === MYSQLI_TYPE_FLOAT || $meta->type === MYSQLI_TYPE_NEWDECIMAL,
				'date' => $meta->type === MYSQLI_TYPE_DATE,
				'time' => $meta->type === MYSQLI_TYPE_TIME,
				'datetime' => $meta->type === MYSQLI_TYPE_DATETIME,
				'boolean' => $meta->type === MYSQLI_TYPE_BIT || $meta->type === MYSQLI_TYPE_TINY,
				'autoIncrement' => $meta->flags & 512,
				'blob' => $meta->flags & 128));
		}
	}
?>