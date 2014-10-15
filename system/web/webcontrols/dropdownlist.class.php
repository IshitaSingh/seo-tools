<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a DropDownList Control
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class DropDownList extends ListBox
	{
		/**
		 * size of listbox
		 * @var int
		 */
		protected $listSize				= 1;

		/**
		 * size of listbox
		 * @var bool
		 */
		protected $multiple				= false;


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * 
		 * @return mixed
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'listSize' || $field === 'multiple' )
			{
				throw new \System\Base\BadMemberCallException("call to readonly property $field in ".get_class($this));
			}
			else
			{
				parent::__set($field,$value);
			}
		}
	}
?>