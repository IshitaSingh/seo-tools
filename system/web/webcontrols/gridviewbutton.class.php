<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView button
	 *
	 * @property  string	$confirmation	confirmation message
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class GridViewButton extends GridViewControlBase
	{
		/**
		 * confirmation message
		 * @var string
		 */
		protected $confirmation				= '';

		/**
		 * item button name
		 * @var string
		 */
		private $itemButtonName				= '';

		/**
		 * footer button name
		 * @var string
		 */
		private $footerButtonName			= '';


		/**
		 * @param  string		$dataField			field name
		 * @param  string		$itemButtonName		name of item button
		 * @param  string		$prameter			item parameter
		 * @param  string		$confirmation		confirmation text on button click
		 * @param  string		$headerText			header text
		 * @param  string		$footerText			footer text
		 * @param  string		$className			column CSS class name
		 * @param  string		$footerButtonName	name of footer button
		 * @return void
		 */
		public function __construct( $dataField, $itemButtonName='', $parameter = '', $confirmation = '', $headerText='', $footerText='', $className='', $footerButtonName='' )
		{
			parent::__construct($dataField, $dataField, $parameter, $headerText, $footerText, $className);

			$this->itemButtonName = $itemButtonName?$itemButtonName:$dataField;
			$this->footerButtonName = $footerButtonName;
			$this->confirmation = $confirmation;

			$clickEvent='on'.ucwords(str_replace(" ","_",$this->parameter)).'Click';
			$AjaxClickEvent='on'.ucwords(str_replace(" ","_",$this->parameter)).'AjaxClick';

			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $clickEvent))
			{
				$this->events->registerEventHandler(new \System\Web\Events\GridViewColumnPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $clickEvent));
			}
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $AjaxClickEvent))
			{
				$this->ajaxPostBack = true;
				$this->events->registerEventHandler(new \System\Web\Events\GridViewColumnAjaxPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $AjaxClickEvent));
			}
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'confirmation' ) {
				return $this->confirmation;
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
			if( $field === 'confirmation' ) {
				$this->confirmation = (string)$value;
			}
			else {
				parent::__set( $field, $value );
			}
		}


		/**
		 * handle request events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		public function onRequest( &$request )
		{
			if( isset( $request[$this->dataField] ))
			{
				unset( $request[$this->dataField] );
			}
			parent::onRequest($request);
		}


		/**
		 * get item text
		 *
		 * @return string
		 */
		public function fetchUpdateControl()
		{
			if($this->footerButtonName) {
				$this->gridView->showInsertRow = true;
			}

			$params = $this->getRequestData() . "&{$this->dataField}='.\\rawurlencode(%{$this->dataField}%).'&{$this->parameter}={$this->itemButtonName}";
			$uri = \Rum::config()->uri;

			if( $this->ajaxPostBack )
			{
				return "'<input {$this->getAttrs()} type=\"button\" title=\"{$this->itemButtonName}\" value=\"{$this->itemButtonName}\" onclick=\"".($this->confirmation?'if(!confirm(\\\''.\addslashes(\addslashes($this->escape($this->confirmation)))."\\')){return false;}":"")."Rum.evalAsync(\'{$uri}/\',\'".$this->escape($params)."&\',\'POST\',".\addslashes($this->ajaxStartHandler).",".\addslashes($this->ajaxCompletionHandler).");\" />'";
			}
			else
			{
				return "'<input {$this->getAttrs()} type=\"button\" title=\"{$this->itemButtonName}\" value=\"{$this->itemButtonName}\" onclick=\"".($this->confirmation?'if(!confirm(\\\''.\addslashes(\addslashes($this->escape($this->confirmation)))."\\')){return false;}":"")."Rum.sendSync(\'{$uri}/\',\'".$this->escape($params)."&\'+Rum.getParams(this.parentNode.parentNode),\'POST\');\" />'";
			}
		}


		/**
		 * get footer text
		 *
		 * @return string
		 */
		public function fetchInsertControl()
		{
			$params = $this->getRequestData() . "&{$this->parameter}={$this->footerButtonName}";
			$uri = \Rum::config()->uri;

			if( $this->ajaxPostBack )
			{
				return "'<input {$this->getAttrs()} type=\"button\" title=\"{$this->footerButtonName}\" value=\"{$this->footerButtonName}\" onclick=\"Rum.evalAsync(\'{$uri}/\',\'".$this->escape($params)."&\'+Rum.getParams(this.parentNode.parentNode),\'POST\');\" />'";
			}
			else
			{
				return "'<input {$this->getAttrs()} type=\"button\" title=\"{$this->footerButtonName}\" value=\"{$this->footerButtonName}\" onclick=\"Rum.sendSync(\'{$uri}/\',\'".$this->escape($params)."&\'+Rum.getParams(this.parentNode.parentNode),\'POST\');\" />'";
			}
		}
	}
?>