<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 *
	 *
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Button Control
	 *
	 * @property string $text Specifies button text
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class Label extends WebControlBase
	{
		/**
		 * specifies button text
		 * @var string
		 */
		protected $text						= '';


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

			$this->text = $text;

			// event handling
			/*
			$this->events->add(new \System\Web\Events\InputPostEvent());

			$onPostMethod = 'on' . ucwords( $this->controlId ) . 'Click';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onPostMethod))
			{
				$this->events->registerEventHandler(new \System\Web\Events\InputPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onPostMethod));
			}

			$onAjaxPostMethod = 'on' . ucwords( $this->controlId ) . 'AjaxClick';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onAjaxPostMethod))
			{
				$this->ajaxPostBack = true;
				$this->events->registerEventHandler(new \System\Web\Events\InputAjaxPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onAjaxPostMethod));
			}
			*/
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
			else {
				parent::__set($field,$value);
			}
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
			$span = $this->createDomObject('label');
			$span->nodeValue = $this->text;

			if( !$this->visible )
			{
				$span->setAttribute( 'style', 'display:none;' );
			}

			return $span;
		}
	}
?>