<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Represents a folder on the file system
	 * 
	 * @property string $name name of folder
	 * @property string $path path to folder
	 * @property array $files list of files
	 * @property array $folders list of sub folders
	 * @property int $modified specifies when folder last modified
	 * @property int $accessed specifies when folder last accessed
	 * @property int $created specifies when folder created
	 *
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	class Folder
	{
		/**
		 * collection of files
		 * @var array
		 */
		private $files			= array();

		/**
		 * collection of sub folders
		 * @var array
		 */
		private $folders			= array();

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
			if( $field === 'files' )
			{
				return $this->files;
			}
			elseif( $field === 'folders' )
			{
				return $this->folders;
			}
			elseif( $field === 'name' )
			{
				return $this->name;
			}
			elseif( $field === 'path' )
			{
				return $this->path;
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
		 * @param  string   $dirname	Name of Directory
		 * @return void
		 */
		public function __construct( $dirname )
		{
			if( is_dir( $dirname ))
			{
				$this->path	 = $dirname;
				$this->name	 = substr( strrchr( $dirname, '/' ), 1, strlen( strrchr( $dirname, '/' )));
				$this->modified = filemtime( $dirname );
				$this->accessed = fileatime( $dirname );
				$this->created  = filectime( $dirname );

				$handle = opendir( $dirname );
				if( $handle )
				{
					while(( $file = readdir( $handle )) !== false )
					{
						if( $file != '.' && $file != '..')
						{
							if( is_file( $this->path . '/' . $file ))
							{
								$this->files[] = new File( $this->path . '/' . $file );
							}
							elseif( is_dir( $this->path . '/' . $file ))
							{
								$this->folders[] = new Folder( $this->path . '/' . $file );
							}
						}
					}
				}
				closedir( $handle );
			}
			else
			{
				throw new DirectoryNotFoundException("directory {$dirname} does not exist");
			}
		}


		/**
		 * copy folder to destination
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
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty Folder object");
			}
		}


		/**
		 * move folder to destination
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
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty Folder object");
			}
		}


		/**
		 * delete folder
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
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty Folder object");
			}
		}


		/**
		 * rename folder
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
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty Folder object");
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
				throw new \System\Base\InvalidOperationException("attempt to perform action on empty Folder object");
			}
		}
	}
?>