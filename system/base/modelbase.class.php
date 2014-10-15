<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * This class represents a data model.
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class ModelBase implements \ArrayAccess
	{
		/**
		 * static method to create new ActiveRecordBase of this type
		 *
		 * @param  array		$args		optional associative array of initial properties
		 * @return ActiveRecordBase
		 */
		final static protected function getClass()
		{
			$className = \get_called_class();
			if($className == 'System\ActiveRecords\ActiveRecordBase')
			{
				$backtrace = debug_backtrace();
				$className = $backtrace[2]['class'];
			}

			return $className;
		}
	}
?>