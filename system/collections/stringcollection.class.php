<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Collections;


	/**
	 * Represents a collection of strings
	 * 
	 * @package			PHPRum
	 * @subpackage		Collections
	 * @author			Darnell Shinbine
	 */
	class StringCollection extends CollectionBase
	{
		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( (string)$index, $this->items ))
			{
				if( is_string( $item ))
				{
					$this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected string in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add item to Collection
		 *
		 * @param  string	$item		item
		 * @return void
		 */
		public function add( $item )
		{
			if( is_string( $item ))
			{
				return parent::add( $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be a string");
			}
		}


		/**
		 * return item at a specified index
		 *
		 * @param  int		$index			index of item
		 * @return string				   item
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}


		/**
		 * trim whitespace for all values
		 * 
		 * @return void
		 */
		public function trim()
		{
			for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
			{
				$this->items[$i] = trim( $this->items[$i] );
			}
		}
	}
?>