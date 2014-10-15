<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Caching;


	/**
	 * Provides access to read/write cache objects from memcache
	 *
	 * @package			PHPRum
	 * @subpackage		Caching
	 * @author			Darnell Shinbine
	 */
	class MEMCache extends CacheBase
	{
		/**
		 * mem cache object
		 * @var resource
		 */
		private $memcache;

		/**
		 * mem cache version
		 * @var string
		 */
		private $version;

		/**
		 * Constructor
		 *
		 * @param string $server server, default is localhost
		 * @param string $port port, default is 11211
		 */
		final public function __construct($server = 'localhost', $port = 11211)
		{
			$this->memcache = new \Memcache;
			if($this->memcache->connect((string)$server, (int)$port))
			{
				$this->version = $this->memcache->getVersion();
			}
			else
			{
				throw new CacheException("Could not connect to Memcache server");
			}
		}


		/**
		 * Retrieves a value from cache with a specified key.
		 *
		 * @param string	$id			the key identifying the value to be cached
		 * @return string				the value stored in cache
		 */
		public function get( $id )
		{
			return $this->memcache->get((string)$id);
		}


		/**
		 * Stores a value identified by a key into cache.
		 *
		 * If the cache already contains such a key, the existing value and
		 * expiration time will be replaced with the new ones.
		 *
		 * @param string	$id		the key identifying the value to be cached
		 * @param string 	$value	the value to be cached
		 * @param integer	$expire		the expiration time of the value in seconds
		 * 								0 means never expire,
		 * @return void
		 */
		public function put( $id, $value, $expires = 0 )
		{
			if(!$this->memcache->set((string)$id, $value, false, (int)$expires))
			{
				throw new CacheException("Could not write to Memcache object");
			}
		}


		/**
		 * Deletes a value with the specified key from cache
		 *
		 * @param  string	$id		the key identifying the value to be cached
		 * @return void
		 */
		public function clear( $id )
		{
			$this->memcache->delete((string)$id);
		}


		/**
		 * Deletes all values from cache.
		 *
		 * @return void
		 */
		public function flush()
		{
			$this->memcache->flush();
		}
	}
?>