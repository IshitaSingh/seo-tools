<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Comm;


	/**
	 * Represents the response from a HTTPWebRequest message
	 *
	 * @property string $httpStatus response headers
	 * @property array $headers response headers
	 * @property string $contentType response headers
	 * @property string $content response headers
	 * 
	 * @package			PHPRum
	 * @subpackage		Comm
	 * @author			Darnell Shinbine
	 */
	class HTTPWebResponse
	{
		/**
		 * response headers
		 * @var array
		 */
		private $headers				= array();

		/**
		 * response content-type
		 * @var string
		 */
		private $contentType			= '';

		/**
		 * response content
		 * @var string
		 */
		private $content				= '';

		/**
		 * HTTP status
		 * @var string
		 */
		private $httpStatus				= '';


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'contentType' )
			{
				return $this->contentType;
			}
			elseif( $field === 'httpStatus' )
			{
				return $this->httpStatus;
			}
			elseif( $field === 'headers' )
			{
				return $this->headers;
			}
			elseif( $field === 'content' )
			{
				return $this->content;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * read data from Stream object
		 *
		 * @param  string		$header		header to set
		 * @return void
		 */
		public function readFromStream( \System\Utils\StreamBase &$stream )
		{
			// get HTTP status
			$this->httpStatus = $stream->readln();

			// handle Continue condition
			if( strstr( $this->httpStatus, "HTTP/1.1 100 Continue" ) !== false )
			{
				while( !$stream->eos() && ( $headers = $stream->readln() ))
				{
					if( str_replace( "\r", '', $headers ) === "\n" )
					{
						break;
					}
				}

				// get HTTP status
				$this->httpStatus = $stream->readln();
			}

			// get response headers
			while( $headers = $stream->readln() )
			{
				if( str_replace( "\r", '', $headers ) === "\n" )
				{
					break;
				}

				$data = explode( ':', str_replace( "\r", '', str_replace( "\n", '', $headers )), 2);

				if( isset( $data[1] ))
				{
					$this->headers[$data[0]] = $data[1];
				}
			}

			// get contentType
			foreach( $this->headers as $key => $val )
			{
				if( strtolower( $key ) === 'content-type' )
				{
					$this->contentType = $val;
				}
			}

			// get content
			while( $content = $stream->readln() )
			{
				$this->content .= $content;
			}
		}
	}
?>