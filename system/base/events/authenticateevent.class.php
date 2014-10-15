<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base\Events;
	use System\Base\EventBase;


	/**
	 * Provides event handling
	 *
	 * @package			PHPRum
	 * @subpackage		Base
	 * @author			Darnell Shinbine
	 */
	final class AuthenticateEvent extends EventBase
	{
		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			parent::__construct("onAuthenticate");
		}
	}
?>