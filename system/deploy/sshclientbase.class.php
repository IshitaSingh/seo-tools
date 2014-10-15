<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Deploy;


	/**
	 * Provides base functionality for connecting via SSH
	 *
	 * The DeploymentBase exposes 4 protected properties, these properties must be defined in the sub class
	 * @property string $server Specifies the server name
	 * @property string $port Specifies the server port
	 * @property string $username Specifies the login username
	 * @property string $password Specifies the login password
	 *
	 * @package			PHPRum
	 * @subpackage		Deploy
	 * @author			Darnell Shinbine
	 */
	abstract class SSHClientBase
	{
		/**
		 * Specifies the server name
		 * @var string
		 */
		protected $server		= 'localhost';

		/**
		 * Specifies the server port
		 * @var int
		 */
		protected $port			= 22;

		/**
		 * Specifies the login username
		 * @var string
		 */
		protected $username		= 'root';

		/**
		 * Specifies the login password
		 * @var string
		 */
		protected $password		= '';

		/**
		 * password
		 * @var string
		 */
		private $_commands	= array();


		/**
		 * execute on remote server
		 *
		 * @param   string		$cmd		command to run on remote server
		 * @return  void
		 */
		final protected function run( $cmd )
		{
			$this->_commands[] = $cmd;
		}


		/**
		 * put file on remote server
		 *
		 * @param   string		$repository_path		local path
		 * @param   string		$home_path				remote path
		 * @return  void
		 */
		final protected function put( $local_path, $remote_path )
		{
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') // if windows
			{
				\passthru("pscp -v -r -P {$this->port} ".($this->password?"-pw {$this->password}":"")." \"{$local_path}\" {$this->username}@{$this->server}:{$remote_path}");
			}
			else
			{
				\passthru("scp -v -r -P {$this->port} ".($this->password?"-pw {$this->password}":"")." \"{$local_path}\" {$this->username}@{$this->server}:{$remote_path}");
			}
		}


		/**
		 * execute deployment script
		 *
		 * @return  void
		 */
		final public function exec()
		{
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') // if windows
			{
				\passthru("plink -v -P {$this->port} -ssh {$this->server} -l {$this->username} ".($this->password?"-pw {$this->password}":"")." \"".\implode(";", $this->_commands)."\"");
			}
			else
			{
				\passthru("ssh {$this->server} -p {$this->port} -l {$this->username} \"".\implode(";", $this->_commands)."\"");
			}
		}
	}
?>