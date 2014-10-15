<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\Services;


	/**
	 * This class handles all remote procedure calls for a REST web service
	 *
	 * @property array $options specifies json encoding options
	 * @property array $collectionName specifies collection name
	 * @property array $itemName specifies resource name
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	abstract class RESTWebServiceBase extends WebServiceBase
	{
		/**
		 * specifies json encoding options
		 * @var string
		 */
		protected $options = null;

		/**
		 * specifies collection name
		 * @var string
		 */
		protected $collectionName = 'collection';

		/**
		 * specifies resource name
		 * @var string
		 */
		protected $itemName = 'item';


		/**
		 * handle get requests
		 * @return void
		 */
		public function get(array $args) {return null;}


		/**
		 * handle post requests
		 * @return void
		 */
		public function post(array $args) {return null;}


		/**
		 * handle put requests
		 * @return void
		 */
		public function put(array $args) {return null;}


		/**
		 * handle delete requests
		 * @return void
		 */
		public function delete(array $args) {return null;}


		/**
		 * return view component for rendering
		 *
		 * @param   HTTPRequest		&$request	HTTPRequest object
		 * @return  View			view control
		 */
		public function getView( \System\Web\HTTPRequest &$request )
		{
			if(isset($request["format"]))
			{
				if($request["format"]=="json")
				{
					$this->contentType = "application/json";
				}
				elseif($request["format"]=="xml")
				{
					$this->contentType = "application/xml";
				}
			}
			elseif(strpos($_SERVER["HTTP_ACCEPT"], "application/json")!==false)
			{
				$this->contentType = 'application/json';
			}
			else
			{
				$this->contentType = 'application/xml';
			}

			return parent::getView( $request );;
		}


		/**
		 * configure the server
		 */
		protected function configure()
		{
			$rpc = new WebServiceMethod('get');
			$rpc->setParameters('args', 'array');
			$this->remoteProcedures[] = $rpc;

			$rpc = new WebServiceMethod('post');
			$rpc->setParameters('args', 'array');
			$this->remoteProcedures[] = $rpc;

			$rpc = new WebServiceMethod('put');
			$rpc->setParameters('args', 'array');
			$this->remoteProcedures[] = $rpc;

			$rpc = new WebServiceMethod('delete');
			$rpc->setParameters('args', 'array');
			$this->remoteProcedures[] = $rpc;
		}


		/**
		 * encode the object
		 * @param object $object
		 * @return string
		 */
		protected function encode( $object, $contentType )
		{
			if($contentType == 'text/xml' || $contentType == 'application/xml')
			{
				if(is_string($object))
				{
					return $object;
				}
				elseif(is_array($object))
				{
					return $this->xml_encode(($object));
				}
				else
				{
					throw new \System\Base\InvalidOperationException("Invalid object for XML encoding");
				}
			}
			elseif($contentType == 'application/json')
			{
				return json_encode($object, $this->options);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Invalid content type provided for Web Service");
			}
		}


		/**
		 * this method will handle the web service request
		 *
		 * @param   HTTPRequest		&$request	HTTPRequest object
		 * @return  void
		 */
		final public function handle( \System\Web\HTTPRequest &$request )
		{
			if(\System\Web\HTTPRequest::getRequestMethod() == 'GET')
			{
				unset($_GET[\Rum::config()->requestParameter]);
				$this->view->setData($this->encode(call_user_method('get', $this, $_GET), $this->contentType));
			}
			elseif(\System\Web\HTTPRequest::getRequestMethod() == 'POST')
			{
				unset($_POST[\Rum::config()->requestParameter]);
				$this->view->setData($this->encode(call_user_method('post', $this, $_POST), $this->contentType));
			}
			elseif(\System\Web\HTTPRequest::getRequestMethod() == 'PUT')
			{
				$data = fopen("php://input", "r");
				unset($data[\Rum::config()->requestParameter]);
				$this->view->setData($this->encode(call_user_method('put', $this, $data), $this->contentType));
			}
			elseif(\System\Web\HTTPRequest::getRequestMethod() == 'DELETE')
			{
				$data = fopen("php://input", "r");
				unset($data[\Rum::config()->requestParameter]);
				$this->view->setData($this->encode(call_user_method('delete', $this, $data), $this->contentType));
			}
			else
			{
				\Rum::sendHTTPError(400);
			}
		}


		/**
		 * returns xml encoded string
		 * @param mixed $object
		 * @return 
		 */
		private function xml_encode($array, $indent=false, $i=0) {
			if(!$i) {
					$data = '<?xml version="1.0"?>'.($indent?"\r\n":'').'<'.$this->collectionName.'>'.($indent?"\r\n":'');
			} else {
					$data = '';
			}
			foreach($array as $k=>$v) {
					if(is_numeric($k)) {
							$k = $this->itemName;
					}
					$data .= ($indent?str_repeat("\t", $i):'').'<'.$k.'>';
					if(is_array($v)) {
							$data .= ($indent?"\r\n":'').$this->xml_encode($v, $indent, ($i+1)).($indent?str_repeat("\t", $i):'');
					} else {
							$data .= \Rum::escape($v);
					}
					$data .= '</'.$k.'>'.($indent?"\r\n":'');
			}
			if(!$i) {
					$data .= '</'.$this->collectionName.'>';
			}
			return $data;
		}
	}
?>