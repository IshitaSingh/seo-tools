<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Get;
	use System\Console\ConsoleApplicationBase;


	/**
	 * deplomyent script
	 *
	 * @package			PHPRum
	 * @subpackage		Deploy
	 * @author			Darnell Shinbine
	 */
	final class Get extends ConsoleApplicationBase
	{
		/**
		 * packages
		 * @var XMLEntity
		 */
		private $packages;

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
			$repo = $dev["repository"];

			$task = isset($argv[1])?(\strpos($argv[1], "-")===0?"update":$argv[1]):"update";
			$package = isset($argv[2])?(\strpos($argv[2], "-")===0?"all":$argv[2]):"all";

			// get repo
			try
			{
				$this->packages = $xmlParser->parse(file_get_contents($repo));
			}
			catch(\Exception $e)
			{
				echo "Unable to connect to repo at " . "{$repo}" . PHP_EOL;
				return;
			}

			if($task == "list")
			{
				echo "listing all packages...".PHP_EOL;

				// get package list
				foreach($this->packages->children as $package)
				{
					echo "{$package["id"]} \"{$package["name"]} {$package["version"]}\"".PHP_EOL;
				}
			}
			elseif($task == "updateall")
			{
				echo "listing all packages...".PHP_EOL;

				// get package list
				foreach($this->packages->children as $package)
				{
					echo "{$package["id"]} \"{$package["name"]} {$package["version"]}\"".PHP_EOL;
				}
			}
			elseif($task == "install" || $task == "update")
			{
				$meta = $this->getPackage($package);
				echo "installing package {$meta["id"]}...".PHP_EOL;
				echo "this is not undoable, MAKE A BACKUP!".PHP_EOL;

				\passthru("pause");

				// download package
				echo "downloading package {$meta["path"]}...".PHP_EOL;
				eval("\$dest=".$meta["dest"].";");

				if(!file_exists($dest)) {
					mkdir($dest);
				}

				$zip = $dest."/{$meta["id"]}.zip";
				try {
					$data = file_get_contents($meta["path"]);
				} catch(\Exception $e) {
					die("could not download package!");
				}
				try {
					file_put_contents($zip, $data);
				} catch(\Exception $e) {
					die("could not save package, check permissions!");
				}

				echo "unpacking package {$meta["id"]} to {$dest}...".PHP_EOL;

				if($task=="update") {
					\passthru("unzip -o \"{$zip}\" -d \"{$dest}\"");
				}
				else {
					\passthru("unzip \"{$zip}\" -d \"{$dest}\"");
				}

				echo "cleaning up...".PHP_EOL;
				unlink($zip);
			}
			elseif($task == "rem" || $task == "remove")
			{
				$meta = $this->getPackage($package);
				echo "removing package {$meta["id"]}...".PHP_EOL;
				eval("\$dest=".$meta["dest"].".'/{$meta["id"]}';");

				if(file_exists($dest)) {
					\System\Utils\FileSystem::removeDirectory($dest);
				}
			}
			else
			{
				throw new \System\Base\InvalidArgumentException( "unkown task `$task`" );
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

PHP GET [task] [package]

   [task] [package]
              Specifies the task to execute on the target package. The
              default values are [list] [all]

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

		private function getPackage($id)
		{
			foreach($this->packages->children as $package)
			{
				if($package["id"] == $id) return $package;
			}

			echo "package {$id} does not exist".PHP_EOL;
			exit;
		}
	}
?>