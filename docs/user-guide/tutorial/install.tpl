<h1>1.0 Install</h1>

<ol>
	<li>In order to use this tutorial, you will need the latest version of the framework and the database creation script.
		<ul>
			<li><a href="<?=\Rum::config()->uri ?>/downloads/current_release/php_rum.zip">download the latest version of the framework here</a></li>
		</ul>
	</li>

	<li>Create a new database on your dev MySQL server and run the SQL script.</li>

	<li>Unpack the contents into your web folder and rename the "/public"
folder to whatever the web folder on the web server is.</li>

	<li>Edit the /app/config/environments/dev/application.xml file and uncomment and modify
the following line with the appropriate connection parameters to your dev MySQL database.<br />

<?php highlight_string("  <data-source                   dsn = \"adapter=mysql;
                                        uid=user;
                                        pwd=password;
                                        server=localhost;
                                        database=database_dev;\" />") ?></li>

	<li>Open the application in a browser and browse to the public folder, you should see a sample
start page.</li>

</ol>

<p>
	<?=\Rum::link('next - run the database migration', 'tutorials/migrate') ?>
</p>
