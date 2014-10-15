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
	 * Represents a Collection of WebControl attributes
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class WebControlAttributeCollection extends StringDictionary
	{
		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( is_string( $index ))
			{
				if( is_string( $item ))
				{
					return $this->items[(string)$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected string in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("invalid index expected string in ".get_class($this));
			}
		}


		/**
		 * append attribute of xml node
		 * overwrite attribute with same name
		 *
		 * @param  string		$name		name of attribute
		 * @param  string		$value		value of attribute
		 * @return void
		 */
		public function append( $name, $value ) {
			if( isset( $this[strtolower((string)$name )] )) {
				$this[strtolower((string)$name )] .= (string)$value;
			}
			else {
				$this[strtolower((string)$name )] = (string)$value;
			}
		}
	}
?>