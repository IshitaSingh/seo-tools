<h1>5.0 Comments model</h1>

<ol>
	<li>Create a new model class in /app/models called comments.class.php<br />
		<pre style="background-color:#000000;color:#ffffff;">
php make model comments</pre>
	</li>
	<li>You can optionally manually create a new model file in /app/models called comments.class.php<br />
<?php highlight_string("<?php

	namespace MyBlog\Models;

	class Comments extends \System\ActiveRecord\ActiveRecordBase
	{
		protected \$table = 'comments';

		protected \$pkey = 'comment_id';

		protected \$fields = array(
			'author'=>'string',
			'body'=>'blob'
		);

		protected \$rules = array(
			'author'=>'required'
		);

		protected \$relationships	= array(
			array(
				'relationship' => 'belongs_to',
				'type' => 'MyBlog\Models\Entries',
				'table' => 'entries',
				'columnRef' => 'entry_id',
				'columnKey' => 'entry_id',
				'notNull' => '1'
		));
	}
?>") ?></li>

</ol>

<p>
	<?=\Rum::link('next - create the comments controller', 'tutorials/comments_controller') ?>
</p>
