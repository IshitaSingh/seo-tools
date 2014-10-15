<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView DropDownList
	 * 
	 * @property string $textField Specifies name of value text in datasource
	 * @property string $valueField Specifies name of value field in datasource
	 * @property ListItemCollection $items Collection of list items
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class GridViewDropDownList extends GridViewControlBase
	{
		/**
		 * collection of list items
		 * @var ListItemCollection
		 */
		protected $items;

		/**
		 * Specifies name of text field in datasource
		 * @var string
		 */
		protected $textField			= '';

		/**
		 * Specifies name of value field in datasource
		 * @var string
		 */
		protected $valueField			= '';


		/**
		 * @param  string		$dataField			field name
		 * @param  string		$pkey				primary key
		 * @param  array		$values				list values
		 * @param  string		$value				value of Control
		 * @param  string		$parameter			parameter
		 * @param  string		$headerText			header text
		 * @param  string		$footerText			footer text
		 * @param  string		$className			css class name
		 * @return void
		 */
		public function __construct( $dataField, $pkey, array $values, $parameter='', $headerText='', $footerText='', $className='' )
		{
			parent::__construct( $dataField, $pkey, $parameter, $headerText, $footerText, $className );

			$this->items = new ListItemCollection($values);
			$this->textField = $dataField;
			$this->valueField = $dataField;
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'textField' ) {
				return $this->textField;
			}
			elseif( $field === 'valueField' ) {
				return $this->valueField;
			}
			elseif( $field === 'items' ) {
				return $this->items;
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
			if( $field === 'textField' ) {
				$this->textField = (string)$value;
			}
			elseif( $field === 'valueField' ) {
				$this->valueField = (string)$value;
			}
			else {
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

				$html = "'<select {$this->getAttrs()} onchange=\"Rum.evalAsync(\'{$uri}/\',\'".$this->escape($params)."\',\'POST\',".\addslashes($this->ajaxStartHandler).",".\addslashes($this->ajaxCompletionHandler).");\">";
				foreach($this->items as $key=>$value)
				{
					if(is_array($value)) {
						$key = $value[$this->textField];
						$value = $value[$this->valueField];
					}
					$value = \Rum::escape($value, ENT_QUOTES);
					$key = \Rum::escape($key, ENT_QUOTES);

					$html .= "<option value=\"{$value}\" '.(%{$this->dataField}%=='{$value}'?'selected=\"selected\"':'').'>{$key}</option>";
				}
				$html .= '</select>\'';

				return $html;
			}
			else
			{
				$html = "'<select {$this->getAttrs()}>";
				foreach($this->items as $key=>$value)
				{
					if(is_array($value)) {
						$key = $value[$this->textField];
						$value = $value[$this->valueField];
					}
					$value = \Rum::escape($value, ENT_QUOTES);
					$key = \Rum::escape($key, ENT_QUOTES);

					$html .= "<option value=\"{$value}\" '.(%{$this->dataField}%=='{$value}'?'selected=\"selected\"':'').'>{$key}</option>";
				}
				$html .= '</select>\'';

				return $html;
			}
		}

		/**
		 * get footer text
		 *
		 * @return string
		 */
		public function fetchInsertControl()
		{
			$html = "'<select {$this->getAttrs()}>";
			foreach($this->items as $key=>$value)
			{
				if(is_array($value)) {
					$key = $value[$this->textField];
					$value = $value[$this->valueField];
				}
				$value = \Rum::escape($value, ENT_QUOTES);
				$key = \Rum::escape($key, ENT_QUOTES);

				if($value==$this->default) {
					$html .= "<option selected=\"selected\" value=\"{$value}\">{$key}</option>";
				}
				else {
					$html .= "<option value=\"{$value}\">{$key}</option>";
				}
			}
			$html .= '</select>\'';

			return $html;
		}
	}
?>