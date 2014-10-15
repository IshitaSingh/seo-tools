#php
	/**
	 * @package <Namespace>
	 */
	namespace <Namespace>;

	/**
	 * This class handles client connections for the /<PageURI> web service.  In addition provides access to
	 * a the generated WDSL via the ?wdsl paremeter
	 *
	 * The ControllerBase exposes 4 protected properties
	 * @property string $encoding specifies the character set for the soap message, default is ISO-8859-1
	 * @property string $cache specifies whether to cache the WDSL, default is WSDL_CACHE_NONE
	 * @property string $version specifies the SOAP version, default is SOAP_1_2
	 * @property string $namespace specifies the namespace, default is controller id
	 *
	 * @package			<Namespace>
	 */
	final class <ClassName> extends <BaseClassName>
	{
		/**
		 * This method is called remotely
		 *
		 * @return  mixed
		 */
		public function get(array $args)
		{
			if( \System\Security\WebServiceAuthentication::authenticated() )
			{
				return 'You are logged in';
			}
			else
			{
				return 'You are not logged in';
			}
		}

		/**
		 * This method is called remotely
		 *
		 * @return  mixed
		 */
		public function post(array $args)
		{
			
		}

		/**
		 * This method is called remotely
		 *
		 * @return  mixed
		 */
		public function put(array $args)
		{
			
		}

		/**
		 * This method is called remotely
		 *
		 * @return  mixed
		 */
		public function delete(array $args)
		{
			
		}
	}
#end