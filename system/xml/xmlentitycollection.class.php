<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\XML;
	use \System\Collections\CollectionBase;


	/**
	 * Represents a Collection of XMLEntity objects
	 * 
	 * @package			PHPRum
	 * @subpackage		XML
	 * @author			Darnell Shinbine
	 */
	final class XMLEntityCollection extends CollectionBase
	{
		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				if( $item instanceof XMLEntity )
				{
					return $this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected object of type XMLEntity in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add XMLEntity to Collection
		 *
		 * @param  XMLEntity $item
		 * @return bool
		 */
		public function add( $item )
		{
			if( $item instanceof XMLEntity )
			{
				array_push( $this->items, $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type XMLEntity");
			}
		}


		/**
		 * return XMLEntity at a specified index
		 *
		 * @param  int		$index			index of ActiveRecord
		 *
		 * @return XMLEntity				XMLEntity
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}


		/**
		 * returns true if array item is found
		 *
		 * @param  string		$entity_name		name of entity
		 * @return bool
		 */
		public function contains( $entity_name )
		{
			for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
			{
				if( strtoupper( $this->items[$i]->name ) === strtoupper( $entity_name ))
				{
					return true;
				}
			}
			return false;
		}


		/**
		 * returns index if value is found in collection
		 *
		 * @param  string		$entity_name		name of entity
		 * @return int
		 */
		public function indexOf( $entity_name )
		{
			for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
			{
				if( strtoupper( $this->items[$i]->name ) === strtoupper( $entity_name ))
				{
					return $i;
				}
			}
			return -1;
		}
	}
?>