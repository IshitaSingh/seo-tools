<?php
	/**
	 * @package SEOWerx\Controllers
	 */
	namespace SEOWerx\Controllers;

	include(__ROOT__.'/libs/html.inc');

	/**
	 * This class handles all requests for the /report page.  In addition provides access to
	 * a Page component to manage any WebControl components
	 *
	 * The PageControllerBase exposes 3 protected properties
	 * @property int $outputCache Specifies how long to cache page output in seconds, 0 disables caching
	 * @property Page $page Contains an instance of the Page component
	 * @property string $theme Specifies the theme for this page
	 *
	 * @package			SEOWerx\Controllers
	 */
	final class Report extends \SEOWerx\ApplicationController
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
			// implement here
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
			$pages = array();
			$score = 0;

			$hash = \System\Web\HTTPRequest::$request["id"];
			$website = \SEOWerx\Models\Websites::find(array('url'=>\System\Web\HTTPRequest::$request["q"]));

			if($website)
			{
				// validate hash
				$valid_hash = false;
				foreach(\SEOWerx\Models\Subscribers::all(array('website'=>\System\Web\HTTPRequest::$request["q"]))->rows as $subscriber) {
					if($hash == md5($website["url"] . strtotime($subscriber["registered"]))) {
						$valid_hash = true;
					}
				}

				if($valid_hash)
				{
					foreach($website->getAllWebpages()->rows as $webpage)
					{
						/* get keyphrases */
						$keyphrases = explode('-', \get_meta_content($webpage["content"], 'keywords'));
						array_walk($keyphrases, function (&$value){$value=trim($value);});

						/* analyze page */
						$seo_analyzer = new \SEOWerx\SEOAnalyzer();
						$seo_analyzer->analyze( $webpage['url'], $webpage['response_time'], $keyphrases );

						/* get result */
						$webpage['keyworddensity']  = $seo_analyzer->keyworddensity;
						$webpage['score']           = $seo_analyzer->score;
						$webpage['warnings']        = $seo_analyzer->warnings;
						$webpage['recommendations'] = $seo_analyzer->recommendations;
						$webpage['keywords']        = $keyphrases;

						if( stripos( $webpage['http_status'], '200 ok' )) {
							$pages[] = $webpage;
						}
						$score  += $seo_analyzer->score;
					}

					$this->page->assign( 'pages', $pages );
					$this->page->assign( 'score', $score / count( $pages ));
					$this->page->assign( 'website', $website );
				}
				else
				{
					\Rum::sendHTTPError(401);
				}
			}
			else
			{
				\Rum::sendHTTPError(404);
			}
		}
	}
?>