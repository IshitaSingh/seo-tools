<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Console;
	use System\Base\ApplicationBase;


	/**
	 * Provides the base funcionality of a console application.  This class recieves command line
	 * data and executes the application
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class ConsoleApplicationBase extends ApplicationBase
	{
		/**
		 * returns the environment
		 *
		 * @return  string
		 */
		final protected function getEnv()
		{
			if(isset($_SERVER["APP_ENV"]))
			{
				return $_SERVER["APP_ENV"];
			}
			else return "";
		}
	}
?>