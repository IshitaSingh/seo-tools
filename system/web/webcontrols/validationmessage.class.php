<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Provides base functionality for ValidationMessage Controls
	 *
	 * @property InputBase $controlToValidate Name id the control to validate
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class ValidationMessage extends WebControlBase
	{
		/**
		 * Name of the data field in the datasource
		 * @var InputBase
		 */
		protected $controlToValidate				= '';

		/**
		 * Specifies the error message
		 * @var string
		 */
		protected $errMsg							= '';


		/**
		 * Constructor
		 *
		 * The constructor sets attributes based on session data, triggering events, and is responcible for
		 * formatting the proper request value and garbage handling
		 *
		 * @param  string   $controlId	  Control Id
		 * @param  string   $default		Default value
		 * @return void
		 */
		public function __construct( $controlId, InputBase &$controlToValidate = null)
		{
			parent::__construct( $controlId );

			$this->controlToValidate = $controlToValidate;
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'controlToValidate' ) {
				return $this->controlToValidate;
			}
			else {
				return parent::__get($field);
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return mixed
		 * @ignore
		 */
		public function __set( $field, $value ) {
			if( $field === 'controlToValidate' ) {
				if($value instanceof InputBase) {
					$this->controlToValidate = $value;
				}
				else {
					throw new \System\Base\BadMemberCallException("controlToValidate must be type InputBase");
				}
			}
			else {
				parent::__set( $field, $value );
			}
		}


		/**
		 * Event called when request is processed
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			if($this->controlToValidate->submitted) {
				if(!$this->controlToValidate->validate($err)) {
					$this->errMsg = $err;
				}
				$this->needsUpdating = true;
			}
		}


		/**
		 * renders form open tag
		 *
		 * @param   array	$args	attribute parameters
		 * @return void
		 */
		public function begin( $args = array() )
		{
			$span = $this->createDomObject( 'span' );
			$span->setAttribute('id', $this->getHTMLControlId());
			if(!$this->errMsg) {
				$span->setAttribute('style', 'display:none;');
			}

			\System\Web\HTTPResponse::write(str_replace( '</span>', '', $span->fetch($args)));
		}


		/**
		 * renders form close tag
		 *
		 * @return void
		 */
		public function end()
		{
			\System\Web\HTTPResponse::write( '</span>' );
		}


		/**
		 * returns an input DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$span = $this->createDomObject( 'span' );
			$span->setAttribute('id', $this->getHTMLControlId());
			if($this->errMsg) {
				$span->nodeValue = $this->errMsg;
			}
			else {
				$span->setAttribute('style', 'display:none;');
			}
			return $span;
		}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			if($this->errMsg) {
				// TODO: move inside Rum.js
				$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("if(Rum.id('{$this->getHTMLControlId()}')){Rum.id('{$this->getHTMLControlId()}').style.display='inline';}");
			}
			else {
				// TODO: move inside Rum.js
				$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("if(Rum.id('{$this->getHTMLControlId()}')){Rum.id('{$this->getHTMLControlId()}').style.display='none';}");
			}

			// TODO: move inside Rum.js
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("if(Rum.id('{$this->getHTMLControlId()}')){Rum.id('{$this->getHTMLControlId()}').innerHTML='{$this->errMsg}';}");
		}
	}
?>