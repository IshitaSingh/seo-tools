<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Logger;


	/**
	 * Provides access to read/write log messages
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	abstract class LoggerBase
	{
		/**
		 * This method writes a log message to memory
		 *
		 * @param  string	$message		message to log
		 * @param  string	$category		message category
		 * @return void
		 */
		abstract public function log($message, $category);


		/**
		 * This method retrieves all log messages
		 *
		 * @param  string	$category		message category
		 * @return array
		 */
		abstract public function logs($category);


		/**
		 * This method flushes all messages
		 *
		 * @param  string	$category		message category
		 * @return void
		 */
		abstract public function flush($category);
	}
?>