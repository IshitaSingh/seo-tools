<?php
	/**
	 * @package System\Migrate
	 */
	namespace System\Migrate;

	final class V001_init extends MigrationBase
	{
		public $version = 1;

		public function up()
		{
			\Rum::db()->prepare("
				CREATE TABLE `websites` (
				  `website_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `url` VARCHAR(255) NOT NULL,
				  PRIMARY KEY (`website_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")->execute();

			\Rum::db()->prepare("
				CREATE TABLE `webpages` (
				  `webpage_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `website_id` INT(10) UNSIGNED NOT NULL,
				  `url` VARCHAR(255) NOT NULL,
				  `http_status` VARCHAR(50) NOT NULL,
				  `headers` TEXT NOT NULL,
				  `content` TEXT NOT NULL,
				  `response_time` FLOAT UNSIGNED NOT NULL,
				  `last_crawled` DATETIME NOT NULL,
				  PRIMARY KEY (`webpage_id`),
				  KEY `website_id` (`website_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")->execute();

			return \Rum::db()->prepare("ALTER TABLE `webpages`
  ADD CONSTRAINT `fk_webpages_websites` FOREIGN KEY (`website_id`) REFERENCES `websites` (`website_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;");
		}

		public function down()
		{
			return \Rum::db()->prepare("DROP TABLE `webpages`, `websites`");
		}
	}
?>