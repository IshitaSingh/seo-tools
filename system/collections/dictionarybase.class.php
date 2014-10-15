<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Collections;


	/**
	 * Provides the base functionality for creating Dictionaries
	 *
	 * @property   KeyCollection $keys Collection of keys
	 * @property   ValueCollection $values Collection of values
	 * 
	 * @package			PHPRum
	 * @subpackage		Collections
	 * @author			Darnell Shinbine
	 */
	abstract class DictionaryBase extends IteratorBase
	{
		/**
		 * Constructor
		 * 
		 * @param  mixed	$dictionary		can be DictionaryBase or array used to initialize Dictionary
		 * @return void
		 */
		public function __construct( $dictionary = null )
		{
			if( isset( $dictionary ))
			{
				if( is_array( $dictionary ))
				{
					$this->items = $dictionary;
				}
				elseif( $dictionary instanceof DictionaryBase )
				{
					$this->items = $dictionary->items;
				}
				else
				{
					throw new \System\Base\InvalidArgumentException("No overloaded constructor accepts ".gettype($dictionary));
				}
			}
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return bool					true on success
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'keys' )
			{
				return array_keys( $this->items );
			}
			elseif( $field === 'values' )
			{
				return array_values( $this->items );
			}
			else
			{
				return parent::__get($field);
			}
		}


		/**
		 * Adds a new key/item pair to a Dictionary object if key does not already exist
		 *
		 * @param  mixed		$key			key
		 * @param  mixed		$value			value
		 * @return void
		 */
		public function add( $key, $value )
		{
			if( !$this->contains( $key ))
			{
				$this->items[$key] = $value;
			}
			else
			{
				throw new \System\Base\InvalidOperationException( "key `$key` already exists" );
			}
		}


		/**
		 * return the value of a key in the Dictionary
		 *
		 * @param  mixed		$key			key
		 * @return mixed						value
		 */
		public function item( $key )
		{
			if( isset( $this->items[$key] ))
			{
				return $this->items[$key];
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("key $key not found in ".get_class($this));
			}
		}


		/**
		 * returns a Boolean value that indicates whether a specified key exists in the Dictionary object
		 *
		 * @param  mixed		$key		key
		 * @return bool
		 */
		public function contains( $key )
		{
			return (bool) array_key_exists( $key, $this->items );
		}


		/**
		 * returns index of key found in Dictionary
		 *
		 * @param  mixed	$key		key
		 * @return int					index of key
		 */
		public function indexOf( $key )
		{
			$keys = $this->keys;
			for( $i = 0, $count = count($keys); $i < $count; $i++ )
			{
				if( $keys[$i] === $key )
				{
					return $i;
				}
			}
			return -1;
		}


		/**
		 * returns the value of a specified index
		 *
		 * @param  int			$index			index
		 * @return mixed						value
		 */
		public function itemAt( $index )
		{
			$values = $this->values;
			if( isset( $values[$index] ))
			{
				return $values[$index];
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("Argument out of range in ".get_class($this)."::itemAt()");
			}
		}


		/**
		 * returns the key of a specified index
		 *
		 * @param  int			$index			index
		 * @return mixed						value
		 */
		public function keyAt( $index )
		{
			$keys = $this->keys;
			if( isset( $keys[$index] ))
			{
				return $keys[$index];
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("Argument out of range in ".get_class($this)."::keyAt()");
			}
		}


		/**
		 * remove key/value pair from Dictionary
		 *
		 * @param  string	$key		key to remove
		 * @return void
		 */
		public function remove( $key )
		{
			if( isset( $this->items[$key] ))
			{
				unset( $this->items[$key] );
				return true;
			}
			else
			{
				return false;
			}
		}


		/**
		 * remove key/value of a specified index
		 *
		 * @param  int		$index		index to remove
		 * @return void
		 */
		public function removeAt( $index )
		{
			$this->remove( $this->itemAt( $index ));
		}


		/**
		 * emptys the dictionary
		 *
		 * @return void
		 */
		public function removeAll()
		{
			$this->items = array();
		}
	}
?>
