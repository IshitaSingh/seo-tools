<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Deploy;


	/**
	 * Provides base functionality for deploying applications to remote servers.
	 *
	 * The DeploymentBase exposes 2 protected properties, these properties must be defined in the sub class
	 * @property string $home_path Specifies the home path on the remote server
	 * @property string $release_path Specifies the release path on the remote server
	 *
	 * @package			PHPRum
	 * @subpackage		Deploy
	 * @author			Darnell Shinbine
	 */
	abstract class DeploymentBase extends SSHClientBase
	{
		/**
		 * Specifies the home path on the remote server
		 * @var string
		 */
		protected $home_path;

		/**
		 * Specifies the release path on the remote server
		 * @var string
		 */
		protected $release_path;


		/**
		 * Constructor
		 *
		 * @return  void
		 */
		public function __construct()
		{
			$this->release_path = $this->release_path?$this->release_path:"{$this->home_path}/releases/" . date('YmdHis', time());
		}
	}
?>