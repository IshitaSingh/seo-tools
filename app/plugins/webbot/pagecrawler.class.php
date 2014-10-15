<?php
	/**
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	namespace Webbot;

	/**
	 * crawls individual pages and parses the response
	 *
     * @property-read string $url
     * @property-read string $httpStatus
     * @property-read string $headers
     * @property-read string $content
     * @property-read int $elapsed
     * 
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2011
	 * @version			1.0.0
	 * @since			4.6.0
	 * @package			PHPRum
	 * @subpackage		Webbot
	 */
	class PageCrawler {

		/**
		 * specifies url
		 * @var string
		 */
		private $url					= '';

		/**
		 * contains http status
		 * @var string
		 */
		private $httpStatus				= '';

		/**
		 * contains http headers
		 * @var string
		 */
		private $headers				= '';

		/**
		 * contains http content
		 * @var string
		 */
		private $content				= '';

		/**
		 * contains elapsed time
		 * @var int
		 */
		private $elapsed				= 0;


		/**
		 * get object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @access protected
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'url' ) {
				return $this->url;
			}
			elseif( $field === 'httpStatus' ) {
				return $this->httpStatus;
			}
			elseif( $field === 'headers' ) {
				return $this->headers;
			}
			elseif( $field === 'content' ) {
				return $this->content;
			}
			elseif( $field === 'elapsed' ) {
				return $this->elapsed;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * read HTTP response from request and store it in a variable
		 *
		 * @param  string		$url			url
		 *
		 * @return void
		 * @access public
		 */
		function crawl( $url ) {

			$timer = new \System\Utils\Timer(true);
			$this->url = $url;

			$httpWebRequest = new \System\Comm\HTTPWebRequest();
			$httpWebRequest->method = 'GET';
			$httpWebRequest->referer = $this->url;
			$httpWebRequest->url = $this->url;
			$httpWebResponse = $httpWebRequest->getResponse();

			// handle errors

			$this->httpStatus  = $httpWebResponse->httpStatus;
			$this->contentType = $httpWebResponse->contentType;
			$this->headers     = $httpWebResponse->headers;
			$this->content     = $httpWebResponse->content;
			$this->elapsed     = $timer->elapsed();
		}


		/**
		 * search result for a string pattern
		 *
		 * @param  string		$needle		string to find
		 *
		 * @return int						number of matches found
		 * @access public
		 */
		function search( $needle ) {
			return strpos( str_replace( "\n", '', str_replace( "\r", '', $this->response )), $needle );
		}


		/**
		 * search result for a hyperlink
		 *
		 * @param  string		$url		url to find
		 *
		 * @return bool						TRUE if text found
		 * @access public
		 */
		function searchLink( $url ) {

			if( $this->response ) {

				$url  = str_replace( '://www.', '://', $url ); // remove www
				$host = preg_replace( "`(http|ftp)+(s)?:(//)((\w|\.|\-|_)+)(/)?(\S+)?`i", "\\4", $url ); // extract host
				$path = str_replace( 'http://', '', str_replace( $host, '', $url )); // extract path

				// find pattern - support for https implemented but not tested
				$pattern = "^<a(.*?)href( *)=( *)[\\\"'](http|https)://(www.)?$host$path(.*?)[\\\"'](.*?)>(.+?)</a>^i";
				return preg_match( $pattern, str_replace( "\n", '', str_replace( "\r", '', $this->response )));
			}
			return false;
		}


		/**
		 * get all anchor tags on page
		 *
		 * @return StringDictionary		Dictionary of links
		 * @access public
		 */
		function getLinks()
		{
			if( $this->content )
			{
				preg_match_all( "/<a.*?href *= *[\\\"'](.*?)[\\\"'].*?>(.*?)<\\/a>/i", str_replace( "\r", ' ', str_replace( "\n", ' ', $this->content )), $urls );
				return $urls;
			}
			else
			{
				return array();
			}
		}
	}
?>