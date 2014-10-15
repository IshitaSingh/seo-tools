<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Provides a web-based server-side UI to manage the HTML that ends up on client machine
	 * by abstracting away the presentation logic and data access layer
	 *
	 * @property string $controlId Control Id
	 * @property bool $enableViewState Specifies whether to enable view state preservation
	 * @property bool $visible Specifies whether control is visible
	 * @property bool $escapeOutput Specifies whether to escape the output
	 * @property WebControlAttributeCollection $attributes Collection of attributes
	 * @property WebControlCollection $controls Collection of child controls
	 * @property WebControlBase $parent parent control
	 * @property DataSet $dataSource Reference to data-source
	 * @property mixed $value Control value
	 * @property bool $needsUpdating Specifies if control needs to be updated
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	abstract class WebControlBase extends \System\Base\Object
	{
		/**
		 * specifies whether the server control persists its view state, Default is false
		 * @var bool
		 */
		protected $enableViewState		= false;

		/**
		 * determines whether the control is visible, Default is true
		 * @var bool
		 */
		protected $visible				= true;

		/**
		 * determines whether to escape the output
		 * @var bool
		 */
		protected $escapeOutput			= true;

		/**
		 * specifies the ajax callback uri (optional)
		 * @var string
		 */
		protected $ajaxCallback			= '';

		/**
		 * collection of arbitrary attributes (for rendering only)
		 * @var WebControlAttributeCollection
		 */
		protected $attributes			= null;

		/**
		 * collection of child controls
		 * @var WebControlCollection
		 */
		protected $controls				= null;

		/**
		 * instance of the datasource
		 * @var DataSet
		 */
		protected $dataSource			= null;

		/**
		 * specifies if control needs to be updated
		 * @var bool
		 */
		protected $needsUpdating		= false;

		/**
		 * Id of the control
		 * @var string
		 */
		private $_controlId				= '';

		/**
		 * reference to the parent control
		 * @var WebControlBase
		 */
		private $_parent				= null;

		/**
		 * specifies whether control can be assigned children
		 * @var bool
		 */
		private $_locked				= false;


		/**
		 * sets the controlId and prepares the control attributes
		 *
		 * @param  string   $controlId  Control Id
		 * @return void
		 */
		public function __construct( $controlId )
		{
			// set control id
			$this->_controlId = $this->formatControlId( $controlId );

			// set collections
			$this->controls = new WebControlCollection( $this );
			$this->attributes = new WebControlAttributeCollection();

			// set ajax handlers
			$this->ajaxCallback	= $_SERVER['PHP_SELF'];

			// set viewstate
			$this->enableViewState = \Rum::config()->viewStateEnabled;

			// event handling
			$this->events->add(new \System\Web\Events\WebControlCreateEvent());
			$this->events->add(new \System\Web\Events\WebControlInitEvent());
			$this->events->add(new \System\Web\Events\WebControlLoadEvent());
			$this->events->add(new \System\Web\Events\WebControlPreRenderEvent());

			$onCreateMethod = 'on'.ucwords($this->controlId).'Create';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onCreateMethod))
			{
				$this->events->registerEventHandler(new \System\Web\Events\WebControlCreateEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onCreateMethod));
			}
			$onInitMethod = 'on'.ucwords($this->controlId).'Init';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onInitMethod))
			{
				$this->events->registerEventHandler(new \System\Web\Events\WebControlInitEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onInitMethod));
			}
			$onLoad = 'on'.ucwords($this->controlId).'Load';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onLoad))
			{
				$this->events->registerEventHandler(new \System\Web\Events\WebControlLoadEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onLoad));
			}
			$onPreRender = 'on'.ucwords($this->controlId).'PreRender';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onPreRender))
			{
				$this->events->registerEventHandler(new \System\Web\Events\WebControlPreRenderEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onPreRender));
			}

			$this->events->raise(new \System\Web\Events\WebControlCreateEvent(), $this);
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'controlId' )
			{
				return $this->_controlId;
			}
			elseif( $field === 'enableViewState' )
			{
				return $this->enableViewState;
			}
			elseif( $field === 'visible' )
			{
				return $this->visible;
			}
			elseif( $field === 'escapeOutput' )
			{
				return $this->escapeOutput;
			}
			elseif( $field === 'attributes' )
			{
				return $this->attributes;
			}
			elseif( $field === 'controls' )
			{
				return $this->controls;
			}
			elseif( $field === 'parent' )
			{
				return $this->_parent;
			}
			elseif( $field === 'dataSource' )
			{
				return $this->dataSource;
			}
			elseif( $field === 'needsUpdating' )
			{
				return $this->needsUpdating;
			}
			else
			{
				$control = $this->findControl($field);
				if( !is_null( $control ))
				{
					return $control;
				}
				else
				{
					return parent::__get($field);
				}
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'enableViewState' )
			{
				$this->setEnableViewState($value);
			}
			elseif( $field === 'visible' )
			{
				$this->visible = (bool)$value;
			}
			elseif( $field === 'escapeOutput' )
			{
				$this->escapeOutput = (bool)$value;
			}
			elseif( $field === 'dataSource' )
			{
				$this->bind($value);
			}
			elseif( $field === 'needsUpdating' )
			{
				$this->needsUpdating = (bool)$value;
			}
			elseif( $field === 'parent' )
			{
				if( !$this->parent )
				{
					if( $value instanceof \System\Web\WebControls\WebControlBase )
					{
						$this->_parent = $value;
					}
					else
					{
						throw new \System\Base\BadMemberCallException("parent must be type WebControlBase");
					}
				}
				else
				{
					throw new \System\Base\BadMemberCallException("parent is already set");
				}
			}
			else
			{
				parent::__set($field, $value);
			}
		}


		/**
		 * set parent control
		 *
		 * @param  WebControlBase	&$control		instance of a WebControl
		 * @return void
		 */
		final public function setParent( WebControlBase $control )
		{
			if( !$this->_locked )
			{
				$this->_parent = &$control;
			}
			else
			{
				throw new InvalidOperationException("cannot set parent after init()");
			}
		}


		/**
		 * bind a data source to control
		 *
		 * @param  \ArrayAccess		$ds		data source to attach
		 * @return void
		 */
		final public function bind(\System\Base\IBindable $ds)
		{
			$this->dataSource =& $ds;
			$this->onDataBind();

			// throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::bind() must implement the IBindable interface or be of the type array");
		}


		/**
		 * attach a data source to control
		 *
		 * @param  \ArrayAccess		$ds		data source to attach
		 * @return void
		 * @ignore
		 */
		final public function attachDataSource(\System\Base\IBindable $ds)
		{
			return $this->bind($ds);
		}


		/**
		 * refresh data source
		 *
		 * @return void
		 */
		final public function refreshDataSource()
		{
			$this->dataSource->refresh();
			$this->onDataBind();
		}


		/**
		 * add a child control to collection
		 *
		 * @param  WebControlBase	&$control		instance of a WebControl
		 * @return void
		 */
		final protected function addControl( WebControlBase $control )
		{
			if( !$this->_locked )
			{
				$this->controls->add( $control );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot add child controls after init()");
			}
		}


		/**
		 * remove a child control
		 *
		 * @param  WebControlBase	&$webcontrol		instance of a WebControl
		 * @return bool						true if successfull
		 */
		final public function removeControl( WebControlBase &$webcontrol )
		{
			if( !$this->_locked )
			{
				return $this->controls->remove( $webcontrol );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Cannot remove child controls after load()");
			}
		}


		/**
		 * replace a child control
		 *
		 * @param  WebControlBase	&$oldWebControl		instance of a WebControl
		 * @param  WebControlBase	&$newWebControl		instance of a WebControl
		 * @return bool									true if successfull
		 */
		final public function replaceControl( WebControlBase &$webcontrol1, WebControlBase &$webcontrol2 )
		{
			if( !$this->_locked )
			{
				return $this->controls->replace( $webcontrol1->controlId, $webcontrol2 );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Cannot replace child controls after load()");
			}
		}


		/**
		 * compares control id, returns true if control id matches
		 *
		 * @param  string	$controlId		control id
		 * @return bool						true if match
		 */
		final public function isControl( $controlId )
		{
			return (bool) ($this->formatControlId( (string)$controlId ) === $this->controlId);
		}


		/**
		 * returns reference to child control
		 *
		 * @param  string	$controlId			id of control
		 * @return WebControlBase				reference to control object
		 */
		final public function getControl( $controlId )
		{
			$index = $this->controls->indexOf( $controlId );
			if( $index > -1 )
			{
				return $this->controls->itemAt( $index );
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("control id `$controlId` is not a child of control id `{$this->controlId}`");
			}
		}


		/**
		 * returns Webtrue if control is found in Collection
		 *
		 * @param  string		$controlId			control id
		 * @return bool
		 */
		final public function hasControl( $controlId )
		{
			if( $this->controls->indexOf( $controlId ) > -1 )
			{
				return true;
			}
			else
			{
				for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
				{
					$childControl = $this->controls->itemAt( $i );

					if( $childControl->hasControl( $controlId ))
					{
						return true;
					}
				}
			}

			return false;
		}


		/**
		 * returns WebControl if control is found in Collection
		 *
		 * @param  string		$controlId			control id
		 * @return WebControlBase
		 */
		final public function findControl( $controlId )
		{
			$index = $this->controls->indexOf( $controlId );

			if( $index > -1 )
			{
				return $this->controls->itemAt($index);
			}
			else
			{
				for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
				{
					$childControl = $this->controls->itemAt( $i );

					$control = $childControl->findControl( $controlId );
					if( !is_null( $control ))
					{
						return $control;
					}
				}
			}

			return null;
		}


		/**
		 * returns 1st parent object by type
		 *
		 * @param  string				type
		 * @return WebControlBase		parent WebControl
		 */
		final public function & getParentByType( $type )
		{
			if( $this->_parent )
			{
				if( $this->_parent instanceof $type )
				{
					return $this->_parent;
				}
				else
				{
					return $this->_parent->getParentByType( $type );
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("{$type} must be a child of type ".get_class($this));
			}
		}


		/**
		 * returns string of parent id's seperated by .
		 *
		 * @return string			parent string
		 */
		final public function getParentString()
		{
			if( $this->_parent )
			{
				return $this->_parent->getParentString() . $this->_parent->controlId . '_';
			}
			else
			{
				return '';
			}
		}


		/**
		 * called when control is initiated
		 *
		 * @return void
		 */
		final public function init()
		{
			// onLoad event
			$this->onInit();
			$this->events->raise(new \System\Web\Events\WebControlInitEvent(), $this);

			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$this->controls->itemAt( $i )->init();
			}

			// Lock control
			$this->_locked = true;
		}


		/**
		 * called when all controls are loaded
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function load()
		{
			// onLoad event
			$this->onLoad();
			$this->events->raise(new \System\Web\Events\WebControlLoadEvent(), $this);

			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$this->controls->itemAt( $i )->load();
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
			if( $this->enableViewState && isset( $viewState['c'] ))
			{
				$this->onLoadViewState( $viewState );

				for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
				{
					$childControl = $this->controls->itemAt( $i );

					if( $childControl->enableViewState && isset( $viewState['c'][$childControl->controlId] ))
					{
						$childControl->loadViewState( $viewState['c'][$childControl->controlId] );
					}
				}
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
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$this->controls->itemAt( $i )->requestProcessor( $request );
			}

			$this->onRequest( $request );
		}


		/**
		 * updates the ui control asyncronously
		 *
		 * @return void
		 */
		final public function updateAjax()
		{
			if(\Rum::app()->requestHandler->isAjaxPostBack)
			{
				for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
				{
					$this->controls->itemAt( $i )->updateAjax();
				}

				if($this->needsUpdating) {
					$this->onUpdateAjax();
				}
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
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$this->controls->itemAt( $i )->handlePostEvents( $request );
			}

			$this->onPost( $request );
		}


		/**
		 * called before control is ready for rendering
		 *
		 * @return void
		 */
		final public function preRender()
		{
			// onLoad event
			$this->onPreRender();

			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$this->controls->itemAt( $i )->preRender();
			}

			$this->events->raise(new \System\Web\Events\WebControlPreRenderEvent(), $this);
		}


		/**
		 * write view state to session
		 *
		 * @param  array	&$viewState	session data
		 * @return void
		 */
		final public function saveViewState( array &$viewState )
		{
			if( $this->enableViewState )
			{
				$this->onSaveViewState( $viewState );

				$controls = array();

				for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
				{
					$childControl = $this->controls->itemAt( $i );

					if( $childControl->enableViewState )
					{
						$controls[$childControl->controlId] = array();
						$childControl->saveViewState( $controls[$childControl->controlId] );
					}
				}

				$viewState['c'] = $controls;
			}
		}


		/**
		 * returns an html string
		 *
		 * @param   array		$args			widget parameters
		 * @return  string
		 */
		public function fetch( array $args = array() )
		{
			return $this->getDomObject()->fetch( $args );
		}


		/**
		 * This method will render the control to
		 * the html canvass
		 *
		 * @param   array		$args		widget parameters
		 * @return  void
		 */
		public function render( array $args = array() )
		{
			\System\Web\HTTPResponse::write( $this->fetch( $args ));
		}


		/**
		 * creates controlId string for html
		 *
		 * @return string	html id string
		 */
		final public function getHTMLControlId()
		{
			return $this->getParentString() . $this->controlId;
		}


		/**
		 * creates controlId string for html
		 *
		 * @return string	html id string
		 * @deprecated since version number
		 * @ignore
		 */
		final public function getHTMLControlIdString()
		{
			trigger_error('WebControlBase::getHTMLControlIdString() is deprecated, use WebControlBase::getHTMLControlId() instead', E_USER_DEPRECATED);
			return $this->getHTMLControlId();
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		abstract public function getDomObject();


		/**
		 * protected events
		 */


		/**
		 * Event called when view state is loaded
		 *
		 * @param  array	&$viewState	session data
		 * @return void
		 */
		protected function onLoadViewState( array &$viewState ) {}


		/**
		 * Event called when control is initiated
		 *
		 * @return void
		 */
		protected function onInit() {}


		/**
		 * Event called when page and all controls are loaded
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onLoad() {}


		/**
		 * Event called when request is processed
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onRequest( array &$request ) {}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onPost( array &$request ) {}


		/**
		 * Event called when DataBind is called
		 *
		 * @return void
		 */
		protected function onDataBind() {}


		/**
		 * Event called when control is ready for rendering
		 *
		 * @return void
		 */
		protected function onPreRender() {}


		/**
		 * Event called when view state is written
		 *
		 * @param  array	&$viewState	session data
		 *
		 * @return void
		 */
		protected function onSaveViewState( array &$viewState ) {}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax() {}


		/**
		 * protected helpers
		 */


		/**
		 * escape
		 * 
		 * @param type $string string to escape
		 * @return string
		 */
		final protected function escape( $string )
		{
			if( $this->escapeOutput )
			{
				return \Rum::escape( $string );
			}
			else
			{
				return $string;
			}
		}


		/**
		 * returns the default DomObject
		 *
		 * @return DomObject
		 */
		final protected function createDomObject( $name )
		{
			$dom = new \System\XML\DomObject( $name );

			foreach( $this->attributes as $key => $value )
			{
				$dom->setAttribute( $key, $value );
			}

			return $dom;
		}


		/**
		 * creates string of all vars currently in the request object seperated by an '&'
		 * used to preserve application state
		 *
		 * @param   string		$queryString	query string
		 * @return  string						new URL
		 */
		final protected function getQueryString( $queryString = '' )
		{
			$queryString = $this->getRequestData() . '&' . $queryString;

			// extract parameters
			$args = array();
			foreach( explode( '&', $queryString) as $param ) {
				$data = explode( '=', $param );
				if( isset( $data[1] )) {
					$args[$data[0]] = $data[1];
				}
			}

			// extract page
			$page = '';
			if( isset( $args[\Rum::config()->requestParameter] )) {
				$page = $args[\Rum::config()->requestParameter];
				unset( $args[\Rum::config()->requestParameter] );
			}

			return \System\Web\WebApplicationBase::getInstance()->getPageURI( $page, $args );
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
						$data = $vars[$i] . '=' . \System\Web\HTTPRequest::$request[$vars[$i]];
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


		/**
		 * format controlId
		 *
		 * @param  string	$controlId		control id
		 *
		 * @return string					new control id
		 */
		private function formatControlId( $controlId )
		{
			$controlId = str_replace( ' ', '_', (string)$controlId );
			$controlId = str_replace( '\'', '_', $controlId );
			$controlId = str_replace( '"', '_', $controlId );
			$controlId = str_replace( '/', '_', $controlId );
			$controlId = str_replace( '\\', '_', $controlId );
			$controlId = str_replace( '.', '_', $controlId );

			return $controlId;
		}


		/**
		 * set enableViewState on all child controls
		 *
		 * @param  bool $enableViewState viewstate state
		 * @return void
		 */
		private function setEnableViewState($enableViewState = true)
		{
			$this->enableViewState = (bool)$enableViewState;
			if($this->enableViewState && $this->parent)
			{
				$this->parent->setEnableViewState($enableViewState);
			}
		}
	}
?>