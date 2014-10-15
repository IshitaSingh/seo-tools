<?php
	/**
	 * Startup script
	 * This script creates an instance of the application and executes
	 *
	 * This should be the only PHP script directly accessable via the web
	 * Do not modify this script!
	 *
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2011
	 */
	namespace SEOWerx;

	/**
	 * load framework and application
	 */
	include '../system/base/rum.php';

	// create instance of the application and run!!!
	\System\Base\ApplicationBase::getInstance(new Main())->run();
?>