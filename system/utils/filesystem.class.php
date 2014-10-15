<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Provides access to the file system
	 *
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	class FileSystem
	{
		/**
		 * Recursively copy a file or folder and its contents
		 *
		 * @param	   string   $src		Source path
		 * @param	   string   $dest		Destination path
		 * @return	  void
		 */
		static public function copy( $src, $dest = '' )
		{
			if( is_file( $src ))
			{
				// auto generate filename
				if( !$dest )
				{
					$file = new File( $src );
					$dest = str_replace( $file->name, 'Copy of ' . $file->name, $src );

					$i = 2;
					while( is_file( $dest ))
					{
						$dest = str_replace( $file->name, 'Copy (' . $i++ . ') of ' . $file->name, $src );
					}
				}
				if( !copy( $src, $dest ))
				{
					throw new IOException("could not copy source file $src to destination $dest");
				}
			}
			elseif( is_dir( $src ))
			{
				// auto generate directory name
				if( !$dest )
				{
					$file = new Folder( $src );
					$dest = str_replace( $file->name, 'Copy of ' . $file->name, $src );

					$i = 2;
					while( is_dir( $dest ))
					{
						$dest = str_replace( $file->name, 'Copy (' . $i++ . ') of ' . $file->name, $src );
					}
				}

				if( !is_dir( $dest ))
				{
					mkdir( $dest );
				}

				$dir = dir( $src );

				while( false !== $entry = $dir->read() )
				{
					if( $entry === '.' || $entry === '..' )
					{
						continue;
					}

					// deep copy
					if( $dest !== "{$src}/{$entry}" )
					{
						FileSystem::copy( "{$src}/{$entry}", "{$dest}/{$entry}");
					}
				}

				$dir->close();
			}
			else
			{
				throw new FileNotFoundException("source {$src} does not exist");
			}
		}


		/**
		 * Recursively move a file or folder and its contents
		 *
		 * @param	   string   $src		Source path
		 * @param	   string   $dest		Destination path
		 * @return	  void
		 */
		static public function move( $src, $dest )
		{
			if( is_file( $src ))
			{
				FileSystem::copy( $src, $dest );

				if( !unlink( $src ))
				{
					throw new IOException("could not delete source file $src");
				}
			}
			elseif( is_dir( $src ))
			{
				if( is_dir( $dest ))
				{
					throw new IOException("directory $dest already exists");
				}
				else
				{
					mkdir( $dest );
				}

				$dir = dir( $src );
				while( false !== $entry = $dir->read() )
				{
					if( $entry === '.' || $entry === '..' )
					{
						continue;
					}

					// deep move
					if( $dest !== "{$src}/{$entry}" )
					{
						FileSystem::move( "{$src}/{$entry}", "{$dest}/{$entry}");
					}
				}

				$dir->close();
				if( !rmdir( $src ))
				{
					throw new IOException("could not delete source directory $src");
				}
			}
			else
			{
				throw new FileNotFoundException("source $src does not exist");
			}
		}


		/**
		 * Recursively delete a file or folder and its contents using wild cards
		 *
		 * @param   string		$glob				if string, must be a file name (foo.txt),
		 *											if glob pattern (*.txt), or directory name.
		 * @return  void
		 */
		static public function delete( $glob )
		{
			$files = glob( (string) $glob );

			foreach( $files as $file )
			{
				if( is_file( $file ))
				{
					if( !unlink( $file ))
					{
						throw new IOException("could not delete file $file");
					}
				}
				elseif( is_dir( $file ))
				{
					FileSystem::removeDirectory( $file );
				}
			}
		}


		/**
		 * Rename a file or folder to another location
		 *
		 * @param	   string   $old		Old filename
		 * @param	   string   $new		New filename
		 * @return	  void
		 */
		static public function rename( $old, $new )
		{
			if( is_file( $old ))
			{
				if( !rename( $old, $new ))
				{
					throw new IOException("could not rename file {$old}");
				}
			}
			elseif( is_dir( $old ))
			{
				FileSystem::move( $old, $new );
			}
			else
			{
				throw new FileNotFoundException("source file {$old} does not exist");
			}
		}


		/**
		 * Change the file mode (chmod)
		 *
		 * @param   string		$glob				if string, must be a file name (foo.txt),
		 *											if glob pattern (*.txt), or directory name.
		 * @return  void
		 */
		static public function changeFileMode( $glob, $mode = 0755 )
		{
			$files = glob( (string) $glob );

			foreach( $files as $file )
			{
				if( is_file( $file ))
				{
					if( !chmod( $file, $mode ))
					{
						throw new IOException("could not chmod file $file");
					}
				}
				elseif( is_dir( $file ))
				{
					if( !chmod( $file, $mode ))
					{
						throw new IOException("could not chmod directory $file");
					}
				}
			}
		}


		/**
		 * Recursively delete a dir and its contents
		 *
		 * @param	   string   $directory			Path of directory
		 * @return     void
		 */
		static function removeDirectory( $directory )
		{
			if( is_dir( $directory ))
			{
				if( substr($directory,strlen($directory)-1,1) !== '.' && substr($directory,strlen($directory)-2,2) !== '..' )
				{
					$dir = dir( $directory );
					while( false !== $entry = $dir->read() )
					{
						if( $entry === '.' || $entry === '..' )
						{
							continue;
						}

						FileSystem::delete("{$directory}/{$entry}");
					}

					$dir->close();

					if( !rmdir( $directory ))
					{
						throw new IOException("could not delete directory {$directory}");
					}
				}
			}
			else
			{
				throw new DirectoryNotFoundException("directory {$directory} does not exist");
			}
		}
	}
?>