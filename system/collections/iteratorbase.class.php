<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Collections;
	use \ArrayAccess;
	use \Iterator;
	use \Countable;


	/**
	 * Provides the base functionality for creating Iterators
	 *
	 * @property   int $count number of collection items
	 *
	 * @package			PHPRum
	 * @subpackage		Collections
	 * @author			Darnell Shinbine
	 */
	abstract class IteratorBase implements ArrayAccess, Iterator, Countable
	{
		/**
		 * internal array
		 * @var array
		 */
		protected $items = array();


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 *
		 * @return bool					true on success
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'count' )
			{
				return count( $this->items );
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property {$field} in ".get_class($this));
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return void					string of variables
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
		}


		/**
		 * Constructor
		 *
		 * @param  IteratorBase	 $iterator used to initialize DictionaryBase
		 * @return void
		 */
		public function __construct( IteratorBase $iterator = null )
		{
			if( isset( $iterator ))
			{
				$this->items = $iterator->items;
			}
		}


		/**
		 * implement Countable methods
		 * @ignore
		 */
		public function count()
		{
			return count($this->items);
		}


		/**
		 * implement Iterator methods
		 * @ignore 
		 */
		public function rewind()
		{
			return reset($this->items);
		}

		/**
		 * implement Iterator methods
		 * @ignore 
		 */
		public function current()
		{
			return current($this->items);
		}

		/**
		 * implement Iterator methods
		 * @ignore 
		 */
		public function key()
		{
			return key($this->items);
		}

		/**
		 * implement Iterator methods
		 * @ignore 
		 */
		public function next()
		{
			return next($this->items);
		}

		/**
		 * implement Iterator methods
		 * @ignore 
		 */
		public function valid()
		{
			return (bool) null !== $this->current();
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetExists($index)
		{
			return (bool) array_key_exists( $index, $this->items );
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetGet($index)
		{
			if( array_key_exists( $index, $this->items ))
			{
				return $this->items[$index];
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				$this->items[$index] = $item;
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
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
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * convert Iterator object into array
		 * 
		 * @return array
		 */
		public function toArray()
		{
			return $this->items;
		}
	}
?>