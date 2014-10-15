<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\PDO;


	/**
	 * Represents an open connection to a PDO
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class PDOTransaction extends \System\DB\TransactionBase
	{
		/**
		 * Begins a transaction
		 */
		protected function beginTransaction()
		{
			$this->resource->beginTransaction();
		}


		/**
		 * Implements a rollback
		 */
		protected function rollbackTransaction()
		{
			$this->resource->rollBack();
		}


		/**
		 * Implements a commit
		 */
		protected function commitTransaction()
		{
			$this->resource->commit();
		}
	}
?>