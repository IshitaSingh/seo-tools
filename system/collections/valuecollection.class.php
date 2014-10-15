<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Collections;


	/**
	 * Represents a collection of values
	 * 
	 * @package			PHPRum
	 * @subpackage		Collections
	 * @author			Darnell Shinbine
	 */
	class ValueCollection extends CollectionBase
	{
		/**
		 * return value at a specified index
		 *
		 * @param  int		$index			index of item
		 * @return string				   value
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}
	}
?>