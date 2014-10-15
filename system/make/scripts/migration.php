<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
    namespace System\Make;


	/**
	 * Provides functionality to generate controller/view files
	 *
	 * @package			PHPRum
	 * @subpackage		Make
	 */
	class Migration extends MakeBase
	{
		/**
		 * make
		 *
		 * @param string $target target
		 * @param array $options options
		 * @return void
		 */
		public function make($target, array $options = array())
		{
			$current_version = $this->getLatestVersion();

			$version = (int)$current_version+1;
			$name = 'v' . str_pad($version, 3, '0', STR_PAD_LEFT) . '_' . $options[2];
			$className = ucwords(substr(strrchr('/'.$name, '/'), 1));
			$namespace = 'System\Migrate';
			$baseClassName = 'MigrationBase';

			$path = __MIGRATIONS_PATH__ . '/' . strtolower($name) . '.php';

			$template = file_get_contents(\System\Base\ApplicationBase::getInstance()->config->root . "/system/make/templates/migration.tpl");
			$template = str_replace("<Namespace>", $namespace, $template);
			$template = str_replace("<ClassName>", $className, $template);
			$template = str_replace("<BaseClassName>", $baseClassName, $template);
			$template = str_replace("<Version>", $version, $template);

			$this->export($path, $template);
		}

		/**
		 * get latest version
		 * @return real
		 */
		private function getLatestVersion()
		{
			$migrations = $this->getMigrations();
			if(count($migrations)>0) {
				return $migrations[count($migrations)-1]->version;
			}
			else {
				return 0;
			}
		}

		/**
		 * get sorted array of MigrationBase objects
		 * @return array
		 */
		private function getMigrations()
		{
			$migrations = array();
			foreach(\System\DB\DataAdapter::create("adapter=dir;source=".__MIGRATIONS_PATH__.";")->openDataSet()->rows as $row)
			{
				if(\strpos($row["name"], '.php'))
				{
					require $row["path"];
					$migration = \str_replace(".php", "", $row["name"]);
					eval("\$migration = new \\System\\Migrate\\{$migration}();");

					$migrations[] = new $migration();
				}
			}

			$CSort = new \System\Migrate\MigrationCompare();
			usort( $migrations, array( &$CSort, 'compareVersion' ));
			return $migrations;
		}
	}
?>