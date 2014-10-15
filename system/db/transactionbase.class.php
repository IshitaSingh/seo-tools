<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Represents a transaction
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	abstract class TransactionBase
	{
		/**
		 * Contains a reference to an object
		 * @var DataAdapter
		**/
		protected $resource				= null;

		/**
		 * Specifies whether the transaction has begun
		 * @var bool
		**/
		private $begun				= false;


		/**
		 * Constructor
		 *
		 * @param  object	$resource	instance of a DataAdapter
		 * @return void
		 */
		final public function __construct( &$resource )
		{
			$this->resource =& $resource;
			$this->begin();
		}


		/**
		 * Begins a transaction
		 */
		final public function begin()
		{
			if(!$this->begun)
			{
				$this->begun = true;
				$this->beginTransaction();
			}
			else
			{
				throw new \System\DB\TransactionException('Transaction has already started');
			}
		}


		/**
		 * Implements a rollback
		 */
		final public function rollback()
		{
			if($this->begun)
			{
				$this->begun = false;
				$this->rollbackTransaction();
			}
			else
			{
				throw new \System\DB\TransactionException('Transaction has not been started');
			}
		}


		/**
		 * Implements a commit
		 */
		final public function commit()
		{
			if($this->begun)
			{
				$this->begun = false;
				$this->commitTransaction();
			}
			else
			{
				throw new \System\DB\TransactionException('Transaction has not been started');
			}
		}


		/**
		 * Begins a transaction
		 */
		abstract protected function beginTransaction();


		/**
		 * Implements a rollback
		 */
		abstract protected function rollbackTransaction();


		/**
		 * Implements a commit
		 */
		abstract protected function commitTransaction();
	}
?>