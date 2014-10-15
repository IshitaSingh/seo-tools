<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web;


	/**
	 * Pprovides a way to send HTTP response information to the client
	 *
	 * @property int $statusCode code HTTP response status code
	 * @property string $status HTTP response status
	 * @property array $headers HTTP response headers
	 * @property string $contents HTTP response
	 *
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	class HTTPResponse
	{
		/**
		 * specifies status
		 * @var string
		 */
		static private $status		= '';

		/**
		 * specifies status code
		 * @var int
		 */
		static private $statusCode	= 0;

		/**
		 * speciefies whether information is ready to be sent
		 * @var HTTPResponse
		 */
		static private $outputBuffer = null;

		/**
		 * speciefies whether information is ready to be sent
		 * @var bool
		 */
		private $readyToSend		= true;


		/**
		 * Constructor
		 *
		 * @return  void
		 */
		public function __construct()
		{
			ob_start();
		}


		/**
		 * Destructor
		 *
		 * @return  void
		 * @ignore
		 */
		public function __destruct()
		{
			if( $this->readyToSend )
			{
				ob_end_flush();
			}
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		key
		 *
		 * @return string
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'statusCode' )
			{
				return self::$statusCode;
			}
			elseif( $field === 'status' )
			{
				return self::$status;
			}
			elseif( $field === 'contents' )
			{
				return self::getResponseContent();
			}
			elseif( $field === 'headers' )
			{
				return self::getResponseHeaders();
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'statusCode' )
			{
				return $this->_setStatusCode($value);
			}
			elseif( $field === 'status' )
			{
				throw new \System\Base\BadMemberCallException("attempt to set readonly property HTTPResponse::status");
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * adds header to output stream
		 *
		 * @param  string	$header			header information
		 * @return  void
		 */
		public static function addHeader( $header )
		{
			if( self::getOutputBuffer()->readyToSend )
			{
				header((string)$header);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("response information already sent");
			}
		}


		/**
		 * sends information to an output stream
		 *
		 * @param  string	$content		information to send
		 * @return  void
		 */
		public static function write( $content )
		{
			if( self::getOutputBuffer()->readyToSend )
			{
				echo (string)$content;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("response information already sent");
			}
		}


		/**
		 * send all content to client
		 *
		 * @return  void
		 */
		public static function flush()
		{
			if( self::getOutputBuffer()->readyToSend )
			{
				ob_flush();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("response information already sent");
			}
		}


		/**
		 * clear all content from output buffer
		 *
		 * @return  void
		 */
		public static function clear()
		{
			if( self::getOutputBuffer()->readyToSend )
			{
				ob_clean();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("response information already sent");
			}
		}


		/**
		 * send response to client and stop execution
		 *
		 * @return  void
		 */
		public static function end()
		{
			if(isset($GLOBALS["__DISABLE_HEADER_REDIRECTS__"]))
			{
				self::clear();
				return;
			}
			else
			{
				self::flush();
				exit;
			}
		}


		/**
		 * return response content
		 *
		 * @return  string
		 */
		public static function getResponseContent() {
			return ob_get_contents();
		}


		/**
		 * set cookie
		 *
		 * @param   string		$name		the name of the cookie
		 * @param   string		$value		the value of the cookie
		 * @param   int			$expires	timestamp that the cookie will expire on
		 * @param   string		$path		the path on the server that the cookie will be available on
		 * @param   string		$domain		the domain that the cookie is available on
		 * @param   bool		$secure		requires the cookie be sent over secure HTTPS
		 * @return  void
		 */
		public static function setCookie( $name, $value = '', $expires = 0, $path = '/', $domain = null, $secure = false )
		{
			if( self::getOutputBuffer()->readyToSend )
			{
				setcookie( (string)$name, (string)$value, (int)$expires, (string)$path, !is_null($domain)?(string)$domain:__HOST__, (bool)$secure);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("response information already sent");
			}
		}


		/**
		 * redirect to new URL
		 *
		 * @param   string		$location		the URL to redirect to
		 * @param   bool		$terminate		specifies whether to stop execution
		 *
		 * @return  void
		 */
		public static function redirect( $location, $terminate = true )
		{
			self::addHeader("location:".(string)$location);

			if($terminate)
			{
				self::end();
			}
		}


		/**
		 * return response headers
		 *
		 * @return  StringCollection
		 */
		public static function getResponseHeaders() {
			return new \System\Collections\StringCollection( headers_list() );
		}


		/**
		 * get output buffer
		 *
		 * @return HTTPResponse
		 */
		private static function & getOutputBuffer()
		{
			if( !self::$outputBuffer )
			{
				self::$outputBuffer = new HTTPResponse();
			}

			return self::$outputBuffer;
		}


		/**
		 * set status code
		 *
		 * @param   int		$statuscode			status code
		 * @return  void
		 */
		private function _setStatusCode( $statuscode )
		{
			$status = '';
			switch( (int)$statuscode )
			{
				// ok
				case 200: $status = 'OK'; break;
				case 201: $status = 'Created'; break;
				case 202: $status = 'Accepted'; break;
				case 203: $status = 'Non-Authoritative Information'; break;
				case 204: $status = 'No Content'; break;
				case 205: $status = 'Reset Content'; break;
				case 207: $status = 'Partial Content'; break;

				// redirection
				case 300: $status = 'Multiple Choices'; break;
				case 301: $status = 'Moved Permanently'; break;
				case 302: $status = 'Found'; break;
				case 303: $status = 'See Other'; break;
				case 304: $status = 'Not Modified'; break;
				case 305: $status = 'Use Proxy'; break;
				case 307: $status = 'Temporary Redirect'; break;

				// client error
				case 400: $status = 'Bad Request'; break;
				case 401: $status = 'Unauthorized'; break;
				case 402: $status = 'Payment Required'; break;
				case 403: $status = 'Forbidden'; break;
				case 404: $status = 'Not Found'; break;
				case 405: $status = 'Method Not Allowed'; break;
				case 406: $status = 'Not Acceptable'; break;
				case 407: $status = 'Proxy Authentication Required'; break;
				case 408: $status = 'Request Timeout'; break;
				case 409: $status = 'Conflict'; break;
				case 410: $status = 'Gone'; break;
				case 411: $status = 'Length Required'; break;
				case 412: $status = 'Precondition Failed'; break;
				case 413: $status = 'Request Entity Too Large'; break;
				case 414: $status = 'Request-URI Too Long'; break;
				case 415: $status = 'Unsupported Media Type'; break;
				case 416: $status = 'Requested Range Not Satisfiable'; break;
				case 417: $status = 'Expectation Failed'; break;

				// server error
				case 500: $status = 'Internal Server Error'; break;
				case 501: $status = 'Not Implemented'; break;
				case 502: $status = 'Bad Gateway'; break;
				case 503: $status = 'Service Unavailable'; break;
				case 504: $status = 'Gateway Timeout'; break;
				case 505: $status = 'HTTP Version Not Supported'; break;

				default: throw new \System\Base\ArgumentOutOfRangeException("Status is set to an invalid status code"); break;
			}

			$this->addHeader("HTTP/1.1 {$statuscode} {$status}");

			self::$statusCode = $statuscode;
			self::$status = $status;
		}
	}
?>