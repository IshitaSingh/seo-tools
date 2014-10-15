<?php
	/**
	 * @package SEOWerx\Controllers
	 */
	namespace SEOWerx\Controllers;

	ini_set('max_execution_time', '300');

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
	final class Index extends \SEOWerx\ApplicationController
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
			$this->page->add(\SEOWerx\Models\Websites::form('form'));
			$this->page->form->add(new \System\Web\WebControls\Button('submit'));
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
		}


		/**
		 * onSubmitPost
		 *
		 * handle submit event
		 *
		 * @param  object $sender Sender object
		 * @param  EventArgs $args Event args
		 * @return void
		 */
		public function onSubmitAjaxPost($sender, $args)
		{
			if($this->form->validate($err))
			{
				$transaction = \Rum::db()->beginTransaction();

				// create new website record
				$website = \SEOWerx\Models\Websites::find(array('url'=>$this->url->value));
				if(!$website) {
					$website = \SEOWerx\Models\Websites::create(array('url'=>$this->url->value));
					$website->save();
				}

				$website->deleteAllWebpages();

				// let the webbot run loose on all pages
				$sitecrawler = new \Webbot\SiteCrawler();
				$sitecrawler->crawl($website["url"]);

				// store each page crawled
				foreach($sitecrawler->pages as $page)
				{
					$webpage = \SEOWerx\Models\Webpages::find(array('website_id'=>$website["website_id"],'url'=>$page["url"]));
					if(!$webpage) {
						$webpage = $website->createWebPages();
					}

					$webpage["url"] = $page["url"];
					$webpage["http_status"] = $page["http_status"];
					$webpage["headers"] = $page["headers"];
					$webpage["content"] = $page["content"];
					$webpage["response_time"] = $page["response_time"];
					$webpage["last_crawled"] = date('c');
					$webpage->save();
				}

				$transaction->commit();
				\Rum::forward('signup', array('q'=>$website["url"]));
			}
		}
	}
?>