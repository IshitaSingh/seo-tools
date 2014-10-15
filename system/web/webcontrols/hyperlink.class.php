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
	 * @property string $text Specifies hyperlink text
	 * @property string $url Specifies hyperlink URL
	 * @property string $target Specifies hyperlink target
	 * @property string $src Specifies hyperlink image source
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class HyperLink extends InputBase
	{
		/**
		 * specifies hyperlink text
		 * @var string
		 */
		protected $text						= '';

		/**
		 * specifies hyperlink URL
		 * @var string
		 */
		protected $url						= '';

		/**
		 * specifies hyperlink target
		 * @var string
		 */
		protected $target					= '_self';

		/**
		 * specifies hyperlink image source
		 * @var string
		 */
		protected $src						= '';


		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 * @param  string   $text	   Button text
		 * @return void
		 */
		public function __construct( $controlId, $text = '', $url = '', $target = '_self' )
		{
			parent::__construct( $controlId );

			$this->text = $text;
			$this->url = $url;
			$this->target = $target;

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
			elseif( $field === 'url' ) {
				return $this->url;
			}
			elseif( $field === 'target' ) {
				return $this->target;
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
			elseif( $field === 'url' ) {
				$this->url = (string)$value;
			}
			elseif( $field === 'target' ) {
				$this->target = (string)$value;
			}
			elseif( $field === 'src' ) {
				$this->src = (string)$value;
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
			$a = $this->createDomObject('a');
//			$a->setAttribute( 'class', ' hyperlink' );
			$a->setAttribute('href', $this->url);
			$a->setAttribute('target', $this->target);

			if( !$this->visible )
			{
				$a->setAttribute( 'style', 'display:none;' );
			}

			if( $this->src )
			{
				$img = new \System\XML\DomObject('img');
				$img->src = $this->src;
				$img->alt = $this->text;

				$a->addChild($img);
			}
			else
			{
				$a->nodeValue = $this->text;
			}

			return $a;
		}
	}
?>