<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Password Control
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
	class Password extends InputBase
	{
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
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'disableEnterKey' ) {
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
			if( $field === 'disableEnterKey' ) {
				$this->disableEnterKey = (bool)$value;
			}
			elseif( $field === 'placeholder' ) {
				$this->placeholder = (string)$value;
			}
			else {
				parent::__set($field,$value);
			}
		}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			return;
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$input = $this->getInputDomObject();
//			$input->setAttribute( 'class', ' password' );

			if( $this->ajaxPostBack )
			{
				$input->setAttribute( 'onkeyup', 'if(Rum.isReady(\''.$this->getHTMLControlId().'__err\')){' . 'Rum.evalAsync(\'' . $this->ajaxCallback . '\',\'' . $this->getHTMLControlId().'=\'+encodeURIComponent(this.value)+\'&'.$this->getRequestData().'\',\'POST\','.\addslashes($this->ajaxStartHandler).','.\addslashes($this->ajaxCompletionHandler).');}' );
			}

			if( $this->visible )
			{
				$input->setAttribute( 'type', 'password' );
			}

			if( $this->disableEnterKey )
			{
				$input->setAttribute( 'onkeydown', 'if(event.keyCode==13){return false;}' );
			}

			if( $this->placeholder )
			{
				$input->setAttribute( 'placeholder', $this->placeholder );
			}

			return $input;
		}
	}
?>