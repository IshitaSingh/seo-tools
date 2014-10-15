<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\Events;
	use \System\Base\EventBase;


	/**
	 * Provides event handling
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class ReportGroupHeaderEvent extends EventBase
	{
		/**
		 * Constructor
		 *
		 * @param string $groupName group name
		 * @return void
		 */
		public function __construct($groupName = 'Group')
		{
			parent::__construct("onReport".ucwords((string)$groupName)."Header");
		}
	}
?>