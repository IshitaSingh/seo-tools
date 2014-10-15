<h1>2.0 Entries model</h1>

<ol>
	<li>Create a new model class in /app/models called entries.class.php<br />
		<pre style="background-color:#000000;color:#ffffff;">
php make model entries</pre>
	</li>
	<li>You can optionally manually create a new model file in /app/models called entries.class.php<br />
<?php highlight_string("<?php

	namespace MyBlog\Models;

	class Entries extends \System\ActiveRecord\ActiveRecordBase
	{
		protected \$table = 'entries';

		protected \$pkey = 'entry_id';

		protected \$fields = array(
			'title'=>'string',
			'body'=>'blob'
		);

		protected \$rules = array(
			'title'=>'required'
		);

		protected \$relationships	= array(
			array(
				'relationship' => 'has_many',
				'type' => 'MyBlog\Models\Comments',
				'table' => 'comments',
				'columnRef' => 'entry_id',
				'columnKey' => 'entry_id',
				'notNull' => '1'
		));
	}
?>") ?></li>

</ol>

<p>
	<?=\Rum::link('next - create the blog controller', 'tutorials/blog_controller') ?>
</p>
