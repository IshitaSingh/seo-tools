<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * automatically includes classes
	 *
	 * @param   string		$className		name of class
	 * @return  void
	 * @ignore
	 */
	function __autoload( $className )
	{
		if( defined( '__ROOT__' ))
		{
			if( \stripos('/'.$className, 'System\\') === 1 || \stripos('/'.$className, 'System\\') === 0 )
			{
				$path = __ROOT__ . '/' . strtolower(str_replace('\\', '/', $className)) . __CLASS_EXTENSION__;

				include $path;
			}
			else
			{
				$base = __APP_PATH__ . '/';

				if(false !== strpos($className, '\\'))
				{
					$className = substr(strrchr($className, '\\'), 1);
				}

				$idString = 'path:';
				$path = '';

				// get path to class definition
				$path = Build::get( $idString . $className );
				if( !$path )
				{
					$path = class_search( $className, $base ) . strtolower( $className ) . __CLASS_EXTENSION__;

					// create new build file
					Build::put( $idString . $className, $path );
				}

				if( strlen( $path ) > 0 )
				{
					if( !defined( INCLUDEPREFIX . $path ))
					{
						// define class name
						define( INCLUDEPREFIX . $path, true );

						// include class
						if(file_exists($path)) {
							include $path;
						}
					}
				}
			}
		}
		else
		{
			throw new Exception( '__ROOT__ not defined' );
		}
	}


	/**
	 * class_search
	 *
	 * Returns path to class folder if found
	 *
	 * @param   string		$className		name of class
	 * @return  string						path to class folder
	 */
	function class_search( $className, $base = '/' )
	{
		$dir = dir( $base );

		if( file_exists( $base . strtolower( $className ) . __CLASS_EXTENSION__ ))
		{
			return $base;
		}

		while( false !== ( $folder = $dir->read() ))
		{
			if( $folder != '.' && $folder != '..' )
			{
				if( is_dir( $base . $folder ))
				{
					$subFolder = class_search( strtolower( $className ), $base . $folder . '/' );

					if( !is_null( $subFolder ))
					{
						return $subFolder;
					}
				}
			}
		}
		$dir->close();

		return null;
	}
?>