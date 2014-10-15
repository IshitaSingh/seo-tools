<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Collections;


	/**
	 * Provides the base functionality for creating Collections
	 * 
	 * @package			PHPRum
	 * @subpackage		Collections
	 * @author			Darnell Shinbine
	 */
	abstract class CollectionBase extends IteratorBase
	{
		/**
		 * Constructor
		 * 
		 * @param  mixed	$collection		can be CollectionBase or array used to initialize Collection
		 * @return void
		 */
		public function __construct( $collection = null )
		{
			if( isset( $collection ))
			{
				if( is_array( $collection ))
				{
					$this->items = $collection;
				}
				elseif( $collection instanceof CollectionBase )
				{
					$this->items = $collection->items;
				}
				else
				{
					throw new \System\Base\InvalidArgumentException("No overloaded constructor accepts ".gettype($collection));
				}
			}
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetUnset($index)
		{
			if( array_key_exists( $index, $this->items ))
			{
				unset( $this->items[$index] );
				$this->items = array_values( $this->items );
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add item to Collection
		 *
		 * @param  mixed	$item		item
		 * @return void
		 */
		public function add( $item )
		{
			array_push( $this->items, $item );
		}


		/**
		 * returns true if item array contains item
		 *
		 * @param  mixed	$item		item
		 * @return bool					true if item found
		 */
		public function contains( $item )
		{
			return in_array( $item, $this->items );
		}


		/**
		 * returns first index of item found in Collection
		 *
		 * @param  mixed	$item		item
		 * 
		 * @return int					index of item
		 */
		public function indexOf( $item )
		{
			for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
			{
				if( $this->items[$i] === $item )
				{
					return $i;
				}
			}
			return -1;
		}


		/**
		 * return item at a specified index
		 *
		 * @param  int		$index			index of item
		 * 
		 * @return mixed					item
		 */
		public function itemAt( $index )
		{
			if( isset( $this->items[(int)$index] ))
			{
				return $this->items[(int)$index];
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("Argument out of range in ".get_class($this)."::itemAt()");
			}
		}


		/**
		 * remove item from array
		 *
		 * @param  mixed	$item		item
		 * 
		 * @return bool
		 */
		public function remove( $item )
		{
			$index = $this->indexOf( $item );
			if( $index > -1 )
			{
				$this->removeAt( $index );
				return true;
			}
			else
			{
				return false;
			}
		}


		/**
		 * remove item from array at a specified index
		 *
		 * @param  int		$index		index to remove at
		 * 
		 * @return void
		 */
		public function removeAt( $index )
		{
			if( isset( $this->items[(int)$index] ))
			{
				unset( $this->items[(int)$index] );
				$this->items = array_values( $this->items );
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("Argument out of range in ".get_class($this)."::removeAt()");
			}
		}


		/**
		 * emptys the collection
		 *
		 * @return void
		 */
		public function removeAll()
		{
			$this->items = array();
		}
	}
?>