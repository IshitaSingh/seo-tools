<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\MSSQL;


	/**
	 * Represents a MSSQL database query statement
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	class MSSQLStatement extends \System\DB\SQLStatementBase
	{
		/**
		 * Contains an sql statement
		 * @var string
		**/
		protected $statement = '';

		/**
		 * Contains the SQL statement parameters
		 * @var array
		**/
		protected $parameters = array();

		/**
		 * Contains a reference to a mysql connection object
		 * @var resource
		**/
		protected $connection = null;


		/**
		 * Constructor
		 *
		 * @param  DataAdapter	$dataAdapter	instance of a DataAdapter
		 * @param  string		$statement		SQL statement
		 * @param  object		$connection		MSSQL connection object
		 * @return void
		 */
		public function __construct(\System\DB\DataAdapter &$dataAdapter, &$connection) {
			$this->dataAdapter =& $dataAdapter;
			$this->connection =& $connection;
		}


		/**
		 * prepare an SQL statement
		 * Creates a prepared statement bound to parameters specified by the @symbol
		 * e.g. SELECT * FROM `table` WHERE user=@user
		 *
		 * @param  string	$statement	SQL statement
		 * @param  array	$parameters	array of parameters to bind
		 * @return string
		 */
		public function prepare($statement, array $parameters = array()) {
			$this->statement = (string)$statement;
			foreach($parameters as $parameter => $value) {
				$this->bind($parameter, $value);
			}
		}


		/**
		 * prepare an SQL statement
		 *
		 * @param  string	$statement	SQL statement
		 * @return string
		 */
		public function bind($parameter, $value) {
			$this->parameters[$parameter] = $value;
		}


		/**
		 * execute an SQL statement and return a PDOStatement object
		 * Executes a prepared statement bound to parameters specified by the :symbol
		 * e.g. SELECT * FROM `table` WHERE user=:user
		 *
		 * @param  array	$parameters	array of parameters to bind
		 * @return \PDOStatement
		 */
		public function query(array $parameters = array())
		{
			if( $this->connection )
			{
				$this->getPreparedStatement($parameters);
				$result = \sqlsrv_query( $this->connection , $this->statement );
				if( !$result )
				{										
					throw new \System\DB\DatabaseException(implode(' ', array_pop(sqlsrv_errors())));
				}

				return $result;
			}
			else
			{
				throw new \System\DB\DataAdapterException("MSSQL resource in not a valid link identifier");
			}
		}


		/**
		 * execute an SQL statement
		 * Executes a prepared statement bound to parameters specified by the @symbol
		 * e.g. SELECT * FROM `table` WHERE user=@user
		 *
		 * @param  array	$parameters	array of parameters to bind
		 * @return void
		 */
		public function execute(array $parameters = array()) {
			$this->query($parameters);
		}


		/**
		 * get prepared SQL statement as string
		 *
		 * @param  array	$parameters	array of parameters to bind
		 * @return string
		 */
		protected function getPreparedStatement(array $parameters = array()) {
			foreach($parameters as $parameter => $value) {
				$this->bind($parameter, $value);
			}

			$preparedStatement = $this->statement;
			foreach($this->parameters as $parameter => $value) {
				if(strpos($value, '0x' )===0) {
					$preparedStatement = str_replace("@{$parameter}", $value, $preparedStatement);
				}
				elseif(is_string($value)) {
					$preparedStatement = str_replace("@{$parameter}", '\''.  mysql_real_escape_string($value, $this->connection).'\'', $preparedStatement);
				}
				elseif(is_null($value)) {
					$preparedStatement = str_replace("@{$parameter}", 'null', $preparedStatement);
				}
				else {
					$preparedStatement = str_replace("@{$parameter}", (real)$value, $preparedStatement);
				}
			}
			return $preparedStatement;
		}
	}
?>