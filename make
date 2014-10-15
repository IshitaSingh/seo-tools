<?php
	/**
	 * Rum script
	 * Do not modify this script!
	 */

	namespace System\Make;

	// include framework loader
	include 'system/base/rum.php';

	// create instance of the application and run!!!
	\System\Base\ApplicationBase::getInstance(new Make())->run();
?>