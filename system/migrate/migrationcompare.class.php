<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Migrate;


	/**
	 * Provides migration sorting
	 * 
	 * @package			PHPRum
	 * @subpackage		Migrate
	 * @author			Darnell Shinbine
	 */
	final class MigrationCompare
	{
		/**
		 * method compares version numbers
		 *
		 * @param  real		$a			ints a
		 * @param  real		$b			ints b
		 * @return int					compare result
		 */
		public function compareVersion($a, $b)
		{
			$n1 = (real)$a->version;
			$n2 = (real)$b->version;

			if ($n1 === $n2) return 0;
			else return ($n1 < $n2) ? -1 : 1;
		}
	}
?>