<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\Text;
	use \System\DB\DataAdapter;


	/**
	 * Represents an open connection to a tab or comma delimited text file
	 * 
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	class TextDataAdapter extends DataAdapter
	{
		/**
		 * Handle to the open connection to the datasource
		 * @var resource
		 */
		private $link;


		/**
		 * builds a DataBaseSchema object
		 *
		 * @return DatabaseSchema
		 */
		public function buildSchema()
		{
			$ds = $this->openDataSet();

			$tableProperties = array('name'=>$ds->table);
			$foreignKeys = array();
			$columnSchemas = array();

			foreach($ds->fields as $field)
			{
				$columnSchemas[] = new \System\DB\ColumnSchema(array(
					'name' => $field,
					'table' => $ds->table,
					'type' => 'string',
					'length' => 65535,
					'string' => true));
			}

			return new \System\DB\DatabaseSchema(array(), array(new \System\DB\TableSchema($tableProperties, $foreignKeys, $columnSchemas)));
		}


		/**
		 * creats a TableSchema object
		 *
		 * @return DatabaseSchema
		 */
		public function addTableSchema( \System\DB\TableSchema &$tableSchema )
		{
			throw new \System\Base\MethodNotImplementedException();
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
			$txtStatement = new TextStatement($this, $this->link);
			$txtStatement->prepare($statement, $parameters);
			return $txtStatement;
		}


		/**
		 * fetches DataSet from datasource string using source string
		 *
		 * @param  DataSet	&$ds			empty DataSet object
		 * @return void
		 */
		public function fill( \System\DB\DataSet &$ds )
		{
			if( $this->link )
			{
				\rewind( $this->link );
				$fields = \fgetcsv( $this->link,
								filesize( $this->args['source'] ),
								$this->args['delimiter'],
								$this->args['enclosure']);

				$fieldMeta = array();
				foreach($fields as $field)
				{
					$fieldMeta[] = new \System\DB\ColumnSchema(array('name'=>$field, 'type'=>'string', 'length'=>65535, 'table'=>$ds->table, 'string'=>true));
				}

				$rows = array();
				while( $line = fgetcsv( $this->link, filesize( $this->args['source'] ), $this->args['delimiter'], $this->args['enclosure'] ))
				{
					$row = array();
					for($i = 0; $i < count($fields); $i++)
					{
						if( $line[$i] == "NULL" )
						{
							$row[$fields[$i]] = NULL;
						}
						else
						{
							$row[$fields[$i]] = $line[$i];
						}
					}

					$rows[] = $row;
				}

				$ds->setTable($this->args['source']);
				$ds->setFieldMeta($fieldMeta);
				$ds->setFields($fields);
				$ds->setRows($rows);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Connection is closed");
			}
		}


		/**
		 * opens a connection to a text file
		 *
		 * @return bool						TRUE if successfull
		 */
		public function open() {
			if( !$this->link ) {
				if( isset( $this->args['source'] )) {
					if( isset( $this->args['format'] )) {
						if( strtolower( $this->args['format'] ) === 'tabdelimited' ) {
							$this->args['delimiter'] = "\t";
						}
						else {
							$this->args['delimiter'] =',';
						}
					}
					else {
						$this->args['delimiter'] =',';
					}

					if( !isset( $this->args['enclosure'] )) {
						$this->args['enclosure'] = '"';
					}

					if( !isset( $this->args['linefeed'] )) {
						$this->args['linefeed'] = "\n";
					}

					if( !isset( $this->args['nullexpr'] )) {
						$this->args['nullexpr'] = 'NULL';
					}

					$this->link = fopen((string)$this->args['source'], 'r');

					if( $this->link ) {
						return true;
					}
					else {
						throw new \System\DB\DataAdapterException("Could not open file `{$this->args['source']}`");
					}
				}
				else {
					throw new \System\DB\DataAdapterException("Missing required connection string parameter {$this->args['source']}");
				}
			}
			else {
				throw new \System\DB\DataAdapterException("Connection already open");
			}
		}


		/**
		 * closes an open connection
		 *
		 * @return bool					true if successfull
		 */
		public function close() {
			if( $this->link ) {
				if( fclose($this->link)) {
					$this->link = null;
					return true;
				}
				else {
					throw new \System\DB\DataAdapterException("Could not close link to text file");
				}
			}
			else {
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
		 * attempt to insert a record into the datasource
		 *
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		public function insert( \System\DB\DataSet &$ds )
		{
			$CSVString = '';
			foreach($ds->fields as $field) {
				if( !empty( $CSVString )) {
					$CSVString .= $this->args['delimiter'];
				}

				if( $ds[$field] === null ) {
					$CSVString .= $this->args['nullexpr'];
				}
				else {
					$CSVString .= $this->args['enclosure'] . $this->escapeString($ds[$field]) . $this->args['enclosure'];
				}
			}

			$fp = fopen( $this->args['source'], 'w+' );
			if( $fp ) {
				if( fwrite( $fp, $ds->getCSVString( $this->args['enclosure'], $this->args['delimiter'] ) . $this->args['linefeed'] . $CSVString )) {
					fclose( $fp );
					return;
				}
			}

			throw new \System\DB\DataAdapterException("Could not write to file");
		}


		/**
		 * attempt to update a record in the datasource
		 *
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		public function update( \System\DB\DataSet &$ds )
		{
			$ds->rows[$ds->cursor] = $ds->row;
			$fp = fopen( $this->args['source'], 'w+' );
			if( $fp ) {
				if( fwrite( $fp, $ds->getCSVString( $this->args['enclosure'], $this->args['delimiter'], $this->args['linefeed'], $this->args['nullexpr'] ))) {
					fclose( $fp );
					return;
				}
			}

			throw new \System\DB\DataAdapterException("Could not write to file");
		}


		/**
		 * attempt to delete a record in the datasource
		 *
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		public function delete( \System\DB\DataSet &$ds )
		{
			$fp = fopen( $this->args['source'], 'w+' );
			if( $fp ) {
				$ds_copy = clone $ds;
				$ds_copy->move($ds_copy->cursor);
				$ds_copy->delete();
				$csv = $ds_copy->getCSVString( $this->args['enclosure'], $this->args['delimiter'] );
				if( fwrite( $fp, $csv )) {
					fclose( $fp );
					return;
				}
			}

			throw new \System\DB\DataAdapterException("Could not write to file");
		}


		/**
		 * creats a QueryBuilder object
		 *
		 * @return QueryBuilder
		 */
		public function queryBuilder()
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * creats a Transaction object
		 *
		 * @return Transaction
		 */
		public function beginTransaction()
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * return id of last record inserted
		 *
		 * @return int
		 */
		public function getLastInsertId()
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * return affected rows
		 *
		 * @return int
		 */
		public function getAffectedRows()
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * Returns escaped string
		 *
		 * @param  string $unescaped_string		String to escape
		 * @return string						Escaped string
		 */
		private function escapeString( $unescaped_string )
		{
			return str_replace( '"', '""', $unescaped_string );
		}
	}
?>