<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView filter
	 * 
	 * @property string $tooltip Specifies control tooltip
	 * @property bool $ajaxPostBack specifies whether to perform ajax postback on change, Default is false
	 * @property bool $ajaxStartHandler specifies the optional ajax start handler
	 * @property bool $ajaxCompletionHandler specifies the optional ajax completion handler
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class GridViewFilterBase extends \System\Base\Object
	{
		/**
		 * filter value
		 * @var string
		 */
		protected $value;

		/**
		 * Specifies whether the data has been submitted
		 * @var bool
		 */
		protected $submitted				= false;

		/**
		 * event request parameter
		 * @var string
		 */
		protected $ajaxPostBack				= false;

		/**
		 * column
		 * @var GridViewColumn
		 */
		protected $column;

		/**
		 * specifies control tool tip
		 * @var string
		 */
		protected $tooltip					= '';

		/**
		 * specifies the optional ajax start handler
		 * @var string
		 */
		public $ajaxStartHandler			= 'null';

		/**
		 * specifies the optional ajax completion handler
		 * @var string
		 */
		public $ajaxCompletionHandler		= 'null';

		/**
		 * Constructor
		 */
		public function __construct() {}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'tooltip' ) {
				return $this->tooltip;
			}
			elseif( $field === 'ajaxStartHandler' ) {
				return $this->ajaxStartHandler;
			}
			elseif( $field === 'ajaxCompletionHandler' ) {
				return $this->ajaxCompletionHandler;
			}
			elseif( $field === 'submitted' ) {
				return $this->submitted;
			}
			elseif( $field === 'ajaxPostBack' ) {
				return $this->ajaxPostBack;
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
			if( $field === 'tooltip' ) {
				$this->tooltip = (string)$value;
			}
			elseif( $field === 'ajaxStartHandler' ) {
				$this->ajaxStartHandler = (string)$ajaxStartHandler;
			}
			elseif( $field === 'ajaxCompletionHandler' ) {
				$this->ajaxCompletionHandler = (string)$ajaxCompletionHandler;
			}
			elseif( $field === 'ajaxPostBack' ) {
				$this->ajaxPostBack = (bool)$value;
			}
			else {
				parent::__set( $field, $value );
			}
		}

		/**
		 * set column
		 * @param GridViewColumn $column column
		 * @return void
		 */
		final public function setColumn(GridViewColumn &$column)
		{
			$this->column = &$column;
		}


		/**
		 * handle load events
		 *
		 * @return void
		 */
		protected function onLoad() {}


		/**
		 * read view state from session
		 *
		 * @param  array	&$viewState	session data
		 *
		 * @return void
		 */
		public function loadViewState( array &$viewState )
		{
			if( isset( $viewState["f_{$this->column->dataField}"] ))
			{
				$this->value = $viewState["f_{$this->column->dataField}"];
			}
		}


		/**
		 * called when all controls are loaded
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function load()
		{
			$this->onLoad();
		}


		/**
		 * process the HTTP request array
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		abstract public function requestProcessor( array &$request );


		/**
		 * write view state to session
		 *
		 * @param  array	&$viewState	session data
		 * @return void
		 */
		public function saveViewState( array &$viewState )
		{
			$viewState["f_{$this->column->dataField}"] = $this->value;
		}


		/**
		 * reset filter
		 *
		 * @return void
		 */
		public function resetFilter()
		{
			$this->value = "";
		}


		/**
		 * set filter value
		 *
		 * @param  string	$value	filter value
		 * @return void
		 */
		public function setValue($value)
		{
			$this->value = $value;
		}


		/**
		 * get filter value
		 *
		 * @return string
		 */
		public function getValue()
		{
			return $this->value;
		}


		/**
		 * filter DataSet
		 *
		 * @param  DataSet	&$ds		DataSet
		 * @return void
		 */
		abstract public function filterDataSet(\System\DB\DataSet &$ds );


		/**
		 * returns filter Dom Object
		 * 
		 * @param  string	$requestString a string containing request data
		 * @return DomObject
		 */
		abstract public function getDomObject($requestString);


		/**
		 * returns HTML control id string
		 * 
		 * @return string
		 */
		final protected function getHTMLControlId()
		{
			return $this->column->gridView->getHTMLControlId() . '_' . $this->formatDataField($this->column->dataField);
		}


		/**
		 * format data field
		 *
		 * @param  string	$dataField		data field
		 * @return string					formatted date field
		 */
		private function formatDataField( $dataField )
		{
			$dataField = str_replace( ' ', '_', (string)$dataField );
			$dataField = str_replace( '\'', '_', $dataField );
			$dataField = str_replace( '"', '_', $dataField );
			$dataField = str_replace( '/', '_', $dataField );
			$dataField = str_replace( '\\', '_', $dataField );
			$dataField = str_replace( '.', '_', $dataField );

			return $dataField;
		}
	}
?>