<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\Services;


	/**
	 * This class handles all remote procedure calls for a SOAP web service
	 *
	 * @property string $cache specifies whether to cache the WSDL, default is WSDL_CACHE_NONE
	 * @property string $version specifies the SOAP version, default is SOAP_1_2
	 * @property string $namespace specifies the namespace, default is controller id
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	abstract class SOAPWebServiceBase extends WebServiceBase
	{
		/**
		 * specifies whether to cache the WSDL
		 * @var string
		 */
		protected $cache = WSDL_CACHE_NONE;

		/**
		 * specifies the SOAP version
		 * @var string
		 */
		protected $version = SOAP_1_2;

		/**
		 * specifies the namespace
		 * @var string
		 */
		protected $namespace;

		/**
		 * array of reserved methods
		 * @var array
		 */
		protected static $reserved_methods = array(
			'__construct',
			'__set',
			'__get',
			'getView',
			'getRoles',
			'getCacheId',
			'configure',
			'handle',
			'getWSDL',
			'getWSDLURL');


		/**
		 * configure the web service
		 * @return void
		 */
		final protected function configure()
		{
			parent::configure();
		}


		/**
		 * this method will handle the web service request
		 *
		 * @param   HTTPRequest		&$request	HTTPRequest object
		 * @return  void
		 */
		final public function handle( \System\Web\HTTPRequest &$request )
		{
			// Configure Web Service
			$this->namespace = $this->controllerId;

			// Generate WSDL
			if(isset($request["wsdl"]))
			{
				$this->view->setData($this->getWSDL());
				return;
			}

			// Configure Soap Server
			$soap_server = new \SoapServer($this->getWSDLURL(), array(
				'soap_version' => $this->version,
				'cache_wsdl'=>$this->cache,
				'encoding'=>$this->charaset
				));

			$soap_server->setObject($this);
			foreach($this->remoteProcedures as $rpc)
			{
				$soap_server->addFunction($rpc->getFunction());
			}

			$soap_server->handle();
		}

		/**
		 * returns the URL of the WSDL
		 * @return string
		 */
		private function getWSDLURL()
		{
			return \Rum::url('', array('wsdl'=>'1'));
		}

		/**
		 * returns the WSDL
		 * @return string
		 */
		private function getWSDL()
		{
			$messages = '';
			$portTypes = '';
			$bindings = '';
			foreach($this->remoteProcedures as $rpc)
			{
				// messages
				$messages .= "
<message name='{$rpc->getFunction()}Request'>";
				foreach($rpc->getParameters() as $name=>$type)
				{
					$messages .= "
  <part name='{$name}' type='{$type}'/>";
				}
				$messages .= "
</message>
<message name='{$rpc->getFunction()}Response'>
  <part name='Result' type='xsd:string'/>
</message>";

				// port types
				$portTypes .= "
  <operation name='{$rpc->getFunction()}'>
    <input message='tns:{$rpc->getFunction()}Request'/>
    <output message='tns:{$rpc->getFunction()}Response'/>
  </operation>";

				// bindings
				$bindings .= "
<operation name='{$rpc->getFunction()}'>
    <soap:operation soapAction='urn:localhost-{$this->namespace}#{$rpc->getFunction()}'/>
    <input>
      <soap:body use='encoded' namespace='urn:localhost-{$this->namespace}'
        encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/>
    </input>
    <output>
      <soap:body use='encoded' namespace='urn:localhost-{$this->namespace}'
        encodingStyle='http://schemas.xmlsoap.org/soap/encoding/'/>
    </output>
  </operation>";
			}

			return "<?xml version ='1.0' encoding ='".$this->charaset."' ?>
<definitions name='{$this->namespace}'
  targetNamespace='{$this->getWSDLURL()}'
  xmlns:tns='{$this->getWSDLURL()}'
  xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/'
  xmlns='http://schemas.xmlsoap.org/wsdl/'>
{$messages}
<portType name='{$this->namespace}PortType'>
{$portTypes}
</portType>
<binding name='{$this->namespace}Binding' type='tns:{$this->namespace}PortType'>
  <soap:binding style='rpc'
    transport='http://schemas.xmlsoap.org/soap/http'/>
{$bindings}
</binding> 
<service name='{$this->namespace}Service'> 
  <port name='{$this->namespace}Port' binding='{$this->namespace}Binding'> 
    <soap:address location='".\Rum::url()."'/>
  </port>
</service>
</definitions>";
		}
	}
?>