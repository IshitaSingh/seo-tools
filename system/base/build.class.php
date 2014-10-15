<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Provides access to read/write build files
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class Build
	{
		/**
		 * verbose
		 * @var bool
		 */
		static $verbose = false;


		/**
		 * Retrieves a value from cache with a specified key.
		 *
		 * @param string	$id			the key identifying the value to be cached
		 * @param integer	$expire		the expiration time of the value in seconds
		 * 								0 means never expire,
		 * @return string				the value stored in cache, null if nothing is found
		 */
		public static function get( $id )
		{
			$file = self::getPath( $id );

			if( file_exists( $file ))
			{
				$file = self::getPath( $id );
				$value = file_get_contents( $file );

				if( $value !== false )
				{
					return \unserialize( $value );
				}
				else
				{
					throw new \Exception("Could not read build file `$file`");
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
		 * @return void
		 */
		public static function put( $id, $value )
		{
			$file = self::getPath( $id );
			$value = \serialize( $value );

			$fp = @fopen( $file, 'wb+' );
			if( $fp )
			{
				if( self::$verbose ) echo "Rebuilding {$file}\r\n";

				if( fwrite( $fp, $value, strlen( $value )) !== false )
				{
					fclose( $fp );
				}
				else
				{
					throw new \System\Utils\FileNotWritableException("Could not write to `{$file}`, check that directory " . __BUILD_PATH__ . " is writable");
				}
			}
			else
			{
				throw new \System\Utils\FileNotWritableException("Could not write to `{$file}`, check that directory " . __BUILD_PATH__ . " is writable");
			}
		}


		/**
		 * Deletes a value with the specified key from cache
		 *
		 * @param  string	$id		the key identifying the value to be cached
		 * @return void
		 */
		public static function clear( $id )
		{
			$file = self::getPath( $id );

			if( self::exists( $id ))
			{
				if( self::$verbose ) echo "Preparing {$file}\r\n";

				if( !unlink( $file ))
				{
					throw new \Exception("Could not delete build file {$file}");
				}
			}
			else
			{
				return false;
			}
		}


		/**
		 * Cleans and prepares build folder
		 *
		 * @return void
		 */
		public static function clean()
		{
			$files = glob( __BUILD_PATH__ . '/*' );

			if( $files )
			{
				foreach( $files as $file )
				{
					if( is_file( $file ))
					{
						if( self::$verbose ) echo "Preparing {$file}\r\n";

						if( !unlink( $file ))
						{
							throw new \Exception("Could not delete build file {$file}");
						}
					}
				}
			}
		}


		/**
		 * Rebuild build folder
		 *
		 * @return void
		 */
		public static function rebuild()
		{
			$app = ApplicationBase::getInstance();
			if($app instanceof \System\Web\WebApplicationBase)
			{
				ob_start();
				foreach(ApplicationBase::getInstance()->getAllControllers() as $module)
				{
					try
					{
						// disable header redirects
						$GLOBALS["__DISABLE_HEADER_REDIRECTS__"] = true;
						$controller = $app->getRequestHandler($module);
						$view = $controller->getView(new \System\Web\HTTPRequest());
						unset($GLOBALS["__DISABLE_HEADER_REDIRECTS__"]);
					}
					catch(\Exception $e)
					{
					}
				}
				ob_end_flush();
			}
		}


		/**
		 * get id array
		 *
		 * @return array
		 */
		public static function getFileArray()
		{
			$idArray = array();
			$files = glob( __BUILD_PATH__ . '/*' );

			foreach( $files as $file )
			{
				if( is_file( $file ))
				{
					$idArray[] = $file;
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
		private static function getPath( $id )
		{
			// TODO: handle windows?
			return __BUILD_PATH__ . '/' . str_replace( '/', '_', base64_encode( (string)$id ));
		}
	}
?>