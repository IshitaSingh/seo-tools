<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Page Control
	 *
	 * @property string $contentType Document content-type
	 * @property string $charset Document character set
	 * @property int $contentExpires Cache expiration in seconds, default is 0 (no-cache)
	 * @property string $template Document template
	 * @property array $parameters Document parameters
	 * @property MasterView $master Document master page
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class View extends WebControlBase
	{
		/**
		 * specifies content-Type, default is text/plain
		 * @var string
		 */
		protected $contentType					= 'text/plain';

		/**
		 * specifies charset, default is utf-8
		 * @var string
		 */
		protected $charset						= 'utf-8';

		/**
		 * specifies content cache expiration in seconds, default is 0 (no-cache)
		 * @var int
		 */
		protected $contentExpires				= 0;

		/**
		 * specifies view template
		 * @var string
		 */
		protected $template					= '';

		/**
		 * array of parameters
		 * @var array
		 */
		private $_parameters					= array();

		/**
		 * array of headers
		 * @var array
		 */
		private $_headers					= array();

		/**
		 * specifies stream data
		 * @var string
		 */
		private $_data						= '';

		/**
		 * master page view object
		 * @var MasterView
		 */
		private $_master					= null;

		/**
		 * specifies if view is locked
		 * @var bool
		 */
		private $_locked					= false;


		/**
		 * sets the controlId and prepares the control attributes
		 *
		 * @param  string   $controlId  Control Id
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct( $controlId );

			// set charset
			$this->charset = \System\Web\WebApplicationBase::getInstance()->charset;
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
			if( $field === 'contentType' )
			{
				return $this->contentType;
			}
			elseif( $field === 'charset' )
			{
				return $this->charset;
			}
			elseif( $field === 'parameters' )
			{
				return $this->_parameters;
			}
			elseif( $field === 'template' )
			{
				return $this->template;
			}
			elseif( $field === 'master' )
			{
				return $this->_master;
			}
			else
			{
				return parent::__get( $field );
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
		public function __set( $field, $value )
		{
			if( $field === 'contentType' )
			{
				$this->contentType = (string)$value;
			}
			elseif( $field === 'charset' )
			{
				$this->charset = (string)$value;
			}
			elseif( $field === 'template' )
			{
				$this->template = (string)$value;
			}
			elseif( $field === 'attributes' )
			{
				throw new \System\Base\BadMemberCallException("call to readonly property attributes in ".get_class($this));
			}
			else
			{
				parent::__set($field,$value);
			}
		}


		/**
		 * adds child control to collection
		 *
		 * @param  WebControlBase	&$control		instance of a WebControl
		 * @return void
		 */
		final public function add( WebControlBase $control )
		{
			return parent::addControl($control);
		}


		/**
		 * assign template variables to the view
		 *
		 * @param   string		$field		var name
		 * @param   mixed		$value		var value
		 * @return  void
		 */
		public function assign( $field, $value )
		{
			if(!$this->_locked)
			{
				if(!isset($this->_parameters[$field]))
				{
					if(is_int($value))
					{
						$this->_parameters[$field] = (int)$value;
					}
					elseif(is_float($value))
					{
						$this->_parameters[$field] = (float)$value;
					}
					elseif(is_bool($value))
					{
						$this->_parameters[$field] = (bool)$value;
					}
					elseif(is_string($value))
					{
						$this->_parameters[$field] = (string)$value;
					}
					elseif(is_array($value))
					{
						$this->_parameters[$field] = (array)$value;
					}
					elseif($value instanceof \System\Base\IBindable)
					{
						$this->_parameters[$field] = $value->toArray();
					}
					elseif($value instanceof \ArrayAccess)
					{
						$this->_parameters[$field] = $value;
					}
					elseif($value instanceof \Iterator)
					{
						$this->_parameters[$field] = $value;
					}
					else
					{
						throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::assign() must be one of type bool, string, int, float, array, or implement the \\ArrayAccess and \\Iterator interface");
					}
				}
				else
				{
					throw new \System\Base\InvalidOperationException("cannot assign property {$field}, property has alreay been assigned");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot modify Page, Page alreay rendered");
			}
		}


		/**
		 * assign master view to this view
		 *
		 * @param   MasterView		$master		master Page
		 * @return  void
		 */
		public function setMaster( MasterView $master )
		{
			if(!$this->_locked)
			{
				if($this->_master === null)
				{
					$this->_master = $master;
					$master->setParent($this);
				}
				else
				{
					$master->setMaster($this->_master);
					$this->_master = $master;
					$master->setParent($this);

				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot modify Page, Page alreay rendered");
			}
		}


		/**
		 * set data
		 *
		 * @param   string		$data			raw data
		 * @return  void
		 */
		public function setData( $data )
		{
			if(!$this->_locked)
			{
				$this->_data = (string) $data;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot modify Page, Page alreay rendered");
			}
		}


		/**
		 * add header
		 *
		 * @param   string		$header		header to add
		 * @return  void
		 */
		public function addHeader( $header )
		{
			if(!$this->_locked)
			{
				array_push( $this->_headers, (string) $header );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot modify Page, Page alreay rendered");
			}
		}


		/**
		 * returns a DomObject for rendering
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * returns an html string
		 *
		 * @param   array		$args			widget parameters
		 * @return  string
		 */
		public function fetch( array $args = array() )
		{
			$output = '';

			if( $this->_data )
			{
				$output = (string) $this->_data;
			}
			elseif( $this->template )
			{
				$output = (string) $this->fetchTemplate();
			}

			if(!is_null($this->_master))
			{
				return str_replace($this->_master->getContentAreaIdString(), $output, $this->_master->fetch($args));
			}
			else
			{
				return $output;
			}
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
			// lock object
			$this->_locked = true;

			// send headers
			$this->sendHeaders();

			// render
			parent::render( $args );
		}


		/**
		 * Event called when control is initiated
		 *
		 * @return void
		 */
		protected function onInit()
        {
            if(!is_null($this->master))
            {
                $this->master->initMasterView();
            }
        }


		/**
		 * Event called when page and all controls are loaded
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			if(!is_null($this->master))
			{
				$this->master->loadMasterView();
			}
		}


		/**
		 * Event called when request is processed
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			if(!is_null($this->master))
			{
				$this->master->masterViewRequestProcessor($request);
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
            if(!is_null($this->master))
            {
                $this->master->handleMasterViewPostEvents( $request );
            }
        }


		/**
		 * send headers
		 *
		 * @return  void
		 */
		protected function sendHeaders()
		{
			\System\Web\HTTPResponse::addHeader("Content-type: {$this->contentType}; charset={$this->charset};");
			\System\Web\HTTPResponse::addHeader("Expires: " . gmdate("D, d M Y H:i:s", time() + $this->contentExpires) . " GMT");
			\System\Web\HTTPResponse::addHeader("Cache-Control: max-age={$this->contentExpires}, must-revalidate"); 

			foreach( $this->_headers as $header )
			{
				\System\Web\HTTPResponse::addHeader($header);
			}
		}


		/**
		 * return template
		 *
		 * @return  void
		 */
		protected function fetchTemplate()
		{
			ob_start();

			// convert params to vars
			$_18E7AF = array_keys( $this->_parameters );
			$_070666 = array_values( $this->_parameters );

			$_8C2D80 = sizeof( $this->_parameters );
			for( $i=0; $i < $_8C2D80; $i++ ) {
				${$_18E7AF[$i]} =& $_070666[$i];
			}

			// include template file
			if( !include( (string) $this->template )) {
				throw new \System\Base\InvalidOperationException( 'The Template File: (' . (string) $this->template . ') was not found' );
			}

			return ob_get_clean();
		}
	}
?>