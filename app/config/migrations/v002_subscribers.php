<?php
	/**
	 * @package System\Migrate
	 */
	namespace System\Migrate;

	final class V002_subscribers extends MigrationBase
	{
		public $version = 2;

		public function up()
		{
			return \Rum::db()->prepare("CREATE TABLE `subscribers` (
  `subscriber_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(80) NOT NULL,
  `website` varchar(100) NOT NULL,
  `accept_terms` tinyint(1) NOT NULL,
  `registered` datetime NOT NULL,
  PRIMARY KEY (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
		}

		public function down()
		{
			return \Rum::db()->prepare("DROP TABLE `subscribers`");
		}
	}
?>