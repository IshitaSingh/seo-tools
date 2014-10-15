<h1>8.0 Deploy</h1>

<ol>
	<li>Run the database migration tool<br />
<pre style="background-color:#000000;color:#ffffff;">
php migrate</pre>
	</li>
	<li>You can optionally manually create the database
		<code><pre>
CREATE TABLE `comments` (
  `comment_id` int(10) unsigned NOT NULL auto_increment,
  `entry_id` int(10) unsigned NOT NULL,
  `author` varchar(45) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`comment_id`),
  KEY `post_id` (`entry_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE `entries` (
  `entry_id` int(11) NOT NULL auto_increment,
  `title` varchar(45) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`entry_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;</pre></code>
	</li>
</ol>

<p>
	<?=\Rum::link('next - create the entries model', 'tutorials/entries_model') ?>
</p>
