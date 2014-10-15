#php
	/**
	 * @package <Namespace>
	 */
	namespace <Namespace>;

	/**
	 * This class handles all requests for the /<PageURI> page.  In addition provides access to
	 * a Page component to manage any WebControl components
	 *
	 * The PageControllerBase exposes 3 protected properties
	 * @property int $outputCache Specifies how long to cache page output in seconds, 0 disables caching
	 * @property Page $page Contains an instance of the Page component
	 * @property string $theme Specifies the theme for this page
	 *
	 * @package			<Namespace>
	 */
	final class <ClassName> extends <BaseClassName>
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
			$this->page->add(<ObjectName>::form('form'));
			$this->page->form->legend = '<ControlTitle> record';
			$this->page->form->ajaxValidation = true;
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
			if(isset(\System\Web\HTTPRequest::$request["id"]))
			{
				$<ControlName>Record = <ObjectName>::findById(\System\Web\HTTPRequest::$request["id"]);

				if($<ControlName>Record)
				{
					$this->page->form->bind($<ControlName>Record);
				}
				else
				{
					\Rum::sendHTTPError(404);
				}
			}
			else
			{
				$this->page->form->bind(<ObjectName>::create());
			}
		}


		/**
		 * Event called when the Submit button is clicked
		 *
		 * @param  object $sender Sender object
		 * @param  EventArgs $args Event args
		 * @return void
		 */
		public function onSubmitClick($sender, $args)
		{
			if($this->form->validate())
			{
				$this->form->save();

				\Rum::flash("s:<ControlTitle> record has been saved");
				\Rum::forward('<ReturnURI>');
			}
			else
			{
				\Rum::flash(\Rum::tl('validation_failed'), \System\Base\AppMessageType::Warning());
			}
		}
	}
#end