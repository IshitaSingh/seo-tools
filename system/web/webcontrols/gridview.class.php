<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 *
	 *
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView Control
	 *
	 * @property string $caption
	 * @property int $pageSize
	 * @property int $page
	 * @property bool $canSort
	 * @property bool $canFilter
	 * @property bool $canUpdateView specifies whether the GridView can update its own view when a postback is made
	 * @property bool $showFilters
	 * @property bool $showHeader
	 * @property bool $showFooter
	 * @property bool $showPageNumber
	 * @property bool $showInsertRow
	 * @property bool $showList
	 * @property bool $autoGenerateColumns
	 * @property string $valueField
	 * @property string $orderByField
	 * @property GridViewColumnCollection $columns
	 * @property bool $multiple
	 * @property array $selected
	 * @property string $listName
	 * @property string $sortBy
	 * @property string $sortOrder
	 * @property string $rowDataField specifies an optional row data-field
	 * @property bool $updateRowsOnly specifies whether to update rows only, or the entire table on updateAjax()
	 *
	 * @version			2.0
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class GridView extends WebControlBase
	{
		/**
		 * Table caption, Default is none
		 * @var string
		 */
		protected $caption					= '';

		/**
		 * Number of rows to display per page, Default is 20
		 * @var int
		 */
		protected $pageSize					= 20;

		/**
		 * current grid page
		 * @var int
		 */
		protected $page						= 1;

		/**
		 * Specifies if table is sortable, Default is true
		 * @var bool
		 */
		protected $canSort					= true;

		/**
		 * Specifies if table is filterable, Default is true
		 * @var bool
		 */
		protected $canFilter				= true;

		/**
		 * Specifies whether the GridView can update its own view when a postback is made
		 * @var bool
		 */
		protected $canUpdateView			= true;

		/**
		 * Specifies if table order can be changed, Default is false
		 * @var bool
		 */
		protected $canChangeOrder			= false;

		/**
		 * Set to display column filters, Default is false
		 * @var bool
		 */
		protected $showFilters				= false;

		/**
		 * Set to display column headers, Default is true
		 * @var bool
		 */
		protected $showHeader				= true;

		/**
		 * Set to display table footer, Default is false
		 * @var bool
		 */
		protected $showFooter				= false;

		/**
		 * Set to display table footer, Default is true
		 * @var bool
		 */
		protected $showPageNumber			= true;

		/**
		 * Set to display insert row, Default is false
		 * @var bool
		 */
		protected $showInsertRow			= false;

		/**
		 * Show list view, default is false
		 * @var bool
		 */
		protected $showList					= false;

		/**
		 * Specifies whether to generate columns from datasource, default is false
		 * @var bool
		 */
		protected $autoGenerateColumns		= false;

		/**
		 * specifies if list can have multiple values (used when showList is true), default is false
		 * @var bool
		 */
		protected $multiple					= false;

		/**
		 * array of values for selected items (used when showList is true)
		 * @var array
		 */
		protected $selected					= array();

		/**
		 * specifies the value field
		 * @var string
		 */
		protected $valueField				= '';

		/**
		 * specifies the order by field
		 * @var string
		 */
		protected $orderByField				= '';

		/**
		 * optional name for the list
		 * @var string
		 */
		protected $listName					= '&nbsp;';

		/**
		 * current grid sorting field
		 * @var string
		 */
		protected $sortBy					= '';

		/**
		 * current grid sorting order
		 * @var bool
		 */
		protected $sortOrder				= '';

		/**
		 * Specifies an optional row data-field
		 * @var GridView
		 */
		protected $rowDataField				= '';

		/**
		 * collection of columns
		 * @var GridViewColumnCollection
		 */
		protected $columns;

		/**
		 * specifies the action to take on mouseover events
		 * @ignore
		 */
		protected $onmouseover				= '';

		/**
		 * specifies the action to take on onmouseout events
		 * @ignore
		 */
		protected $onmouseout				= '';

		/**
		 * specifies the action to take on click events
		 * @ignore
		 */
		protected $onclick					= '';

		/**
		 * specifies the action to take on double click events
		 * @ignore
		 */
		protected $ondblclick				= '';

		/**
		 * Specifies whether to update rows only, or the entire table on updateAjax()
		 * @var bool
		 */
		protected $updateRowsOnly			= false;

		/**
		 * @deprecated
		 * @ignore
		 */
		private $__filterValues				= array();


		/**
		 * sets the controlId and prepares the control attributes
		 *
		 * @param  string   $controlId  Control Id
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct( $controlId );

			$this->columns = new GridViewColumnCollection($this);

			// event handling
			$this->events->add(new \System\Web\Events\PagePostEvent());

			// default events
			$onPostMethod = 'on' . ucwords( $this->controlId ) . 'Post';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onPostMethod))
			{
				trigger_error("GridViewPostEvent is deprecated, use PageRequestEvent instead", E_USER_DEPRECATED);
				$this->events->registerEventHandler(new \System\Web\Events\PagePostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onPostMethod));
			}
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function & __get( $field ) {
			if( $field === 'caption' ) {
				return $this->caption;
			}
			elseif( $field === 'pageSize' ) {
				return $this->pageSize;
			}
			elseif( $field === 'canSort' ) {
				return $this->canSort;
			}
			elseif( $field === 'canFilter' ) {
				return $this->canFilter;
			}
			elseif( $field === 'canUpdateView' ) {
				return $this->canUpdateView;
			}
			elseif( $field === 'showFilters' ) {
				return $this->showFilters;
			}
			elseif( $field === 'showHeader' ) {
				return $this->showHeader;
			}
			elseif( $field === 'showFooter' ) {
				return $this->showFooter;
			}
			elseif( $field === 'showPageNumber' ) {
				return $this->showPageNumber;
			}
			elseif( $field === 'showPrimaryKey' ) {
				return false;
			}
			elseif( $field === 'showInsertRow' ) {
				return $this->showInsertRow;
			}
			elseif( $field === 'showList' ) {
				return $this->showList;
			}
			elseif( $field === 'autoGenerateColumns' ) {
				return $this->autoGenerateColumns;
			}
			elseif( $field === 'multiple' ) {
				return $this->multiple;
			}
			elseif( $field === 'selected' ) {
				return $this->selected;
			}
			elseif( $field === 'valueField' ) {
				return $this->valueField;
			}
			elseif( $field === 'listName' ) {
				return $this->listName;
			}
			elseif( $field === 'page' ) {
				return $this->page;
			}
			elseif( $field === 'columns' ) {
				return $this->columns;
			}
			elseif( $field === 'sortBy' ) {
				return $this->sortBy;
			}
			elseif( $field === 'sortOrder' ) {
				return $this->sortOrder;
			}
			elseif( $field === 'rowDataField' ) {
				return $this->rowDataField;
			}
			elseif( $field === 'onmouseover' ) {
				trigger_error("GridView::onmouseover is deprecated, use GridView::render(args) instead", E_USER_DEPRECATED);
				return $this->onmouseover;
			}
			elseif( $field === 'onmouseout' ) {
				trigger_error("GridView::onmouseout is deprecated, use GridView::render(args) instead", E_USER_DEPRECATED);
				return $this->onmouseout;
			}
			elseif( $field === 'onclick' ) {
				trigger_error("GridView::onclick is deprecated, use GridView::render(args) instead", E_USER_DEPRECATED);
				return $this->onclick;
			}
			elseif( $field === 'ondblclick' ) {
				trigger_error("GridView::ondblclick is deprecated, use GridView::render(args) instead", E_USER_DEPRECATED);
				return $this->ondblclick;
			}
			elseif( $field === 'ajaxPostBack' ) {
				trigger_error("GridView::ajaxPostBack is deprecated", E_USER_DEPRECATED);
				$false=false;return $false;
			}
			elseif( $field === 'updateRowsOnly' ) {
				return $this->updateRowsOnly;
			}
			elseif( $field === 'filters' ) {
				$filters = array();
				foreach($this->columns as $column) {
					if( $column->filter ) {
						if($column->filter instanceof GridViewRangeFilterBase) {
							$minValue = $column->filter->getMinValue();
							if( $minValue ) {
								$filters[$column->dataField]['Min'] = $minValue;
							}
							$maxValue = $column->filter->getMaxValue();
							if( $maxValue ) {
								$filters[$column->dataField]['Max'] = $maxValue;
							}
						}
						else {
							$value = $column->filter->getValue();
							if( $value ) {
								$filters[$column->dataField] = $value;
							}
						}
					}
				}
				return $filters;
			}
			else
			{
				$result = parent::__get( $field );
				return $result;
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
			if( $field === 'caption' ) {
				$this->caption = (string)$value;
			}
			elseif( $field === 'pageSize' ) {
				$this->pageSize = (int)$value;
			}
			elseif( $field === 'page' ) {
				$this->page = (int)$value;
			}
			elseif( $field === 'canSort' ) {
				$this->canSort = (bool)$value;
			}
			elseif( $field === 'canFilter' ) {
				$this->canFilter = (bool)$value;
			}
			elseif( $field === 'canUpdateView' ) {
				$this->canUpdateView = (bool)$value;
			}
			elseif( $field === 'canChangeOrder' ) {
				$this->canChangeOrder = (bool)$value;
			}
			elseif( $field === 'showFilters' ) {
				$this->showFilters = (bool)$value;
			}
			elseif( $field === 'showHeader' ) {
				$this->showHeader = (bool)$value;
			}
			elseif( $field === 'showFooter' ) {
				$this->showFooter = (bool)$value;
			}
			elseif( $field === 'showPageNumber' ) {
				$this->showPageNumber = (bool)$value;
			}
			elseif( $field === 'showPrimaryKey' ) {
				trigger_error("GridView::showPrimaryKey is deprecated", E_USER_DEPRECATED);
			}
			elseif( $field === 'showInsertRow' ) {
				$this->showInsertRow = (bool)$value;
			}
			elseif( $field === 'showList' ) {
				$this->showList = (bool)$value;
			}
			elseif( $field === 'autoGenerateColumns' ) {
				$this->autoGenerateColumns = (bool)$value;
			}
			elseif( $field === 'multiple' ) {
				$this->multiple = (bool)$value;
			}
			elseif( $field === 'selected' ) {
				$this->selected = (array)$value;
			}
			elseif( $field === 'valueField' ) {
				$this->valueField = (string)$value;
			}
			elseif( $field === 'orderByField' ) {
				$this->orderByField = (string)$value;
			}
			elseif( $field === 'listName' ) {
				$this->listName = (string)$value;
			}
			elseif( $field === 'sortBy' ) {
				$this->sortBy = (string)$value;
			}
			elseif( $field === 'sortOrder' ) {
				$this->sortOrder = (string)$value;
			}
			elseif( $field === 'rowDataField' ) {
				$this->rowDataField = (string) $value;
			}
			elseif( $field === 'onmouseover' ) {
				trigger_error("GridView::onmouseover is deprecated", E_USER_DEPRECATED);
				$this->onmouseover = (string)$value;
			}
			elseif( $field === 'onmouseout' ) {
				trigger_error("GridView::onmouseout is deprecated", E_USER_DEPRECATED);
				$this->onmouseout = (string)$value;
			}
			elseif( $field === 'onclick' ) {
				trigger_error("GridView::onclick is deprecated", E_USER_DEPRECATED);
				$this->onclick = (string)$value;
			}
			elseif( $field === 'ondblclick' ) {
				trigger_error("GridView::ondblclick is deprecated", E_USER_DEPRECATED);
				$this->ondblclick = (string)$value;
			}
			elseif( $field === 'ajaxPostBack' ) {
				trigger_error("GridView::ajaxPostBack is deprecated", E_USER_DEPRECATED);
				$this->columns->ajaxPostBack = (bool)$value;
			}
			elseif( $field === 'updateRowsOnly' ) {
				$this->updateRowsOnly = (bool)$value;
			}
			else {
				parent::__set($field,$value);
			}
		}


		/**
		 * returns GridViewColumn if column is found in Collection
		 *
		 * @param  string		$dataField			data field
		 * @return GridViewColumn
		 */
		final public function findColumn( $dataField )
		{
			return $this->columns->findColumn( $dataField );
		}


		/**
		 * add a bound column
		 *
		 * @param  GridViewColumn		$column				GridView column
		 * @return void
		 */
		public function addColumn( GridViewColumn &$column )
		{
			$this->columns->add($column);
		}


		/**
		 * validate all controls in GridView object
		 *
		 * @param  string $errMsg error message
		 * @return bool
		 */
		public function validate(&$errMsg = '')
		{
			$valid = true;
			for($i = 0; $i < count($this->columns); $i++)
			{
				if( $this->columns[$i] instanceof GridViewControlBase )
				{
					if( !$this->columns[$i]->validate( $errMsg ))
					{
						$valid = false;
					}
				}
			}

			return $valid;
		}


		/**
		 * insert row in DataSet
		 *
		 * @return void
		 */
		public function insertRow()
		{
			trigger_error("GridView::insertRow() is deprecated, use GridViewColumn::fill() instead", E_USER_DEPRECATED);
			$request = \System\Web\HTTPRequest::$post;

			if( $this->dataSource )
			{
				$pkey = '';
				foreach($this->dataSource->fieldMeta as $meta)
				{
					if($meta->primaryKey)
					{
						$pkey = $meta->name;
						break;
					}
				}

				if($pkey)
				{
					$this->dataSource[$pkey] = null;
					foreach($this->dataSource->fields as $field)
					{
						if(isset($request[str_replace(' ', '_', $field)]))
						{
							$this->dataSource[$field] = $request[str_replace(' ', '_', $field)];
						}
					}
					$this->dataSource->insert();
				}
				else
				{
					throw new \System\Base\InvalidOperationException("GridView::dataSource contains no primary key");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("GridView::insertRow() called with null dataSource");
			}
		}


		/**
		 * update row in DataSet
		 *
		 * @param string $id entity id (primary key value) of the current row
		 * @return void
		 */
		public function updateRow($id)
		{
			trigger_error("GridView::insertRow() is deprecated, use GridViewColumn::fill() instead", E_USER_DEPRECATED);
			$request = \System\Web\HTTPRequest::$post;

			if( $this->dataSource )
			{
				$pkey = '';
				foreach($this->dataSource->fieldMeta as $meta)
				{
					if($meta->primaryKey)
					{
						$pkey = $meta->name;
						break;
					}
				}

				if($pkey)
				{
					if($this->dataSource->seek($pkey, $id))
					{
						foreach($this->dataSource->fields as $field)
						{
							if(isset($request[str_replace(' ', '_', $field)]))
							{
								$this->dataSource[$field] = $request[str_replace(' ', '_', $field)];
							}
						}
						$this->dataSource->update();
					}
					else
					{
						throw new \System\Base\InvalidOperationException("GridView::dataSource contains no record with primary key `{$id}`");
					}
				}
				else
				{
					throw new \System\Base\InvalidOperationException("GridView::dataSource contains no primary key");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("GridView::updateRow() called with null dataSource");
			}
		}


		/**
		 * delete row in DataSet
		 *
		 * @param string $id entity id (primary key value) of the current row
		 * @return void
		 */
		public function deleteRow($id)
		{
			trigger_error("GridView::insertRow() is deprecated, use GridViewColumn::fill() instead", E_USER_DEPRECATED);
			$request = \System\Web\HTTPRequest::$request;

			if( $this->dataSource )
			{
				$pkey = '';
				foreach($this->dataSource->fieldMeta as $meta)
				{
					if($meta->primaryKey)
					{
						$pkey = $meta->name;
						break;
					}
				}

				if($pkey)
				{
					if($this->dataSource->seek($pkey, $id))
					{
						$this->dataSource->delete();
					}
					else
					{
						throw new \System\Base\InvalidOperationException("GridView::dataSource contains no record with primary key `{$id}`");
					}
				}
				else
				{
					throw new \System\Base\InvalidOperationException("GridView::dataSource contains no primary key");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("GridView::updateRow() called with null dataSource");
			}
		}


		/**
		 * fill an existing \ArrayAccess object with data from a GridView button post back
		 * 
		 * @param \ArrayAccess $object object to fill
		 * @throws \System\Base\InvalidOperationException
		 * @return void
		 */
		public function fill(\ArrayAccess &$object)
		{
			$request = \System\Web\HTTPRequest::$post;

			foreach($this->dataSource->fields() as $field)
			{
				if(isset($request[str_replace(' ', '_', $field)]))
				{
					$object[$field] = $request[str_replace(' ', '_', $field)];
				}
			}
		}


		/**
		 * set filter values
		 *
		 * @param  array &$filter	Instance of a GridViewFilterBase
		 * @return void
		 */
		final public function setFilterValues($field, array $values)
		{
			trigger_error("GridView::setFilterValues() is deprecated, use GridViewColumn::setFilter() instead", E_USER_DEPRECATED);
			$this->__filterValues[$field] = $values;
		}


		/**
		 * reset filters
		 * @return void
		 */
		public function resetFilters()
		{
			$this->columns->resetFilters();
		}


		/**
		 * reset sorting
		 * @return void
		 */
		public function resetSorting()
		{
			$this->sortBy = '';
			$this->sortOrder = '';
		}


		/**
		 * apply filter and sort
		 * @return void
		 */
		public function applyFilterAndSort()
		{
			if( !$this->dataSource ) {
				throw new \System\Base\InvalidOperationException("GridView must have a valid data source before rendering");
			}

			// filter results
			if( $this->canFilter ) {
				$filter_event = new \System\Web\Events\GridViewFilterEvent();
				if($this->events->contains( $filter_event )) {
					$this->events->raise( $filter_event, $this );
				}
				else {
					// filter DataSet
					$this->columns->filterDataSet( $this->dataSource );
				}
			}

			// sort results
			if( $this->sortBy && $this->canSort) {
				$sort_event = new \System\Web\Events\GridViewSortEvent();

				if($this->events->contains( $sort_event )) {
					$this->events->raise( $sort_event, $this );
				}
				else {
					// sort DataSet
					$this->dataSource->sort( $this->sortBy, (strtolower($this->sortOrder)=='asc'?false:true), true );
				}
			}
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$count = count($this->dataSource);
			$this->columns->render();

			if( !$this->dataSource ) {
				throw new \System\Base\InvalidOperationException("GridView must have a valid data source before rendering");
			}

			// create table Dom
			$table   = $this->createDomObject( 'table' );
			$caption = new \System\XML\DomObject( 'caption' );
			$thead   = new \System\XML\DomObject( 'thead' );
			$tbody   = new \System\XML\DomObject( 'tbody' );
			$tfoot   = new \System\XML\DomObject( 'tfoot' );

			// set some basic attributes/properties
			$table->setAttribute( 'id', $this->getHTMLControlId() );

			$caption->nodeValue .= $this->caption;

			// display all
			if( $this->pageSize === 0 ) {
				$this->pageSize = $count;
				$this->showPageNumber = FALSE;
			}



			/*
			 * begin
			 */



			/**********************************************************************
			 *
			 * <thead>
			 *
			 **********************************************************************/

			/**
			 * Header
			 */
			if( $this->showHeader ) {
				$tr = $this->getRowHeader();

				// add thead to table
				$thead->addChild( $tr );
			}

			/**
			 * Filters
			 */
			if( $this->showFilters ) {
				$tr = $this->getRowFilter();

				// add thead to table
				if(count($tr->children)>0) {
					$thead->addChild( $tr );
				}
			}

			/**********************************************************************
			 *
			 * <tbody>
			 *
			 **********************************************************************/

			// validate grid page
			$this->dataSource->pageSize = $this->pageSize;
			if( $this->page > $this->dataSource->pageCount() ) {
				// if page is beyond DataSet, set to last page
				$this->page = $this->dataSource->pageCount();
			}
			elseif( $this->page < 1 ) {
				// if page is before DataSet, set to first page
				$this->page = 1;
			}

			$this->dataSource->page( $this->page );

			// loop through each item (record)
			while( !$this->dataSource->eof() && $this->dataSource->page() === $this->page )
			{
				$tr = $this->getRowBody( $this->dataSource );

				// add row to tbody
				$tbody->addChild( $tr );

				// move record pointer
				$this->dataSource->next();
			}

			/**
			 * Insert row
			 */
			if( $this->showInsertRow ) {
				$tr = $this->getInsertRow();

				$tbody->addChild( $tr );
			}

			/**********************************************************************
			 *
			 * <tfoot>
			 *
			 **********************************************************************/

			/**
			 * Footer
			 */
			if( $this->showFooter ) {
				$tr = $this->getRowFooter( $this->dataSource );

				$tfoot->addChild( $tr );
			}

			/**
			 * RecordNavigation
			 */
			if( $this->showPageNumber ) {
				$tr = $this->getPagination( $this->dataSource );
				$tfoot->addChild( $tr );
			}

			// empty table
			if( !$count ) {
				$tr = new \System\XML\DomObject( 'tr' );
				$td = new \System\XML\DomObject( 'td' );

				// list item
				if( $this->valueField && $this->showList ) {
					$td->setAttribute( 'colspan', sizeof( $this->columns ) + 1 );
				}
				else {
					$td->setAttribute( 'colspan', sizeof( $this->columns ));
				}

				$tr->addChild( $td );
				$tbody->addChild( $tr );
			}

			/*
			 * end
			 */

			if( $this->caption )				$table->addChild( $caption );
			if( $this->showFilters ||
				$this->showHeader )				$table->addChild( $thead );
			if( $this->showPageNumber ||
				$this->showFooter )				$table->addChild( $tfoot );
												$table->addChild( $tbody );

			return $table;
		}


		/**
		 * read view state from session
		 *
		 * @param  array	$viewState	session data
		 * @return void
		 */
		protected function onLoadViewState( array &$viewState )
		{
			if( $this->enableViewState )
			{
				if( isset( $viewState['p'] ) &&
					isset( $viewState['sb'] ) &&
					isset( $viewState['so'] ) &&
					isset( $viewState['s'] ))
				{
					$this->page = (int) $viewState['p'];
					$this->sortBy = $viewState['sb'];
					$this->sortOrder = $viewState['so'];
					$this->selected = $viewState['s'];

					foreach($this->columns as $column)
					{
						$column->loadViewState($viewState);
					}
				}
			}
		}


		/**
		 * bind control to data
		 *
		 * @param  $default			value
		 * @return void
		 */
		protected function onDataBind()
		{
			if( $this->dataSource instanceof \System\DB\DataSet )
			{
				if( $this->autoGenerateColumns || count($this->columns) === 0 )
				{
					$this->_generateColumns();
				}
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::bind() must implement the iBindable interface");
			}
		}


		/**
		 * called when control is loaded
		 *
		 * @return bool			true if successfull
		 */
		protected function onLoad()
		{
			// default events
			$onPostMethod = 'on' . ucwords( $this->controlId ) . 'ColumnPost';
			if(!\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onPostMethod)) {
				$onPostMethod = '';
			}
			$onAjaxPostMethod = 'on' . ucwords( $this->controlId ) . 'AjaxColumnPost';
			if(!\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onAjaxPostMethod)) {
				$onAjaxPostMethod = '';
			}

			// Set events for columns
			foreach($this->columns as $column) {
				if($onPostMethod) {
					$column->events->registerEventHandler(new \System\Web\Events\GridViewColumnPostEvent('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onPostMethod));
				}
				if($onAjaxPostMethod) {
					$column->events->registerEventHandler(new \System\Web\Events\GridViewColumnAjaxPostEvent('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onAjaxPostMethod));
				}
			}

			// Backgwards compatibilty code - remove in Version 6.6
			if($this->showFilters) {
				$nofilters = true;
				foreach( $this->columns as $column ) {
					if($column->filter) {
						$nofilters = false;
					}
				}
				if($nofilters) {
					foreach( $this->columns as $column ) {
						if(isset($this->__filterValues[$column->dataField])) {
							$column->setFilter(new GridViewListFilter($this->__filterValues[$column->dataField]));
						}
						else {
							$column->setFilter(new GridViewStringFilter());
						}
						$column->filter->ajaxPostBack = $column->ajaxPostBack;
					}
				}
			}
			// End Backwards compatibilty code

			$this->columns->load();
		}


		/**
		 * process the HTTP request array
		 *
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			$this->columns->requestProcessor( $request );

			if( isset( $request[$this->getHTMLControlId().'__sort_by'] ))
			{
				$this->sortBy = $request[$this->getHTMLControlId().'__sort_by'];
//				unset( $request[$this->getHTMLControlId().'__sort_by'] );
			}

			if( isset( $request[$this->getHTMLControlId().'__sort_order'] ))
			{
				$this->sortOrder = $request[$this->getHTMLControlId().'__sort_order'];
//				unset( $request[$this->getHTMLControlId().'__sort_order'] );
			}

			if( isset( $request[$this->getHTMLControlId().'__page'] ))
			{
				$this->page = (int) $request[$this->getHTMLControlId().'__page'];
//				unset( $request[$this->getHTMLControlId().'__page'] );
			}

			if( isset( $request[$this->getHTMLControlId().'__selected'] ))
			{
				$this->selected = $request[$this->getHTMLControlId().'__selected'];
//				unset( $request[$this->getHTMLControlId().'__selected'] );
			}
		}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onPost( array &$request )
		{
			$this->events->raise(new \System\Web\Events\PagePostEvent(), $this, $request);
			$this->columns->handlePostEvents( $request );
		}


		/**
		 * Event called when control is ready for rendering
		 *
		 * @return void
		 */
		protected function onPreRender()
		{
			$this->applyFilterAndSort();
			parent::onPreRender();
		}


		/**
		 * write view state to session
		 *
		 * @param  object	$viewState	session array
		 * @return void
		 */
		protected function onSaveViewState( array &$viewState )
		{
			if( $this->enableViewState )
			{
				$viewState['p'] = $this->page;
				$viewState['sb'] = $this->sortBy;
				$viewState['so'] = $this->sortOrder;
				$viewState['s'] = $this->selected;

				foreach($this->columns as $column)
				{
					$column->saveViewState($viewState);
				}
			}
		}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			// Update only tbody element
			$page = $this->getParentByType('\System\Web\WebControls\Page');

//			$tbody = \addslashes(str_replace("<tbody>", '', str_replace("</tbody>", '', $this->getDomObject()->tbody->fetch())));
			$tbody = \addslashes(str_replace("<tbody>", '', str_replace("</tbody>", '', trim(preg_replace('/\n+/','' ,preg_replace('/\r+/','' ,$this->getDomObject()->tbody->fetch()))))));

			// Update rows
			$page->loadAjaxJScriptBuffer('var tbody1 = Rum.id(\''.$this->getHTMLControlId().'\').getElementsByTagName(\'tbody\')[0];');
			$page->loadAjaxJScriptBuffer('var tbody2 = document.createElement(\'tbody\');');
			$page->loadAjaxJScriptBuffer('tbody2.innerHTML = \''.$tbody.'\';');
			$page->loadAjaxJScriptBuffer('tbody1.parentNode.insertBefore(tbody2, tbody1);');
			$page->loadAjaxJScriptBuffer('tbody1.parentNode.removeChild(tbody1);');

			if(($this->showFooter || $this->showPageNumber) && !$this->updateRowsOnly)
			{
				$tfoot = \addslashes(str_replace("\n", '', str_replace("\r", '', str_replace("<tfoot>", '', str_replace("</tfoot>", '', $this->getDomObject()->tfoot->fetch())))));

				// Update footer
				$page->loadAjaxJScriptBuffer('var tfoot1 = Rum.id(\''.$this->getHTMLControlId().'\').getElementsByTagName(\'tfoot\')[0];');
				$page->loadAjaxJScriptBuffer('var tfoot2 = document.createElement(\'tfoot\');');
				$page->loadAjaxJScriptBuffer('tfoot2.innerHTML = \''.$tfoot.'\';');
				$page->loadAjaxJScriptBuffer('tfoot1.parentNode.insertBefore(tfoot2, tfoot1);');
				$page->loadAjaxJScriptBuffer('tfoot1.parentNode.removeChild(tfoot1);');
			}
		}


		/**
		 * generic method for handling the row header
		 *
		 * @return DomObject
		 */
		protected function getRowHeader()
		{
			// create header node
			$tr = new \System\XML\DomObject( 'tr' );

			// list item
			if( $this->valueField && $this->showList ) {

				// create column node (field)
				$th = new \System\XML\DomObject( 'th' );

				// set column attributes
//				$th->setAttribute( 'class', 'listcolumn' );
				$th->innerHtml .= $this->listName;

				if( $this->multiple )
				{
					$input = new \System\XML\DomObject( 'input' );
					$input->setAttribute( 'type', 'checkbox' );
					$input->setAttribute( 'onclick', 'Rum.gridViewSelectAll(\''.$this->getHTMLControlId().'\');' );
					$input->setAttribute( 'id', $this->getHTMLControlId() . '__selectall' );
					$input->setAttribute( 'name', $this->getHTMLControlId() . '__selectall' );

					// add input to th
					$th->addChild( $input );
				}

				// add thead to table
				$tr->addChild( $th );
			}

			// loop through each column
			foreach( $this->columns as $column )
			{
				// create column node
				$th = new \System\XML\DomObject( 'th' );

				// set data-field attributes
				if($column->dataField) {
					$th->setAttribute( 'data-field', $column->dataField );
				}

				// set class
				if($column['Classname']) {
					$th->setAttribute( 'class', $column['Classname'] );
				}

				// get class based on sort field and order
				$class = '';
				$title = 'Sort ascending';
				if( $this->sortBy === $column['DataField'] ) {
					if( $this->sortOrder=='desc' ) {
						$class = 'sort_desc';
						$title = 'Sort ascending';
					}
					else {
						$class = 'sort_asc';
						$title = 'Sort descending';
					}
				}

				// column is canSort
				if( $this->canSort && !$this->canChangeOrder && $column['DataField'] )
				{
					$a = new \System\XML\DomObject( 'a' );

					// set column attributes
					$a->innerHtml .= $column['Header-Text'];
					if( $class ) $a->setAttribute( 'class', $class );
					$a->setAttribute( 'title', $title );

					// generate sort URL
					if(( $this->sortBy === $column['DataField'] ) && $this->sortOrder=='asc' ) {
						$order = "desc";
					}
					else {
						$order = "asc";
					}

					$a->setAttribute( 'href', $this->getQueryString($this->getHTMLControlId().'__page='.$this->page.'&'.$this->getHTMLControlId().'__sort_by='.($column['DataField']).'&'.$this->getHTMLControlId().'__sort_order='.$order));

					// add link node to column
					$th->addChild( $a );
				}

				// column is not canSort
				else
				{
					// set column attributes
					$th->innerHtml .= ($column['Header-Text']?$column['Header-Text']:'&nbsp;');
				}

				// add column to header
				$tr->addChild( $th );
			}

//			if($this->canChangeOrder)
//			{
//				$th = new \System\XML\DomObject('th');
//				$th->setAttribute( 'class', 'movecolumn' );
//				$tr->addChild( $th );
//			}

			return $tr;
		}


		/**
		 * generic method for handling the row header
		 *
		 * @return DomObject
		 */
		protected function getRowFilter()
		{
			// create header node
			$tr = new \System\XML\DomObject( 'tr' );

			// list item
			if( $this->valueField && $this->showList ) {

				// create column node (field)
				$th = new \System\XML\DomObject( 'td' );

				// add thead to table
				$tr->addChild( $th );
			}

			// loop through each column
			foreach( $this->columns as $column )
			{
				// create column node
				$th = new \System\XML\DomObject( 'td' );
				if($column['Classname']) {
					$th->setAttribute( 'class', $column['Classname'] );
				}

				// column is filterable
				if( $column->filter )
				{
					$th->addChild($column->getFilterDomObject($this->getHTMLControlId()));
				}

				// add column to header
				$tr->addChild( $th );
			}

			if($this->canChangeOrder)
			{
				$th = new \System\XML\DomObject('th');
				$tr->addChild( $th );
			}

			return $tr;
		}


		/**
		 * generic method for handling the row body
		 *
		 * @param  DataSet	$ds			DataSet object with current resultset
		 * @return DomObject
		 */
		protected function getRowBody( \System\DB\DataSet &$ds )
		{
			// js events
			$onmouseover = $this->onmouseover;
			$onmouseout  = $this->onmouseout;
			$onclick	 = $this->onclick;
			$ondblclick  = $this->ondblclick;

			// create item node
			$tr = new \System\XML\DomObject( 'tr' );

			// set row attributes
			$tr->setAttribute( 'class', ($ds->cursor & 1)?'row_alt':'row' );// set data-field attributes

			// set data-field attributes
			if($this->rowDataField) {
				$tr->setAttribute( 'data-field', $ds[$this->rowDataField] );
			}

			// list item
			if( $this->valueField && $this->showList ) {

				// create column node (field)
				$td = new \System\XML\DomObject( 'td' );

				$input = new \System\XML\DomObject( 'input' );
				$input->setAttribute( 'type',	( $this->multiple?'checkbox':'radio' ) );
				$input->setAttribute( 'onclick', 'if(this.checked)this.checked=false;else this.checked=true;' );
				$input->setAttribute( 'id', $this->getHTMLControlId() . '__item_' . \rawurlencode( $ds[$this->valueField] ));
				$input->setAttribute( 'name', $this->getHTMLControlId() . '__selected' . ( $this->multiple?'[]':'' ));
				$input->setAttribute( 'value', $ds[$this->valueField] );

				if( $this->multiple ) {
					if( is_array( $this->selected )) {
						if( array_search( $ds[$this->valueField], $this->selected ) !== false ) {
							$input->setAttribute( 'checked', 'checked' );
							$tr->setAttribute( 'class', ' selected' );
						}
					}
				}
				else {
					if( $this->selected === $ds[$this->valueField] ) {
						$input->setAttribute( 'checked', 'checked' );
						$tr->setAttribute( 'class', ' selected' );
					}

					$tr->setAttribute( 'onclick', 'Rum.gridViewUnSelectAll( \'' . $this->getHTMLControlId() . '\' );' );
				}

				$tr->setAttribute( 'onclick', 'if( Rum.id(\'' . (string) $this->getHTMLControlId() . '__item_' . \rawurlencode( $ds[$this->valueField] ) . '\').checked ) { Rum.id(\''. (string) $this->getHTMLControlId() . '__item_' . \rawurlencode( $ds[$this->valueField] ) . '\').checked = false; } else { Rum.id(\'' . (string) $this->getHTMLControlId() . '__item_' . \rawurlencode( $ds[$this->valueField] ) . '\').checked = true; }' );
				$tr->setAttribute( 'onclick', 'if( Rum.id(\'' . (string) $this->getHTMLControlId() . '__item_' . \rawurlencode( $ds[$this->valueField] ) . '\').checked ) { if(this.className === \'row\' ) { this.className = \'selected row\'; } else { this.className = \'selected row_alt\'; }}' );
				$tr->setAttribute( 'onclick', 'if(!Rum.id(\'' . (string) $this->getHTMLControlId() . '__item_' . \rawurlencode( $ds[$this->valueField] ) . '\').checked ) { if(this.className === \'selected row\' ) { this.className = \'row\'; } else { this.className = \'row_alt\'; }}' );

				// add td element to tr
				$td->addChild( $input );
				$tr->addChild( $td );
			}

			$values = array();

			// loop through each column (field name)
			foreach( $this->columns as $column )
			{
				// create column node (field)
				$td = new \System\XML\DomObject( 'td' );

				// set data-field attributes
				if($column->dataField) {
					$td->setAttribute( 'data-field', $column->dataField );
				}

				// set column attributes
				if( $column['Classname'] ) {
					$td->setAttribute( 'class', $column['Classname'] );
				}

				// auto format with column value
				if( !$column['Item-Text'] && $column['DataField'] ) {
					foreach( $ds->fields as $field ) {
						$values[$field] = $ds[$field];
					}
					$td->nodeValue = $values[$column['DataField']];
				}
				elseif( $column['Item-Text'] ) {

					// get all field values in array
					$html = $column['Item-Text'];
					foreach( $ds->fields as $field ) {
						$values[$field] = $ds[$field];
						$html = str_replace( '%' . $field . '%', '$values[\''.addslashes($field).'\']', $html );
					}

					// eval
					$eval = eval( '$html = ' . $html . ';' );
					if($eval===false) {
						//TODO: better error message
						throw new \System\Base\InvalidOperationException("Could not run expression in GridView on column `".$column["DataField"]."`: \$html = " . ($html) . ';');
					}

					$td->innerHtml = strlen($html)>0?$html:'&nbsp;';
				}

				// add td element to tr
				$tr->addChild( $td );
			}

			// parse event string
			foreach( $ds->fields as $field ) {

				if( $onmouseover ) {
					$onmouseover = str_replace( '%' . $field . '%', $ds[$field], $onmouseover );
				}
				if( $onmouseout ) {
					$onmouseout = str_replace( '%' . $field . '%', $ds[$field], $onmouseout );
				}
				if( $onclick ) {
					$onclick = str_replace( '%' . $field . '%', $ds[$field], $onclick );
				}
				if( $ondblclick ) {
					$ondblclick = str_replace( '%' . $field . '%', $ds[$field], $ondblclick );
				}
			}

			if( $this->onmouseover ) {
				$tr->setAttribute( 'onmouseover', $onmouseover );
			}
			if( $this->onmouseout ) {
				$tr->setAttribute( 'onmouseout', $onmouseout );
			}
			if( $this->onclick ) {
				$tr->setAttribute( 'onclick', $onclick );
			}
			if( $this->ondblclick ) {
				$tr->setAttribute( 'ondblclick', $ondblclick );
			}

			return $tr;
		}


		/**
		 * generic method for handling the table insert row
		 *
		 * @return DomObject
		 */
		protected function getInsertRow( )
		{
			// create footer node
			$tr = new \System\XML\DomObject( 'tr' );

			// set row attributes
			$tr->setAttribute( 'class', ($this->dataSource->cursor % 2)?'insert row_alt':'insert row' );

			// add blank listcolumn
			if( $this->valueField && $this->showList ) {
				$td = new \System\XML\DomObject( 'td' );
				$tr->addChild( $td );
			}

			// loop through each column
			foreach( $this->columns as $column )
			{
				// create column node
				$td = new \System\XML\DomObject( 'td' );

				// set column attributes
				if( $column['Classname'] ) {
					$td->setAttribute( 'class', $column['Classname'] );
				}

				if( $column instanceof GridViewControlBase ) {
					$html = $column->fetchInsertControl();
					$insertText = '';
					if(false === eval("\$insertText ={$html};")) {
						throw new \System\Base\InvalidOperationException("Could not run expression in GridView on column `".$column["DataField"]."`: \$html = " . ($html) . ';');
					}
					$td->innerHtml .= $insertText;
				}

				$tr->addChild( $td );
			}

			return $tr;
		}


		/**
		 * generic method for handling the table footer
		 *
		 * @return DomObject
		 */
		protected function getRowFooter( )
		{
			// create footer node
			$tr = new \System\XML\DomObject( 'tr' );

			// set row attributes
			$tr->setAttribute( 'class', ($this->dataSource->cursor % 2)?'footer row_alt':'footer row' );

			// add blank listcolumn
			if( $this->valueField && $this->showList ) {
				$td = new \System\XML\DomObject( 'td' );
				$tr->addChild( $td );
			}

			// loop through each column
			foreach( $this->columns as $column )
			{
				// create column node
				$td = new \System\XML\DomObject( 'td' );

				// set column attributes
				if( $column['Classname'] ) {
					$td->setAttribute( 'class', $column['Classname'] );
				}

				if( $column['Footer-Text'] ) {
					$html = $column['Footer-Text'];
					$footerText = '';
					if(false === eval("\$footerText ={$html};")) {
						throw new \System\Base\InvalidOperationException("Could not run expression in GridView on column `".$column["DataField"]."`: \$html = " . ($html) . ';');
					}
					$td->innerHtml .= $footerText;
				}

				$tr->addChild( $td );
			}

			return $tr;
		}


		/**
		 * generic method for handling the table pagination
		 *
		 * @return DomObject
		 */
		protected function getPagination( )
		{
			$tr = new \System\XML\DomObject( 'tr' );

			$td = new \System\XML\DomObject( 'td' );
			$inc = 0;
			if( $this->valueField && $this->showList ) {
				$inc++;
			}

			$td->setAttribute( 'colspan', sizeof( $this->columns ) + $inc );

			$span = new \System\XML\DomObject( 'span' );
			$span->setAttribute( 'class', 'pagination' );

			// prev
			$a = new \System\XML\DomObject( 'a' );
			$a->nodeValue .= 'prev';
			if( $this->page > 1 )
			{
				$a->setAttribute( 'href', $this->getQueryString($this->getHTMLControlId().'__page='.($this->page-1).'&'.$this->getHTMLControlId().'__sort_by='.$this->sortBy.'&'.$this->getHTMLControlId().'__sort_order='.$this->sortOrder));
			}
			else
			{
				$a->setAttribute('class', 'disabled');
			}
			$span->addChild( $a );
			$span->addChild( new \System\XML\TextNode(' '));

			// page jump
			$count = count($this->dataSource);
			for( $page=1; $this->pageSize && (( $page * $this->pageSize ) - $this->pageSize ) < $count; $page++ )
			{
				$start = ((( $page * $this->pageSize ) - $this->pageSize ) + 1 );

				if( $page * $this->pageSize < $count )
				{
					$end = ( $page * $this->pageSize );
				}
				else
				{
					$end = $count;
				}

				// page select
				if( $this->page <> $page )
				{
					$a = new \System\XML\DomObject( 'a' );
					$a->setAttribute( 'href', $this->getQueryString($this->getHTMLControlId().'__page='.$page.'&'.$this->getHTMLControlId().'__sort_by='.$this->sortBy.'&'.$this->getHTMLControlId().'__sort_order='.$this->sortOrder));
					$a->nodeValue .= $page;
					$span->addChild( $a );
					$span->addChild( new \System\XML\TextNode(' '));
				}
				else
				{
					$a = new \System\XML\DomObject( 'a' );
					$a->setAttribute( 'class', 'disabled' );
					$a->nodeValue .= $page;
					$span->addChild( $a );
					$span->addChild( new \System\XML\TextNode(' '));
				}
			}

			// next
			$a = new \System\XML\DomObject( 'a' );
			$a->nodeValue .= 'next';
			if(( $this->page * $this->pageSize ) < $count && $this->pageSize )
			{
				$a->setAttribute( 'href', $this->getQueryString($this->getHTMLControlId().'__page='.($this->page+1).'&'.$this->getHTMLControlId().'__sort_by='.$this->sortBy.'&'.$this->getHTMLControlId().'__sort_order='.$this->sortOrder));
			}
			else
			{
				$a->setAttribute('class', 'disabled');
			}
			$span->addChild( $a );
			$span->addChild( new \System\XML\TextNode(' '));

			$td->addChild( $span );

			// get page info
			$start = ((( $this->page * $this->pageSize ) - $this->pageSize ) + 1 );
			if( !$count ) $start = 0;

			$end = 0;
			if( $this->page * $this->pageSize < $count )
			{
				$end = ( $this->page * $this->pageSize );
			}
			else
			{
				$end = $count;
			}

			$span = new \System\XML\DomObject( 'span' );
			$span->setAttribute('class', 'summary');
			$span->nodeValue .= "showing {$start} to {$end} of " . $count;

			$td->addChild( $span );
			$tr->addChild( $td );

			return $tr;
		}


		/**
		 * generates grid columns using datsource
		 *
		 * @return void
		 */
		private function _generateColumns()
		{
			if( $this->dataSource )
			{
				$this->columns = new GridViewColumnCollection($this);

				foreach( $this->dataSource->fieldMeta as $field )
				{
					if( !$field->primaryKey )
					{
						if( $field->boolean )
						{
							$this->addColumn( new GridViewColumn( $field->name, ucwords( str_replace( '_', ' ', $field->name )), "%{$field->name}%?'Yes':'No'" ));
						}
						elseif( $field->blob )
						{
							continue;
						}
						else
						{
							$this->addColumn( new GridViewColumn( $field->name, ucwords( str_replace( '_', ' ', $field->name ))));
						}
					}
				}
			}
		}
	}
?>