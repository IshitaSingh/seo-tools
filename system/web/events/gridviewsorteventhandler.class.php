<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\Events;
	use \System\Base\EventHandlerBase;


	/**
	 * Provides event handling
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class GridViewSortEventHandler extends EventHandlerBase
	{
		/**
		 * Constructor
		 *
		 * @param  string $callback call back
		 * @return void
		 */
		public function __construct($callback)
		{
			parent::__construct("onGridViewSort", $callback);
		}
	}
?>