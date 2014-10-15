<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web;


	/**
	 * Provides a way to access the persistant session
	 *
	 * @property string $sessionId Session Id
	 * @property string $sessionName Session Name
	 *
	 * @package			PHPRum
	 * @subpackage		IO
	 * @author			Darnell Shinbine
	 */
	final class Session implements \ArrayAccess, \Iterator
	{
		/**
		 * session id
		 * @var string
		 */
		private $sessionId				= null;

		/**
		 * session name
		 * @var string
		 */
		private $sessionName			= '';

		/**
		 * session data
		 * @var array
		 */
		private $data					= array();


		/**
		 * Read values from request, clean up variables, and merge get and post requests.
		 *
		 * @return  void
		 */
		public function __construct()
		{
			$this->data = array();
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		key
		 * @return string
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'sessionId' )
			{
				return $this->sessionId;
			}
			elseif( $field === 'sessionName' )
			{
				return $this->sessionName;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function rewind()
		{
			return reset($this->data);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function current()
		{
			return current($this->data);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function key()
		{
			return key($this->data);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function next()
		{
			return next($this->data);
		}

		/**
		 * implement Iterator methods
		 * @ignore
		 */
		public function valid()
		{
			return (bool) $this->current();
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetExists($index)
		{
			return isset( $this->data[$index] );
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetGet($index)
		{
			if( isset( $this->data[$index] ))
			{
				return $this->data[$index];
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetSet($index, $item)
		{
			$this->data[$index] = $item;
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetUnset($index)
		{
			if( isset( $this->data[$index] ))
			{
				unset( $this->data[$index] );
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * This method will start a new session or continue an existing session.
		 * You can optionally set the session id
		 *
		 * @param   string		$sessId			new session id
		 * @return  void
		 */
		public function start( $sessId = null )
		{
			if( !isset( $_SESSION ))
			{
				// set session id
				if( $sessId ) session_id( $sessId );

				// start session
				session_start();

				// store session id
				$this->sessionId = session_id();
				$this->sessionName = session_name();

				// read from session
				$this->read();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("session already started");
			}
		}


		/**
		 * initialize and read session data once started (implicit)
		 *
		 * @param   string		$sessId			new session id
		 * @return  void
		 */
		public function read()
		{
			if( isset( $_SESSION ))
			{
				// load session data
				$this->data = $_SESSION;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("session already started");
			}
		}


		/**
		 * write and close the session
		 *
		 * @return  void
		 */
		public function write() {

			if( $this->sessionId )
			{
				$_SESSION = array();

				// save data to session object
				foreach( $this->data as $key => $var )
				{
					$_SESSION[$key] = $var;
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("session has not been started");
			}
		}


		/**
		 * close the session
		 *
		 * @return  void
		 */
		public function close()
		{
			$this->write();

			$this->sessionId = null;
			$this->removeAll();

			// write session object
			session_write_close();
		}


		/**
		 * destroy the session
		 *
		 * @return  void
		 */
		public function destroy()
		{
			if( $this->sessionId )
			{
				session_destroy();
				$this->removeAll();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("session has not been started");
			}
		}


		/**
		 * regenerate the session id
		 *
		 * @param   bool $delete specifies whether to delete the old associated session file or not
		 * @return  void
		 */
		public function regenerateId($delete = false)
		{
			if( $this->sessionId )
			{
				if( session_regenerate_id((bool)$delete) )
				{
					$this->sessionId = session_id();
				}
				else
				{
					throw new IOException("could not regenerate session id");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("session has not been started");
			}
		}


		/**
		 * remove all session keys
		 *
		 * @return  void
		 */
		public function removeAll()
		{
			$this->data = array();
		}


		/**
		 * return session array
		 *
		 * @return  array
		 */
		public function & getSessionData()
		{
			return $this->data;
		}
	}
?>