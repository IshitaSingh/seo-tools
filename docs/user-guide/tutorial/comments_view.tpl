<h1>7.0 Comments view</h1>

<ol>
	<li>Modify the view file in /app/views called comments.tpl</li>
	<li>Add the following to our new template<br />
<?php highlight_string("<h1>Comments</h1>

<?php foreach(\$comments as \$comment) : ?>

<h3><?php echo htmlentities(\$comment[\"author\"]) ?></h3>
<p><?php echo htmlentities(\$comment[\"body\"]) ?></p>

<hr />

<?php endforeach; ?>

<?php \$this->form->render() ?>

<hr />

<?php echo \Rum::link('Return to blog', 'blog') ?>") ?></li>

</ol>

<p>
	<?=\Rum::link('next - test/deploy the blog application', 'tutorials/deploy') ?>
</p>
