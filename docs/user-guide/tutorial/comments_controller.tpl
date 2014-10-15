<h1>6.0 Comments controller</h1>

<ol>
	<li>Create a new controller class in /app/controllers called comments.php<br />
		<pre style="background-color:#000000;color:#ffffff;">
php make controller comments</pre>
	</li>
	<li>Add the following methods to our new controller<br />
<?php highlight_string("<?php

	namespace MyBlog\Controllers;

	class Comments extends \MyBlog\ApplicationController
	{
		public function onPageInit(\$sender, \$args)
		{
			\$this->page->add(\MyBlog\Models\Comments::form('form'));
		}

		public function onPageLoad(\$sender, \$args)
		{
			\$entry = \\MyBlog\\Models\\Entries::findById(\Rum::app()->request[\"id\"]);

			\$this->page->form->dataSource = \$entry->createComments();
			\$this->page->assign('comments', \$entry->getAllComments());
		}

		public function onFormAjaxPost(\$sender, \$args)
		{
			if(\$this->page->form->validate(\$err))
			{
				\$this->page->form->save();

				\Rum::forward('blog');
				\Rum::flash(\Rum::tl('s:Comment has been added!'));
			}
		}
	}
?>") ?></li>

</ol>

<p>
	<?=\Rum::link('next - create the comments view', 'tutorials/comments_view') ?>
</p>
