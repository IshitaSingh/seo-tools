<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\MySQL;


	/**
	 * Represents an open connection to a MSSQL database
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine 
	 */
	final class MSSQLTransaction extends \System\DB\TransactionBase
	{
		/**
		 * Begins a transaction
		 */
		protected function beginTransaction()
		{
			$this->resource->execute( 'SET autocommit=0' );
			$this->resource->execute( 'START TRANSACTION' );
		}


		/**
		 * Implements a rollback
		 */
		protected function rollbackTransaction()
		{
			$this->resource->execute( 'ROLLBACK' );
		}


		/**
		 * Implements a commit
		 */
		protected function commitTransaction()
		{
			$this->resource->execute( 'COMMIT' );
		}
	}
?>