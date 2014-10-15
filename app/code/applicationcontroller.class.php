<?php
	/**
	 * @package SEOWerx
	 */
	namespace SEOWerx;

	/**
	 * All controllers should inherit this class.
	 *
	 * @package			Hive
	 */
	abstract class ApplicationController extends \System\Web\PageControllerBase
	{
		/**
		 * Called as soon as the Page component is created
		 * use this method to perform any common layer tasks.
		 *
		 * @param  Page $sender Sender object
		 * @param  EventArgs $args Event args
		 * @return void
		 */
		public function onPageCreate($page, $args)
		{
			$page->setMaster(new \System\Web\WebControls\MasterView('common'));

			// place any common behavior here...
			$page->master->assign('title', "SEO Analyzer");
		}
	}
?>