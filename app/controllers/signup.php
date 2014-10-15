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
	final class Signup extends \SEOWerx\ApplicationController
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
			$this->page->add(\SEOWerx\Models\Subscribers::form('form'));
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
			if(isset(\System\Web\HTTPRequest::$request["q"]))
			{
				$this->page->form->bind(\SEOWerx\Models\Subscribers::create(array('website'=>\System\Web\HTTPRequest::$request["q"])));
				$info = parse_url(\System\Web\HTTPRequest::$request["q"]);
				$this->page->form->email->addValidator(new \SEOWerx\PartOfDomainValidator($info["host"]));
			}
			else
			{
				\Rum::sendHTTPError(404);
			}
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
		public function onSubmitPost($sender, $args)
		{
			if($this->form->validate($err))
			{
				$this->form->save();

//				\Rum::forward('report', array('q'=>$this["website"], 'id'=>$hash));
				\Rum::flash(\Rum::tl('registration_success_msg'), \System\Base\AppMessageType::Success());
			}
		}
	}
?>