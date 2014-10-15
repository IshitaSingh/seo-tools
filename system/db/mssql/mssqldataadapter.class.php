<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\MSSQL;
	use \System\DB\DataAdapter;


	/**
	 * Represents an open connection to a MSSQL database
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class MSSQLDataAdapter extends DataAdapter
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
			trigger_error("The MSSQLDataAdapter is beta, use with caution", E_USER_NOTICE);

			if( !$this->link )
			{
				if( isset( $this->args['server'] ) &&
					isset( $this->args['uid'] ) &&
					isset( $this->args['pwd'] ) &&
					isset( $this->args['database'] ))
				{
					$this->link = \sqlsrv_connect( $this->args['server'] , array( "UID"=>$this->args['uid'], "PWD"=>$this->args['pwd'], "Database"=>$this->args["database"] ));

					if( $this->link )
					{
						return true;
					}
					else
					{
						throw new \System\DB\DatabaseException("could not connect to database " . implode(' ', array_pop(sqlsrv_errors())));
					}
				}
				else
				{
					throw new \System\DB\DataAdapterException("missing required connection string parameter");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("connection already open");
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
				if( sqlsrv_close( $this->link ))
				{
					$this->link = null;
					return true;
				}
				else
				{
					throw new DatabaseException("could not close mssql connection");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("connection already closed");
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
			$mssqlStatement = new MSSQLStatement($this, $this->link);
			$mssqlStatement->prepare($statement, $parameters);
			return $mssqlStatement;
		}


		/**
		 * fetches DataSet from database string using source string
		 *
		 * @param  DataSet	&$ds		empty DataSet object
		 * @return void
		 */
		public function fill( \System\DB\DataSet &$ds )
		{
			if( $this->link )
			{
				$result = $this->query( $ds->source );

				$fields = array();
				if( $result )
				{
					// get table of mssql types
					$mssql_type = array();
					// int
					$mssql_type[-7]							= 'BIT';
					$mssql_type[-6]							= 'TINYINT';
					$mssql_type[5]							= 'SMALLINT';
					$mssql_type[4]							= 'INT';
					$mssql_type[-5]							= 'BIGINT';
					$mssql_type[-2]							= 'TIMESTAMP';
					$mssql_type[-11]						= 'UNIQUEIDENTIFIER';

					$mssql_type[2]							= 'NUMERIC';
					$mssql_type[3]							= 'DECIMAL';
					$mssql_type[6]							= 'FLOAT';
					$mssql_type[7]							= 'REAL';

					$mssql_type[91]							= 'DATE';
					$mssql_type[93]							= 'DATETIME';
					$mssql_type[-155]						= 'DATETIMEOFFSET';
					$mssql_type[-154]						= 'TIME';

					$mssql_type[1]							= 'CHAR';
					$mssql_type[-8]							= 'NCHAR';
					$mssql_type[12]							= 'VARCHAR';
					$mssql_type[-9]							= 'NVARCHAR';
					$mssql_type[-1]							= 'TEXT';
					$mssql_type[-10]						= 'NTEXT';

					$mssql_type[-2]							= 'BINARY';
					$mssql_type[-3]							= 'VARBINARY';
					$mssql_type[-4]							= 'IMAGE';
					$mssql_type[-151]						= 'UDT';
					$mssql_type[-152]						= 'XML';

					/*
					 * create field objects
					 *
					 * this code loops through fields of resultset
					 * checks is field is primary key, if so, set the primary key field name
					 * then adds all field names to an array
					 *
					 * cannot be used when resultset is emtpy (mysql_num_fields wil fail)
					 */
					$colcount = sqlsrv_num_fields( $result );

					// set table property
					$ds->setTable($this->getTableFromSQL( $ds->source ));
					$fieldMeta = \sqlsrv_field_metadata( $result );

					for( $i=0; $i < $colcount; $i++ )
					{
						$field = $this->getField($ds->table, $fieldMeta[$i]["Name"]);
						// mssql field info
						$fieldMetas[] = new \System\DB\ColumnSchema(array(
							'name' => (string) $fieldMeta[$i]["Name"],
							'table' => (string) $ds->table,
							'type' => (string) $mssql_type[$fieldMeta[$i]["Type"]],
							'length' =>  intval($fieldMeta[$i]["Size"])==0?intval($fieldMeta[$i]["Precision"]):intval($fieldMeta[$i]["Size"]),
							'notNull' => (bool)  ( !$fieldMeta[$i]["Nullable"] ),
							'primaryKey' => (bool) $field['primaryKey'],
							'foreignKey' => false,
							'unique' => (bool) $field['unique'],
							'numeric' => (bool) (( $fieldMeta[$i]["Type"] === -7 ) ||
														( $fieldMeta[$i]["Type"] === -6 ) ||
														( $fieldMeta[$i]["Type"] === 5 ) ||
														( $fieldMeta[$i]["Type"] === 4 ) ||
														( $fieldMeta[$i]["Type"] === -5 ) ||
														( $fieldMeta[$i]["Type"] === -2 ) ||
														( $fieldMeta[$i]["Type"] === -11 ) ||
														( $fieldMeta[$i]["Type"] === 2 ) ||
														( $fieldMeta[$i]["Type"] === 3 ) ||
														( $fieldMeta[$i]["Type"] === 6 ) ||
														( $fieldMeta[$i]["Type"] === 7 )),
							'string' => (bool) (( $fieldMeta[$i]["Type"] === 1 ) ||
														( $fieldMeta[$i]["Type"] === -3  && (bool)$fieldMeta[$i]["Size"]) ||
														( $fieldMeta[$i]["Type"] === -8 ) ||
														( $fieldMeta[$i]["Type"] === 12 ) ||
														( $fieldMeta[$i]["Type"] === -9 ) ||
														( $fieldMeta[$i]["Type"] === -1 ) ||
														( $fieldMeta[$i]["Type"] === -10 )),
							'integer' => (bool) (( $fieldMeta[$i]["Type"] === -7 ) ||
														( $fieldMeta[$i]["Type"] === -6 ) ||
														( $fieldMeta[$i]["Type"] === 5 ) ||
														( $fieldMeta[$i]["Type"] === 4 ) ||
														( $fieldMeta[$i]["Type"] === -5 ) ||
														( $fieldMeta[$i]["Type"] === -11 )),
							'real' => (bool) (( $fieldMeta[$i]["Type"] === 2 ) ||
														( $fieldMeta[$i]["Type"] === 3 ) ||
														( $fieldMeta[$i]["Type"] === 6 ) ||
														( $fieldMeta[$i]["Type"] === 7 )),
							'date' => (bool)  ( $fieldMeta[$i]["Type"] === 91 ),
							'time' => (bool)  ( $fieldMeta[$i]["Type"] === -154 ),
							'datetime' => (bool)  ( $fieldMeta[$i]["Type"] === 93 ),
							'boolean' => (bool)  ( $fieldMeta[$i]["Type"] === -7 ),
							'autoIncrement' => $field['autoIncrement'],
							'blob' => (bool) (( $fieldMeta[$i]["Type"] === -2 ) ||
														( $fieldMeta[$i]["Type"] === -3 ))
							));

							$fields[]=$fieldMeta[$i]["Name"];
					}


					/*
					 * create record objects
					 *
					 * this code loops through all rows and fields
					 * then creates the following array...
					 * DataSet[row number][field name] = value
					 */

					$rowcount = sqlsrv_num_rows( $result );

					$rows = array();$j=0;
					while($row = sqlsrv_fetch_array( $result ))
					{
						// add row to DataSet
						for( $i=0; $i < $colcount; $i++ ) $rows[$j][$fields[$i]]=$row[$i];						
						$j++;												
					}
					// set rows
					$ds->setRows( $rows );
					// set field meta
					$ds->setFieldMeta( $fieldMetas );
					// set fields
					$ds->setFields($fields);
						// cleanup
					sqlsrv_free_stmt( $result );
				}
				else
				{
					throw new \System\DB\DatabaseException(implode(' ', array_pop(sqlsrv_errors())));
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("connection is closed");
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

			$tables = $this->query( "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE';" );

			while($table = \sqlsrv_fetch_array($tables))
			{
				$i=0;
				$tableProperties = array('name'=>$table[0]);
				$foreignKeys = array();
				$columnSchemas = array();

				$columns = $this->query( "SELECT * FROM [{$table[0]}]" );

				// get table of mssql types
				$mssql_type = array();
				$mssql_type[-7]							= 'BIT';
				$mssql_type[-6]							= 'TINYINT';
				$mssql_type[5]							= 'SMALLINT';
				$mssql_type[4]							= 'INT';
				$mssql_type[-5]							= 'BIGINT';
				$mssql_type[-2]							= 'TIMESTAMP';
				$mssql_type[-11]						= 'UNIQUEIDENTIFIER';

				$mssql_type[2]							= 'NUMERIC';
				$mssql_type[3]							= 'DECIMAL';
				$mssql_type[6]							= 'FLOAT';
				$mssql_type[7]							= 'REAL';

				$mssql_type[91]							= 'DATE';
				$mssql_type[93]							= 'DATETIME';
				$mssql_type[-155]						= 'DATETIMEOFFSET';
				$mssql_type[-154]						= 'TIME';

				$mssql_type[1]							= 'CHAR';
				$mssql_type[-8]							= 'NCHAR';
				$mssql_type[12]							= 'VARCHAR';
				$mssql_type[-9]							= 'NVARCHAR';
				$mssql_type[-1]							= 'TEXT';
				$mssql_type[-10]						= 'NTEXT';

				$mssql_type[-2]							= 'BINARY';
				$mssql_type[-3]							= 'VARBINARY';
				$mssql_type[-4]							= 'IMAGE';
				$mssql_type[-151]						= 'UDT';
				$mssql_type[-152]						= 'XML';

				$fieldMeta = sqlsrv_field_metadata( $columns );
				while($i < \sqlsrv_num_fields($columns))
				{
					$field = $this->getField($table[0], $fieldMeta[$i]["Name"]);
					// setting primary key
					if((bool) $field['primaryKey'])
					{						
						$tableProperties['primaryKey'] = $fieldMeta[$i]["Name"];
					}
					// mssql field info
					$columnSchemas[] = new \System\DB\ColumnSchema(array(
							'name' => (string) $fieldMeta[$i]["Name"],
							'table' => (string) $table[0],
							'type' => (string) $mssql_type[$fieldMeta[$i]["Type"]],
							'length' =>  intval($fieldMeta[$i]["Size"])==0?intval($fieldMeta[$i]["Precision"]):intval($fieldMeta[$i]["Size"]),
							'notNull' => (bool)  ( !$fieldMeta[$i]["Nullable"] ),
							'primaryKey' => (bool) $field['primaryKey'],
							'foreignKey' => false,
							'unique' => (bool) $field['unique'],
							'numeric' => (bool) (( $fieldMeta[$i]["Type"] === -7 ) ||
														( $fieldMeta[$i]["Type"] === -6 ) ||
														( $fieldMeta[$i]["Type"] === 5 ) ||
														( $fieldMeta[$i]["Type"] === 4 ) ||
														( $fieldMeta[$i]["Type"] === -5 ) ||
														( $fieldMeta[$i]["Type"] === -2 ) ||
														( $fieldMeta[$i]["Type"] === -11 ) ||
														( $fieldMeta[$i]["Type"] === 2 ) ||
														( $fieldMeta[$i]["Type"] === 3 ) ||
														( $fieldMeta[$i]["Type"] === 6 ) ||
														( $fieldMeta[$i]["Type"] === 7 )),
							'string' => (bool) (( $fieldMeta[$i]["Type"] === 1 ) ||
														( $fieldMeta[$i]["Type"] === -8 ) ||
														( $fieldMeta[$i]["Type"] === 12 ) ||
														( $fieldMeta[$i]["Type"] === -9 ) ||
														( $fieldMeta[$i]["Type"] === -1 ) ||
														( $fieldMeta[$i]["Type"] === -10 )),
							'integer' => (bool) (( $fieldMeta[$i]["Type"] === -7 ) ||
														( $fieldMeta[$i]["Type"] === -6 ) ||
														( $fieldMeta[$i]["Type"] === 5 ) ||
														( $fieldMeta[$i]["Type"] === 4 ) ||
														( $fieldMeta[$i]["Type"] === -5 ) ||														
														( $fieldMeta[$i]["Type"] === -11 )),
							'real' => (bool) (( $fieldMeta[$i]["Type"] === 2 ) ||
														( $fieldMeta[$i]["Type"] === 3 ) ||
														( $fieldMeta[$i]["Type"] === 6 ) ||
														( $fieldMeta[$i]["Type"] === 7 )),
							'date' => (bool)  ( $fieldMeta[$i]["Type"] === 91 ),
							'time' => (bool)  ( $fieldMeta[$i]["Type"] === -154 ),
							'datetime' => (bool)  ( $fieldMeta[$i]["Type"] === 93 ),
							'boolean' => (bool)  ( $fieldMeta[$i]["Type"] === -7 ),
							'autoIncrement' => $field['autoIncrement'],
							'blob' => (bool) (( $fieldMeta[$i]["Type"] === -2 ) ||
														( $fieldMeta[$i]["Type"] === -3 ))
							));

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
					$type = "FLOAT({$columnSchema->length})";
				}
				elseif($columnSchema->boolean)
				{
					$type = "BIT";
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
					$type = "VARBINARY({$columnSchema->length})";
				}
				else
				{
					$type = "VARCHAR({$columnSchema->length}) ";
				}

				if($columns) $columns .= ",\n	";
				$columns .= "{$columnSchema->name} {$type}".($columnSchema->notNull?' NOT NULL':'').($columnSchema->autoIncrement?' AUTO_INCREMENT':'');

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
					$column .= "{$primaryKey}";
				}

				$columns .= ",\n	PRIMARY KEY ({$column})";
			}

			if($indexKeys)
			{
				$column = "";
				foreach($indexKeys as $indexKey)
				{
					if($column) $column .= ", ";
					$column .= "{$indexKey}";
				}

				$columns .= ",\n	INDEX ({$column})";
			}

			if($uniqueKeys)
			{
				$column = "";
				foreach($uniqueKeys as $uniqueKey)
				{
					if($column) $column .= ", ";
					$column .= "{$uniqueKey}";
				}

				$columns .= ",\n	UNIQUE ({$column})";
			}

			$this->execute("CREATE TABLE {$tableSchema->name} (\n	{$columns}\n);");
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
		 * creats a Transaction object
		 *
		 * @return MSSQLTransaction
		 */
		public function beginTransaction()
		{
			return new MSSQLTransaction($this);
		}


		/**
		 * attempt to insert a record into the datasource
		 *
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		final public function insert(\System\DB\DataSet &$ds )
		{
			if( $this->link )
			{
				$tableSchema = $ds->dataAdapter->getSchema()->seek($ds->table);
				$fields = $ds->fields;
				$row = $ds->row;

				for($i=0; $i<count($ds->fieldMeta); $i++)
				{
					if( $ds->fieldMeta[$i]->name == $tableSchema->primaryKey && $row[$tableSchema->primaryKey] == null)
					{
							unset($row[$tableSchema->primaryKey]);
							unset($fields[$i]);
					}							
				}

				$this->queryBuilder()
					->insertInto($ds->table, $fields)
					->values($row)
					->execute();

				if($tableSchema->primaryKey)
				{
					$ds[$tableSchema->primaryKey] = (int)  $this->getLastInsertId();
				
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
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		final public function update( \System\DB\DataSet &$ds  )
		{
			if( $this->link )
			{
				$tableSchema = $ds->dataAdapter->getSchema()->seek($ds->table);
				$fields = $ds->fields;
				$row = $ds->row;

				for($i=0; $i<count($ds->fieldMeta); $i++)
				{
					if( $ds->fieldMeta[$i]->name == $tableSchema->primaryKey)
					{
							unset($row[$tableSchema->primaryKey]);
							unset($fields[$i]);
					}							
				}

				if($tableSchema->primaryKey)
				{
					$this->queryBuilder()
						->update($ds->table)
						->setColumns($ds->table, $fields, $row)
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
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		final public function delete( \System\DB\DataSet &$ds  )
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
			return new MSSQLQueryBuilder($this, $this->link, '[', ']', '\'');
		}


		/**
		 * begin transaction
		 *
		 * @return void
		 */
		public function beginTransation()
		{
			return new \System\DB\MySQL\MSSQLTransaction($this);
		}


		/**
		 * return id of last record inserted
		 *
		 * @return int
		 */
		public function getLastInsertId()
		{
			if($this->link)
			{
				$id = \sqlsrv_fetch_array($this->query("SELECT  @@Identity"));
				return $id[0];
			}
			else
			{				
				throw new \System\Base\InvalidOperationException("mssql resource is not a valid link identifier");
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
				return sqlsrv_rows_affected( $this->link );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("mssql resource is not a valid link identifier");
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
			if(is_numeric($unescaped_string)) {
				return $unescaped_string;
			}
			elseif(strtotime($unescaped_string)!==false) {
				return $unescaped_string;
			}
			$unpacked = unpack('H*hex', $unescaped_string);
			return '0x' . $unpacked['hex'];
		}


		/**
		 * 
		 * @param type $sql
		 * @return type
		 */
		private function getTableFromSQL($sql)
		{
			if($sql instanceof MSSQLStatement) $sql = $sql->getPreparedStatement();
			$posStart = stripos($sql,'from');
			while(!$this->removeWhitespace($sql,$posStart) && $posStart < strlen($sql)){
			$posStart++;
			}
			$posEnd = $posStart + 1;
			while(!$this->removeWhitespace($sql,$posEnd) && $posEnd < strlen($sql)){
			$posEnd++;
			}

			$table = substr($sql,$posStart,$posEnd - $posStart + 1);

			$table = rtrim(ltrim(str_replace('[','',str_replace(']','',$table))));

			return $table;
		}


		/**
		 * 
		 * @param type $table
		 * @param type $fieldName
		 * @return type
		 */
		private function getField($table, $fieldName)
		{
			$sql = "select  c.status as status, 
							case when pc.colid = c.colid then '1' else '' end as xtype, 
							case when systypes.name = 'uniqueidentifier' then 1 else 0 end as guid
											from sysobjects o
											left join (sysindexes i
												join sysobjects pk ON i.name = pk.name
												and pk.parent_obj = i.id
												and pk.xtype = 'PK'
												join sysindexkeys ik on i.id = ik.id
												and i.indid = ik.indid
												join syscolumns pc ON ik.id = pc.id
												AND ik.colid = pc.colid) ON i.id = o.id
											join syscolumns c ON c.id = o.id
											left join systypes on c.xusertype = systypes.xusertype
											where o.name = '".$table."'
											AND c.name = '".$fieldName."'
											order by ik.keyno";

			$link = $this->query( $sql );
			$result = sqlsrv_fetch_array( $link );

			$field = array();
			$field['name'] = $fieldName;
			$field['table'] = $table;
			$field['autoIncrement'] = ($result[0] & 128) == 128;
			$field['primaryKey'] = ($result[1] == '1');
			$field['unique'] = ($result[2] == '1');

			return $field;
		}


		/**
		 * 
		 * @param type $string
		 * @param type $start
		 * @return boolean
		 */
		private function removeWhitespace($string,$start)
		{
			if($start >= strlen($string)) {
				return false;
			}
			return substr($string,$start,1)==' ' ||
				substr($string,$start,1)=="\t" ||
				substr($string,$start,1)=="\n" ||
				substr($string,$start,1)=="\r";
		}
	}
?>