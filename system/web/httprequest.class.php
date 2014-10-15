<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web;


	/**
	 * Provides a way to access HTTP Request information from the client
	 *
	 * @property array $post HTTP POST request variables
	 * @property array $get HTTP GET request variables
	 * @property array $request HTTP GET/POST request variables
	 * @property array $cookies HTTP Cookies
	 *
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	class HTTPRequest implements \ArrayAccess, \Iterator
	{
		/**
		 * var to hold get vars
		 * @var array
		 */
		static public $get				= null;

		/**
		 * var to hold post vars
		 * @var array
		 */
		static public $post				= null;

		/**
		 * var to hold cookie vars
		 * @var array
		 */
		static public $cookie			= null;

		/**
		 * var to hold request vars
		 * @var array
		 */
		static public $request			= null;


		/**
		 * Constructor
		 *
		 * Read values from request, clean up variables, and merge get and post requests.
		 *
		 * @return  void
		 */
		public function __construct()
		{
			if(!self::$get) self::$get = $_GET;
			if(!self::$post) self::$post = $_POST;
			if(!self::$cookie) self::$cookie = $_COOKIE;
			if(!self::$request) self::$request = array_merge( $_GET, $_POST ); // post overrides get
		}


		/**
		 * gets object property
		 *
		 * gets object property
		 *
		 * @param  string	$field		key
		 * @return string
		 * @ignore
		 */
		public function & __get( $field )
		{
			if( $field === 'post' )
			{
				return self::$post;
			}
			elseif( $field === 'get' )
			{
				return self::$get;
			}
			elseif( $field === 'request' )
			{
				return self::$request;
			}
			elseif( $field === 'cookies' )
			{
				return self::$cookie;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function rewind()
		{
			return reset(self::$request);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function current()
		{
			return current(self::$request);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function key()
		{
			return key(self::$request);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function next()
		{
			return next(self::$request);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function valid()
		{
			return (bool) $this->current();
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetExists($index)
		{
			return isset( self::$request[$index] );
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetGet($index)
		{
			if( isset( self::$request[$index] ))
			{
				return self::$request[$index];
			}
			else
			{
				return null;
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetSet($index, $item)
		{
			$_POST[$index] = $item;
			$_GET[$index] = $item;
			self::$get[$index] = $item;
			self::$post[$index] = $item;
			self::$request[$index] = $item;
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetUnset($index)
		{
			if( isset( self::$request[$index] ))
			{
				unset( self::$request[$index] );
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * return the URL
		 *
		 * @return  string						URL
		 */
		public static function getQueryString() {
			return isset( $_SERVER['QUERY_STRING'] )?$_SERVER['QUERY_STRING']:'';
		}


		/**
		 * return the URI
		 *
		 * @return  string						URI
		 */
		public static function getRequestURI() {
			return isset( $_SERVER['REQUEST_URI'] )?$_SERVER['REQUEST_URI']:'';
		}


		/**
		 * return raw post data
		 *
		 * @return  string						URI
		 */
		public static function getRawPostData() {
			return file_get_contents('php://input');
		}


		/**
		 * return the request method
		 *
		 * @return  string						method
		 */
		public static function getRequestReferer() {
			return isset( $_SERVER['HTTP_REFERER'] )?$_SERVER['HTTP_REFERER']:'';
		}


		/**
		 * return the request method
		 *
		 * @return  string						method
		 */
		public static function getRequestMethod() {
			return isset( $_SERVER['REQUEST_METHOD'] )?strtoupper($_SERVER['REQUEST_METHOD']):'';
		}


		/**
		 * return request headers
		 *
		 * @return  array
		 */
		public static function getRequestHeaders() {
			if( function_exists( 'apache_request_headers' )) {
				return apache_request_headers();
			}
			else {
				throw new \System\Base\InvalidOperationException("HTTPRequest::getRequestHeaders() requires PHP running as an apache module");
			}
		}
	}
?>