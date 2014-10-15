<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView date range filter
	 * 
	 * @property string $dateFormat Specifies date format
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	class GridViewDateRangeFilter extends GridViewRangeFilterBase
	{
		/**
		 * specifies control tool tip
		 * @var string
		 */
		protected $tooltip					= 'Select a date range';


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
		 * process the HTTP request array
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		public function requestProcessor( array &$request )
		{
			$HTMLControlId = $this->getHTMLControlId();

			if(isset($request[$HTMLControlId . '__filter_startdate']))
			{
				$this->submitted = true;
				$this->minValue = $request[$HTMLControlId . '__filter_startdate'];
//				unset($request[$HTMLControlId . '__filter_startdate']);
			}
			if(isset($request[$HTMLControlId . '__filter_enddate']))
			{
				$this->submitted = true;
				$this->maxValue = $request[$HTMLControlId . '__filter_enddate'];
//				unset($request[$HTMLControlId . '__filter_enddate']);
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
			if($this->minValue) {
				$ds->filter($this->column->dataField, '>=', date('Y-m-d', strtotime($this->minValue)));
				if($this->column->gridView->canUpdateView) {
					$this->column->gridView->needsUpdating = true;
				}
			}
			if($this->maxValue) {
				$ds->filter($this->column->dataField, '<=', date('Y-m-d', strtotime($this->maxValue)));
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
			$uri = \System\Web\WebApplicationBase::getInstance()->config->uri;

			$span = new \System\XML\DomObject('span');

			$date_start = new \System\XML\DomObject('input');
			$date_start->setAttribute('type', 'date');
			$date_start->setAttribute('name', "{$HTMLControlId}__filter_startdate");
			$date_start->setAttribute('value', !in_array($this->minValue, array('0000-00-00',NULL))?date($this->dateFormat, strtotime($this->minValue)):'');
			$date_start->setAttribute('title', $this->tooltip);
//			$date_start->setAttribute('class', 'daterangefilter');

			$date_end = new \System\XML\DomObject('input');
			$date_end->setAttribute('type', 'date');
			$date_end->setAttribute('name', "{$HTMLControlId}__filter_enddate");
			$date_end->setAttribute('value', !in_array($this->maxValue, array('0000-00-00',NULL))?date($this->dateFormat, strtotime($this->maxValue)):'');
			$date_end->setAttribute('title', $this->tooltip);
//			$date_end->setAttribute('class', 'daterangefilter');

			if($this->ajaxPostBack)
			{
				$date_start->setAttribute( 'onchange', "Rum.evalAsync('{$uri}/','{$requestString}&{$HTMLControlId}__filter_startdate='+encodeURIComponent(this.value),'POST',".($this->ajaxStartHandler).",".($this->ajaxCompletionHandler).");" );
				$date_end->setAttribute(   'onchange', "Rum.evalAsync('{$uri}/','{$requestString}&{$HTMLControlId}__filter_enddate='+encodeURIComponent(this.value),'POST',".($this->ajaxStartHandler).",".($this->ajaxCompletionHandler).");" );
			}
			else
			{
				$date_start->setAttribute( 'onchange', "Rum.sendSync('{$uri}/','{$requestString}&{$HTMLControlId}__filter_startdate='+encodeURIComponent(this.value));" );
				$date_end->setAttribute(   'onchange', "Rum.sendSync('{$uri}/','{$requestString}&{$HTMLControlId}__filter_enddate='+encodeURIComponent(this.value));" );
			}

			$span->addChild($date_start);
			$span->addChild($date_end);
			return $span;
		}
	}
?>