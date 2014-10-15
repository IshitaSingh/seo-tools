<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Provides base functionality for streaming
	 * 
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	abstract class StreamBase
	{
		/**
		 * handle to file
		 * @var resource
		 */
		protected $handle			= null;


		/**
		 * gets object property
		 *
		 * @return void
		 * @ignore
		 */
		public function __get( $field )
		{
			throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
		}


		/**
		 * closes an open connection
		 *
		 * @return void
		 */
		final public function close()
		{
			if( $this->handle )
			{
				fclose( $this->handle );
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Reads the entire stream or a specified number of bytes from a binary Stream object
		 *
		 * @param  int			$bytes		no. of bytes to read
		 * @return string					raw binary data
		 */
		final public function read( $bytes = 0 )
		{
			if( $this->handle )
			{
				if( (int) $bytes )
				{
					$read = fread( $this->handle, (int) $bytes );

					if( $read !== false )
					{
						return $read;
					}
					else
					{
						throw new IOException("fread on stream object failed");
					}
				}
				else
				{
					$data = '';
					while( $line = $this->readln() )
					{
						$data .= $line;
					}
					return $data;
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Reads one line from a TextStream file and returns the result
		 *
		 * @param  int			$bytes		no. of bytes to read
		 * @return string					raw binary data
		 */
		final public function readln()
		{
			if( $this->handle )
			{
				$line = fgets( $this->handle );

				if( $line !== false )
				{
					return $line;
				}
				else
				{
					return '';
					// throw new IOException("fgets on stream object failed");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Writes binary data to a binary Stream object
		 *
		 * @param  string					raw binary data or text string
		 * @return int						number of bytes written
		 */
		final public function write( $raw )
		{
			if( $this->handle )
			{
				if( is_string( $raw ))
				{
					$bytes = fwrite( $this->handle, $raw, strlen( $raw ));

					if( $bytes !== false )
					{
						return $bytes;
					}
					else
					{
						throw new IOException("fwrite on stream object failed");
					}
				}
				else
				{
					throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::write() must be a string");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Writes a specified number of new-line character to a TextStream file
		 *
		 * @param  string			$string		string to write
		 * @return int						number of bytes written
		 */
		final public function writeln( $line )
		{
			if( $this->handle )
			{
				if( is_string( $line ))
				{
					return $this->write( $line . "\n" );
				}
				else
				{
					throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::write() must be a string");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Returns the size of the data
		 *
		 * @return int						size of array
		 */
		final public function length()
		{
			if( $this->handle )
			{
				$pos = $this->getPosition();
				$this->setBos();
				$size = strlen( $this->read() );
				$this->setPosition( $pos );
				return $size;
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Sets the current position from the beginning of a Stream object
		 *
		 * @param  int		$offset			number of bytes from beggining of file
		 * @return void
		 */
		final public function setPosition( $offset )
		{
			if( $this->handle )
			{
				if( is_int( $offset ))
				{
					if( fseek( $this->handle, $offset ) === -1 )
					{
						throw new \System\Base\ArgumentOutOfRangeException("offset $offset is out of range");
					}
				}
				else
				{
					throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::write() must be an int");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Returns the current position from the beginning of a Stream object
		 *
		 * @return int						current fle pointer position
		 */
		final public function getPosition()
		{
			if( $this->handle )
			{
				$pos = ftell( $this->handle );

				if( $pos !== false )
				{
					return $pos;
				}
				else
				{
					throw new IOException("ftell on stream object failed");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Sets the current position to be the end of the stream (EOS)
		 *
		 * @return void
		 */
		final public function setBOS()
		{
			if( $this->handle )
			{
				if( fseek( $this->handle, 0 ) === -1 )
				{
					throw new IOException("fseek on stream object failed");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Sets the current position to be the end of the stream (EOS)
		 *
		 * @return void
		 */
		final public function setEOS()
		{
			if( $this->handle )
			{
				if( fseek( $this->handle, 0, SEEK_END ) === -1 )
				{
					throw new IOException("fseek on stream object failed");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Returns whether the current position is at the end of the stream or not
		 *
		 * @return bool				True if end of stream
		 */
		final public function eos()
		{
			if( $this->handle )
			{
				return feof( $this->handle );
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}


		/**
		 * Sends the contents of the Stream buffer to the associated underlying object
		 *
		 * @return void
		 */
		final public function flush()
		{
			if( $this->handle )
			{
				if( fflush( $this->handle ) === false )
				{
					throw new IOException("fflush on stream object failed");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." is closed");
			}
		}
	}
?>