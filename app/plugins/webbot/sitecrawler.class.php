<?php
	/**
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	namespace Webbot;

	/**
	 * crawls entire websites and parses each page
     *
     * @property-read string $url
     * @property-read array $pages
     * @property-read int $elapsed
	 * 
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2011
	 * @version			1.0.0
	 * @since			4.6.0
	 * @package			PHPRum
	 * @subpackage		Webbot
	 */
	class SiteCrawler {

		/**
		 * specifies url
		 * @var string
		 */
		private $url					= '';

		/**
		 * contains array of crawled pages
		 * @var array
		 */
		private $pages					= array();

		/**
		 * contains elapsed time
		 * @var int
		 */
		private $elapsed				= 0;

		/**
		 * specifies max no. of crawls
		 * @var int
		 */
		private $max					= 100;

		/**
		 * specifies max depth
		 * @var int
		 */
		private $maxDepth				= 3;

		/**
		 * contains crawl count
		 * @var int
		 */
		private $_crawlCount			= 0;


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
			elseif( $field === 'pages' ) {
				return $this->pages;
			}
			elseif( $field === 'elapsed' ) {
				return $this->elapsed;
			}
			else {
				throw new \System\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * crawl website and build a list of pages
		 *
		 * @param  string $url
		 * @return void
		 */
		public function crawl( $url ) {
			$timer = new \System\Utils\Timer(true);
			$this->icrawl( $this->url = $url );
			$this->elapsed = $timer->elapsed();
		}


		/**
		 * internal recursive page crawl
		 *
		 * @param  string $url
		 * @return void
		 */
		private function icrawl( $url, $depth = 0 ) {

			// only crawl each page once
			if( !defined( '__URL_' . $url )) {
				define( '__URL_' . $url, TRUE );

				if( ++$this->_crawlCount > $this->max and $depth <= $this->maxDepth ) return;

				// crawl page
				$pagecrawler = new PageCrawler();
				$pagecrawler->crawl( $url );

				// check content type
				if( strpos( strtolower( $pagecrawler->contentType ), 'html' ) !== false ) {

					// save webpage info
					$this->pages[] = array('url'=>$pagecrawler->url,
										'http_status'=>$pagecrawler->httpStatus,
										'headers'=>$pagecrawler->headers,
										'content'=>$pagecrawler->content,
										'response_time'=>$pagecrawler->elapsed,
						);

					// check http status
					if( strpos( strtolower( $pagecrawler->httpStatus ), '200 ok' ) !== false ) {

						// get links
						$links = $pagecrawler->getLinks();

						// crawl all subpages
						foreach( $links[1] as $link ) {
							if( $this->isValidURL( $link )) {
								$this->icrawl( $this->parseURL( $link ), $depth+1 );
							}
						}
					}
					elseif( strpos( strtolower( $pagecrawler->httpStatus ), '301 moved permanently' ) !== false ) {
						// moved
						$this->icrawl( $this->parseURL( $pagecrawler->headers["Location"] ));
					}
					else {
						// not found
					}
				}
				else {
					// not a valid HTML doc
				}
			}
		}


		/**
		 * check if URL is valid
		 *
		 * @param  string $url
		 * @return bool
		 */
		private function isValidURL( $url ) {
			if(( strpos( $url, 'http://' ) !== false || strpos( $url, 'https://' ) !== false ) && strpos( str_replace('www.', '', $url), str_replace('www.', '', $this->url)) === false ) {
				// link is external
				return false;
			}
			if( strpos( $url, 'mailto:' ) !== false ) {
				// link contains mailto:
				return false;
			}
			if( strpos( $url, 'javascript:' ) !== false ) {
				// link contains mailto:
				return false;
			}
			if( strpos( $url, 'tel:' ) !== false ) {
				// link contains tel:
				return false;
			}
			if( strpos( $url, '.pdf' ) !== false ) {
				// link is a PDF
				return false;
			}
			if( strpos( $url, '.exe' ) !== false ) {
				// link is an executable
				return false;
			}
			if( strpos( $url, '.txt' ) !== false ) {
				// link is a text doc
				return false;
			}
			if( strpos( $url, '.swf' ) !== false ) {
				// link is a flash movie
				return false;
			}
			if( strpos( $url, '.doc' ) !== false ) {
				// link is a doc
				return false;
			}
			if( strpos( $url, '.xls' ) !== false ) {
				// link is a xls file
				return false;
			}
			if( strpos( $url, '.xml' ) !== false ) {
				// link is an xml file
				return false;
			}
			return true;
		}


		/**
		 * parse URL
		 * @param  string $url
		 * @return string
		 */
		private function parseURL( $uri ) {
			if( strpos( $uri, 'http://' ) === false && strpos( $uri, 'https://' ) === false ) {
				// link is uri
				if( strpos( $uri, '/' ) === 0 ) {
					$url = $this->url . $uri;
				}
				else {
					$url = $this->url . '/' . $uri;
				}
			}
			else {
				// link is url
				$url = $uri;
			}

			return trim( substr( $url, 0, 8 ) . str_replace( '//', '/', substr( $url, 8 )));
		}
	}
?>