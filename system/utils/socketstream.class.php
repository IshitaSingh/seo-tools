<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Provides a way to stream data to and from a socket
	 * 
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	class SocketStream extends StreamBase
	{
		/**
		 * hostname
		 * @var int
		 */
		private $_errno				= 0;

		/**
		 * port
		 * @var string
		 */
		private $_errstr			= '';


		/**
		 * opens a socket for input output
		 *
		 * @param  string 		$host			host
		 * @param  int	 		$port			port
		 * @param  int			$timeout		timeout is seconds
		 * @return bool							true if successfull
		 */
		public function open( $host, $port = 80, $timeout = 30 )
		{
			$this->handle = fsockopen( $host, $port, $this->_errno, $this->_errstr, $timeout );
			if( $this->handle )
			{
				return stream_set_timeout( $this->handle, $timeout );
			}
			else
			{
				return false;
			}
		}
	}
?>