<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Represents a DataSet type
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class DataSetType
	{
		private $flags;

		private function __construct($flags)
		{
			$this->flags = (int)$flags;
		}

		/**
		 * Specifies the DataSet type is OpenDynamic
		 * @return DataSetType
		 */
		public static function OpenDynamic() {return new DataSetType(1);}

		/**
		 * Specifies the DataSet type is OpenStatic
		 * @return DataSetType
		 */
		public static function OpenStatic() {return new DataSetType(2);}

		/**
		 * Specifies the DataSet type is OpenReadonly
		 * @return DataSetType
		 */
		public static function OpenReadonly() {return new DataSetType(4);}
	}
?>