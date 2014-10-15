<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Represents a GDImage Resize Mode
	 *
	 * @author			Darnell Shinbine
	 * @package			PHPRum
	 * @subpackage		GD
	 */
	final class GDImageResizeMode
	{
		private $flags;

		private function  __construct($flags)
		{
			$this->flags = (int)$flags;
		}

		/**
		 * specifies scaleToFit resize mode
		 * @var GDImageResizeMode
		 */
		public static function scaleToFit() {return new GDImageResizeMode(1);}

		/**
		 * specifies scaleToCrop resize mode
		 * @var GDImageResizeMode
		 */
		public static function scaleToCrop() {return new GDImageResizeMode(2);}

		/**
		 * specifies scaleToWidth resize mode
		 * @var GDImageResizeMode
		 */
		public static function scaleToWidth() {return new GDImageResizeMode(4);}

		/**
		 * specifies scaleToHeight resize mode
		 * @var GDImageResizeMode
		 */
		public static function scaleToHeight() {return new GDImageResizeMode(8);}

		/**
		 * specifies resize resize mode
		 * @var GDImageResizeMode
		 */
		public static function resize() {return new GDImageResizeMode(16);}
	}
?>