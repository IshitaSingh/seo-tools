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
			$this->page->add(<ObjectName>::gridview('gridview'));
			$this->page->gridview->caption = '<ControlTitle>';
			$this->page->gridview->showInsertRow = true;
			$this->page->gridview->columns->ajaxPostBack = true;
			$this->page->gridview->columns->add(new \System\Web\WebControls\GridViewButton('<PrimaryKey>', 'Delete', 'action', 'Are you sure you want to delete this <ControlTitle> record?', '', '', 'action', 'Add' ));
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
			$this->page->gridview->bind(<ObjectName>::all());
		}


		/**
		 * Event called after Viewstate and Page are loaded but before Post events are handled
		 *
		 * This method should be idempotent as it invoked every page request
		 *
		 * @param  object $sender Sender object
		 * @param  EventArgs $args Event args
		 * @return void
		 */
		public function onPageRequest($sender, $args)
		{
			if($this->isAjaxPostBack)
			{
				if(isset(\System\Web\HTTPRequest::$post["<PrimaryKey>"]))
				{
					$entity = <ObjectName>::findById(\System\Web\HTTPRequest::$post["<PrimaryKey>"]);

					foreach($entity->fields as $field=>$type)
					{
						if(isset(\System\Web\HTTPRequest::$post[$field]))
						{
							$entity[$field] = \System\Web\HTTPRequest::$post[$field];
						}
					}

					$entity->save();
					\Rum::flash("s:<ControlTitle> record has been updated");
				}
			}
		}


		/**
		 * on button post
		 *
		 * @param  object $sender Sender object
		 * @param  EventArgs $args Event args
		 * @return void
		 */
		public function onActionPost($sender, $args)
		{
			if($args["action"]=="Add")
			{
				// Insert
				try
				{
					$this->gridview->insertRow();
					\Rum::flash("s:<ControlTitle> record has been added");
					$this->gridview->refreshDataSource();
					$this->gridview->needsUpdating = true;
				}
				catch(\System\DB\DatabaseException $e)
				{
					\Rum::flash("s:Cannot add <ControlTitle> record, duplicate key detected");
				}
			}
			elseif($args["action"]=="Edit")
			{
				// Update
				try
				{
					$this->gridview->updateRow($args["<PrimaryKey>"]);
					\Rum::flash("s:<ControlTitle> record has been updated");
					$this->gridview->refreshDataSource();
					$this->gridview->needsUpdating = true;
				}
				catch(\System\DB\DatabaseException $e)
				{
					\Rum::flash("s:Cannot update <ControlTitle> record, duplicate key detected");
				}
			}
			elseif($args["action"]=="Delete")
			{
				// Delete
				try
				{
					$this->gridview->deleteRow($args["<PrimaryKey>"]);
					\Rum::flash("s:<ControlTitle> record has been deleted");
					$this->gridview->refreshDataSource();
					$this->gridview->needsUpdating = true;
				}
				catch(\System\DB\DatabaseException $e)
				{
					\Rum::flash("s:Cannot delete <ControlTitle> record, duplicate key detected");
				}
			}
		}
	}
#end