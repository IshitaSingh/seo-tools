<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;

	/**
	 * name of hidden field
	 * @var string
	 */
	const GOTCHAFIELD = 'subject';


	/**
	 * Represents a Form
	 *
	 * @property string $action form action
	 * @property string $method form submit method
	 * @property string $encodeType form encoding type
	 * @property string $forward controller to forward to
	 * @property bool $ajaxPostBack specifies whether to perform ajax postback, Default is false
	 * @property string $honeyPot specifies the content of the honeypot field
	 * @property string $submitted specifies if form was submitted
	 * @property RequestParameterCollection $parameters form parameters
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class Form extends WebControlBase
	{
		/**
		 * Fieldset legend, Default is none
		 * @var string
		 * @ignore
		 */
		protected $legend				= '';

		/**
		 * URL to send form data, Default is self
		 * @var string
		 */
		protected $action				= '';

		/**
		 * method to send form data, Default is 'POST'
		 * @var string
		 */
		protected $method				= 'POST';

		/**
		 * Form encoding type
		 * @var string
		 */
		protected $encodeType			= 'application/x-www-form-urlencoded';

		/**
		 * controller to forward to, Default is the current controller
		 * @var string
		 */
		protected $forward				= '';

		/**
		 * turn on or off ajax post backs
		 * @var bool
		 */
		protected $ajaxPostBack			= false;

		/**
		 * specifies whether to check for hidden field before processing request
		 * @var bool
		 */
		protected $honeyPot			= false;

		/**
		 * set if the form was submitted
		 * @var bool
		 */
		protected $submitted			= false;

		/**
		 * array of variables to post on submit
		 * @var StringDictionary
		 */
		protected $parameters			= null;

		/**
		 * specify javascript to execute on form submit
		 * @var string
		 */
		private $_onsubmit				= '';


		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 *
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct( $controlId );

			$this->action = \Rum::config()->uri . '/';
			$this->forward = \System\Web\WebApplicationBase::getInstance()->currentPage;
			$this->parameters = array();

			// event handling
			$this->events->add(new \System\Web\Events\FormPostEvent());
			$this->events->add(new \System\Web\Events\FormAjaxPostEvent());

			$onPostMethod = 'on' . ucwords( $this->controlId ) . 'Post';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onPostMethod))
			{
				$this->events->registerEventHandler(new \System\Web\Events\FormPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onPostMethod));
			}

			$onAjaxPostMethod = 'on' . ucwords( $this->controlId ) . 'AjaxPost';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onAjaxPostMethod))
			{
				$this->ajaxPostBack = true;
				$this->events->registerEventHandler(new \System\Web\Events\FormAjaxPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onAjaxPostMethod));
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
			if( $field === 'legend' )
			{
				trigger_error("Form::legend is deprecated", E_USER_DEPRECATED);
				$this->legend = (string)$value;
			}
			elseif( $field === 'action' )
			{
				$this->action = (string)$value;
			}
			elseif( $field === 'method' )
			{
				if( strtolower( $value === 'get' ) ||
					strtolower( $value === 'put' ) ||
					strtolower( $value === 'post' ) ||
					strtolower( $value === 'delete' ))
				{
					$this->method = (string)$value;
				}
				else
				{
					throw new \System\Base\TypeMismatchException(get_class($this)."::method must be a string of get put post or delete");
				}
			}
			elseif( $field === 'encodeType' )
			{
				$this->encodeType = (string)$value;
			}
			elseif( $field === 'forward' )
			{
				$this->forward = (string)$value;
			}
			elseif( $field === 'ajaxPostBack' )
			{
				$this->setAjaxPostBack($value);
			}
			elseif( $field === 'ajaxValidation' )
			{
				trigger_error("Form::ajaxValidation is deprecated, use ValidationMessage instead", E_USER_DEPRECATED);
			}
			elseif( $field === 'autoFocus' )
			{
				trigger_error("Form::autoFocus is deprecated, use InputBase::autoFocus instead", E_USER_DEPRECATED);
			}
			elseif( $field === 'hiddenField' )
			{
				trigger_error("Form::hiddenField is deprecated, use Form::honeyPot instead", E_USER_DEPRECATED);
				$this->honeyPot = (string)$value;
			}
			elseif( $field === 'honeyPot' )
			{
				$this->honeyPot = (string)$value;
			}
			elseif( $field === 'onPost' )
			{
				trigger_error("Form::onPost is deprecated", E_USER_DEPRECATED);
				$this->onPost = (string)$value;
			}
			else
			{
				parent::__set( $field, $value );
			}
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
			if( $field === 'legend' )
			{
				return $this->legend;
			}
			elseif( $field === 'action' )
			{
				return $this->action;
			}
			elseif( $field === 'method' )
			{
				return $this->method;
			}
			elseif( $field === 'encodeType' )
			{
				return $this->encodeType;
			}
			elseif( $field === 'forward' )
			{
				return $this->forward;
			}
			elseif( $field === 'autoFocus' )
			{
				trigger_error("Form::autoFocus is deprecated, use InputBase::autoFocus instead", E_USER_DEPRECATED);
				return false;
			}
			elseif( $field === 'ajaxPostBack' )
			{
				return $this->ajaxPostBack;
			}
			elseif( $field === 'honeyPot' )
			{
				return $this->honeyPot;
			}
			elseif( $field === 'onPost' )
			{
				trigger_error("Form::onPost is deprecated", E_USER_DEPRECATED);
				return $this->onPost;
			}
			elseif( $field === 'submitted' )
			{
				return $this->submitted;
			}
			elseif( $field === 'submit' )
			{
				if(null===$this->findControl('submit')) {
					trigger_error("ActiveRecordBase::form()->submit is deprecated, no longer generates a submit button", E_USER_DEPRECATED);
					try {
						$this->add(new Button('submit'));
					}
					catch(\Exception $e) {
						throw new \System\Base\InvalidOperationException("ActiveRecordBase::form()->submit is no longer generated");
					}
				}
				return $this->findControl('submit');
			}
			else
			{
				return parent::__get($field);
			}
		}


		/**
		 * adds child control to collection
		 *
		 * @param  DataFieldControlBase		&$control		instance of an DataFieldControlBase
		 * @return void
		 */
		final public function add( WebControlBase $control )
		{
			return parent::addControl($control);
		}


		/**
		 * add parameter
		 *
		 * @param  string $key key
		 * @param  string $value value
		 * @return void
		 */
		public function addParameter( $key, $value )
		{
			$this->parameters[(string)$key] = (string)$value;
		}


		/**
		 * update DataSet using values from child controls
		 *
		 * @return void
		 */
		public function save()
		{
			if( $this->dataSource )
			{
				// loop through child controls
				foreach( $this->controls as $childControl )
				{
					if( $childControl instanceof DataFieldControlBase || $childControl instanceof Fieldset ) // TODO: Rem backwards compatability code
					{
						$childControl->fillDataSource( $this->dataSource );
					}
				}

				$this->dataSource->save();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Form::save() called with null dataSource");
			}
		}


		/**
		 * validate all controls in Form object
		 *
		 * @param  string $errMsg error message
		 * @return bool
		 */
		public function validate(&$errMsg = '')
		{
			$valid = true;
			for($i = 0; $i < $this->controls->count; $i++)
			{
				if( $this->controls[$i] instanceof InputBase || $this->controls[$i] instanceof Fieldset ) // TODO: Rem backwards compatability code
				{
					if( !$this->controls[$i]->validate( $errMsg ))
					{
						$valid = false;
					}
				}
			}

			return $valid;
		}


		/**
		 * renders form open tag
		 *
		 * @param   array	$args	attribute parameters
		 * @return void
		 * @ignore
		 */
		public function start( $args = array() )
		{
			trigger_error("Form::start() is deprecated, use Form::begin() instead", E_USER_DEPRECATED);
			$this->begin( $args );
		}


		/**
		 * renders form open tag
		 *
		 * @param   array	$args	attribute parameters
		 * @return void
		 */
		public function begin( $args = array() )
		{
			$result = $this->getFormDomObject()->fetch( $args );
			\System\Web\HTTPResponse::write( str_replace( '</form>', '', $result ));
		}


		/**
		 * renders form close tag
		 *
		 * @return void
		 */
		public function end()
		{
			\System\Web\HTTPResponse::write( '</form>' );
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			trigger_error("Form::render() is deprecated, use Form::begin() instead", E_USER_DEPRECATED);
			$form = $this->getFormDomObject();
			$buttons = array();
			$fieldset = '';
			$dl = '';

			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$childControl = $this->controls->itemAt( $i );

				if( $childControl instanceof Fieldset )
				{
					$form->innerHtml .= $childControl->fetch();
				}
				elseif( $childControl instanceof Button )
				{
					$buttons[] = $childControl;
				}
				else
				{
					// create list item
//					if( !$childControl->visible )
//					{
//						$dt = '<dt style="display:none;">';
//						$dd = '<dd style="display:none;">';
//					}
//					else
//					{
//						$dt = '<dt>';
//						$dd = '<dd>';
//					}

					$dt = '<dt>';
					$dd = '<dd>';

					// create label
					$dt .= '<label for="'.$childControl->getHTMLControlId().'">' . $childControl->label . '</label>';

					// Get input control
					$dd .= $childControl->fetch();

				  	// create validation message span tag
					$errMsg = '';
					if( $this->submitted )
					{
						$childControl->validate($errMsg);
					}

					$dd .= $childControl->fetchError(array('class'=>'warning'));

					$dl .= $dt . '</dt>';
					$dl .= $dd . '</dd>';
				}
			}

			$dt = '<dt>';
			$dd = '<dd>';

			foreach( $buttons as $button )
			{
				$dd .= $button->fetch();
			}

			$dl .= $dt . '</dt>';
			$dl .= $dd . '</dd>';

			if($dl)
			{
				$fieldset .= '<fieldset>';
				$fieldset .= '<legend><span>' . $this->legend . '</span></legend>';
				$fieldset .= '<dl>';
				$fieldset .= $dl;
				$fieldset .= '</dl>';
				$fieldset .= '</fieldset>';
			}

			$form->innerHtml .= $fieldset;

			return $form;
		}


		/**
		 * returns form as widget
		 *
		 * @return void
		 */
		protected function getFormDomObject()
		{
			$form = $this->createDomObject( 'form' );

			$form->setAttribute( 'id', $this->getHTMLControlId() );
			$form->setAttribute( 'action', $this->action );
			$form->setAttribute( 'method', strtolower( $this->method ));
			$form->setAttribute( 'enctype', $this->encodeType );
//			$form->setAttribute( 'class', ' form' );

			if( $this->_onsubmit )
			{
				$form->setAttribute( 'onsubmit', $this->_onsubmit );
			}

			// public to check if form has been submitted
			$this->parameters[$this->getHTMLControlId() . '__submit'] = '1';

			// send session id as http var
			if( \Rum::config()->cookielessSession ) {
				$this->parameters['PHPSESSID'] = session_id();
			}

			// get current variables
			$vars = array_keys( \System\Web\HTTPRequest::$request );

			for( $x=0, $xCount=count( $vars ); $x < $xCount; $x++ )
			{
				// loop through objects
				for( $b=true, $y=0, $yCount=$this->controls->count; $y < $yCount; $y++ )
				{
					// get control
					$obj = $this->controls->itemAt($y);

					// if request public = object name, unset found flag
					if( $obj->getHTMLControlId() === $vars[$x] )
					{
						// found instance
						$b=false;
					}
				}

				// if not found in object
				if( $b && $vars[$x] != $this->getHTMLControlId() )
				{
					$this->parameters[$vars[$x]] = \System\Web\HTTPRequest::$request[$vars[$x]];
				}
			}

			$hiddenElements = '';

			// set forward controller
			if( isset( $this->parameters[\Rum::config()->requestParameter] ))
			{
				unset( $this->parameters[\Rum::config()->requestParameter] );
			}

			$this->parameters[\Rum::config()->requestParameter] = $this->forward;

			foreach( $this->parameters as $field => $value )
			{
				if( !empty( $value ))
				{
					if( is_array( $value ))
					{
						foreach($value as $index)
						{
							$hiddenElements .= "<input type=\"hidden\" name=\"{$field}[]\" value=\"".$this->escape($index)."\" />";
						}
					}
					else
					{
						$hiddenElements .= "<input type=\"hidden\" name=\"{$field}\" value=\"".$this->escape($value)."\" />";
					}
				}
			}

			if( $this->honeyPot )
			{
				$hiddenElements .= "<div class=\"hp\"><input type=\"text\" name=\"".GOTCHAFIELD."\" /></div>";
			}

			$form->innerHtml = $hiddenElements;

			return $form;
		}


		/**
		 * called when control is loaded
		 *
		 * @return bool			true if successfull
		 */
		protected function onLoad()
		{
			parent::onLoad();

			// perform ajax request
			if( $this->ajaxPostBack )
			{
				$this->_onsubmit = "return Rum.submit(this);";
			}
		}


		/**
		 * process the HTTP request array
		 *
		 * @param  array	&$request	request array
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			if( isset( $request[ $this->getHTMLControlId() . '__submit'] ))
			{
				if( $this->honeyPot )
				{
					if( isset( $request[GOTCHAFIELD] )?!$request[GOTCHAFIELD]:false )
					{
						$this->submitted = true;
					}
					else
					{
						\System\Base\ApplicationBase::getInstance()->logger->log( 'spam submission attack detected from IP ' . $_SERVER['REMOTE_ADDR'], 'security' );
					}
				}
				else
				{
					$this->submitted = true;
				}

				unset( $request[ $this->getHTMLControlId() . '__submit'] );
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
			if( $this->submitted )
			{
				if( $this->ajaxPostBack )
				{
					$this->events->raise(new \System\Web\Events\FormAjaxPostEvent(), $this, $request);
				}
				else
				{
					$this->events->raise(new \System\Web\Events\FormPostEvent(), $this, $request);
				}
			}
		}


		/**
		 * bind all child controls in form to datasource
		 *
		 * @return void
		 */
		protected function onDataBind()
		{
			// loop through input controls
			foreach( $this->controls as $childControl )
			{
				if( $childControl instanceof DataFieldControlBase || $childControl instanceof Fieldset ) // TODO: Remove backwards compatibility code
				{
					$childControl->readDataSource( $this->dataSource );
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
			// loop through input controls
			foreach( $this->controls as $childControl )
			{
				$childControl->needsUpdating = true;
			}
		}


		/**
		 * set postback state on all child controls
		 *
		 * @param  bool $ajaxPostBack postback state
		 * @return void
		 */
		private function setAjaxPostBack($ajaxPostBack = true)
		{
			$this->ajaxPostBack = (bool)$ajaxPostBack;
			foreach( $this->controls as $childControl )
			{
				if( $childControl instanceof InputBase || $childControl instanceof Fieldset ) // TODO: rem backwards compatability
				{
					$childControl->ajaxPostBack = (bool)$ajaxPostBack;
				}
			}
		}


		/**
		 * set ajax validation state on all child controls
		 *
		 * @param  bool $ajaxValidation ajax validation state
		 * @return void
		 * /
		private function setAjaxValidation($ajaxValidation = true)
		{
			$this->ajaxValidation = (bool)$ajaxValidation;
			foreach( $this->controls as $childControl )
			{
				if( $childControl instanceof InputBase || $childControl instanceof Fieldset ) // TODO: rem backwards compatability
				{
					$childControl->ajaxValidation = (bool)$ajaxValidation;
				}
			}
		}
		*/
	}
?>