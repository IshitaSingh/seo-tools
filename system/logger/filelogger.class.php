<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Logger;


	/**
	 * Provides access to read/write log messages
	 *
	 * @property   string $path path to log file
	 * @property   string $file name of log file
	 * @property   int $maxFileSize max size of log file in KB, 0 for infinite
	 * 
	 * @package			PHPRum
	 * @subpackage		Logger
	 * @author			Darnell Shinbine
	 */
	class FileLogger extends LoggerBase
	{
		/**
		 * path to log file
		 * @var string
		 */
		private $path			= __LOG_PATH__;

		/**
		 * max size of log file in KB, 0 for infinite
		 * @var int
		 */
		private $maxFileSize	= 0;


		/**
		 * Constructor
		 *
		 * @param string $path path of log files
		 * @param string $maxFileSize max file size
		 * @return void
		 */
		public function __construct($path = __LOG_PATH__, $maxFileSize = 0)
		{
			$this->path = $path;
			$this->maxFileSize = $maxFileSize;
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return mixed
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'path' )
			{
				return $this->path;
			}
			elseif( $field === 'maxFileSize' )
			{
				return $this->maxFileSize;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property `$field` in ".get_class($this));
			}
		}


		/**
		 * sets an object property
		 *
		 * @param  string	$field		name of the field
		 * @param  mixed	$value		value of the field
		 * @return void
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'path' )
			{
				$this->path = (string)$value;
			}
			elseif( $field === 'maxFileSize' )
			{
				$this->maxFileSize = (string)$value;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property `$field` in ".get_class($this));
			}
		}


		/**
		 * This method writes a log message to memory
		 *
		 * @param  string	$message		message to log
		 * @param  string	$category		message category
		 * @return void
		 */
		public function log($message, $category)
		{
			$this->trim($category);
			$file = $this->path . '/' . $category . '.log';

			$log = fopen( $file, 'ab+' );
			if($log)
			{
				if( fwrite( $log, date( 'Y-m-d H:i:s e', time() ) . "\t" . $message . "\n" ))
				{
					fclose( $log );
				}
				else
				{
					throw new \System\Utils\FileNotWritableException("Could not write to log file `{$file}`, check that directory " . $this->path . " is writable");
				}
			}
			else
			{
				throw new \System\Utils\FileNotWritableException("Could not write to log file `{$file}`, check that directory " . $this->path . " is writable");
			}
		}


		/**
		 * This method retrieves all log messages
		 * 
		 * @param  string	$category		message category
		 * @return array
		 */
		public function logs($category)
		{
			$file = $this->path . '/' . $category . '.log';
			$log = fopen( $file, 'r' );

			if($log)
			{
				$logs = array();
				while($message = \fgets( $log ))
				{
					$logs[] = $message;
				}
				fclose( $log );
				return $logs;
			}
			else
			{
				throw new LoggerException("Could not open file `{$file}` for output");
			}
		}


		/**
		 * This method flushes all messages
		 *
		 * @param  string	$category		message category
		 * @return void
		 */
		public function flush($category)
		{
			$file = $this->path . '/' . $category . '.log';
	
			if(!unlink($file))
			{
				throw new LoggerException("Could not delete file `{$file}`");
			}
		}


		/**
		 * This method flushes all messages
		 *
		 * @param  string	$category		message category
		 * @return void
		 */
		private function trim($category)
		{
			$file = $this->path . '/' . $category . '.log';

			if($this->maxFileSize > 0)
			{
				while(\filesize($file) > $this->maxFileSize)
				{
					$log = fopen( $file, 'r' );
					\fgets($log);
					$logs = '';
					while($message = \fgets( $log ))
					{
						$logs .= $message;
					}

					if(\fwrite($log, $logs))
					{
						\fclose($log);
					}
					else
					{
						throw new LoggerException("Could not write to log file `{$file}`");
					}
				}
			}
		}
	}
?>