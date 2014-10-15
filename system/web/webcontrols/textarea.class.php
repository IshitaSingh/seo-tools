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
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class TextArea extends Text
	{
		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$textarea = $this->createDomObject( 'textarea' );
			$textarea->setAttribute( 'name', $this->getHTMLControlId() );
			$textarea->setAttribute( 'id', $this->getHTMLControlId() );
//			$textarea->setAttribute( 'class', ' textarea' );
			$textarea->setAttribute( 'title', $this->tooltip );
			$textarea->nodeValue = $this->value;

			if( $this->submitted && !$this->validate() )
			{
				$textarea->setAttribute( 'class', ' invalid' );
			}

			if( $this->autoPostBack )
			{
				$textarea->setAttribute( 'onchange', 'Rum.id(\''.$this->getParentByType( '\System\Web\WebControls\Form')->getHTMLControlId().'\').submit();' );
			}

			if( $this->ajaxPostBack )
			{
				$textarea->setAttribute( 'onchange', 'Rum.evalAsync(\'' . $this->ajaxCallback . '\',\'' . $this->getHTMLControlId().'=\'+encodeURIComponent(this.value)+\'&'.$this->getRequestData().'\',\'POST\','.\addslashes($this->ajaxStartHandler).','.\addslashes($this->ajaxCompletionHandler).');' );
				$textarea->setAttribute( 'onkeyup', 'if(Rum.isReady(\''.$this->getHTMLControlId().'__err\')){' . 'Rum.evalAsync(\'' . $this->ajaxCallback . '\',\''.$this->getHTMLControlId().'=\'+encodeURIComponent(this.value)+\'&'.$this->getRequestData().'\',\'POST\','.\addslashes($this->ajaxStartHandler).','.\addslashes($this->ajaxCompletionHandler).');}' );
			}

			if( $this->readonly )
			{
				$textarea->setAttribute( 'readonly', 'readonly' );
			}

			if( $this->disabled )
			{
				$textarea->setAttribute( 'disabled', 'disabled' );
			}

			if( !$this->visible )
			{
				$textarea->setAttribute( 'style', 'display: none;' );
			}

			if( $this->maxLength )
			{
				// KLUDGY: -2 is bug fix
				$textarea->setAttribute( 'onkeyup', 'if(this.value.length > '.(int)($this->maxLength-2).'){ alert(\'You have exceeded the maximum number of characters allowed\'); this.value = this.value.substring(0, '.(int)($this->maxLength-2).') }' );
			}

			if( $this->disableEnterKey )
			{
				$textarea->setAttribute( 'onkeydown', 'if(event.keyCode==13){return false;}' );
			}

			if( $this->disableAutoComplete )
			{
				$textarea->setAttribute( 'autocomplete', 'off' ); // not xhtml compliant
			}

			if( $this->placeholder )
			{
				$textarea->setAttribute( 'placeholder', $this->placeholder );
			}

			return $textarea;
		}
	}
?>