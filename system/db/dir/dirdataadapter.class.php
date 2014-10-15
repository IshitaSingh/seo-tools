<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\Dir;
	use \System\DB\DataAdapter;


	/**
	 * Represents an open connection to the file system
	 * 
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	class DirDataAdapter extends DataAdapter
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
					'length' => 255,
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
				$fieldnames = array(
					'name'=>array('name'=>'name', 'table'=>$this->args['source'], 'type'=>'string', 'string'=>true),
					'path'=>array('name'=>'path', 'table'=>$this->args['source'], 'type'=>'string', 'string'=>true),
					'size'=>array('name'=>'size', 'table'=>$this->args['source'], 'type'=>'int', 'integer'=>true),
					'modified'=>array('name'=>'modified', 'table'=>$this->args['source'], 'type'=>'int', 'integer'=>true),
					'accessed'=>array('name'=>'accessed', 'table'=>$this->args['source'], 'type'=>'int', 'integer'=>true),
					'created'=>array('name'=>'created', 'table'=>$this->args['source'], 'type'=>'int', 'integer'=>true),
					'isfolder'=>array('name'=>'isfolder', 'table'=>$this->args['source'], 'type'=>'int', 'boolean'=>true));

				$fields = array();
				$fieldMeta = array();
				foreach($fieldnames as $name=>$meta)
				{
					$fields[] = $name;
					$fieldMeta[] = new \System\DB\ColumnSchema($meta);
				}

				$files = array();
				$folders = array();

				while(( $file = readdir( $this->link )) !== false ) {
					if( $file != '.' && $file != '..') {
						if( is_file( $this->args['source'] . '/' . $file )) {
							$files[] = new \System\Utils\File( $this->args['source'] . '/' . $file );
						}
						elseif( is_dir( $this->args['source'] . '/' . $file )) {
							$folders[] = new \System\Utils\Folder( $this->args['source'] . '/' . $file );
						}
					}
				}

				$rows = array();
				foreach( $folders as $folder )
				{
					$row = array();
					$row[$fieldnames['name']['name']] = $folder->name;
					$row[$fieldnames['path']['name']] = $folder->path;
					$row[$fieldnames['size']['name']] = 0;
					$row[$fieldnames['modified']['name']] = $folder->modified;
					$row[$fieldnames['accessed']['name']] = $folder->accessed;
					$row[$fieldnames['created']['name']] = $folder->created;
					$row[$fieldnames['isfolder']['name']] = 1;

					$rows[] = $row;
				}
				foreach( $files as $file ) {

					$row   = array();
					$row[$fieldnames['name']['name']] = $file->name;
					$row[$fieldnames['path']['name']] = $file->path;
					$row[$fieldnames['size']['name']] = $file->size;
					$row[$fieldnames['modified']['name']] = $file->modified;
					$row[$fieldnames['accessed']['name']] = $file->accessed;
					$row[$fieldnames['created']['name']] = $file->created;
					$row[$fieldnames['isfolder']['name']] = 0;

					$rows[] = $row;
				}

				$ds->setTable($this->args['source']);
				$ds->setFieldMeta($fieldMeta);
				$ds->setFields($fields);
				$ds->setRows($rows);
			}
			else
			{
				throw new \System\DB\DataAdapterException("Connection is closed");
			}
		}


		/**
		 * opens a connection to a filesystem resource
		 *
		 * @return bool						TRUE if successfull
		 */
		public function open()
		{
			if( !$this->link ) {
				if( isset( $this->args['source'] )) {
					if( is_dir( (string) $this->args['source'] )) {
						$this->link = \opendir($this->args['source']);

						if( $this->link ) {
							return true;
						}
						else {
							throw new \System\DB\DataAdapterException("Could not open directory");
						}
					}
					else {
						throw new \System\DB\DataAdapterException("`{$this->args['source']}` is not a valid directory");
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
		public function close()
		{
			if( $this->link ) {
				closedir($this->link);
				$this->link = null;
				return true;
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
		 * @param  DataSet	$ds		reference to a DataSet
		 * @return void
		 */
		public function insert( \System\DB\DataSet &$ds )
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * attempt to update a record in the datasource
		 *
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		public function update( \System\DB\DataSet &$ds )
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * attempt to delete a record in the datasource
		 *
		 * @param  DataSet	&$ds		reference to a DataSet
		 * @return void
		 */
		public function delete( \System\DB\DataSet &$ds )
		{
			throw new \System\Base\MethodNotImplementedException();
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
		public function escapeString( $unescaped_string )
		{
			return \addslashes( '"', '""', $unescaped_string );
		}
	}
?>