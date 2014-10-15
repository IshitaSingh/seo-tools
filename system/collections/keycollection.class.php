<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Collections;


	/**
	 * Represents a collection of keys
	 * 
	 * @package			PHPRum
	 * @subpackage		Collections
	 * @author			Darnell Shinbine
	 */
	class KeyCollection extends CollectionBase
	{
		/**
		 * return key at a specified index
		 *
		 * @param  int		$index			index of item
		 * @return string				   key
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}
	}
?>