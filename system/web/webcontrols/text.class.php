<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Text Control
	 *
	 * @property int $maxLength Specifies Max Length of value when defined
	 * @property bool $disableEnterKey Specifies whether to disable the enter key
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class Text extends InputBase
	{
		/**
		 * type
		 * @ignore
		 */
		const type = 'text';

		/**
		 * Max Length of value when set to non zero, default is 0
		 * @var int
		 */
		protected $maxLength				= 0;

		/**
		 * Specifies whether to disable the enter key, default is false
		 * @var bool
		 */
		protected $disableEnterKey			= false;

		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'maxLength' ) {
				return $this->maxLength;
			}
			elseif( $field === 'watermark' ) {
				trigger_error("Text::watermark is deprecated, use Text::placeholder instead", E_USER_DEPRECATED);
				return $this->placeholder;
			}
			elseif( $field === 'disableEnterKey' ) {
				return $this->disableEnterKey;
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
			if( $field === 'maxLength' ) {
				$this->maxLength = (int)$value;
			}
			elseif( $field === 'watermark' ) {
				trigger_error("Text::watermark is deprecated, use Text::placeholder instead", E_USER_DEPRECATED);
				$this->placeholder = (string)$value;
			}
			elseif( $field === 'disableEnterKey' ) {
				$this->disableEnterKey = (bool)$value;
			}
			else {
				parent::__set($field,$value);
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
//			$input->setAttribute( 'class', ' text' );

			if(!is_null($this->value))
			{
				$input->setAttribute( 'value', $this->value );
			}

			if( $this->ajaxPostBack )
			{
				$input->setAttribute( 'onkeyup', 'if(Rum.isReady(\''.$this->getHTMLControlId().'__err\')){' . 'Rum.evalAsync(\'' . $this->ajaxCallback . '\',\'' . $this->getHTMLControlId().'=\'+encodeURIComponent(this.value)+\'&'.$this->getRequestData().'\',\'POST\','.\addslashes($this->ajaxStartHandler).','.\addslashes($this->ajaxCompletionHandler).');}' );
			}

			if( $this->visible )
			{
				$input->setAttribute( 'type', self::type );
			}

			if( $this->maxLength )
			{
				$input->setAttribute( 'maxlength', (int)$this->maxLength );
			}

			if( $this->disableEnterKey )
			{
				$input->setAttribute( 'onkeydown', 'if(event.keyCode==13){return false;}' );
			}

			return $input;
		}
	}
?>