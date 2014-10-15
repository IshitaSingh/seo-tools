<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 *
	 *
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView column
	 *
	 * @property string $controlId control id
	 * @property string $dataField datefield
	 * @property string $headerText header text
	 * @property string $itemText item text (templating allowed)
	 * @property string $footerText footer text
	 * @property string $className class name
	 * @property GridView $gridView instance of the GridView
	 * @property EventCollection $events event collection
	 * @property GridViewFilter $filter specifies the column filter
	 * @property bool $ajaxPostBack specifies whether to perform ajax postback on change, Default is false
	 * @property string $ajaxStartHandler specifies the optional ajax start handler
	 * @property string $ajaxCompletionHandler specifies the optional ajax completion handler
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class GridViewColumn extends \System\Base\Object implements \ArrayAccess
	{
		/**
		 * control id
		 * @var string
		 */
		protected $controlId			= '';

		/**
		 * datefield
		 * @var string
		 */
		protected $dataField			= '';

		/**
		 * header text
		 * @var string
		 */
		protected $headerText			= '';

		/**
		 * item text
		 * @var string
		 */
		protected $itemText				= '';

		/**
		 * footer text
		 * @var string
		 */
		protected $footerText			= '';

		/**
		 * class name
		 * @var string
		 */
		protected $className			= '';

		/**
		 * specifies the column filter
		 * @var GridViewFilterBase
		 */
		protected $filter				= null;

		/**
		 * instance of the GridView object
		 * @var GridView
		 */
		protected $gridView				= null;

		/**
		 * event request parameter
		 * @var string
		 */
		protected $ajaxPostBack				= false;

		/**
		 * specifies the optional ajax start handler
		 * @var string
		 */
		protected $ajaxStartHandler			= 'null';

		/**
		 * specifies the optional ajax completion handler
		 * @var string
		 */
		protected $ajaxCompletionHandler	= 'null';

		/**
		 * set when viewState loaded
		 * @var bool
		 */
		private $_loaded				= false;

		/**
		 * specifies the filter values (for a key/value list)
		 * @var array
		 * /
		protected $filterValues			= array();


		/**
		 * @param  string		$dataField			name of data field to bind column to
		 * @param  string		$headerText			column header text
		 * @param  string		$itemText			column item text (templating allowed)
		 * @param  string		$footerText			column footer text
		 * @param  string		$className			column css class name
		 * @return void
		 */
		public function __construct( $dataField, $headerText = '', $itemText = '', $footerText = '', $className = '' )
		{
			$this->controlId = (string) $dataField;
			$this->dataField = (string) $dataField;
			$this->headerText = (string) $headerText;
			$this->itemText = (string) $itemText;
			$this->footerText = (string) $footerText;
			$this->className = (string) $className;

			// event handling
			$this->events->add(new \System\Web\Events\GridViewColumnPostEvent());
			$this->events->add(new \System\Web\Events\GridViewColumnAjaxPostEvent());

			// default events
			$postEvent='on'.ucwords(str_replace(" ","_",$this->dataField)).'Post';
			$ajaxPostEvent='on'.ucwords(str_replace(" ","_",$this->dataField)).'AjaxPost';

			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $postEvent))
			{
				$this->events->registerEventHandler(new \System\Web\Events\GridViewColumnPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $postEvent));
			}
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $ajaxPostEvent))
			{
				$this->ajaxPostBack = true;
				$this->events->registerEventHandler(new \System\Web\Events\GridViewColumnAjaxPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $ajaxPostEvent));
			}
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return void
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'controlId' ) {
				return $this->controlId;
			}
			elseif( $field === 'dataField' ) {
				return $this->dataField;
			}
			elseif( $field === 'headerText' ) {
				return $this->headerText;
			}
			elseif( $field === 'itemText' ) {
				return $this->itemText;
			}
			elseif( $field === 'footerText' ) {
				return $this->footerText;
			}
			elseif( $field === 'className' ) {
				return $this->className;
			}
			elseif( $field === 'filter' ) {
				return $this->filter;
			}
			elseif( $field === 'gridView' ) {
				return $this->gridView;
			}
			elseif( $field === 'ajaxPostBack' ) {
				return $this->ajaxPostBack;
			}
			elseif( $field === 'ajaxStartHandler' ) {
				return $this->ajaxStartHandler;
			}
			elseif( $field === 'ajaxCompletionHandler' ) {
				return $this->ajaxCompletionHandler;
			}
			elseif( $field === 'canFilter' ) {
				trigger_error("GridViewColumn::canFilter is deprecated", E_USER_DEPRECATED);
				return true;
			}
			elseif( $field === 'ondblclick' ) {
				trigger_error("GridViewColumn::ondblclick is deprecated", E_USER_DEPRECATED);
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
		 * @return void
		 * @ignore
		 */
		public function __set( $field, $value ) {
			if( $field === 'controlId' ) {
				$this->controlId = (string) $value;
			}
			elseif( $field === 'dataField' ) {
				$this->dataField = (string) $value;
			}
			elseif( $field === 'headerText' ) {
				$this->headerText = (string) $value;
			}
			elseif( $field === 'itemText' ) {
				$this->itemText = (string) $value;
			}
			elseif( $field === 'footerText' ) {
				$this->footerText = (string) $value;
			}
			elseif( $field === 'className' ) {
				$this->className = (string) $value;
			}
			elseif( $field === 'ajaxPostBack' ) {
				$this->ajaxPostBack = (bool)$value;
			}
			elseif( $field === 'ajaxStartHandler' ) {
				$this->ajaxStartHandler = (string)$value;
			}
			elseif( $field === 'ajaxCompletionHandler' ) {
				$this->ajaxCompletionHandler = (string)$value;
			}
			elseif( $field === 'ondblclick' ) {
				trigger_error("GridViewColumn::ondblclick is deprecated", E_USER_DEPRECATED);
			}
			elseif( $field === 'canFilter' ) {
				trigger_error("GridViewColumn::canFilter is deprecated", E_USER_DEPRECATED);
			}
			else {
				parent::__set($field, $value);
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetExists($index)
		{
			if(\in_array($this->getName($index), array_keys(\get_class_vars(\get_class($this)))))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetGet($index)
		{
			if($this->offsetExists($index))
			{
				return $this->{$this->getName($index)};
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetSet($index, $item)
		{
			if($this->offsetExists($index))
			{
				$this->{$this->getName($index)} = (string) $item;
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetUnset($index)
		{
			if($this->offsetExists($index))
			{
				$this->{$this->getName($index)} = null;
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * set GridView
		 *
		 * @param  GridViewFilterBase	&$filter	Instance of a GridViewFilterBase
		 * @return void
		 */
		final public function setGridView(GridView &$gridView)
		{
			if(!$this->_loaded)
			{
				$this->gridView = &$gridView;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Cannot set GridView after column is loaded");
			}
		}


		/**
		 * set filter
		 *
		 * @param  GridViewFilterBase	&$filter	Instance of a GridViewFilterBase
		 * @return void
		 */
		final public function setFilter(GridViewFilterBase &$filter)
		{
			if(!$this->_loaded)
			{
				$filter->ajaxPostBack = $this->ajaxPostBack;
				$this->filter = $filter;
				$this->filter->setColumn($this);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Cannot add filter after column is loaded");
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
			$this->_loaded = true;
			$this->onLoad();
			if($this->filter) {
				$this->filter->load();
			}
		}


		/**
		 * read view state from session
		 *
		 * @param  array	&$viewState	session data
		 *
		 * @return void
		 */
		final public function loadViewState( array &$viewState )
		{
			$this->onLoadViewState( $viewState );
			if($this->filter) {
				$this->filter->loadViewState($viewState);
			}
		}


		/**
		 * process the HTTP request array
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function requestProcessor( array &$request )
		{
			$this->onRequest( $request );
			if($this->filter) {
				$this->filter->requestProcessor($request);
			}
		}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function handlePostEvents( array &$request )
		{
			$this->onPost( $request );
		}


		/**
		 * reset filter
		 *
		 * @return void
		 */
		final public function resetFilter()
		{
			if($this->filter)
			{
				$this->filter->resetFilter();
			}
		}


		/**
		 * filter DataSet
		 *
		 * @param  DataSet	&$ds		DataSet
		 * @param  array	&$request	reqeust data
		 * @return void
		 */
		final public function filterDataSet(\System\DB\DataSet &$ds)
		{
			if($this->filter)
			{
				$this->filter->filterDataSet($ds);

				if($this->filter->submitted == true && $this->gridView->ajaxPostBack) {
					$this->gridView->needsUpdating = true;
				}
			}
		}


		/**
		 * get filter TextNode
		 *
		 * @return DomObject
		 */
		final public function getFilterDomObject()
		{
			if($this->filter)
			{
				return $this->filter->getDomObject($this->getRequestData());
			}
			else
			{
				return new \System\XML\DomObject();
			}
		}


		/**
		 * write view state to session
		 *
		 * @param  array	&$viewState	session data
		 * @return void
		 */
		final public function saveViewState( array &$viewState )
		{
			$this->onSaveViewState( $viewState );
			if($this->filter) {
				$this->filter->saveViewState($viewState);
			}
		}


		/**
		 * handle render events
		 *
		 * @return void
		 */
		final public function render()
		{
			$this->onRender();
		}


		/**
		 * Event called when view state is loaded
		 *
		 * @param  array	&$viewState	session data
		 * @return void
		 */
		protected function onLoadViewState( array &$viewState ) {}


		/**
		 * handle load events
		 *
		 * @return void
		 */
		protected function onLoad() {}


		/**
		 * handle request events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onRequest( &$request ) {}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onPost( &$request ) {}


		/**
		 * handle render events
		 *
		 * @return void
		 */
		protected function onRender() {}


		/**
		 * Event called when view state is written
		 *
		 * @param  array	&$viewState	session data
		 *
		 * @return void
		 */
		protected function onSaveViewState( array &$viewState ) {}


		/**
		 * getName
		 * @param string $index
		 * @return string new index
		 * @ignore
		 */
		private function getName($index)
		{
			if($index=='DataField') return 'dataField';
			elseif($index=='Header-Text') return 'headerText';
			elseif($index=='Item-Text') return 'itemText';
			elseif($index=='Footer-Text') return 'footerText';
			elseif($index=='Classname') return 'className';
			else {throw new \Exception("{$index} is not a valid property");}
		}


		/**
		 * creates string of all vars currently in the request object seperated by an '&'
		 * used to preserve application state
		 *
		 * @return string	string of variables
		 */
		final protected function getRequestData()
		{
			$queryString = '';

			// get current variables
			$vars = array_keys( \System\Web\HTTPRequest::$request );

			// loop through request variables
			for( $i=0, $count=count( $vars ); $i < $count; $i++ )
			{
				$data = '';
				if( is_array( \System\Web\HTTPRequest::$request[$vars[$i]] ))
				{
					foreach( \System\Web\HTTPRequest::$request[$vars[$i]] as $arr )
					{
						if( $data )
						{
							$data .= '&' . $vars[$i] . '[]=' . $arr;
						}
						else
						{
							$data .= $vars[$i] . '[]=' . $arr;
						}
					}
				}
				else
				{
					if($vars[$i] == \Rum::config()->requestParameter)
					{
						$data = $vars[$i] . '=' . \System\Web\HTTPRequest::$request[$vars[$i]];
					}
					else
					{
						$data = $vars[$i] . '=' . \rawurlencode(\System\Web\HTTPRequest::$request[$vars[$i]]);
					}
				}

				if( $queryString )
				{
					$queryString .= '&' . $data;
				}
				else
				{
					$queryString .= $data;
				}
			}

			return $queryString;
		}
	}
?>