<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a FileBrowser Control
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class FileBrowser extends InputBase
	{
		/**
		 * Constructor
		 *
		 * @param  string   $controlId	  Control Id
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct( $controlId, '' );
			trigger_error("FileBrowser is deprecated, use File instead", E_USER_DEPRECATED);
		}


		/**
		 * returns information on the uploaded file
		 *
		 * @return array				array of file details
		 */
		public function getFileInfo()
		{
			if( isset( $_FILES[$this->getHTMLControlId()] ))
			{
				return $_FILES[$this->getHTMLControlId()];
			}
			else
			{
				throw new \System\Base\InvalidOperationException("FileBrowser::getFileInfo() called on null file");
			}
		}


		/**
		 * return raw file data
		 *
		 * @return string				raw file data
		 */
		public function getFileRawData()
		{
			$info = $this->getFileInfo();

			if( $info['error'] === UPLOAD_ERR_OK )
			{
				if( $info['size'] > 0 )
				{
					$fp = fopen( $info['tmp_name'], 'rb' );
					if( $fp )
					{
						$data = fread( $fp, filesize( $info['tmp_name'] ));
						fclose( $fp );
						return $data;
					}
					else
					{
						throw new \System\Base\InvalidOperationException("could not open file for reading");
					}
				}
				else
				{
					return '';
				}
			}
			else
			{
				if( $info['error'] === UPLOAD_ERR_INI_SIZE )
				{
					throw new \System\Base\InvalidOperationException("the uploaded file exceeds the upload_max_filesize directive");
				}
				elseif( $info['error'] === UPLOAD_ERR_FORM_SIZE )
				{
					throw new \System\Base\InvalidOperationException("the uploaded file exceeds the MAX_FILE_SIZE directive");
				}
				elseif( $info['error'] === UPLOAD_ERR_PARTIAL )
				{
					throw new \System\Base\InvalidOperationException("the uploaded file was only partially uploaded");
				}
				elseif( $info['error'] === UPLOAD_ERR_NO_FILE )
				{
					throw new \System\Base\InvalidOperationException("no file was uploaded");
				}
				elseif( $info['error'] === UPLOAD_ERR_NO_TMP_DIR )
				{
					throw new \System\Base\FileLoadException("missing temporary folder");
				}
				elseif( $info['error'] === UPLOAD_ERR_CANT_WRITE )
				{
					throw new \System\Base\InvalidOperationException("failed to write file to disk");
				}
				elseif( $info['error'] === UPLOAD_ERR_EXTENSION )
				{
					throw new \System\Base\InvalidOperationException("file upload stopped by extension");
				}
				else
				{
					throw new \System\Base\InvalidOperationException("unknown file upload failure");
				}
			}
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$input = $this->getInputDomObject();
			$input->setAttribute( 'type', 'file' );
			$input->setAttribute( 'class', ' filebrowser' );

			return $input;
		}


		/**
		 * called when control is loaded
		 *
		 * @return bool			true if successfull
		 */
		protected function onLoad()
		{
			parent::onLoad();

			$form = $this->getParentByType( '\System\Web\WebControls\Form' );
			if( $form )
			{
				$form->encodeType = 'multipart/form-data';
			}
		}


		/**
		 * process the HTTP request array
		 *
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			if( !$this->disabled )
			{
				if( isset( $_FILES[$this->getHTMLControlId()] ))
				{
					$this->submitted = true;
					$this->value = $_FILES[$this->getHTMLControlId()]['tmp_name'];
				}
			}

			parent::onRequest( $request );
		}
	}
?>