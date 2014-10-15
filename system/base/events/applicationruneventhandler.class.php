<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base\Events;
	use System\Base\EventHandlerBase;


	/**
	 * Provides event handling
	 *
	 * @package			PHPRum
	 * @subpackage		Base
	 * @author			Darnell Shinbine
	 */
	final class ApplicationRunEventHandler extends EventHandlerBase
	{
		/**
		 * Constructor
		 *
		 * @param  string $callback call back
		 * @return void
		 */
		public function __construct($callback)
		{
			parent::__construct("onApplicationRun", $callback);
		}
	}
?>