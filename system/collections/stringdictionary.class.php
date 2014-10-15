<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Collections;


	/**
	 * Represents a dictionary of key/value strings
	 * 
	 * @package			PHPRum
	 * @subpackage		Collections
	 * @author			Darnell Shinbine
	 */
	class StringDictionary extends DictionaryBase
	{
		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
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
		 * Adds a new key/item pair to a StringCollection if key does not already exist
		 *
		 * @param  string		$key			key
		 * @param  string		$value			value
		 * @return void
		 */
		public function add( $key, $value )
		{
			if( is_string( $key ))
			{
				if( is_string( $value ))
				{
					return parent::add( $key, $value );
				}
				else
				{
					throw new \System\Base\InvalidArgumentException("Argument 2 passed to ".get_class($this)."::add() must be a string");
				}
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be a string");
			}
		}


		/**
		 * return the value of a key in the Dictionary
		 *
		 * @param  string		$key			key
		 * @return string						value
		 */
		public function item( $key )
		{
			return parent::item($key);
		}


		/**
		 * returns the value of a specified index
		 *
		 * @param  int			$index			index
		 * @return string					   value
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}


		/**
		 * returns the key of a specified index
		 *
		 * @param  int			$index			index
		 * @return string					   key
		 */
		public function keyAt( $index )
		{
			return parent::keyAt($index);
		}


		/**
		 * trim whitespace for all values
		 * 
		 * @return void
		 */
		public function trim()
		{
			foreach( $this->items as $key => $value )
			{
				$this->items[$key] = trim($value);
			}
		}
	}
?>