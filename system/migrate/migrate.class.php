<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Migrate;
	use System\Console\ConsoleApplicationBase;


	/**
	 * deplomyent script
	 *
	 * @package			PHPRum
	 * @subpackage		Migrate
	 * @author			Darnell Shinbine
	 */
	final class Migrate extends ConsoleApplicationBase
	{
		/**
		 * execute the application
		 *
		 * @param	int			$argc		Number of command line arguments
		 * @param	array		$argv		Array of command line arguments
		 *
		 * @return  void
		 */
		protected function execute()
		{
			global $argc, $argv;

			$options = $this->getOptions($argc, $argv);
			$env = isset($argv[1])?(\strpos($argv[1], "-")===0?"":$argv[1]):"";
			$task = isset($argv[2])?(\strpos($argv[2], "-")===0?"upgrade":$argv[2]):"upgrade";
			$version = isset($argv[3])?((\strpos($argv[3], "-")===0||\strpos($argv[3], "-")===0)?null:$argv[3]):null;

			$migrations = new Migrations();
			$this->loadAppConfig( __ENV_PATH__ . '/' . strtolower($env) . __APP_CONF_FILENAME__ );
			\System\Base\Build::clean();

			if($task=="upgrade")
			{
				$migrations->upgrade($version);
			}
			elseif($task=="downgrade")
			{
				$migrations->downgrade($version);
			}
			elseif($task=="to")
			{
				$migrations->to($version);
			}
			elseif($task=="version")
			{
				echo $migrations->getCurrentVersion();
			}
			else
			{
				throw new \System\Base\InvalidOperationException( "task `$task` is not defined" );
			}
		}


		/**
		 * retrieve command line options
		 *
		 * @param	int			$argc		Number of command line arguments
		 * @param	array		$argv		Array of command line arguments
		 *
		 * @return  array
		 */
		protected function getOptions($argc, $argv)
		{
			$options = array();
			for($i = 0; $i < $argc; $i++)
			{
				if($argv[$i] == "--help")
				{
					echo "Performs a database migration

PHP MIGRATE [env] [task] [version]

   [env] [task] [version]
              Specifies the task to execute. The
              default values are [prod] [upgrade]

  --help      Displays this help screen

";
					\passthru("pause");
					exit;
				}
			}

			return $options;
		}


		/**
		 * event triggered by an uncaught Exception thrown in the application, can be overridden to provide error handling.
		 *
		 * @param  \Exception	$e
		 *
		 * @return void
		 */
		protected function handleException(\Exception $e) {die($e->getMessage().PHP_EOL);}


		/**
		 * event triggered by an error in the application, can be overridden to provide error handling.
		 *
		 * @param  string	$errno		error code
		 * @param  string	$errstr		error description
		 * @param  string	$errfile	file
		 * @param  string	$errline	line no.
		 *
		 * @return void
		 */
		protected function handleError($errno, $errstr, $errfile, $errline) {die("{$errstr} in {$errfile} on line {$errline}".PHP_EOL);}
	}
?>