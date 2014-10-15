<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Deploy;
	use System\Console\ConsoleApplicationBase;


	/**
	 * deplomyent script
	 *
	 * @package			PHPRum
	 * @subpackage		Deploy
	 * @author			Darnell Shinbine
	 */
	final class Deploy extends ConsoleApplicationBase
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
			$target = isset($argv[1])?(\strpos($argv[1], "-")===0?"prod":$argv[1]):"prod";
			$task = isset($argv[2])?((\strpos($argv[2], "-")===0||\strpos($argv[2], "-")===0)?"deploy":$argv[2]):"deploy";
			$arg = isset($argv[3])?((\strpos($argv[3], "-")===0||\strpos($argv[3], "-")===0)?"":$argv[3]):"";

			$deploymentScript = __DEPLOY_PATH__ . '/' . strtolower($target) . __DEPLOYMENT_EXTENSION__;

			if( file_exists( $deploymentScript))
			{
				require_once $deploymentScript;

				$deploymentClass = "System\\Deploy\\{$target}";

				if( class_exists( $deploymentClass ))
				{
					$deploy = new $deploymentClass;

					if(\method_exists($deploy, $task))
					{
						if($arg)
						{
							$deploy->{$task}($arg);
						}
						else
						{
							$deploy->{$task}();
						}

						$deploy->exec();
					}
					else
					{
						throw new \System\InvalidOperationException( "task `$task` is not defined" );
					}
				}
				else
				{
					throw new \System\InvalidOperationException( "deployment script `$deploymentClass` is not defined" );
				}
			}
			else
			{
				throw new \System\Utils\FileNotFoundException( "deployment script `$deploymentScript` does not exist" );
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
					echo "Executes a deployment script

PHP DEPLOY [target] [task]

   [target] [task]
              Specifies the target script and the task to execute. The
              default values are [prod] [deploy]

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