<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;
	use \System\Collections\StringDictionary;


	/**
	 * Represents a Collection of list items
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class ListItemCollection extends StringDictionary
	{
		/**
		 * Adds a new key/item pair to a Dictionary object
		 *
		 * @param  mixed		$key			key
		 * @param  mixed		$value			value
		 * @return void
		 */
		public function add( $key, $value )
		{
			$this->items[$key] = $value;
		}


		/**
		 * Adds a new key/item pair to the beggining of a Dictionary object
		 *
		 * @param  mixed		$key			key
		 * @param  mixed		$value			value
		 * @return void
		 */
		public function addToBeginning( $key, $value )
		{
			$this->items =  array($key=>$value) + $this->items;
		}


		/**
		 * Returns a Boolean value that indicates whether a specified value exists in the Dictionary object
		 *
		 * @param  mixed		$value		value
		 * @return bool
		 */
		public function containsItem( $value )
		{
			foreach( $this->values as $itemValue )
			{
				if( $value == $itemValue )
				{
					return true;
				}
			}
			return false;
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
				if( trim($keys[$i]) === $key )
				{
					return $i;
				}
			}
			return -1;
		}


		/**
		 * returns index of item found in Dictionary
		 *
		 * @param  mixed	$key		key
		 * @return int					index of key
		 */
		public function indexOfItem( $value )
		{
			$values = $this->values;
			for( $i = 0, $count = count($values); $i < $count; $i++ )
			{
				if( $values[$i] === $value )
				{
					return $i;
				}
			}
			return -1;
		}
	}
?>