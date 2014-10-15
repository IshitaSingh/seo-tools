<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Caching;


	/**
	 * Provides access to read/write cache objects
	 *
	 * @package			PHPRum
	 * @subpackage		Caching
	 * @author			Darnell Shinbine
	 */
	abstract class CacheBase
	{
		/**
		 * Retrieves a value from cache with a specified key.
		 *
		 * @param string	$id			the key identifying the value to be cached
		 * @param integer	$expire		the expiration time of the value in seconds
		 * 								0 means never expire,
		 * @return string				the value stored in cache
		 */
		abstract public function get( $id );


		/**
		 * Stores a value identified by a key into cache.
		 *
		 * If the cache already contains such a key, the existing value and
		 * expiration time will be replaced with the new ones.
		 *
		 * @param string	$id		the key identifying the value to be cached
		 * @param string 	$value	the value to be cached
		 * @param integer	$expires	the expiration time of the value in seconds
		 * 								0 means never expire,
		 * @return void
		 */
		abstract public function put( $id, $value, $expires = 0 );


		/**
		 * Deletes a value with the specified key from cache
		 *
		 * @param  string	$id		the key identifying the value to be cached
		 * @return void
		 */
		abstract public function clear( $id );


		/**
		 * Deletes all values from cache.
		 *
		 * @return void
		 */
		abstract public function flush();
	}
?>