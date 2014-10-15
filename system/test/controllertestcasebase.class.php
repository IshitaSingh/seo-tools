<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Test;

	/**
	 * include Form, required to load constsants
	 */
	require_once __ROOT__ . '/system/web/webcontrols/form.class.php';

	/**
	 * Provides base functionality for the ControllerTestCase
	 *
	 * @property string $response response as string
	 * @property XMLEntity $responseAsXMLEntity response as XMLEntity
	 *
	 * @package			PHPRum
	 * @subpackage		TestCase
	 * @author			Darnell Shinbine
	 */
	abstract class ControllerTestCaseBase extends TestCaseBase {

		/**
		 * instance of the controller
		 * @var ControllerBase
		 */
		protected $controller		= null;

		/**
		 * View object
		 * @var View
		 */
		protected $view				= null;

		/**
		 * contains the last response
		 * @var string
		 */
		private $_response			= '';

		/**
		 * name of the test case
		 * @var string
		 */
		private $_testCase			= '';


		/**
		 * Constructor
		 *
		 * @param   string			$testCase		Name of test case
		 * @return  void
		 */
		public function __construct( $testCase ) {
			$moduleClass  = ucwords( substr( $testCase, strrpos( $testCase, '/' ) + ( strrpos( $testCase, '/' )?1:0), strlen( $testCase )));
			parent::__construct( ucwords( $moduleClass ) . '_ControllerTestCase' );

			$this->_testCase = $testCase;
		}


		/**
		 * setup test module
		 *
		 * @return  void
		 */
		final public function setUp() {
			\System\Base\ApplicationBase::getInstance()->messages->removeAll();
			\System\Base\ApplicationBase::getInstance()->session->removeAll();

			$_GET					   = array();
			$_POST					   = array();
			$_COOKIE				   = array();
			$_SERVER['HTTP_REFERER']   = (isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'') . (isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'');

			if( \System\Security\Authentication::getAuthMethod() === 'forms' ) {
				if( !\System\Security\FormsAuthentication::authenticated() ) {
					\System\Security\FormsAuthentication::setAuthCookie( 'test' );
				}
			}

			$this->controller = \System\Base\ApplicationBase::getInstance()->getRequestHandler( $this->_testCase );

			parent::setUp();
		}


		/**
		 * clean test module
		 *
		 * @return  void
		 */
		final public function tearDown() {
			parent::tearDown();
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'response' ) {
				return $this->response();
			}
			elseif( $field === 'responseAsXMLEntity' ) {
				return $this->responseAsXMLEntity();
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
		 * @return string				string of variables
		 * @ignore
		 */
		public function __set( $field, $value ) {
			if( strtolower( (string)$field ) === 'get' && is_array( $value )) {
				$this->get( $value );
			}
			elseif( strtolower( (string)$field ) === 'post' && is_array( $value )) {
				$this->post( $value );
			}
			elseif( strpos( (string)$field, 'submit_' ) === 0 ) {
				$this->submit( substr( $field, 7 ), $value );
			}
			else {
				return parent::__set($field, $value);
			}
		}


		/**
		 * simulate a get request
		 *
		 * @param   array		$params		data to send
		 * @return  void
		 */
		protected function get( array $params = array() ) {
			$params[\System\Base\ApplicationBase::getInstance()->config->requestParameter] = $this->_testCase;
			$this->view = $this->controller->getView( $this->_getRequestObject( $params, 'get' ));
			$this->_response = '';
		}


		/**
		 * simulate a post request
		 *
		 * @param   array		$params		data to send
		 * @return  void
		 */
		protected function post( array $params = array() ) {
			$params[\System\Base\ApplicationBase::getInstance()->config->requestParameter] = $this->_testCase;
			$this->view = $this->controller->getView( $this->_getRequestObject( $params, 'post' ));
			$this->_response = '';
		}


		/**
		 * simulate a form submition
		 *
		 * @param string $formId id of the form
		 * @param array $data
		 */
		protected function submit( $formId, array $data = array() )
		{
			if($this->controller instanceof \System\Web\PageControllerBase)
			{
				$params = array();
				$params['page_' . $formId . '__submit'] = '1';
				$params['page_' . $formId . \System\Web\WebControls\GOTCHAFIELD] = '';

				foreach($data as $controlId => $value)
				{
					if(strpos($controlId, 'page_') === false)
					{
						$params['page_' . $formId . '_' . $controlId] = $value;
						$params['page_' . $formId . '_' . $controlId . '__post'] = '1';
					}

					$params[$controlId] = $value;
				}

				$this->post($params);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Controller must be of type PageControllerBase");
			}
		}


		/**
		 * returns the response
		 *
		 * @return  string
		 */
		protected function response() {
			if($this->_response)
			{
				return $this->_response;
			}
			else
			{
				if( $this->view ) {
					$this->_response = $this->view->fetch();
					return $this->_response;
				}
				return '';
			}
		}


		/**
		 * returns the response as XMLEntity object
		 *
		 * @return  XMLEntity
		 */
		protected function responseAsXMLEntity() {
			$xmlParser = new \System\XML\XMLParser();
			$result = $xmlParser->parse( $this->response() );
			$this->assertTrue( (bool) $result, $xmlParser->error );
			return $result;
		}


		/**
		 * asserts true if response exists
		 *
		 * @param   string		$response			response
		 * @param   string		$message			fail message
		 * @return  bool
		 */
		protected function assertResponse( $string, $message = '' ) {
			$message = $message?$message:"Expected string \"$string\" not found in response";
			$response = str_replace( "\n", "", str_replace( "\r", "", $this->response() ) );
			return $this->assertTrue( false !== strpos( $response, $string ), $message );
		}


		/**
		 * asserts true if no response exists that matches $string
		 *
		 * @param	string		$response			response
		 * @param	string		$message			fail message
		 * @return	bool
		 */
		protected function assertNoResponse( $string, $message = '' ) {
			$message = $message?$message:"String \"$string\" found in response";
			$response = str_replace( "\n", "", str_replace( "\r", "", $this->response() ) );
			return $this->assertTrue( false === strpos( $response, $string ), $message );
		}


		/**
		 * asserts true if var is assigned
		 *
		 * @param   string		$var				variable to test
		 * @param   string		$message			fail message
		 * @return  bool
		 */
		protected function assertAssigned( $var, $message = '' ) {
			$message = $message?$message:"Expected variable \"$var\" not assigned";
			$vars = array();
			if( $this->view ) {
				$vars = array_keys( $this->view->parameters );
			}

			return $this->assertTrue( false !== array_search( $var, $vars ), $message );
		}


		/**
		 * asserts true if controller redirected
		 *
		 * @param   string		$forward			forward
		 * @param   array		$forwardParams		forward parameters
		 * @param   string		$message			fail message
		 * @return  bool
		 */
		protected function assertRedirectedTo( $forward, $forwardParams = array(), $message = '' ) {
			$message = $message?$message:"Expected redirect to \"$forward\"";
		  if(!empty($forwardParams)){
			$message .= " with ";
			foreach($forwardParams as $param => $value){
			  $message .= $param . '=>' . $value . ' ';
			}
		  }

		  return $this->assertTrue(( strtolower( \System\Base\ApplicationBase::getInstance()->forwardURI ) === strtolower( $forward ) ||
				strtolower( \System\Base\ApplicationBase::getInstance()->forwardPage ) === strtolower( $forward )) && (empty($forwardParams) || (count(array_diff($forwardParams,\System\Base\ApplicationBase::getInstance()->forwardParams))==0) && count($forwardParams)==count(\System\Base\ApplicationBase::getInstance()->forwardParams)), $message);
		}


		/**
		 * asserts true if message exists
		 *
		 * @param   string			$responseMessage	response message
		 * @param   AppMessageType	$messageType		message type
		 * @param   string			$message			fail message
		 * @return  bool
		 */
		protected function assertMessage( $responseMessage, \System\Base\AppMessageType $messageType = null, $message = '' ) {
			$message = $message?$message:$messageType?"Expected message \"$responseMessage\" of type ".$messageType." not found":"Expected message \"$responseMessage\" not found";

			$assert = false;
			foreach( \System\Base\ApplicationBase::getInstance()->messages->getByType( $messageType ) as $msg ) {
				if( strstr( $msg, $responseMessage ) !== false || !$responseMessage ) {
					$assert = true;
				}
			}

			return $this->assertTrue( $assert, $message );
		}


		/**
		 * returns empty HTTPRequest object
		 *
		 * @return  HTTPRequest
		 */
		private function _getRequestObject( array $params = array(), $method = 'get' ) {

			$_SERVER['REQUEST_METHOD'] = (string)$method;

			$_GET = array();
			$_POST = array();
			$_REQUEST = array();
			$_COOKIE = array();

			\System\Web\HTTPRequest::$get = array();
			\System\Web\HTTPRequest::$post = array();
			\System\Web\HTTPRequest::$request = array();
			\System\Web\HTTPRequest::$cookie = array();

			foreach( $params as $key => $value ) {
				$key = str_replace(' ', '_', str_replace('.', '_', $key));
				$_REQUEST[(string)$key] = $value;
				if( strtolower( (string)$method ) === 'post' ) {
					$_POST[(string)$key] = $value;
				}
				else {
					$_GET[(string)$key] = $value;
				}
			}

			return new \System\Web\HTTPRequest();
		}
	}
?>