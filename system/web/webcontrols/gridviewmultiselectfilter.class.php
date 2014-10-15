<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView list filter
	 *
	 * @property string $textField Specifies name of value text in datasource
	 * @property string $valueField Specifies name of value field in datasource
	 * 
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	class GridViewMultiSelectFilter extends GridViewFilterBase
	{
		/**
		 * values
		 * @var array
		 */
		protected $values = array();

		/**
		 * specifies control tool tip
		 * @var string
		 */
		protected $tooltip					= 'Select one or more options';

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
		 * constructor
		 */
		public function __construct(array $values = array())
		{
			$this->setValues($values);
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
		 * handle load events
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			if(!$this->textField && !$this->valueField)
			{
				if($this->column instanceof GridViewDropDownList)
				{
					$this->textField = $this->column->textField;
					$this->valueField = $this->column->valueField;
				}
				else
				{
					$this->textField = $this->column->dataField;
					$this->valueField = $this->column->dataField;
				}
			}
		}


		/**
		 * set column
		 * @param array $values array of values
		 * @return void
		 */
		final public function setValues(array $values)
		{
			$this->values = $values;
		}


		/**
		 * process the HTTP request array
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		public function requestProcessor( array &$request )
		{
			$HTMLControlId = $this->getHTMLControlId();

			if(isset($request[$HTMLControlId . '__filter_value']))
			{
				$this->submitted = true;
				$this->value = $request[$HTMLControlId . '__filter_value'];
//				unset($request[$HTMLControlId . '__filter_value']);
			}
		}


		/**
		 * filter DataSet
		 *
		 * @param  DataSet	&$ds		DataSet
		 * @return void
		 */
		public function filterDataSet(\System\DB\DataSet &$ds)
		{
			if($this->value) {
				$ds->filter($this->column->dataField, '=', $this->value, true );
				if($this->column->gridView->canUpdateView) {
					$this->column->gridView->needsUpdating = true;
				}
			}
		}


		/**
		 * returns filter text node
		 * 
		 * @param  string $requestString a string containing request data
		 * @return DomObject
		 */
		public function getDomObject($requestString)
		{
			$HTMLControlId = $this->getHTMLControlId();
			$uri = \Rum::config()->uri;

			$select = new \System\XML\DomObject( 'select' );
			$select->setAttribute('name', "{$HTMLControlId}__filter_value");
			$select->setAttribute('title', $this->tooltip);
			$select->setAttribute('multiple', 'multiple');
//			$select->setAttribute('class', 'listfilter');
			$option = new \System\XML\DomObject( 'option' );
			$option->setAttribute('value', '');
			$option->nodeValue = '';
			$select->addChild($option);

			// parse values
			$values = array();
			foreach($this->values as $key=>$value)
			{
				if(is_array($value))
				{
					$values[$value[$this->textField]] = $value[$this->valueField];
				}
				else
				{
					$values[$key] = $value;
				}
			}

			foreach($values as $key=>$value)
			{
				$option = new \System\XML\DomObject( 'option' );
				$option->setAttribute('value', $value);
				$option->nodeValue = $key;

				if(is_array($this->value))
				{
					foreach($this->value as $var)
					{
						if(strtolower($var)==strtolower($value))
						{
							$option->setAttribute('selected', 'selected');
						}
					}
				}
				else
				{
					if(strtolower($this->value)==strtolower($value))
					{
						$option->setAttribute('selected', 'selected');
					}
				}

				$select->addChild($option);
			}

			if($this->ajaxPostBack)
			{
				$select->setAttribute( 'onchange', "Rum.evalAsync('{$uri}/', '{$requestString}&'+Rum.convertValuesFromListBox(this));" );
			}
			else
			{
				$select->setAttribute( 'onchange', "Rum.sendSync('{$uri}/', '{$requestString}&'+Rum.convertValuesFromListBox(this));" );
			}

			return $select;
		}
	}
?>