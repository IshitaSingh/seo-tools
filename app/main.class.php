<?php
	/**
	 * @package			Hive
	 */
	namespace SEOWerx;

	/**
	 * This class represents the base web application.  This class recieves HTTP request input
	 * and delegates the request to a Controller.  Once the Controller has handled the request,
	 * this class will regain control and render the appropriate View selected by the Controller
	 * unless there are additional actions to perform.
	 */
	class Main extends \System\Web\WebApplicationBase
	{
		/**
		 * returns the common cache object (overrided this method in the application class)
		 *
		 * @return  CacheBase
		 * /
		protected function getCache()
		{
			return new \System\Caching\FileCache(__CACHE_PATH__);
		}


		/**
		 * returns the common logger object (overrided this method in the application class)
		 *
		 * @return  LoggerBase
		 * /
		protected function getLogger()
		{
			return new \System\Logger\FileLogger(__ROOT__ . '/logs');
		}


		/**
		 * returns the common translator object (overrided this method in the application class)
		 *
		 * @return  TranslatorBase
		 * /
		protected function getTranslator()
		{
			return new \System\I18N\FileTranslator(__CONFIG_PATH__ . '/langs.xml');
		}


		/**
		 * returns the common mail client object (overrided this method in the application class)
		 *
		 * @return  IMailClient
		 */
		protected function getMailClient()
		{
			$mailClient = new \System\Comm\Mail\SMTPClient(\Rum::config()->appsettings["smtp_server"]);
			$mailClient->authUsername = \Rum::config()->appsettings["smtp_user"];
			$mailClient->authPassword = \Rum::config()->appsettings["smtp_password"];
			return $mailClient;
		}
	}
?>