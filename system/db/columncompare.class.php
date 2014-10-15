<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Provides array comparing by type
	 * 
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class ColumnCompare
	{
		/**
		 * index to compare
		 */
		private $index;


		/**
		 * Constructor
		 *
		 * sets column name to compare
		 *
		 * @param  string	$field		column name
		 * @return void
		 */
		public function __construct( $index ) {
			$this->index = $index;
		}

		/**
		 * method compares strings and returns -1, 0, 1
		 *
		 * @param  string	$a			string a
		 * @param  string	$b			string b
		 * @return int					compare result
		 */
		public function compareStringi($a, $b)
		{
			if( array_key_exists( $this->index, $a ) && array_key_exists( $this->index, $b )) {
				return strcmp( strtolower(trim($a[$this->index])), strtolower(trim($b[$this->index])));
			}
			else return 0;
		}

		/**
		 * method compares strings and returns -1, 0, 1
		 *
		 * @param  string	$a			string a
		 * @param  string	$b			string b
		 * @return int					compare result
		 */
		public function compareString($a, $b)
		{
			if( array_key_exists( $this->index, $a ) && array_key_exists( $this->index, $b )) {
				return strcmp( trim($a[$this->index]), trim($b[$this->index]));
			}
			else return 0;
		}

		/**
		 * method compares ints and returns -1, 0, 1
		 *
		 * @param  ints		$a			ints a
		 * @param  ints		$b			ints b
		 * @return int					compare result
		 */
		public function compareDateString($a, $b)  
		{
			if( array_key_exists( $this->index, $a ) && array_key_exists( $this->index, $b )) {
				$n1 = (int) strtotime( $a[$this->index] );
				$n2 = (int) strtotime( $b[$this->index] );

				if ($n1 === $n2) return 0;
				else return ($n1 < $n2) ? -1 : 1;
			}
			else
			{
				if (isset( $a[$this->index] ))
				{
					return 1;
				}
				else
				{
					return 0;
				}
			}
		}

		/**
		 * method compares numbers and returns -1, 0, 1
		 *
		 * @param  ints		$a			ints a
		 * @param  ints		$b			ints b
		 * @return int					compare result
		 */
		public function compareNumeric($a, $b)  
		{
			if( array_key_exists( $this->index, $a ) && array_key_exists( $this->index, $b )) {

				$n1 = (real)$a[$this->index];
				$n2 = (real)$b[$this->index];

				if ($n1 == $n2) return 0;
				else return ($n1 < $n2) ? -1 : 1;
			}
			else return 0;
		}

		/**
		 * method compares ints and returns -1, 0, 1
		 *
		 * @param  ints		$a			ints a
		 * @param  ints		$b			ints b
		 * @return int					compare result
		 */
		public function compareInt($a, $b)  
		{
			if( array_key_exists( $this->index, $a ) && array_key_exists( $this->index, $b )) {
				$n1 = (int)$a[$this->index];
				$n2 = (int)$b[$this->index];

				if ($n1 === $n2) return 0;
				else return ($n1 < $n2) ? -1 : 1;
			}
			else return 0;
		}
	}
?>