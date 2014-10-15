<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web;


	/**
	 * This class handles all requests for a specific uri.
	 *
	 * @property string $controllerId Specifies the controller id
	 * @property int $outputCache Specifies how long to cache page output in seconds, 0 disables caching
	 * @property bool $requireSSL Specifies whether SSL is required for this page
	 * @property array $allowRoles Specifies the roles that have been granted access this page
	 * @property array $denyRoles Specifies the roles that have been denied access this page
	 * @property EventCollection $events event collection
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	abstract class ControllerBase extends \System\Base\Object
	{
		/**
		 * Specifies the controller id
		 * @var string
		 */
		protected $controllerId			= '';

		/**
		 * Specifies how long to cache page output in seconds, 0 disables caching
		 * @var int
		 */
		protected $outputCache			= 0;

		/**
		 * Specifies whether the controller requires an SSL connection
		 * @var bool
		 */
		protected $requireSSL			= false;

		/**
		 * Specifies the roles that have been granted access this page
		 * @var array
		 */
		protected $allowRoles			= array();

		/**
		 * Specifies the roles that have been denied access this page
		 * @var array
		 */
		protected $denyRoles			= array();


		/**
		 * Constructor
		 *
		 * @param   string		$controllerId	Controller Id
		 * @return  void
		 */
		final public function __construct( $controllerId )
		{
			$this->controllerId = (string)$controllerId;

			if( $this->requireSSL && \Rum::config()->protocol <> 'https' && !isset($GLOBALS["__DISABLE_HEADER_REDIRECTS__"]))
			{
				// redirect to secure server (forward session)
				$url = 'https://' . __NAME__ . (__SSL_PORT__<>443?':'.__SSL_PORT__:'') . \System\Web\WebApplicationBase::getInstance()->getPageURI('', array( \System\Web\WebApplicationBase::getInstance()->session->sessionName => \System\Web\WebApplicationBase::getInstance()->session->sessionId ));

				// write and close session
				\System\Web\WebApplicationBase::getInstance()->session->close();
				HTTPResponse::redirect($url);
			}

			$this->onLoad();
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
			if( $field === 'controllerId' )
			{
				return (string)$this->controllerId;
			}
			elseif( $field === 'outputCache' )
			{
				return (int)$this->outputCache;
			}
			elseif( $field === 'allowRoles' )
			{
				return $this->allowRoles;
			}
			elseif( $field === 'denyRoles' )
			{
				return $this->denyRoles;
			}
			else
			{
				return parent::__get($field);
			}
		}


		/**
		 * Event called when object created
		 *
		 * @return  void
		 */
		protected function onLoad()
		{
			
		}


		/**
		 * this method return an array of roles that have been granted access
		 *
		 * @return  array
		 */
		public function getRoles()
		{
			return $this->allowRoles;
		}


		/**
		 * this method will generate and return a cache id
		 *
		 * @param   HTTPRequest		&$request	HTTPRequest object
		 * @return  string							cache id
		 */
		public function getCacheId()
		{
			return '?agent=' . \substr($_SERVER['HTTP_USER_AGENT'], 0, 16) . '&' . \System\Web\HTTPRequest::getQueryString();
		}


		/**
		 * return view component for rendering
		 *
		 * @param   HTTPRequest		&$request	HTTPRequest object
		 * @return  View			view control
		 */
		abstract public function getView( \System\Web\HTTPRequest &$request );
	}
?>