<h1>3.0 Blog controller</h1>

<ol>
	<li>Create a new controller class in /app/controllers called blog.php<br />
		<pre style="background-color:#000000;color:#ffffff;">
php make controller blog</pre>
	</li>
	<li>Add the following methods to the controller<br />
<?php highlight_string("<?php

	namespace MyBlog\Controllers;

	class Blog extends \MyBlog\ApplicationController
	{
		public function onPageLoad(\$sender, \$args)
		{
			\$this->page->assign('entries', \MyBlog\Models\Entries::all());
		}
	}
?>") ?></li>

</ol>

<p>
	<?=\Rum::link('next - create the blog view', 'tutorials/blog_view') ?>
</p>
