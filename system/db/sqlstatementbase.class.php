<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Represents a database query
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	abstract class SQLStatementBase
	{
		/**
		 * Contains a reference to a DataAdapter object
		 * @var DataAdapter
		**/
		protected $dataAdapter = null;


		/**
		 * Constructor
		 *
		 * @param  DataAdapter	$dataAdapter	instance of a DataAdapter
		 * @param  string		$statement		SQL statement
		 * @return void
		 */
		public function __construct( DataAdapter &$dataAdapter ) {
			$this->dataAdapter =& $dataAdapter;
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return bool					true on success
		 * @ignore
		 */
		public function __get( $field ) {
			throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
		}


		/**
		 * sets an object property
		 *
		 * @param  string	$field		name of the field
		 * @param  mixed	$value		value of the field
		 * @return bool					true on success
		 * @ignore
		 */
		public function __set( $field, $value ) {
			throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
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
		abstract public function prepare($statement, array $parameters = array());


		/**
		 * prepare an SQL statement
		 *
		 * @param  string	$statement	SQL statement
		 * @return string
		 */
		abstract public function bind($parameter, $value);


		/**
		 * execute an SQL statement and return a PDOStatement object
		 * Executes a prepared statement bound to parameters specified by the :symbol
		 * e.g. SELECT * FROM `table` WHERE user=:user
		 *
		 * @param  array	$parameters	array of parameters to bind
		 * @return \PDOStatement
		 */
		abstract public function query(array $parameters = array());


		/**
		 * execute an SQL statement
		 * Executes a prepared statement bound to parameters specified by the @symbol
		 * e.g. SELECT * FROM `table` WHERE user=@user
		 *
		 * @param  array	$parameters	array of parameters to bind
		 * @return void
		 */
		abstract public function execute(array $parameters = array());


		/**
		 * open a DataSet
		 *
		 * @param  array	$parameters	array of parameters to bind
		 * @param  DataSetType	$lock_type	lock type as constant of DataSetType::OpenDynamic(), DataSetType::OpenStatic(), or DataSetType::OpenReadonly()
		 * @return DataSet
		 */
		final public function openDataSet(array $parameters = array(), DataSetType $lock_type = null) {
			foreach($parameters as $parameter => $value) {
				$this->bind($parameter, $value);
			}
			return $this->dataAdapter->openDataSet($this, $lock_type);
		}


		/**
		 * run query
		 *
		 * @return void
		 * @ignore
		 */
		final public function runQuery() {
			trigger_error("SQLStatement::runQuery() is deprecated, use SQLStatement::execute() instead");
			$this->execute();
		}


		/**
		 * get query
		 *
		 * @return string
		 * @ignore
		 */
		final public function getQuery() {
			trigger_error("SQLStatement::getQuery() is deprecated");
		}
	}
?>