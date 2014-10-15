<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView string filter
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	class GridViewStringFilter extends GridViewFilterBase
	{
		/**
		 * specifies control tool tip
		 * @var string
		 */
		protected $tooltip					= 'Enter some text and press return';


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
				$ds->filter($this->column->dataField, 'contains', $this->value, true );
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

			$input = new \System\XML\DomObject('input');
			$input->setAttribute('type', 'search');
			$input->setAttribute('name', "{$HTMLControlId}__filter_value");
			$input->setAttribute('value', $this->value);
			$input->setAttribute('title', $this->tooltip);
//			$input->setAttribute('class', 'stringfilter');

			if($this->ajaxPostBack)
			{
				$input->setAttribute( 'onchange', "Rum.evalAsync('{$uri}/','{$requestString}&{$HTMLControlId}__filter_value='+encodeURIComponent(this.value),'POST',".($this->ajaxStartHandler).",".($this->ajaxCompletionHandler).");" );
				$input->setAttribute( 'onkeypress', "if(event.keyCode==13){event.returnValue=false;Rum.evalAsync('{$uri}','{$requestString}&{$HTMLControlId}__filter_value='+encodeURIComponent(this.value),'POST',".($this->ajaxStartHandler).",".($this->ajaxCompletionHandler).");return false;}" );
			}
			else
			{
				$input->setAttribute( 'onchange', "if(this.value==''){Rum.sendSync('{$uri}/','{$requestString}&{$HTMLControlId}__filter_value='+encodeURIComponent(this.value));}" );
				$input->setAttribute( 'onkeypress', "if(event.keyCode==13){event.returnValue=false;blur();Rum.sendSync('{$uri}','{$requestString}&{$HTMLControlId}__filter_value='+encodeURIComponent(this.value));return false;}" );
			}

			return $input;
		}
	}
?>