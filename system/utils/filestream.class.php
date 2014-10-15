<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Provides a way to stream data to and from a file
	 * 
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	class FileStream extends StreamBase
	{
		/**
		 * size
		 * @var int
		 */
		private $size			= 0;


		/**
		 * gets object property
		 *
		 * @return void
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'size' )
			{
				return $this->size;
			}
			else
			{
				return parent::__get($field);
			}
		}


		/**
		 * opens a text or binary file for input output
		 *
		 * @param  string 		$path			path to file
		 * @param  int			$mode			a = read/write, w = write, r = readonly
		 * @return bool							true if successfull
		 */
		public function open( $path, $mode = 'ab+' )
		{
			$this->handle = fopen( $path, $mode );
			if( $this->handle )
			{
				$this->size = filesize( $path );
				return true;
			}
			else
			{
				return false;
			}
		}
	}
?>