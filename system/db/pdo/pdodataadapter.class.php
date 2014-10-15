<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\PDO;
	use \System\DB\DataAdapter;


	/**
	 * Represents an open connection to a PDO database using the PDO driver
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class PDODataAdapter extends DataAdapter
	{
		/**
		 * Handle to the open PDO object
		 * @var \PDO
		 */
		private $pdo;

		/**
		 * PDO dsn connection string
		 * @var string
		 */
		private $dsn;

		/**
		 * PDO database username
		 * @var string
		 */
		private $username;

		/**
		 * PDO database password
		 * @var string
		 */
		private $password;

		/**
		 * PDO connection options
		 * @var array
		 */
		private $options;


		/**
		 * Constructor
		 * creates a PDO connection object to a datasource
		 *
		 * @param   string   $dsn   connection string
		 * @param   string   $username   database username
		 * @param   string   $password   database password
		 * @return	DataAdapter
		 */
		final protected function __construct( $dsn, $username = '', $password = '', array $options = array() )
		{
			$this->dsn = $dsn;
			$this->username = $username;
			$this->password = $password;
			$this->options = $options;

			$this->open();
		}


		/**
		 * opens a connection to a mysql database
		 * @return bool						TRUE if successfull
		 */
		public function open()
		{
			if( !$this->pdo )
			{
				$this->pdo = new \PDO($this->dsn, $this->username, $this->password, $this->options);
				$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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
			if( $this->pdo )
			{
				$this->pdo = null;
				return true;
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
			return (bool)$this->pdo;
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
			$psoStatement = new PDOStatement($this, $this->pdo);
			$psoStatement->prepare($statement, $parameters);
			return $psoStatement;
		}


		/**
		 * fills a DataSet object with the current DataAdapter
		 *
		 * @param  DataSet	&$ds		empty DataSet object
		 * @return void
		 */
		public function fill( \System\DB\DataSet &$ds )
		{
			if( $this->pdo )
			{
				$result = $this->query( $ds->source );

				$fields = array();
				$fieldMeta = array();
				$fieldCount = $result->columnCount();
				$table = '';

				for($i=0; $i < $fieldCount; $i++)
				{
					$meta = $result->getColumnMeta($i);

					$fields[] = $meta['name'];
					$fieldMeta[] = $this->getColumnSchema($meta);
					$table = $meta['table'];
				}

				$rows = $result->fetchAll(\PDO::FETCH_ASSOC);

				$ds->setTable( $table );
				$ds->setFieldMeta( $fieldMeta );
				$ds->setFields( $fields );
				$ds->setRows( $rows );
				$result->closeCursor();
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection is closed");
			}
		}


		/**
		 * builds a MSSQL DataBaseSchema object
		 *
		 * @return DatabaseSchema
		 */
		public function buildSchema()
		{
			$databaseProperties = array();
			$tableSchemas = array();

			// TODO: Fix, will not work with all db adapters
			$tables = $this->query( "SHOW TABLES" );
//			$tables = $this->query( "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE';" );

			while($table = $tables->fetch( \PDO::FETCH_NUM))
			{
				$i=0;
				$tableProperties = array('name'=>$table[0]);
				$foreignKeys = array();
				$columnSchemas = array();

				$query = $this->queryBuilder()->select()->from($table[0]);
				$query->empty = true;

				$columns = $this->query( $query );

				if( $columns )
				{
					$fields = array();
					$fieldMeta = array();
					$fieldCount = $columns->columnCount();
					$table="";

					for($i=0; $i < $fieldCount; $i++)
					{
						$meta = $columns->getColumnMeta($i);
						$columnSchema = $this->getColumnSchema($meta);
						$columnSchemas[] = $columnSchema;

						if($columnSchema->primaryKey)
						{
							$tableProperties['primaryKey'] = $meta['name'];
						}
					}
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
			if( $this->pdo )
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
			if( $this->pdo )
			{
				$tableSchema = $ds->dataAdapter->getSchema()->seek($ds->table);

				if($tableSchema->primaryKey)
				{
					$this->queryBuilder()
						->update($ds->table)
						->setColumns($ds->table, $ds->fields, $ds->row)
						->where($ds->table, $tableSchema->primaryKey, '=', $ds[$tableSchema->primaryKey])
						->query();
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
			if( $this->pdo )
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
		 * @return PDOQueryBuilder
		 */
		public function queryBuilder()
		{
			return new PDOQueryBuilder($this, $this->pdo);
		}


		/**
		 * creats a Transaction object
		 *
		 * @return MySQLTransaction
		 */
		public function beginTransaction()
		{
			return new PDOTransaction($this->pdo);
		}


		/**
		 * return id of last record inserted
		 *
		 * @return int
		 */
		public function getLastInsertId()
		{
			if( $this->pdo )
			{
				return $this->pdo->lastInsertId();
			}
			else
			{
				throw new \System\DB\DataAdapterException("PDO resource is not a valid link identifier");
			}
		}


		/**
		 * return affected rows
		 *
		 * @return int
		 */
		public function getAffectedRows()
		{
			if( $this->pdo )
			{
				// TODO: fix
				return $this->pdo->rowCount();
			}
			else
			{
				throw new \System\DB\DataAdapterException("PDO resource is not a valid link identifier");
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
			return $this->pdo->prepare( $unescaped_string )->queryString;
		}


		/**
		 * returns a populated ColumnSchema object
		 * @param object $meta meta object
		 * @return \System\DB\ColumnSchema
		 */
		private function getColumnSchema($meta)
		{
			$flags = $meta['flags'];
			$type = $this->_translateNativeType(isset($meta['native_type'])?$meta['native_type']:'');

			return new \System\DB\ColumnSchema(array(
				'name' => $meta['name'],
				'table' => $meta['table'],
				'type' => isset($meta['native_type'])?$meta['native_type']:'',
				'length' => $meta['len'],
				'notNull' => in_array('not_null', $flags)===true,
				'primaryKey' => in_array('primary_key', $flags)===true,
				'foreignKey' => false, // PDO does not provide this
				'unique' => false, // PDO does not provide this
				'numeric' => ($meta['len']==1 && !$type) || $type=='int' || $type=='real',
				'blob' => $type=='blob',
				'string' => $type=='string',
				'integer' => ($meta['len']==1 && !$type) || $type=='int',
				'real' => $type=='real',
				'date' => $type=='date',
				'time' => $type=='time',
				'datetime' => $type=='datetime',
				'boolean' => $meta['len']==1,
				'autoIncrement' => false // PDO does not provide this
				));
		}


		/**
		 * returns the native PHP type
		 * @param string $type db type
		 * @return string native type
		 */
		private function _translateNativeType($type)
		{
			$types = array(
				'VAR_STRING' => 'string',
				'STRING' => 'string',
				'BLOB' => 'blob',
				'LONGLONG' => 'int',
				'LONG' => 'int',
				'SHORT' => 'int',
				'DATETIME' => 'datetime',
				'DATE' => 'date',
				'TIME' => 'time',
				'DOUBLE' => 'real',
				'FLOAT' => 'real',
				'NEWDECIMAL' => 'real',
				'DECIMAL' => 'real',
				'TIMESTAMP' => 'int',
				'' => ''
			);

			return $types[$type];
		}
	}
?>