<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Caching;


	/**
	 * Provides access to read/write cache files
	 *
	 * @package			PHPRum
	 * @subpackage		Caching
	 * @author			Darnell Shinbine
	 */
	class FileCache extends CacheBase
	{
		/**
		 * Retrieves a value from cache with a specified key.
		 *
		 * @param string	$id			the key identifying the value to be cached
		 * @return string				the value stored in cache, returns null if empty
		 */
		public function get( $id )
		{
			$file = $this->getPath( (string)$id );

			if( file_exists( $file ))
			{
				$fp = fopen($file, 'r');
				$expires = fgets($fp);
				$contents = "";
				while(!feof($fp)) {
					$contents .= fgets($fp);
				}
				fclose($fp);

				if( $contents )
				{
					if( $expires > time() )
					{
						return \unserialize( $contents );
					}
				}
			}

			return null;
		}


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
		public function put( $id, $value, $expires = 0 )
		{
			if($expires) {
				$expires = time() + (int)$expires;
			}
			else {
				$expires = 2147483647;
			}

			$file = $this->getPath( (string)$id );
			$value = $expires . "\n" . \serialize( $value );

			$fp = @fopen( $file, 'wb+' );
			if( $fp )
			{
				if( fwrite( $fp, $value, strlen( $value )) !== false )
				{
					fclose( $fp );
				}
				else
				{
					throw new \System\Utils\FileNotWritableException("Could not write to log file `{$file}`, check that directory " . __CACHE_PATH__ . " is writable");
				}
			}
			else
			{
				throw new \System\Utils\FileNotWritableException("Could not write to log file `{$file}`, check that directory " . __CACHE_PATH__ . " is writable");
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
			$file = $this->getPath( (string)$id );

			if( $this->exists( (string)$id ))
			{
				if( !unlink( $file ))
				{
					throw new CacheException("Could not delete cache file {$file}");
				}
			}
			else
			{
				return false;
			}
		}


		/**
		 * Deletes all values from cache.
		 *
		 * @return void
		 */
		public function flush()
		{
			$files = glob( __CACHE_PATH__ . '/*' );

			foreach( $files as $file )
			{
				if( is_file( $file ))
				{
					if( !unlink( $file ))
					{
						throw new CacheException("Could not delete cache file {$file}");
					}
				}
			}
		}


		/**
		 * get id array
		 *
		 * @return array
		 */
		public function getIdArray()
		{
			$idArray = array();
			$files = glob( __CACHE_PATH__ . '/*' );

			foreach( $files as $file )
			{
				if( is_file( $file ))
				{
					$idArray[] = str_replace( '\\', '_', base64_decode( substr( strrchr( $file, '/' ), 1 )));
				}
			}

			return $idArray;
		}


		/**
		 * Returns the path to the cache file
		 *
		 * @param  string	$id		cache id
		 * @return string			path
		 */
		private function getPath( $id )
		{
			// Collission resistant (not proof) hash sring
			$cacheId = substr((string)$id, 0, 1) . md5((string)$id) . substr((string)$id, strlen((string)$id)-1);
			return __CACHE_PATH__ . '/' . $cacheId;
		}
	}
?>