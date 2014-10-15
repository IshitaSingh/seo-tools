<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a TextBox Control
	 *
	 * @property bool $mask Specifies whether to characters will be masked
	 * @property int $maxLength Specifies Max Length of value when defined
	 * @property bool $disableAutoComplete Specifies whether to disable the browsers auto complete feature
	 * @property bool $disableEnterKey Specifies whether to disable the enter key
	 * @property string $placeholder Specifies the text for the placeholder attribute
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class TextBox extends InputBase
	{
		/**
		 * Specifies the size of a textbox, default is 30
		 * @var int
		 */
		protected $size						= 30;

		/**
		 * Specifies whether to characters will be masked, default is false
		 * @var bool
		 */
		protected $mask						= false;

		/**
		 * Max Length of value when set to non zero, default is 0
		 * @var int
		 */
		protected $maxLength				= 0;

		/**
		 * Specifies whether to disable the browsers auto complete feature, default is false
		 * @var bool
		 */
		protected $disableAutoComplete		= false;

		/**
		 * Specifies whether to disable the enter key, default is false
		 * @var bool
		 */
		protected $disableEnterKey			= false;

		/**
		 * Specifies the text for the placeholder attribute
		 * @var string
		 */
		protected $placeholder				= '';


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
		public function __construct( $controlId, $default = null )
		{
			trigger_error("TextBox is deprecated, use Text instead", E_USER_DEPRECATED);
			parent::__construct( $controlId, $default );
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'size' ) {
				return $this->size;
			}
			elseif( $field === 'mask' ) {
				return $this->mask;
			}
			elseif( $field === 'watermark' ) {
				return $this->placeholder;
			}
			elseif( $field === 'maxLength' ) {
				return $this->maxLength;
			}
			elseif( $field === 'disableAutoComplete' ) {
				return $this->disableAutoComplete;
			}
			elseif( $field === 'disableEnterKey' ) {
				return $this->disableEnterKey;
			}
			elseif( $field === 'placeholder' ) {
				return $this->placeholder;
			}
			else {
				return parent::__get( $field );
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
			if( $field === 'size' ) {
				$this->size = (int)$value;
			}
			elseif( $field === 'watermark' ) {
				trigger_error("Text::watermark is deprecated, use Text::placeholder instead", E_USER_DEPRECATED);
				$this->placeholder = (string)$value;
			}
			elseif( $field === 'mask' ) {
				$this->mask = (bool)$value;
			}
			elseif( $field === 'maxLength' ) {
				$this->maxLength = (int)$value;
			}
			elseif( $field === 'disableAutoComplete' ) {
				$this->disableAutoComplete = (bool)$value;
			}
			elseif( $field === 'disableEnterKey' ) {
				$this->disableEnterKey = (bool)$value;
			}
			elseif( $field === 'placeholder' ) {
				$this->placeholder = (string)$value;
			}
			elseif( $field === 'watermark' ) {
				trigger_error('TextBox::watermark is deprecated, use TextBox::placeholder instead', E_USER_DEPRECATED);
			}
			elseif( $field === 'multiline' ) {
				trigger_error('TextBox::multiline is deprecated, use TextArea instead', E_USER_DEPRECATED);
			}
			else {
				parent::__set($field,$value);
			}
		}


		/**
		 * update DataSet with Control data
		 *
		 * @param  DataSet $ds DataSet to fill
		 * @return void
		 */
		public function fillDataSet(\System\DB\DataSet &$ds)
		{
			if( isset( $ds[$this->dataField] ))
			{
				$ds[$this->dataField] = $this->value;
			}
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			trigger_error("TextBox is deprecated, use Text", E_USER_DEPRECATED);
			$input = $this->getInputDomObject();
			$input->setAttribute( 'size', $this->size );
			$input->setAttribute( 'class', ' textbox' );

			if(!is_null($this->value) && !$this->mask)
			{
				$input->setAttribute( 'value', $this->value );
			}

			if( $this->ajaxPostBack )
			{
				$input->setAttribute( 'onkeyup', 'if(Rum.isReady(\''.$this->getHTMLControlId().'__err\')){' . 'Rum.evalAsync(\'' . $this->ajaxCallback . '\',\'' . $this->getHTMLControlId().'=\'+encodeURIComponent(this.value)+\'&'.$this->getRequestData().'\',\'POST\');}' );
			}

			if( $this->visible )
			{
				if( $this->mask )
				{
					$input->setAttribute( 'type', 'password' );
				}
				else
				{
					$input->setAttribute( 'type', 'text' );
				}
			}

			if( $this->maxLength )
			{
				$input->setAttribute( 'maxlength', (int)$this->maxLength );
			}

			if( $this->disableEnterKey )
			{
				$input->setAttribute( 'onkeydown', 'if(event.keyCode==13){return false;}' );
			}

			if( $this->disableAutoComplete )
			{
				$input->setAttribute( 'autocomplete', 'off' );
			}

			if( $this->placeholder )
			{
				$input->setAttribute( 'placeholder', $this->placeholder );
			}

			return $input;
		}
	}
?>