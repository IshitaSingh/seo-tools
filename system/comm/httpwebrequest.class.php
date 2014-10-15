<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Comm;


	/**
	 * Represents an HTTP request (supports SSL)
	 *
	 * @property string $method HTTP request method
	 * @property string $contentType content-type
	 * @property string $referer HTTP referer
	 * @property string $timeout timeout
	 * @property string $httpVersion HTTP version
	 * @property string $url URL to send to
	 * @property bool $keepAlive Specifies whether to keep connection alive
	 *
	 * @package			PHPRum
	 * @subpackage		Comm
	 * @author			Darnell Shinbine
	 */
	class HTTPWebRequest
	{
		/**
		 * specifies the HTTP version, Default is 1.1
		 * @var string
		 */
		private $httpVersion			= '1.1';

		/**
		 * timeout in seconds, Default is 30
		 * @var int
		 */
		private $timeout				= 30;

		/**
		 * HTTP request method, Default is POST
		 * @var string
		 */
		private $method				= 'POST';

		/**
		 * content-type, Default is application/x-www-form-urlencoded
		 * @var string
		 */
		private $contentType			= 'application/x-www-form-urlencoded';

		/**
		 * HTTP referer
		 * @var string
		 */
		private $referer				= '';

		/**
		 * URL to send data to
		 * @var string
		 */
		private $url					= '';

		/**
		 * Specifies whether to keep connection alive
		 * @var bool
		 */
		private $keepAlive				= false;

		/**
		 * headers to send
		 * @var array
		 */
		private $_requestHeaders		= array();

		/**
		 * data to send
		 * @var string
		 */
		private $_data					= '';


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'method' )
			{
				return $this->method;
			}
			elseif( $field === 'contentType' )
			{
				return $this->contentType;
			}
			elseif( $field === 'referer' )
			{
				return $this->referer;
			}
			elseif( $field === 'timeout' )
			{
				return $this->timeout;
			}
			elseif( $field === 'httpVersion' )
			{
				return $this->httpVersion;
			}
			elseif( $field === 'url' )
			{
				return $this->url;
			}
			elseif( $field === 'keepAlive' )
			{
				return $this->keepAlive;
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
			if( $field === 'method' )
			{
				if( strtolower( $value ) === 'get' ||
					strtolower( $value ) === 'put' ||
					strtolower( $value ) === 'post' ||
					strtolower( $value ) === 'delete' )
				{
					$this->method = (string)$value;
				}
				else
				{
					throw new \System\Base\TypeMismatchException(get_class($this)."::method must be a string of get put post or delete");
				}
			}
			elseif( $field === 'contentType' )
			{
				$this->contentType = (string)$value;
			}
			elseif( $field === 'referer' )
			{
				$this->referer = (string)$value;
			}
			elseif( $field === 'timeout' )
			{
				$this->timeout = (int)$value;
			}
			elseif( $field === 'httpVersion' )
			{
				$this->httpVersion = (string)$value;
			}
			elseif( $field === 'url' )
			{
				$this->url = (string)$value;
			}
			elseif( $field === 'keepAlive' )
			{
				$this->keepAlive = (bool)$value;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * add header to request
		 *
		 * @param  string		$header		header to set
		 * @return void
		 */
		function addHeader( $header )
		{
			if( is_string( $header ))
			{
				$this->_requestHeaders[] = (string) $header;
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::setRequestHeader() must be a string");
			}
		}


		/**
		 * set data from string or array
		 *
		 * @param  mixed		$data		data to set (string|array)
		 * @return void
		 */
		function setData( $data )
		{
			if( is_string( $data ))
			{
				$this->_data = (string) $data;
			}
			elseif( is_array( $data ))
			{
				$this->_data = '';
				foreach( $data as $key => $value )
				{
					if( $this->_data )
					{
						$this->_data .= '&';
					}
					$this->_data .= (string)$key . '=' . urlencode((string)$value);
				}
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("No overloaded HTTPWebRequest::setData() accepts ".gettype($data));
			}
		}


		/**
		 * sends request and closes Stream object
		 *
		 * @return void
		 */
		function sendRequest()
		{
			$stream = $this->getRequestStream();
			$stream->write( $this->getRequestString() );
			$stream->close();
		}


		/**
		 * sends request and creates HTTPWebResponse object before closing Stream object
		 *
		 * @return HTTPWebResponse				HTTPWebResponse object
		 */
		function getResponse()
		{
			$stream = $this->getRequestStream();
			$stream->write( $this->getRequestString() );

			$httpWebResponse = new HTTPWebResponse();
			$httpWebResponse->readFromStream( $stream );

			$stream->close();

			return $httpWebResponse;
		}


		/**
		 * returns a string for request
		 *
		 * @return string
		 */
		protected function getRequestString()
		{
			// get host, path and port from URL
			$url_info = parse_url( $this->url );
			$path = '';
			$host = '';
			if( $url_info )
			{
				$path = isset( $url_info['path'] )?$url_info['path']:'/';
				$host = isset( $url_info['host'] )?$url_info['host']:'';
			}
			else
			{
				throw new \System\Base\InvalidOperationException("HTTPWebRequest::url is not a valid URL {$this->url}");
			}

			// get data from QueryString
			if( !$this->_data )
			{
				$this->_data = isset( $url_info['query'] )?$url_info['query']:'';
			}

			// Build Request
			$request = '';
			if( strtoupper( $this->method ) === 'POST' )
			{
				$request = "POST $path HTTP/{$this->httpVersion}\r\n";
			}
			elseif( $this->_data )
			{
				$request = "GET $path?{$this->_data} HTTP/{$this->httpVersion}\r\n";
			}
			else
			{
				$request = "GET $path HTTP/{$this->httpVersion}\r\n";
			}

			$request .= "Host: $host\r\n";

			if( strtoupper( $this->method ) === 'POST' )
			{
				$request .= 'Content-type: ' . $this->contentType . "\r\n";
				$request .= 'Content-length: ' . strlen( $this->_data ) . "\r\n";
			}

			// set referrer
			if( $this->referer )
			{
				$request .= "Referer: $this->referer\r\n";
			}
			else
			{
				$request .= "Referer: " . __PROTOCOL__ . "://" . __HOST__ . $_SERVER['PHP_SELF'] . "\r\n";
			}

			// $request .= "User-Agent: " . $this->agent . "\r\n";

			foreach( $this->_requestHeaders as $header )
			{
				$request .= "$header\r\n";
			}

			if($this->keepAlive) {
				$request .= "Connection: keep-alive\r\n\r\n";
			}
			else {
				$request .= "Connection: Close\r\n\r\n";
			}

			if( strtoupper( $this->method ) === 'POST' )
			{
				$request .= $this->_data;
			}

			return $request;
		}


		/**
		 * returns a Stream object for request
		 *
		 * @return SocketStream
		 */
		protected function getRequestStream()
		{
			// get host, path and port from URL
			$url_info = parse_url( $this->url );
			$path = '';
			$host = '';
			if( $url_info )
			{
				$path = isset( $url_info['path'] )?$url_info['path']:'/';
				$host = isset( $url_info['host'] )?$url_info['host']:'';
			}
			else
			{
				throw new \System\Base\InvalidOperationException("HTTPWebRequest::url is not a valid URL {$this->url}");
			}

			// get protocol scheme
			if( isset( $url_info['scheme'] )?$url_info['scheme'] === 'https':false )
			{
				$port = isset($url_info['port'])?$url_info['port']:443; // set default https port
				$host = 'ssl://' . $host;
			}
			else
			{
				$port = isset($url_info['port'])?$url_info['port']:80;
			}

			// Create Stream object
			$stream = new \System\Utils\SocketStream();
			$stream->open( $host, $port, $this->timeout );
			return $stream;
		}
	}
?>