<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView Date
	 * 
	 * @property string $dateFormat Specifies date format
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class GridViewDate extends GridViewControlBase
	{
		/**
		 * specifies date format
		 * @var string
		 */
		protected $dateFormat				= 'Y-m-d';


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'dateFormat' ) {
				return $this->dateFormat;
			} else {
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
			if( $field === 'dateFormat' ) {
				$this->dateFormat = (string)$value;
			} else {
				parent::__set( $field, $value );
			}
		}


		/**
		 * get item text
		 *
		 * @return string
		 */
		public function fetchUpdateControl()
		{
			if($this->ajaxPostBack)
			{
				$uri = \Rum::config()->uri;
				$params = $this->getRequestData() . "&".$this->formatParameter($this->pkey)."='.\\rawurlencode(%{$this->pkey}%).'&{$this->parameter}=\'+encodeURIComponent(this.value)+\'";
				return "'<input {$this->getAttrs()} type=\"date\" value=\"'.(!in_array(%{$this->dataField}%, array('0000-00-00',NULL))?date('{$this->dateFormat}',strtotime(%{$this->dataField}%)):'').'\" onchange=\"Rum.evalAsync(\'{$uri}/\',\'".$this->escape($params)."\',\'POST\',".\addslashes($this->ajaxStartHandler).",".\addslashes($this->ajaxCompletionHandler).");\" />'";
			}
			else
			{
				return "'<input {$this->getAttrs()} type=\"date\" value=\"'.(!in_array(%{$this->dataField}%, array('0000-00-00',NULL))?date('{$this->dateFormat}',strtotime(%{$this->dataField}%)):'').'\"/>'";
			}
		}

		/**
		 * get footer text
		 *
		 * @return string
		 */
		public function fetchInsertControl()
		{
			return "'<input {$this->getAttrs()} type=\"date\" value=\"'.(!in_array('{$this->default}', array('0000-00-00',NULL))?date('{$this->dateFormat}',strtotime('{$this->default}')):'').'\"/>'";
		}
	}
?>