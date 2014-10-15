<?php
	/**
	 * @package SEOWerx\Controllers
	 */
	namespace SEOWerx\Controllers;


	/**
	 * This class handles all requests for the /index page.  In addition provides access to
	 * a Page component to manage any WebControl components
	 *
	 * The PageControllerBase exposes 3 protected properties
	 * @property int $outputCache Specifies how long to cache page output in seconds, 0 disables caching
	 * @property Page $page Contains an instance of the Page component
	 * @property string $theme Specifies the theme for this page
	 *
	 * @package			SEOWerx\Controllers
	 */
	final class Results extends \SEOWerx\ApplicationController
	{
		/**
		 * Event called before Viewstate is loaded and Page is loaded and Post events are handled
		 * use this method to create the page components and set their relationships and default values.
		 *
		 * This method should not contain dynamic content as it may be cached for performance
		 * This method should be idempotent as it invoked every page request
		 *
		 * @param  object $sender Sender object
		 * @param  EventArgs $args Event args
		 * @return void
		 */
		public function onPageInit($sender, $args)
		{
		}


		/**
		 * Event called after Viewstate is loaded but before Page is loaded and Post events are handled
		 * use this method to bind components and set component values.
		 *
		 * This method should be idempotent as it invoked every page request
		 *
		 * @param  object $sender Sender object
		 * @param  EventArgs $args Event args
		 * @return void
		 */
		public function onPageLoad($sender, $args)
		{
			if(isset(\System\Web\HTTPRequest::$request["q"]))
			{
				$website = \SEOWerx\Models\Websites::find(array('url'=>\System\Web\HTTPRequest::$request["q"]));

				if($website)
				{
					$this->page->assign('website', $website);
					$this->page->assign('pages', $website->getAllWebpages());
				}
				else
				{
					\Rum::sendHTTPError(404);
				}
			}
			else
			{
				\Rum::sendHTTPError(400);
			}
		}
	}
?>