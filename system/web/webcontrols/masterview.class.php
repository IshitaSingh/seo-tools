<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a MasterView Control
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class MasterView extends View
	{
		/**
		 * specifies content id string
		 * @var string
		 */
		private $_contentId = '';


		/**
		 * Constructor
		 *
		 * sets the controlId and prepares the control attributes
		 *
		 * @param  string   $controlId  Control Id
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct($controlId);

			$this->_contentId = $this->controlId . time();
			$this->template = \Rum::config()->templates . '/' . $this->controlId . __TEMPLATE_EXTENSION__;

			// event handling
			$this->events->add(new \System\Web\Events\MasterViewInitEvent());
			$this->events->add(new \System\Web\Events\MasterViewLoadEvent());

			$onInitMethod = 'on' . ucwords( $this->controlId ) . 'Init';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onInitMethod))
			{
				$this->events->registerEventHandler(new \System\Web\Events\MasterViewInitEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onInitMethod));
			}

			$onLoadMethod = 'on' . ucwords( $this->controlId ) . 'Load';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onLoadMethod))
			{
				$this->events->registerEventHandler(new \System\Web\Events\MasterViewLoadEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onLoadMethod));
			}
		}


		/**
		 * render content
		 *
		 * @return  void
		 */
		public function content()
		{
			\System\Web\HTTPResponse::write( $this->_contentId );
		}


		/**
		 * get content area id string
		 *
		 * @return  string
		 */
		public function getContentAreaIdString()
		{
			return $this->_contentId;
		}


		/**
		 * called when control is initiated
		 *
		 * @return void
		 */
		final public function initMasterView()
		{
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
                $this->controls->itemAt( $i )->init();
			}

			$this->onInit();

			$this->events->raise(new \System\Web\Events\MasterViewInitEvent(), $this);
		}


		/**
		 * read view state from session
		 *
		 * @param  array	&$viewState	session data
		 * @return void
		 */
		final public function loadMasterViewViewState( array &$viewState )
		{
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
                $this->controls->itemAt( $i )->loadViewState( $viewState );
			}

			if($this->master)
			{
				$this->master->loadMasterViewViewState( $viewState );
			}
		}


		/**
		 * called when all controls are loaded
		 *
		 * @return void
		 */
		final public function loadMasterView()
		{
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
                $this->controls->itemAt( $i )->load();
			}

			$this->onLoad();

			$this->events->raise(new \System\Web\Events\MasterViewLoadEvent(), $this);
		}


		/**
		 * process the HTTP request array
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function masterViewRequestProcessor( array &$request )
		{
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
                $this->controls->itemAt( $i )->requestProcessor( $request );
			}

			$this->onRequest( $request );
		}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function handleMasterViewPostEvents( array &$request )
		{
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$this->controls->itemAt( $i )->handlePostEvents( $request );
			}

			$this->onPost( $request );
		}


		/**
		 * write view state to session
		 *
		 * @param  array	&$viewState	session data
		 * @return void
		 */
		final public function saveMasterViewViewState( array &$viewState )
		{
			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
                $this->controls->itemAt( $i )->saveViewState($viewState);
			}

			if($this->master)
			{
				$this->master->saveMasterViewViewState( $viewState );
			}
		}
	}
?>