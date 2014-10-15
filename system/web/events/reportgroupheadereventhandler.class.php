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
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class ReportGroupHeaderEventHandler extends EventHandlerBase
	{
		/**
		 * Constructor
		 *
		 * @param  string $callback call back
		 * @param string $groupName group name
		 * @return void
		 */
		public function __construct($callback, $groupName = 'Group')
		{
			parent::__construct("onReport".ucwords((string)$groupName)."Header", $callback);
		}
	}
?>