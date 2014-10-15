<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView boolean filter
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	class GridViewBooleanFilter extends GridViewFilterBase
	{
		/**
		 * specifies control tool tip
		 * @var string
		 */
		protected $tooltip					= 'Check/uncheck the checkbox';


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
				if($request[$HTMLControlId . '__filter_value'])
				{
					$this->value = $request[$HTMLControlId . '__filter_value'];
//					unset($request[$HTMLControlId . '__filter_value']);
				}
				else
				{
					$this->value = null;
				}
			}
		}


		/**
		 * filter DataSet
		 *
		 * @param  DataSet	&$ds		DataSet
		 * @return void
		 */
		public function filterDataSet(\System\DB\DataSet &$ds )
		{
			if($this->value) {
				$ds->filter($this->column->dataField, '=', $this->value=='true'?1:0 );
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
//			$select->setAttribute('class', 'booleanfilter');
			$option = new \System\XML\DomObject( 'option' );
			$option->setAttribute('value', '');
			$option->nodeValue = '';
			$select->addChild($option);

			// get values
			foreach(array('yes'=>'true', 'no'=>'false') as $key=>$value)
			{
				$option = new \System\XML\DomObject( 'option' );
				$option->setAttribute('value', $value);
				$option->nodeValue = $key;

				if($this->value==$value)
				{
					$option->setAttribute('selected', 'selected');
				}

				$select->addChild($option);
			}

			if($this->ajaxPostBack)
			{
				$select->setAttribute( 'onchange', "Rum.evalAsync('{$uri}/', '{$requestString}&{$HTMLControlId}__filter_value='+encodeURIComponent(this.value),'POST',".($this->ajaxStartHandler).",".($this->ajaxCompletionHandler).");" );
			}
			else
			{
				$select->setAttribute( 'onchange', "Rum.sendSync('{$uri}/', '{$requestString}&{$HTMLControlId}__filter_value='+encodeURIComponent(this.value));" );
			}

			return $select;
		}
	}
?>