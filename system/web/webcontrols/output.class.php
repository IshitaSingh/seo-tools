<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Search Control
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class Output extends DataFieldControlBase
	{
		/**
		 * getDomObject
		 *
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$output = $this->createDomObject( 'span' );
			$output->setAttribute( 'name', $this->getHTMLControlId() );
			$output->setAttribute( 'id', $this->getHTMLControlId() );
			$output->nodeValue = $this->value;

			return $output;
		}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("if(Rum.id('{$this->getHTMLControlId()}').firstChild){Rum.id('{$this->getHTMLControlId()}').removeChild(Rum.id('{$this->getHTMLControlId()}').firstChild)};");
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("Rum.id('{$this->getHTMLControlId()}').appendChild(document.createTextNode('".addslashes($this->value)."'));");
		}
	}
?>