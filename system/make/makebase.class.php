<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Make;


	/**
	 * Provides the interface for Make scripts
	 *
	 * @package			PHPRum
	 * @subpackage		Make
	 * @author			Darnell Shinbine
	 */
	abstract class MakeBase
	{
		/**
		 * make
		 *
		 * @param string $target target
		 * @param array $options options
		 * @return void
		 */
		abstract public function make($target, array $options = array());


		/**
		 * export
		 *
		 * @param string $path path
		 * @param string $contents contents
		 * @return vod
		 */
		protected function export($path, $contents)
		{
			$dir = ucwords(substr($path, 0, strrpos($path, '/')));

			if(!\is_dir($dir))
			{
				print("creating directory ".$dir.PHP_EOL);
				\mkdir($dir);
			}

			if(!file_exists($path))
			{
				print("writing file ".$path.PHP_EOL);

				$fp = @fopen($path, "w+");
				if(is_resource($fp))
				{
					fwrite($fp, str_replace('#php', '<?php', str_replace('#end', '?>', $contents)));
					fclose($fp);
				}
				else
				{
					throw new \System\Utils\FileNotWritableException("could not write file {$path}, make sure directory exists and write permissions are granted");
				}
			}
			else
			{
				print("cannot write file {$path}, file already exists".PHP_EOL);
			}
		}
	}
?>