<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Button Control
	 *
	 * @property string $text Specifies button text
	 * @property string $src Specifies button image source
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class Button extends InputBase
	{
		/**
		 * specifies button text
		 * @var string
		 */
		protected $text						= '';

		/**
		 * specifies button image source
		 * @var string
		 */
		protected $src						= '';

		/**
		 * contains tmp args array
		 * @var array
		 */
		private $_args				= array();


		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 * @param  string   $text	   Button text
		 * @return void
		 */
		public function __construct( $controlId, $text = '' )
		{
			parent::__construct( $controlId );

			$this->text = $text?$text:$controlId;
//			$this->label = ''; // deprecated

			// event handling
			$this->events->add(new \System\Web\Events\InputPostEvent());

			$onPostMethod = 'on' . ucwords( $this->controlId ) . 'Click';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onPostMethod))
			{
				trigger_error("built in method {$onPostMethod} is deprecated, use on" . ucwords( $this->controlId ) . 'Post instead', E_USER_DEPRECATED);
				$this->events->registerEventHandler(new \System\Web\Events\InputPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onPostMethod));
			}

			$onAjaxPostMethod = 'on' . ucwords( $this->controlId ) . 'AjaxClick';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onAjaxPostMethod))
			{
				trigger_error("built in method {$onAjaxPostMethod} is deprecated, use on" . ucwords( $this->controlId ) . 'AjaxPost instead', E_USER_DEPRECATED);
				$this->ajaxPostBack = true;
				$this->events->registerEventHandler(new \System\Web\Events\InputAjaxPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onAjaxPostMethod));
			}
		}


		/**
		 * __get
		 *
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'text' ) {
				return $this->text;
			}
			elseif( $field === 'src' ) {
				return $this->src;
			}
			else {
				return parent::__get( $field );
			}
		}


		/**
		 * __set
		 *
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return mixed
		 * @ignore
		 */
		public function __set( $field, $value ) {
			if( $field === 'text' ) {
				$this->text = (string)$value;
			}
			elseif( $field === 'src' ) {
				$this->src = (string)$value;
			}
			else {
				parent::__set($field,$value);
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
			$this->_args = $args;
			ob_start();
		}


		/**
		 * renders form close tag
		 *
		 * @return void
		 */
		public function end()
		{
			$this->text = ob_get_clean();
			\System\Web\HTTPResponse::write( $this->getDomObject()->fetch( $this->_args ));
		}


		/**
		 * getDomObject
		 *
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$input = $this->createDomObject( 'button' );
			$input->setAttribute( 'name', $this->getHTMLControlId() );
			$input->setAttribute( 'id', $this->getHTMLControlId() );

			if( $this->autoFocus )
			{
				$input->setAttribute( 'autofocus', 'autofocus' );
			}

			if( $this->disabled )
			{
				$input->setAttribute( 'disabled', 'disabled' );
			}

			$input->nodeValue = $this->text;
//			$input->setAttribute( 'class', ' button' );

			if( $this->src )
			{
				$input->setAttribute( 'type', 'image' );
				$input->setAttribute( 'src', $this->src );
			}
			else
			{
				$input->setAttribute( 'type', 'submit' );
			}

			if( !$this->visible )
			{
				$input->setAttribute( 'style', 'display:none;' );
			}

			if( $this->readonly )
			{
				$input->setAttribute( 'disabled', 'disabled' );
			}

			$input->setAttribute('onchange', '');
			$input->setAttribute('onblur', '');
			$input->setAttribute('onkeyup', '');

			return $input;
		}


		/**
		 * onLoad
		 *
		 * called when control is loaded
		 *
		 * @return bool			true if successfull
		 */
		protected function onLoad()
		{
			parent::onLoad();

			// perform ajax request
			if( $this->ajaxPostBack )
			{
				$form = $this->getParentByType('\System\Web\WebControls\Form');
				if($form)
				{
					$this->attributes->add('onclick', 'return Rum.submit(Rum.id(\'' . $form->getHTMLControlId() . '\'));' );
				}
			}
		}


		/**
		 * onRequest
		 *
		 * process the HTTP request array
		 *
		 * @param  array		&$request	request data
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			if( !$this->disabled )
			{
				if( $this->readonly )
				{
					$this->readonly = false;
					$this->disabled = true;
				}

				if( isset( $request[$this->getHTMLControlId() . '__x'] ) &&
					isset( $request[$this->getHTMLControlId() . '__y'] ))
				{
					$request[$this->getHTMLControlId()] = $this->text;
					unset( $request[$this->getHTMLControlId() . '__x'] );
					unset( $request[$this->getHTMLControlId() . '__y'] );
				}
			}

			parent::onRequest( $request );
		}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("Rum.id('{$this->getHTMLControlId()}').value='$this->text';");
		}
	}
?>