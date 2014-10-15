<?php
	/**
	 * @package			Hive
	 */
	namespace System\Deploy;

	/**
	 * Provides base functionality for deploying applications to remote servers.
	 *
	 * The DeploymentBase exposes 6 protected properties, these properties must be defined in the sub class
	 * @property string $home_path Specifies the home path on the remote server
	 * @property string $release_path Specifies the release path on the remote server
	 * @property string $server Specifies the server name
	 * @property string $port Specifies the server port
	 * @property string $username Specifies the login username
	 * @property string $password Specifies the login password
	 * 
	 * @package			Hive
	 */
	class Staging extends \System\Deploy\DeploymentBase
	{
		// server name
		protected $server="stage";
		// optional port
		protected $port=22;
		// username
		protected $username="darnell";
		// password
		protected $password="R146D";
		// remote home path
		protected $home_path="/home/www/seowerx";
		// local/svn repository
		protected $repository_path="git@git.commerx.com:cmx/seo-tools.git";
		// remote env
		protected $env="staging";
		// max releases
		protected $max_releases=10;


		/**
		 * default init task
		 *
		 * @return  void
		 */
		final public function init()
		{
			$this->run("echo initializing environment");

			// initializing folder structure
			$this->run("mkdir {$this->home_path}/releases");
			$this->run("mkdir {$this->home_path}/shared");
			$this->run("mkdir {$this->home_path}/shared/logs");

			$this->run("chmod 775 {$this->home_path}/shared/logs");
		}


		/**
		 * default deploy task
		 *
		 * @return  void
		 */
		final public function deploy()
		{
			$this->run("echo deploying to {$this->release_path}");

			// export application
			$this->run("git clone --depth 1 {$this->repository_path} {$this->release_path}");

			// create tmp folders
			$this->run("mkdir {$this->release_path}/.cache");
			$this->run("mkdir {$this->release_path}/.build");
			$this->run("chmod 775 {$this->release_path}/.cache");
			$this->run("chmod 775 {$this->release_path}/.build");

			// perform database migrations
			$this->run("php {$this->release_path}/migrate {$this->env}");
			$this->run("php {$this->release_path}/migrate {$this->env} version > {$this->release_path}/version");

			// sym link to logs
			$this->run("ln -s {$this->home_path}/shared/logs {$this->release_path}/logs");

			// sym link to current
			$this->run("unlink {$this->home_path}/current");
			$this->run("ln -s {$this->release_path} {$this->home_path}/current");

			// cleanup
			$this->cleanup();
		}


		/**
		 * default rollback task
		 *
		 * @return  void
		 */
		final public function rollback($release)
		{
			$this->run("echo rolling back to release {$release}");
			$release_path = $this->home_path . "/releases/" . (string)$release;

			// perform database migrations
			$this->run("php {$release_path}/migrate {$this->env} to < {$release_path}/version");

			// sym link to current
			$this->run("unlink {$this->home_path}/current");
			$this->run("ln -s {$release_path} {$this->home_path}/current");
		}


		/**
		 * default list_all task
		 *
		 * @return  void
		 */
		final public function list_all()
		{
			$this->run("echo list all releases");

			// list all releases
			$this->run("ls {$this->home_path}/releases");
		}


		/**
		 * default cleanup task
		 *
		 * @return  void
		 */
		final public function cleanup()
		{
			if((int)$this->max_releases > 0)
			{
				$this->run("echo cleaning up old releases");
				$this->run("rm `find {$this->home_path}/releases/*/ -maxdepth 0 | sort -r  | tail -n +".((int)$this->max_releases+1)."` -R -f");
			}
		}
	}
?>