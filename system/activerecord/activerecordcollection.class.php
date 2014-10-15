<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\ActiveRecord;
	use \System\Collections\CollectionBase;


	/**
	 * Represents a collection of ActiveRecords
	 *
	 * @package			PHPRum
	 * @subpackage		ActiveRecord
	 * @author			Darnell Shinbine
	 */
	final class ActiveRecordCollection extends CollectionBase
	{
		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				if( $item instanceof ActiveRecordBase )
				{
					$this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected object of type ActiveRecordBase in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * Add an ActiveRecord to the Collection
		 *
		 * @param  ActiveRecordBase $item
		 * @return void
		 */
		public function add( $item )
		{
			if( $item instanceof ActiveRecordBase )
			{
				array_push( $this->items, $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException ("Argument 1 passed to ".get_class($this)."::add() must be an object of type ActiveRecordBase");
			}
		}


		/**
		 * return ActiveRecord at a specified index
		 *
		 * @param  int		$index			index of ActiveRecord
		 * @return ActiveRecordBase		 ActiveRecord
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}
	}
?>