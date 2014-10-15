<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Represents a file on the file system
	 *
	 * @property string $name name of file
	 * @property string $path path to file
	 * @property string $size size of file
	 * @property int $modified specifies when file last modified
	 * @property int $accessed specifies when file last accessed
	 * @property int $created specifies when file created
	 *
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	class File
	{
		/**
		 * name of file
		 * @var string
		 */
		private $name				= '';

		/**
		 * path to file
		 * @var string
		 */
		private $path				= '';

		/**
		 * size of file
		 * @var int
		 */
		private $size				= 0;

		/**
		 * last modified
		 * @var int
		 */
		private $modified			= 0;

		/**
		 * last accessed
		 * @var int
		 */
		private $accessed			= 0;

		/**
		 * created
		 * @var int
		 */
		private $created			= 0;


		/**
		 * gets object property
		 *
		 * @return void
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'name' )
			{
				return $this->name;
			}
			elseif( $field === 'path' )
			{
				return $this->path;
			}
			elseif( $field === 'size' )
			{
				return $this->size;
			}
			elseif( $field === 'modified' )
			{
				return $this->modified;
			}
			elseif( $field === 'accessed' )
			{
				return $this->accessed;
			}
			elseif( $field === 'created' )
			{
				return $this->created;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * Constructor
		 *
		 * @param  string   $filename   Name of File
		 * @return void
		 */
		public function __construct( $filename )
		{
			if( is_file( $filename ))
			{
				$this->path = $filename;
				$this->name = substr( strrchr( $filename, '/' ), 1, strlen( strrchr( $filename, '/' )));
				$this->size = filesize( $filename );
				$this->modified = filemtime( $filename );
				$this->accessed = fileatime( $filename );
				$this->created  = filectime( $filename );
			}
			else
			{
				throw new FileNotFoundException("file {$filename} does not exist");
			}
		}


		/**
		 * copy file to destination
		 *
		 * @param  string	$dest	path to destination
		 * @return void
		 */
		public function copy( $dest = '' )
		{
			if( $this->path )
			{
				FileSystem::copy( $this->path, $dest );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty File object");
			}
		}


		/**
		 * move file to destination
		 *
		 * @param  string	$dest	path to destination
		 * @return void
		 */
		public function move( $dest )
		{
			if( $this->path )
			{
				FileSystem::move( $this->path, $dest );
				$this->path = $dest;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty File object");
			}
		}


		/**
		 * delete file
		 *
		 * @return void
		 */
		public function delete()
		{
			if( $this->path )
			{
				FileSystem::delete( $this->path );
				$this->path = '';
			}
			else
			{
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty File object");
			}
		}


		/**
		 * rename file
		 *
		 * @param  string	$newname	new name
		 * @return void
		 */
		public function rename( $newname )
		{
			if( $this->path )
			{
				FileSystem::rename( $this->path, $newname );
				$this->path = $newname;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty File object");
			}
		}


		/**
		 * change file mode
		 *
		 * @param  string	$mode	chmod
		 * @return void
		 */
		public function changeFileMode( $mode = 0755 )
		{
			if( $this->path )
			{
				return FileSystem::changeFileMode( $this->path, $mode );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty File object");
			}
		}


		/**
		 * returns the last line of text file **without** reading the entire file
		 *
		 * @param  string   $_file		path to file to read
		 * @return string				last line of file
		 */
		public function readLastLine()
		{
			if( file_exists( $this->path ))
			{
				$pos = -2;
				$t   = ' ';
				$fp = fopen( $this->path, 'r' );
				if( $fp )
				{
					while( $t != '\n' )
					{
						fseek( $fp, $pos, SEEK_END );
						$t = fgetc( $fp );
						$pos--;
					}

					$line = fgets( $fp );
					fclose( $fp );
					return $line;
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("file {$this->path} does not exist");
			}
		}
	}
?>