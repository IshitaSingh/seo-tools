<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web;
	use System\Base\ApplicationBase;


	/**
	 * This class represents the base web application.  This class recieves HTTP request input
	 * and delegates the request to a Controller.  Once the Controller has handled the request,
	 * this class will regain control and render the appropriate View selected by the Controller
	 * unless there are additional actions to perform.
	 *
	 * @property   PageControllerBase $requestHandler Reference to the current ControllerBase
	 * @property   string $prevPage Previous page
	 * @property   string $currentPage Current page
	 * @property   string $forwardPage Page to forward to
	 * @property   string $forwardURI URI to forward to
	 * @property   array $forwardParams array of forward parameters
	 * @property   Session $session Contains the Session object
	 * @property   HTTPRequest $request Contains the HTTPRequet object
	 * @property   AppMessageCollection $messages Contains the AppMessageCollection object
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class WebApplicationBase extends ApplicationBase
	{
		/**
		 * Contains the current ControllerBase
		 * @var ControllerBase
		 */
		private $requestHandler				= null;

		/**
		 * Specifies the id of the current page
		 * @var string
		 */
		private $currentPage				= '';

		/**
		 * Specifies the id of the previous page
		 * @var string
		 */
		private $prevPage					= '';

		/**
		 * Specifies the id of the next page
		 * @var string
		 */
		private $forwardPage				= '';

		/**
		 * Specifies the id of the forward URI Resource
		 * @var string
		 */
		private $forwardURI					= '';

		/**
		 * Specifies the forward URI parameters
		 * @var array
		 */
		private $forwardParams				= array();

		/**
		 * Contains an array of runtime errors
		 * @var array
		 */
		private $warnings					= array();

		/**
		 * Contains the Session object
		 * @var Session
		 */
		private $session					= null;

		/**
		 * Contains the HTTPRequest object
		 * @var HTTPRequest
		 */
		private $request					= null;

		/**
		 * Contains the AppMessageCollection object
		 * @var AppMessageCollection
		 */
		private $messages					= null;


		/**
		 * Constructor
		 *
		 * Creates an instance of the controller and sets default action map.
		 *
		 * @return  void
		 */
		final public function __construct()
		{
			parent::__construct();

			$this->messages = new \System\Base\AppMessageCollection();
			$this->request = new \System\Web\HTTPRequest();
			$this->session = $this->getSession();

			// Event handling
			$this->events->add(new Events\WebApplicationLoadStateEvent());
			$this->events->add(new Events\WebApplicationHandleRequestEvent());
			$this->events->add(new Events\WebApplicationSaveStateEvent());

			$onLoadStateMethod = 'on'.ucwords($this->applicationId).'LoadState';
			if(\method_exists($this, $onLoadStateMethod))
			{
				$this->events->registerEventHandler(new Events\WebApplicationLoadStateEvent('\System\Base\ApplicationBase::getInstance()->' . $onLoadStateMethod));
			}
			$onHandleRequestMethod = 'on'.ucwords($this->applicationId).'HandleRequest';
			if(\method_exists($this, $onHandleRequestMethod))
			{
				$this->events->registerEventHandler(new Events\WebApplicationHandleRequestEventHandler('\System\Base\ApplicationBase::getInstance()->' . $onHandleRequestMethod));
			}
			$onSaveStateMethod = 'on'.ucwords($this->applicationId).'SaveState';
			if(\method_exists($this, $onSaveStateMethod))
			{
				$this->events->registerEventHandler(new Events\WebApplicationSaveStateEvent('\System\Base\ApplicationBase::getInstance()->' . $onSaveStateMethod));
			}
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		final public function __get( $field )
		{
			if( $field === 'requestHandler' ) {
				return $this->requestHandler;
			}
			elseif( $field === 'currentPage' ) {
				return $this->currentPage;
			}
			elseif( $field === 'forwardPage' ) {
				return $this->forwardPage;
			}
			elseif( $field === 'forwardURI' ) {
				return $this->forwardURI;
			}
			elseif( $field === 'forwardParams' ) {
				return $this->forwardParams;
			}
			elseif( $field === 'session' ) {
				return $this->session;
			}
			elseif( $field === 'request' ) {
				return $this->request;
			}
			elseif( $field === 'messages' ) {
				return $this->messages;
			}
			else {
				return parent::__get( $field );
			}
		}


		/**
		 * return the requestHandler
		 *
		 * @param   string			$controller		name of the controller
		 * @return  ControllerBase					Controller
		 */
		final public function getRequestHandler( $controller )
		{
			// reset state
			$this->currentPage = $controller;
			$this->forwardURI = null;
			$this->forwardPage = null;
			$this->forwardParams = array();

			// replace dashes with underscores
			$controller = str_replace( '-', '_', $controller );

			// get include path
			$includePath = $this->config->controllers . '/' . strtolower( $controller ) . __CONTROLLER_EXTENSION__;
			$className = $this->namespace . "\\Controllers\\" . ucwords( str_replace( '/', '\\', $controller ));

			if( !defined( \System\Base\INCLUDEPREFIX . $includePath ))
			{
				define( \System\Base\INCLUDEPREFIX . $includePath, true );

				if( !include $includePath )
				{
					\System\Web\WebApplicationBase::getInstance()->sendHTTPError(404);
				}
			}

			if( class_exists( $className ))
			{
				// Assign reference
				$this->requestHandler = new $className( $controller );
				return $this->requestHandler;
			}
			else
			{
				throw new \System\Base\InvalidOperationException( "class `{$className}` not found" );
			}
		}


		/**
		 * returns a URI based on the requested page and parameters
		 *
		 * @param   string		$page			name of page
		 * @param   array		$args			array of parameters
		 * @return  string						raw URI
		 */
		final public function getPageURI( $page = '', array $args = array() )
		{
			if( $this->config->cookielessSession )
			{
				$args['PHPSESSID'] = $this->session->sessionId;
			}
			else
			{
				unset($args['PHPSESSID']);
			}

			$uri = $this->config->uri;

			// get controller
			$page = strtolower( \urldecode( $page?str_replace( '.', '/', str_replace( '_', '-', $page ) ):\System\Web\WebApplicationBase::getInstance()->currentPage ));

			if( $this->config->rewriteURIS )
			{
				$id = __PAGE_EXTENSION__;

				if( !empty( $args['id'] ))
				{
					$id = '/' . rawurlencode($args['id']);
					unset( $args['id'] );
				}

				// build uri
				$uri .= '/' . $page . $id;
			}
			else {
				// append page to parameter list
				if( $page ) {
					$args[$this->config->requestParameter] = $page;
				}

				// build uri
				$uri .= substr( $_SERVER['SCRIPT_NAME'], strrpos( $_SERVER['SCRIPT_NAME'], '/' ));
			}

			// add parameters to query string
			$params = '';
			foreach( $args as $key => $value )
			{
				$param = '';
				if(is_array($value))
				{
					$values = '';
					foreach($value as $item)
					{
						$values[] = "{$key}[]=$item";
					}
					$param = join("&", $values);
				}
				else
				{
					$param = "{$key}=".rawurlencode($value);
				}

				if( $params )
				{
					$params .= "&{$param}";
				}
				else {
					$params .= "?{$param}";
				}
			}

			// create query string
			return $uri . $params;
		}


		/**
		 * send HTTP status message to client
		 *
		 * @param   int			$statuscode		HTTP status code
		 * @return void
		 */
		final public function sendHTTPError( $statuscode = 500 )
		{
			$response = new \System\Web\HTTPResponse(); // start output buffer

			// check if error code is mapped to controller
			if( !isset( $this->config->errors[$statuscode] ) || false === strpos($_SERVER["HTTP_ACCEPT"], 'text/html'))
			{
				$response->statusCode = $statuscode;
			}
			else
			{
				$page = $this->config->errors[$statuscode];

				$requestHandler = $this->getRequestHandler( $page );

				// Render View
				$requestHandler->getView( $this->request )->render();
			}

			\System\Web\HTTPResponse::end(); // flush and end output buffer
		}


		/**
		 * This method sets the next URI once the current controller is finished executing, the servlet will
		 * re-request the page with the requested action.  This method allows you to replace post data
		 * with keep friendly urls that can be bookmarked.
		 *
		 * @param   string				$nextPage					Name of requested page
		 * @param   array				$args						args for next action
		 * @param   ForwardMethodType	$method						forward method as constant of ForwardMethodType::URI() or ForwardMethodType::Request()
		 * @return  void
		 */
		final public function setForwardPage( $nextPage = '', array $args = array(), ForwardMethodType $method = null )
		{
			$method = $method?$method:ForwardMethodType::URI();
			$nextPage = (string)$nextPage?(string)$nextPage:$this->currentPage;
			$nextPage = str_replace( '.', '/', $nextPage );

			$this->prevPage = $this->currentPage;

			if( $method == ForwardMethodType::URI() )
			{
				$this->forwardParams = $args;
				$this->forwardURI = $nextPage;
			}
			else
			{
				foreach($args as $key=>$value)
				{
					HTTPRequest::$request[$key] = $value;
				}
				$this->forwardPage = $nextPage;
			}
		}


		/**
		 * This method clear all forward URI's
		 *
		 * @return  void
		 */
		final public function clearForwardPage()
		{
			$this->forwardURI = '';
			$this->forwardPage = '';
			$this->forwardParams = array();
		}


		/**
		 * return all controllers
		 *
		 * @param   string		$path		initial path
		 * @return  array					array of test modules
		 */
		final public function getAllControllers( $path = '' ) {

			if( !$path ) $path = \Rum::config()->controllers;

			$modules = array();
			$dir = dir( $path );

			while( false !== ( $file = $dir->read() )) {
				if( $file != '.' && $file != '..' ) {
					if( is_dir( $path . '/' . $file )) {
						$modules = array_merge( $modules, $this->getAllControllers( $path . '/' . $file ));
					}
					else {
						
						$module = str_replace( \Rum::config()->controllers . '/', '', $path . '/' . $file );
						$module = preg_replace( '^' . '(.*)$^', '\\1', $module );
						$module = preg_replace( '^.php$^', '\\1', $module );
						$modules[] = $module;
					}
				}
			}

			$dir->close();
			return $modules;
		}


		/**
		 * This method retrieves an instance of the previous action and the servlet messages.  These can then
		 * be used by the servlet to populate the view.
		 *
		 * @return  void
		 */
		final public function loadApplicationState()
		{
			if( isset( $this->session[$this->applicationId.'_configuration_messages'] ))
			{
				$this->messages = unserialize( $this->session[$this->applicationId.'_configuration_messages'] );
			}
			if( isset( $this->session[$this->applicationId.'_configuration_forward'] ))
			{
				$this->prevPage = $this->session[$this->applicationId.'_configuration_forward'];
			}

			if( $this->debug )
			{
				if( isset( $this->session[$this->applicationId.'_debug_warnings'] ))
				{
					$this->warnings = array_merge( $this->warnings, unserialize( $this->session[$this->applicationId.'_debug_warnings'] ));
				}
			}
		}


		/**
		 * This method stores an instance of the previous action and the servlet messages.  These can then
		 * be used by the servlet after a page request to populate the view.
		 *
		 * @return  void
		 */
		final public function saveApplicationState()
		{
			$this->session[$this->applicationId.'_configuration_messages'] = serialize( $this->messages );
			$this->session[$this->applicationId.'_configuration_forward'] = $this->prevPage;

			if( $this->debug )
			{
				$this->session[$this->applicationId.'_debug_warnings'] = serialize( $this->warnings );
			}
		}


		/**
		 * returns the common translator object (overrided this method in the application class)
		 *
		 * @return  ITranslator
		 */
		protected function getSession()
		{
			return new Session();
		}


		/**
		 * returns the environment
		 *
		 * @return  string
		 */
		final protected function getEnv()
		{
			$env = '';
			if(isset($_SERVER["APP_ENV"]))
			{
				$env = $_SERVER["APP_ENV"];
			}
			else
			{
				$env = '';
			}

			if($env===__DEV_ENV__ || $env===__TEST_ENV__ || !$env)
			{
				if(isset(\System\Web\HTTPRequest::$request[\Rum::config()->requestParameter])&&strpos(\System\Web\HTTPRequest::$request[\Rum::config()->requestParameter], 'dev')===0)
				{
					// kludge handle
					if(strpos(\System\Web\HTTPRequest::$request[\Rum::config()->requestParameter], 'run_')!==false ||
							\System\Web\HTTPRequest::$request["id"]==='run_all')
					{
						$env = __TEST_ENV__;
					}
					else
					{
						$env =__DEV_ENV__;
					}
	//				end kludge
				}
				elseif(isset(\System\Web\HTTPRequest::$request[\Rum::config()->requestParameter]) && strpos(\System\Web\HTTPRequest::$request[\Rum::config()->requestParameter], 'test')===0)
				{
					$env =__TEST_ENV__;
				}
			}

			return $env;
		}


		/**
		 * execute the application
		 *
		 * @return  void
		 */
		final protected function execute()
		{
			// Start session
			$this->startSession();

			// Handle Request Params
			$this->handleRequestParams(new \System\Web\HTTPRequest());

			// Load Application State
			$this->loadApplicationState();

			// Raise event
			$this->events->raise(new Events\WebApplicationLoadStateEvent(), $this);

			// Process Request
			$this->requestProcessor( new \System\Web\HTTPRequest() );

			// Save Spplication State
			$this->saveApplicationState();

			// Raise event
			$this->events->raise(new Events\WebApplicationSaveStateEvent(), $this);

			// End session
			$this->endSession();
		}


		/**
		 * Processe HTTP Request and create actions to handle business logic.  Then render the view.
		 * Can be overwridden to provide additional or alternaitive private functionality.
		 *
		 * @param   HTTPRequest		$request	HTTPRequest object
		 * @return  void
		 */
		protected function requestProcessor( \System\Web\HTTPRequest &$request )
		{
			// get controller from request
			if( isset( $request->post[$this->config->requestParameter] ))
			{
				// Get Controller based on HTTP POST page parameter
				$this->forwardPage = $request->post[$this->config->requestParameter];
			}
			elseif( isset( $request->get[$this->config->requestParameter] ))
			{
				// Get Controller based on HTTP GET page parameter
				$this->forwardPage = $request->get[$this->config->requestParameter];
			}
			else
			{
				// Get Controller based on XML configuration
				$this->forwardPage = $this->config->defaultController;
			}

			if( isset( $request->request['lang'] ))
			{
				// Get Language
				$this->lang = $request->request['lang'];
			}

			$request[$this->config->requestParameter] = strtolower( $this->forwardPage );

			while( $this->forwardPage )
			{
				// Dispatch controller
				$requestHandler = $this->getRequestHandler( $this->forwardPage );

				// raise event
				$this->events->raise(new Events\WebApplicationHandleRequestEvent(), $this);

				// Authentication/Authorization
				if(\System\Security\Authentication::authenticated())
				{
					if(\System\Security\Authentication::authorized())
					{
						$headersCacheId = '';
						$outputCacheId = '';

						// Output Caching
						if( $requestHandler->outputCache <> 0 )
						{
							$hashId = 'hash:' . $requestHandler->controllerId;

							// Output Caching Enabled
							if( \System\Web\HTTPRequest::getRequestMethod() === 'GET' )
							{
								$hash = \System\Web\WebApplicationBase::getInstance()->cache->get($hashId);

								if(!$hash)
								{
									$hash = $requestHandler->controllerId;

									if( $this->config->cacheEnabled )
									{
										\System\Web\WebApplicationBase::getInstance()->cache->put($hashId, $hash, $requestHandler->outputCache);
									}
								}

								// Generate Cache ID's
								$headersCacheId = 'output_headers:'.$hash.$requestHandler->getCacheId();
								$outputCacheId = 'output:'.$hash.$requestHandler->getCacheId();

								// Output Cache Exists
								$outputCache = \System\Web\WebApplicationBase::getInstance()->cache->get( $outputCacheId );
								if( $outputCache && !isset( $request["nocache"] ))
								{
									$response = new \System\Web\HTTPResponse();
									$headers = \System\Web\WebApplicationBase::getInstance()->cache->get($headersCacheId);

									// Send Headers
									if( $headers )
									{
										foreach( $headers as $header )
										{
											\System\Web\HTTPResponse::addHeader($header);
										}
									}

									// Send Output
									\System\Web\HTTPResponse::write( \System\Web\WebApplicationBase::getInstance()->cache->get( $outputCacheId ));

									$this->insertDebug($response, $requestHandler->outputCache );

									return;
								}
							}
							else
							{
								\System\Web\WebApplicationBase::getInstance()->cache->clear($hashId);
							}
						}

						// get View
						$view = $requestHandler->getView( $request );

						if( !$this->forwardPage )
						{
							if( $this->forwardURI )
							{
								// Forward URI (redirect)
								$this->forwardURI( $this->forwardURI, (array)$this->forwardParams );
								return;
							}

							// Render View
							$response = new \System\Web\HTTPResponse(); // start output buffer
							$view->render();

							// Output Caching Capture
							if( $requestHandler->outputCache > 0 &&
								\System\Web\HTTPRequest::getRequestMethod() === 'GET' &&
								$this->messages->count === 0 &&
								$this->config->cacheEnabled )
							{
								\System\Web\WebApplicationBase::getInstance()->cache->put( $headersCacheId, \System\Web\HTTPResponse::getResponseHeaders(), $requestHandler->outputCache );
								\System\Web\WebApplicationBase::getInstance()->cache->put( $outputCacheId, \System\Web\HTTPResponse::getResponseContent(), $requestHandler->outputCache );
							}

							// Debug
							$this->insertDebug($response);

							// Cleanup
							$this->messages->removeAll();
							$this->prevPage = '';
						}
						else
						{
							// Forward Request (redirect)
							foreach( $this->forwardParams as $key => $value )
							{
								$request[$key] = $value;
							}
							$this->forwardParams = array();
						}

						// Remove reference
						unset($this->requestHandler);
					}
					else
					{
						// Not Authorized
						WebApplicationBase::getInstance()->sendHTTPError(401);
					}
				}
				else
				{
					if( isset( $request->request["async"] ))
					{
						// Not Authenticated
						\Rum::sendHTTPError(401);
					}
					else
					{
						// Not Authenticated
						\System\Security\Authentication::redirectToLogin();
					}
				}
			}
		}


		/**
		 * event triggered by an uncaught Exception thrown in the application, can be overridden to provide error handling.
		 *
		 * @param  Exception	$e
		 *
		 * @return void
		 */
		protected function handleException(\Exception $e)
		{
			if( $this->debug )
			{
				$backtrace = $e->getTrace();

				foreach( $backtrace as $trace )
				{
					if( strstr( $trace['file'], __SYSTEM_PATH__ ) === false )
					{
						$file = $trace['file'];
						$line = $trace['line'];
						break;
					}
				}

				$source = '';
				$line = isset($line)?$line:$e->getLine();
				$file = isset($file)?$file:$e->getFile();
				$contents = file($file);
				$filename = str_replace( __ROOT__, '', $file );

				for( $i = $line > 3 ? $line - 3 : 0, $ii = 0; $ii <= 5; $ii++, $i++ )
				{
					$current_line = (string)$i.':';
					while( strlen( $current_line ) < 6 ) $current_line .= ' ';

					if( isset( $contents[$i-1] ))
					{
						$contents[$i-1] = \Rum::escape($contents[$i-1]);

						if( $i === $line )
						{
							$source .= "<span style=\"font-weight:bold;color:#AA0000\">Line $current_line{$contents[$i-1]}</span>";
						}
						else
						{
							$source .= "<span>Line $current_line{$contents[$i-1]}</span>";
						}
					}
				}

				// KLUDGE: bad form, not structured - but the only way to clear the output buffer
				@ob_clean();

				$response = new \System\Web\HTTPResponse();

				// handle exceptions on ajax requests
				if($this->requestHandler)
				{
					if($this->requestHandler instanceof PageControllerBase)
					{
						if($this->requestHandler->isAjaxPostBack)
						{
							$content = "Unhandled Exception in " . strrchr( $e->getFile(), '/' ) . "\\nRuntime Error: ".addslashes($e->getMessage())."\\n\\rDescription: An unhandled exception occurred during execution\\n\\rDetails: " . get_class($e) . ": ".addslashes($e->getMessage())."\\n\\rSource File: ".addslashes($filename)." on line: {$line}";

							\System\Web\HTTPResponse::clear();
							\System\Web\HTTPResponse::write("console.log('".(str_replace("\n", '', str_replace("\r", '', $content)))."');");
							\System\Web\HTTPResponse::write("alert('An unhandled exception occurred during execution, please check logs');");
							\System\Web\HTTPResponse::end();
						}
					}
				}

				\System\Web\HTTPResponse::addHeader( "Content-Type: text/html" );
				\System\Web\HTTPResponse::write( "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>Unhandled Exception: ".htmlentities($e->getMessage())."</title>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
<link href=\"" . htmlentities($this->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/css', 'asset'=>'debug_tools/exception.css'))) . "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
<link href=\"" . htmlentities($this->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/css', 'asset'=>'debug_tools/debug.css'))) . "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
<script src=\"" . htmlentities($this->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/javascript', 'asset'=>'debug_tools/debug.js'))) . "\" type=\"text/javascript\"></script>
</head>
<body>

<div id=\"page\">

<h1>Unhandled Exception in " . strrchr( $e->getFile(), '/' ) . "</h1>

<div id=\"details\" class=\"fail\">
<h2>Runtime Error: ".htmlentities($e->getMessage())."</h2>
<p><strong>Description:</strong> An unhandled exception occurred during execution</p>
<p><strong>Details:</strong> " . get_class($e) . ": {$e->getMessage()}</p>
<p><strong>Source File:</strong> {$filename} <strong>on line:</strong> {$line}
</div>

<pre>
<strong>Source Information:</strong>

{$source}
</pre>

<pre>
<strong>Stack Trace:</strong>\r\n\r\n");

				$this->dumpCallStack($e->getTrace());
				\System\Web\HTTPResponse::write( "
</pre>

<!--<p class=\"dump\" id=\"debug_show\"><a href=\"#debug_dump\" onclick=\"document.getElementById('debug_info').style.display='block';document.getElementById('debug_show').style.display='none';\">Show Debug Information</a></p>-->
<div style=\"display:none;\" id=\"debug_info\">");

				\System\Web\HTTPResponse::write( "
</div>

<div id=\"version\">
<p><strong>Framework Version:</strong> ".\System\Base\FRAMEWORK_VERSION_STRING."</p>
</div>

</div>

</body>
</html>" );

				\System\Web\HTTPResponse::end();
			}
			else
			{
				$this->logger->log( "Uncaught Exception: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}", 'error' );
				\System\Web\WebApplicationBase::getInstance()->sendHTTPError( 500 );
			}
		}


		/**
		 * event triggered by an error in the application, can be overridden to provide error handling.
		 *
		 * @param  string	$errno		error code
		 * @param  string	$errstr		error description
		 * @param  string	$errfile	file
		 * @param  string	$errline	line no.
		 * @return void
		 */
		protected function handleError($errno, $errstr, $errfile, $errline)
		{
			$errcode = array (
			   E_ERROR				=> "Error",
			   E_WARNING			=> "Warning",
			   E_PARSE				=> "Parsing Error",
			   E_NOTICE				=> "Notice",
			   E_RECOVERABLE_ERROR	=> "Recoverable Fatal Error",
			   E_CORE_ERROR			=> "Core Error",
			   E_CORE_WARNING		=> "Core Warning",
			   E_COMPILE_ERROR		=> "Compile Error",
			   E_COMPILE_WARNING	=> "Compile Warning",
			   E_USER_ERROR			=> "User Error",
			   E_USER_WARNING		=> "User Warning",
			   E_USER_NOTICE		=> "User Notice",
			   E_STRICT				=> "Runtime Notice"
			   );

			if( $errno & ( E_ERROR | E_RECOVERABLE_ERROR | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR ))
			{
				// throw ErrorException on fatal errors
				throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
			}
			else
			{
				if(count($this->warnings)<=__ERROR_LIMIT__+1)
				{
					$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
					array_shift( $backtrace );
					array_shift( $backtrace );

					// add error to warnings
					array_push( $this->warnings, array('errno'		=> $errno,
														'errstr'	=> $errstr,
														'errfile'	=> $errfile,
														'errline'	=> $errline,
														'backtrace'	=> $backtrace ));
				}
			}
		}


		/**
		 * Forward the HTTP request to another HTTP request (redirect)
		 *
		 * @param   string		$page			page name
		 * @param   array		$args			args
		 * @return  void
		 */
		private function forwardURI( $page, array $args = array() )
		{
			\System\Web\HTTPResponse::redirect( $this->getPageURI( $page, $args ), false );
		}


		/**
		 * Start session, sets session id (using request parameter)
		 *
		 * @return  void
		 */
		private function startSession()
		{
			// set session timeout directive
			ini_set( 'session.gc_maxlifetime', $this->config->sessionTimeout );

			if( ApplicationBase::getInstance()->config->cookielessSession )
			{
				if( isset( HTTPRequest::$post['PHPSESSID'] ))
				{
					$this->session->start( HTTPRequest::$post['PHPSESSID'] );
					unset( HTTPRequest::$post['PHPSESSID'] );
				}
				elseif( isset( HTTPRequest::$get['PHPSESSID'] ))
				{
					$this->session->start( HTTPRequest::$get['PHPSESSID'] );
					unset( HTTPRequest::$get['PHPSESSID'] );
				}
				else
				{
					$this->session->start();
				}
			}
			else
			{
				$this->session->start();
			}

			if( $this->config->sessionTimeout > 0 )
			{
				if(isset($this->session[$this->applicationId.'_timeout']))
				{
					if($this->session[$this->applicationId.'_timeout'] < time())
					{
						$this->session->destroy();
					}
				}

				$this->session[$this->applicationId.'_timeout'] = time() + $this->config->sessionTimeout;
			}
		}


		/**
		 * endSession
		 *
		 * @return  void
		 */
		private function endSession()
		{
			$this->session->close();
		}


		/**
		 * run helper commands
		 *
		 * @param   HTTPRequest		$request		HTTPRequest object
		 * @return  void
		 */
		private function handleRequestParams( \System\Web\HTTPRequest &$request )
		{
			if(isset($request->get[\Rum::config()->requestParameter]) && isset($request->get["id"]))
			{
				// modules
				if($request->get[\Rum::config()->requestParameter]===__MODULE_REQUEST_PARAMETER__&&isset($request->get["asset"])&&isset($request->get["type"]))
				{
					if( $request["type"]==='text/html' ||
						$request["type"]==='text/javascript' ||
						$request["type"]==='text/css' ||
						$request["type"]==='image/jpeg' ||
						$request["type"]==='image/gif' ||
						$request["type"]==='image/png')
					{
						$asset = str_replace('./', '', $request->get["asset"]);
						if($request["id"]==='core')
						{
							$path = __SYSTEM_PATH__ . '/web';
						}
						else
						{
							$path = __PLUGINS_PATH__ . '/' . urlencode($request["id"]);
						}

						$offset = 31536000; // 1 year

						$content = file_get_contents($path . '/assets/' . $asset);

						HTTPResponse::addHeader("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
						HTTPResponse::addHeader("Cache-Control: max-age=$offset, must-revalidate"); 
						HTTPResponse::addHeader("content-type:".$request["type"]);

						HTTPResponse::write($content);
						HTTPResponse::end();
					}
				}
				// dev parameters
				elseif($_SERVER[__ENV_PARAMETER__]===__DEV_ENV__||$_SERVER[__ENV_PARAMETER__]===__TEST_ENV__)
				{
					if($request->get[\Rum::config()->requestParameter]==='dev' && ($request->get["id"]==="clean" || $request->get["id"]==="build"))
					{
						\System\Base\Build::$verbose = true;
						\System\Base\Build::clean();
						$prepare = ob_get_clean();
						ob_start();
						$title = "Cleaning application source files: ";
						if($request->get["id"]=="build")
						{
							$title = "Rebuilding application source files: ";
							\System\Base\Build::rebuild();
						}
						$build = ob_get_clean();

						\System\Web\HTTPResponse::addHeader( "Content-Type: text/html" );
						\System\Web\HTTPResponse::write( "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>Building...</title>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
<link href=\"" . $this->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/css')) . "&asset=debug_tools/debug.css\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
".(isset($request["nostyle"])?"":"<link href=\"" . $this->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/css')) . "&asset=debug_tools/exception.css\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />")."
<script src=\"" . $this->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/js')) . "&asset=debug_tools/debug.js\" type=\"text/javascript\"></script>
</head>
<body>

<div id=\"page\">

<h1>Building...</h1>

<div id=\"details\" class=\"success\">
<h2>{$title}</h2>
<p>Building is performed automatically, however rebuilding is necessary when changes to the models, database, or file system have been made.
No building is needed or allowed in a production environment.</p>
<a href=\"".$this->config->url."\">Return to the default page</a>
</div>" );
						\System\Web\HTTPResponse::write( "<pre><strong>Preparing files...</strong>\r\n" );
						\System\Web\HTTPResponse::write( $prepare );
						\System\Web\HTTPResponse::write( "</pre><pre><strong>Rebuilding files...</strong>\r\n" );
						\System\Web\HTTPResponse::write( $build );
						\System\Web\HTTPResponse::write( "\r\n<strong>Build complete...</strong></pre>" );
						\System\Web\HTTPResponse::write( "

<p class=\"dump\" id=\"debug_show\"><a href=\"#debug_dump\" onclick=\"document.getElementById('debug_info').style.display='block';document.getElementById('debug_show').style.display='none';\">Show Debug Information</a></p>
<div style=\"display:none;\" id=\"debug_info\">");

						$this->dumpDebug();

						\System\Web\HTTPResponse::write( "
</div>

<div id=\"version\">
<p><strong>Framework Version:</strong> ".\System\Base\FRAMEWORK_VERSION_STRING."</p>
</div>

</div>

</body>
</html>" );
						exit;
					}
					elseif($request->get[\Rum::config()->requestParameter]=='dev' && $request->get["id"]=="run_all")
					{
						trigger_error("URI mapping dev/run_all is deprecated, use test/run_all instead", E_USER_DEPRECATED);
						$tester = new \System\Test\Tester();
						$tester->runAllTestCases(new \System\Test\HTMLTestReporter());
						exit;
					}
					elseif($request->get[\Rum::config()->requestParameter]=='dev/run_unit_test' )
					{
						trigger_error("URI mapping dev/run_unit_test is deprecated, use test/run_unit_test instead", E_USER_DEPRECATED);
						$tester = new \System\Test\Tester();
						$tester->runUnitTestCase($request->get["id"], new \System\Test\HTMLTestReporter());
						exit;
					}
					elseif($request->get[\Rum::config()->requestParameter]=='dev/run_functional_test' )
					{
						trigger_error("URI mapping dev/run_functional_test is deprecated, use test/run_functional_test instead", E_USER_DEPRECATED);
						$tester = new \System\Test\Tester();
						$tester->runFunctionalTestCase($request->get["id"], new \System\Test\HTMLTestReporter());
						exit;
					}
					elseif($request->get[\Rum::config()->requestParameter]=='test' && $request->get["id"]=="run_all")
					{
						$tester = new \System\Test\Tester();
						$tester->runAllTestCases(new \System\Test\HTMLTestReporter());
						exit;
					}
					elseif($request->get[\Rum::config()->requestParameter]=='test/run_unit_test' )
					{
						$tester = new \System\Test\Tester();
						$tester->runUnitTestCase($request->get["id"], new \System\Test\HTMLTestReporter());
						exit;
					}
					elseif($request->get[\Rum::config()->requestParameter]=='test/run_functional_test' )
					{
						$tester = new \System\Test\Tester();
						$tester->runFunctionalTestCase($request->get["id"], new \System\Test\HTMLTestReporter());
						exit;
					}
				}
			}
		}


		/**
		 * output debugging information
		 *
		 * @param  HTTPResponse		$response		HTTPResponse object
		 * @param  int				$mode			debug mode
		 * @return void
		 */
		private function insertDebug( \System\Web\HTTPResponse $response, $ttl=0 )
		{
			if( $this->debug )
			{
				$raw = \System\Web\HTTPResponse::getResponseContent();
				\System\Web\HTTPResponse::clear();

				$this->dumpDebug($ttl);

				$debug = \System\Web\HTTPResponse::getResponseContent();
				\System\Web\HTTPResponse::clear();

				if( strpos( $raw, '</html>' ) !== FALSE )
				{
					$raw = str_replace( '</body>', '', $raw );
					$raw = str_replace( '</html>', '', $raw );

					\System\Web\HTTPResponse::write( "$raw\n\n$debug\n\n</body>\n</html>" );
				}
				else
				{
					\System\Web\HTTPResponse::write( $raw );
				}
			}
		}


		/**
		 * output debugging information
		 *
		 * @param  int		 	$ttl		time to live (used for output caching)
		 * @return void
		 */
		private function dumpDebug( $ttl=0 )
		{
			if( $this->debug )
			{
				$elapsed = $this->timer->elapsed();
				$response = new \System\Web\HTTPResponse();

				$errcode = array (
				   E_ERROR		   => "Fatal Error",
				   E_WARNING		 => "Warning",
				   E_PARSE		   => "Parsing Error",
				   E_NOTICE		  => "Notice",
				   E_CORE_ERROR	  => "Core Error",
				   E_CORE_WARNING	=> "Core Warning",
				   E_COMPILE_ERROR   => "Compile Error",
				   E_COMPILE_WARNING => "Compile Warning",
				   E_USER_ERROR	  => "User Error",
				   E_USER_WARNING	=> "User Warning",
				   E_USER_NOTICE	 => "User Notice",
				   E_USER_DEPRECATED => "User Deprecated",
				   E_STRICT		  => "Runtime Notice"
				   );

				// dump app stats
				\System\Web\HTTPResponse::write( "<div id=\"debug_toolbar\">" );
				if( sizeof( $this->warnings ) > 0 || sizeof( $this->trace ) > 0 )
				{
					\System\Web\HTTPResponse::write( "<a class=\"debug_open\" href=\"#\" onclick=\"PHPRumDebug.debugOpen()\"><span>Open debug panel</span> <strong>(".sizeof( $this->warnings ).")</strong></a> | " );
				}
				else
				{
					\System\Web\HTTPResponse::write( "<a class=\"debug_open\" href=\"#\" onclick=\"PHPRumDebug.debugOpen()\"><span>Open debug panel</span> <span></span></a> | " );
				}
				\System\Web\HTTPResponse::write( "<a href=\"".__PROTOCOL__ . '://' . __HOST__ . \System\Web\WebApplicationBase::getInstance()->getPageURI('dev', array('id'=>'clean'))."\">Rebuild source</a> | " );
				\System\Web\HTTPResponse::write( "<a href=\"".__PROTOCOL__ . '://' . __HOST__ . \System\Web\WebApplicationBase::getInstance()->getPageURI('dev', array('id'=>'run_all'))."\">Run all tests</a> | " );
				\System\Web\HTTPResponse::write( "<a href=\"#\">Tools</a> | " );
				\System\Web\HTTPResponse::write( "<span><strong>Execution time:</strong> " . number_format($elapsed*1000, 2) . "ms</span>" );
				\System\Web\HTTPResponse::write( "<span style=\"float: right;\">" );
				\System\Web\HTTPResponse::write( "<span>Running in debug mode</span> | " );
				\System\Web\HTTPResponse::write( "<span><strong>Framework version:</strong> ".\System\Base\FRAMEWORK_VERSION_STRING . "</span>" );
				\System\Web\HTTPResponse::write( "</span>" );
				\System\Web\HTTPResponse::write( "</div>" );
				\System\Web\HTTPResponse::write( "<div id=\"debug_panel\" class=\"debug_panel\">" );
				\System\Web\HTTPResponse::write( "<h1>Debug Panel:</h1>" );
				\System\Web\HTTPResponse::write( "<p><strong>Env:</strong> ".$_SERVER[__ENV_PARAMETER__]."</p>" );
				if($ttl>0) \System\Web\HTTPResponse::write( "<p><strong>Output caching:</strong> output is cached {$ttl}s</p>" );
				if(ini_get('apc.enabled')==1) \System\Web\HTTPResponse::write( "<p>APC is enabled: ".ini_get("apc.ttl")."s</p>" );
				\System\Web\HTTPResponse::write( "<p><strong>Caching:</strong> " . (\Rum::config()->cacheEnabled?'enabled':'disabled') . "</p>" );
				\System\Web\HTTPResponse::write( "<p><strong>Execution time:</strong> " . $elapsed . "s</p>" );

				// mem usage
				if( function_exists( 'memory_get_usage' )) {
					//\System\Web\HTTPResponse::write( "memory usage: " . \number_format(( memory_get_usage( true ) / 1048576 ), 2, '.', '' ) . " MB\n" ); // MB = 1048576, Kb = 1024
				}

				// dump data adapter info
				try
				{
					if( $this->dataAdapter )
					{
						$adapter = '';
						if( strrchr( get_class($this->dataAdapter), '\\') !== false )
						{
							$adapter = substr( strrchr( str_replace( 'dataadapter', '', strtolower( get_class( $this->dataAdapter ))), '\\' ), 1 );
						}
						else
						{
							$adapter = str_replace( 'dataadapter', '', strtolower( get_class( $this->dataAdapter )));
						}

						\System\Web\HTTPResponse::write( "<p><strong>Adapter:</strong> " . $adapter . '; ' . $this->dataAdapter->getQueryCount() . " queries in " );
						\System\Web\HTTPResponse::write( $this->dataAdapter->getQueryTime() . "s</p>" );
					}
				}
				catch(\Exception $e) {}
				\System\Web\HTTPResponse::write( "<p><strong>PHP version:</strong> ".phpversion()."</p>" );

				// dump trace
				if( sizeof( $this->trace ) > 0 )
				{
					\System\Web\HTTPResponse::write( "<pre id=\"trace\"><strong>Trace:</strong>\n\n" );
					$i=0;
					foreach( $this->trace as $trace )
					{
						print_r($trace);
						\System\Web\HTTPResponse::write( "\n" );
					}
					\System\Web\HTTPResponse::write( "</pre>" );
				}

				// dump warnings
				if( sizeof( $this->warnings ) > 0 )
				{
					$count = sizeof( $this->warnings );
					if($count > __ERROR_LIMIT__)
					{
						\System\Web\HTTPResponse::write( "<p><strong>Warnings:</strong> &gt;".__ERROR_LIMIT__." (displaying the first ".__ERROR_LIMIT__.")</p>" );
					}
					else
					{
						\System\Web\HTTPResponse::write( "<p><strong>Warnings:</strong> " . $count . "</p>" );
					}

					\System\Web\HTTPResponse::write( "<p><a href=\"#errs\" onclick=\"document.getElementById( 'errs' ).style.display='block';this.style.display='none';\">dump warnings:</a></p>" );

					\System\Web\HTTPResponse::write( "<pre id=\"errs\" style=\"display:none;\">" );
					$i=0;
					foreach( $this->warnings as $err )
					{
						\System\Web\HTTPResponse::write( "<a href=\"#err".$i."\" onclick=\"document.getElementById('err".$i."').style.display='block';\"><b>" . $errcode[$err['errno']] . ":</b> " . $err['errstr'] . " in <b>" . $err['errfile'] . "</b> on line <b>" . $err['errline'] . "</b></a>\n" );
						\System\Web\HTTPResponse::write( "<span id=\"err".$i."\" style=\"display:none;\">" );
						\System\Web\HTTPResponse::write( "<map name=\"errno".$i."\" id=\"errno".$i."\">" );
						$this->dumpCallStack( $err['backtrace'] );
						\System\Web\HTTPResponse::write( "</map>" );
						\System\Web\HTTPResponse::write( "</span>" );

						if(++$i>=__ERROR_LIMIT__) break;
					}
					\System\Web\HTTPResponse::write( "</pre>" );
				}
				$this->warnings = array();

				// dump request data
				\System\Web\HTTPResponse::write( "<p><a href=\"#req\" onclick=\"document.getElementById( 'req' ).style.display='block';this.style.display='none';\">dump request data:</a></p>" );
				\System\Web\HTTPResponse::write( "<div id=\"req\" style=\"display:none;\">" );

				\System\Web\HTTPResponse::write( "  <p><a href=\"#req_get\" onclick=\"document.getElementById( 'req_get' ).style.display='block';this.style.display='none';\">dump get data:</a></p>" );

				\System\Web\HTTPResponse::write( "  <pre id=\"req_get\" style=\"display:none;\"><strong>Get Variables:</strong>\n\n" );
				ob_start();
				foreach($_GET as $key=>$value)
				{
					print("[{$key}] => {$value}\n");
				}
				$output = ob_get_clean();
				\System\Web\HTTPResponse::write( \Rum::escape( $output ));
				\System\Web\HTTPResponse::write( "  </pre>" );

				\System\Web\HTTPResponse::write( "  <p><a href=\"#req_post\" onclick=\"document.getElementById( 'req_post' ).style.display='block';this.style.display='none';\">dump post data:</a></p>" );
				\System\Web\HTTPResponse::write( "  <pre id=\"req_post\" style=\"display:none;\"><strong>Post Variables:</strong>\n\n" );
				ob_start();
				foreach($_POST as $key=>$value)
				{
					if(is_array($value))
					{
						print("[{$key}] => ".serialize($value)."\n");
					}
					else
					{
						print("[{$key}] => {$value}\n");
					}
				}
				$output = ob_get_clean();
				\System\Web\HTTPResponse::write( \Rum::escape( $output ));
				\System\Web\HTTPResponse::write( "  </pre>" );

				\System\Web\HTTPResponse::write( "  <p><a href=\"#req_cookie\" onclick=\"document.getElementById( 'req_cookie' ).style.display='block';this.style.display='none';\">dump cookie data:</a></p>" );
				\System\Web\HTTPResponse::write( "  <pre id=\"req_cookie\" style=\"display:none;\"><strong>Cookies:</strong>\n\n" );

				ob_start();
				foreach($_COOKIE as $key=>$value)
				{
					print("[{$key}] => {$value}\n");
				}
				$output = ob_get_clean();
				\System\Web\HTTPResponse::write( \Rum::escape( $output ));

				\System\Web\HTTPResponse::write( "  </pre>" );

				\System\Web\HTTPResponse::write( "</div>" );

				// dump request headers
				\System\Web\HTTPResponse::write( "<p><a href=\"#req_headers\" onclick=\"document.getElementById( 'req_headers' ).style.display='block';this.style.display='none';\">dump request headers:</a></p>" );
				\System\Web\HTTPResponse::write( "<pre id=\"req_headers\" style=\"display:none;\"><strong>Request Headers:</strong>\n\n" );

				$req_headers = array( 'request headers only supported when running as apache module' );
				if( function_exists( 'apache_request_headers' )) {
					$req_headers = apache_request_headers();
				}

				ob_start();
				foreach($req_headers as $key=>$value)
				{
					print("[{$key}] => {$value}\n");
				}
				$output = ob_get_clean();
				\System\Web\HTTPResponse::write( \Rum::escape( $output ));
				\System\Web\HTTPResponse::write( "</pre>" );

				// dump response headers
				\System\Web\HTTPResponse::write( "<p><a href=\"#response_headers\" onclick=\"document.getElementById( 'response_headers' ).style.display='block';this.style.display='none';\">dump response headers:</a></p>" );
				\System\Web\HTTPResponse::write( "<pre id=\"response_headers\" style=\"display:none;\"><strong>Response Headers:</strong>\n\n" );

				ob_start();
				foreach(headers_list() as $value)
				{
					print("{$value}\n");
				}
				$output = ob_get_clean();
				\System\Web\HTTPResponse::write( \Rum::escape( $this->replaceNonPrinting( $output )));

				\System\Web\HTTPResponse::write( "</pre>" );

				// dump session
				\System\Web\HTTPResponse::write( "<p><a href=\"#session\" onclick=\"document.getElementById( 'session' ).style.display='block';this.style.display='none';\">dump session:</a></p>" );
				\System\Web\HTTPResponse::write( "<pre id=\"session\" style=\"display:none;\"><strong>Session Data:</strong>\n\n" );

				ob_start();
				foreach($this->session->getSessionData() as $key=>$value)
				{
					if(is_array($value)) {
						print("[{$key}] => ".  serialize($value)."\n");
					}
					else {
						print("[{$key}] => {$value}\n");
					}
				}
				$output = ob_get_clean();
				\System\Web\HTTPResponse::write( \Rum::escape( $this->replaceNonPrinting( $output )));

				\System\Web\HTTPResponse::write( "</pre>" );

				\System\Web\HTTPResponse::write( "</div>" );
				\System\Web\HTTPResponse::write( "<script type=\"text/javascript\">PHPRumDebug.debugCheck()</script>" );
			}
		}


		/**
		 * dump call stack
		 *
		 * @return void
		 */
		private function dumpCallStack( array $callstack )
		{
			$response = new \System\Web\HTTPResponse();

			$i=0;
			foreach( $callstack as $call )
			{
				if(isset($call['file'])) {
					$current_level = '#'.$i++;
					while( strlen( $current_level ) < 3 ) $current_level .= ' ';

					\System\Web\HTTPResponse::write( "<span style=\"color:#000000;\">{$current_level}</span> " );

					if( isset( $call['class'] ))
					{
						\System\Web\HTTPResponse::write( "<span style=\"font-weight:bold;color:#0000AA;\">{$call['class']}-></span>" );
					}

					\System\Web\HTTPResponse::write( "<span style=\"font-weight:bold;color:#0000AA;\">{$call['function']}(</span>" );

					$ii=0;
					if(isset($call['args']))
					{
						foreach( $call['args'] as $arg ) {
							$trace = \Rum::escape(\addslashes(\print_r($arg,true)));
							if( $ii++ > 0 ) {
								\System\Web\HTTPResponse::write( "," );
							}
							if( is_object( $arg )) {
								\System\Web\HTTPResponse::write( "<span style=\"color:#0000FF\">Object</span>(<span onclick=\"getElementById('trace_{$call['line']}_{$i}_{$ii}').style.display='inline';\" style=\"text-decoration:underline;cursor:pointer;color:#000000\">".get_class($arg)."</span>)" );
							}
							elseif( is_array( $arg )) {
								\System\Web\HTTPResponse::write( "<span style=\"text-decoration:underline;cursor:pointer;color:#0000FF\" onclick=\"getElementById('trace_{$call['line']}_{$i}_{$ii}').style.display='inline';\">Array</span>" );
							}
							elseif( is_string( $arg )) {
								\System\Web\HTTPResponse::write( "<span style=\"color:#0000FF\">string</span>(<span style=\"color:#FF0000\">\"{$arg}\"</span>)" );
							}
							elseif( is_scalar( $arg )) {
								\System\Web\HTTPResponse::write( "<span style=\"color:#0000FF\">".gettype($arg)."</span>(<span style=\"color:#FF0000\">".$arg."</span>)" );
							}
							else {
								\System\Web\HTTPResponse::write( "<span style=\"color:#0000FF\">".gettype($arg)."</span>(<span style=\"color:#FF0000\">__PHP_Incomplete_Class</span>)" );
							}
							\System\Web\HTTPResponse::write( "<span id=\"trace_{$call['line']}_{$i}_{$ii}\" style=\"display:none;\">$trace</span>" );
						}
					}
					\System\Web\HTTPResponse::write( "<span style=\"font-weight:bold;color:#0000AA;\">)</span>" );
					\System\Web\HTTPResponse::write(" <strong>in</strong> " . str_replace( __ROOT__, '', $call['file'] ). " <strong>on line</strong> {$call['line']}\r\n");
				}
			}
		}


		/**
		 * replace non printing characters
		 *
		 * @param   string		$output		sting to format
		 * @return string
		 */
		private function replaceNonPrinting( $output )
		{
			$output = str_replace( "\x00", '', $output );
			$output = str_replace( "\x01", '', $output );
			$output = str_replace( "\x02", '', $output );
			$output = str_replace( "\x03", '', $output );
			$output = str_replace( "\x04", '', $output );
			$output = str_replace( "\x05", '', $output );
			$output = str_replace( "\x06", '', $output );
			$output = str_replace( "\x07", '', $output );
			$output = str_replace( "\x08", '', $output );
			$output = str_replace( "\x09", '', $output );
			// $output = str_replace( "\x0A", '', $output );
			$output = str_replace( "\x0B", '', $output );
			$output = str_replace( "\x0C", '', $output );
			$output = str_replace( "\x0D", '', $output );
			$output = str_replace( "\x0E", '', $output );
			$output = str_replace( "\x0F", '', $output );

			$output = str_replace( "\x10", '', $output );
			$output = str_replace( "\x11", '', $output );
			$output = str_replace( "\x12", '', $output );
			$output = str_replace( "\x13", '', $output );
			$output = str_replace( "\x14", '', $output );
			$output = str_replace( "\x15", '', $output );
			$output = str_replace( "\x16", '', $output );
			$output = str_replace( "\x17", '', $output );
			$output = str_replace( "\x18", '', $output );
			$output = str_replace( "\x19", '', $output );
			$output = str_replace( "\x1A", '', $output );
			$output = str_replace( "\x1B", '', $output );
			$output = str_replace( "\x1C", '', $output );
			$output = str_replace( "\x1D", '', $output );
			$output = str_replace( "\x1E", '', $output );
			$output = str_replace( "\x1F", '', $output );

			return $output;
		}
	}
?>