<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Make;
	use System\Console\ConsoleApplicationBase;


	/**
	 * deplomyent script
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class Make extends ConsoleApplicationBase
	{
		/**
		 * namespace
		 * @var string
		 */
		static $namespace = '';

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
			
			$xmlParser = new \System\XML\XMLParser();
			$dev = $xmlParser->parse(file_get_contents(__ROOT__ . '/app/config/dev.xml'));
			self::$namespace = $dev["namespace"];

			if(isset($argv[1]) && isset($argv[2]) && \strpos($argv[1], "-")!==0 && \strpos($argv[2], "-")!==0)
			{
				$script = $argv[1];
				$target = $argv[2];

				$makeScript = __SYSTEM_PATH__ . '/make/scripts/' . strtolower($script) . '.php';

				if( file_exists( $makeScript))
				{
					require_once $makeScript;

					$makeClass = "System\\Make\\{$script}";

					if( class_exists( $makeClass ))
					{
						$make = new $makeClass();

						$make->make($target, $argv);
					}
					else
					{
						throw new \System\Base\InvalidOperationException( "deployment script `$makeScript` is not defined" );
					}
				}
				else
				{
					throw new \System\Utils\FileNotFoundException( "deployment script `$makeScript` does not exist" );
				}
			}
			else
			{
				throw new \System\Base\InvalidArgumentException( "you must specify the script and target" );
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
					echo "Executes a make script

PHP MAKE [script] [target]

   [script] [target]
			  Specifies the script and the target output
			  file to generate

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