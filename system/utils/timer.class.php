<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Utils;


	/**
	 * Provides basic timer functionality
	 * 
	 * @package			PHPRum
	 * @subpackage		Utils
	 * @author			Darnell Shinbine
	 */
	class Timer {

		/**
		 * start-time in ms
		 * @var int
		 */
		private $_start	 = 0;

		/**
		 * stop-time in ms
		 * @var int
		 */
		private $_stop	  = 0;

		/**
		 * accumulated time
		 * @var bool
		 */
		private $_accu	  = false;

		/**
		 * true when timer is running
		 * @var bool
		 */
		private $_started   = false;

		/**
		 * true when timer is paused
		 * @var bool
		 */
		private $_paused	= false;


		/**
		 * Constructor
		 *
		 * @param   bool	$autostart		Specifies whether to automatically start the timer
		 *
		 * @return  void
		 */
		public function __construct( $autostart = false ) {
			if( $autostart ) {
				$this->start();
			}
		}


		/**
		 * start timer
		 *
		 * @return  void
		 */
		public function start() {
			if( !$this->_started ) {
				$this->_accu	= 0;
				$this->_start   = $this->_getTime();
				$this->_started = true;
				$this->_paused  = false;
				return true;
			}
			return false;
		}


		/**
		 * pause the timer
		 *
		 * @return  real		time in seconds
		 */
		public function pause() {
			if( $this->_started && !$this->_paused ) {
				$this->_accu += ( $this->_getTime() - $this->_start );

				$this->_paused = true;
				return true;
			}
			return false;
		}


		/**
		 * resume the timer
		 *
		 * @return  real		time in seconds
		 */
		public function resume() {
			if( $this->_started && $this->_paused ) {
				$this->_start = $this->_getTime();

				$this->_paused = false;
				return true;
			}
			return false;
		}


		/**
		 * stop timer
		 *
		 * @return  void
		 */
		public function stop() {
			if( $this->_started ) {
				if( $this->_paused ) {
					$this->_stop = $this->_start;
				}
				else {
					$this->_stop = $this->_getTime();
				}
				$this->_started = false;
				$this->_paused  = false;
				return true;
			}
			return false;
		}


		/**
		 * return elapsed time from start, if timer has been stopped then time will be calculated 
		 * based on stop time
		 *
		 * @return  real		time in seconds
		 */
		public function elapsed() {
			if( $this->_started ) {
				if( $this->_paused ) {
					return number_format( $this->_accu, 8 );
				}
				else {
					return number_format( $this->_accu + ( $this->_getTime() - $this->_start ), 8 );
				}
			}
			else {
				return number_format( $this->_accu + ( $this->_stop - $this->_start ), 8 );
			}
		}


		/**
		 * reset timer
		 *
		 * @return  void
		 */
		public function reset() {
			$this->_accu	= 0;
			$this->_start   = 0;
			$this->_stop	= 0;
			$this->_started = false;
			$this->_paused  = false;
		}


		/**
		 * returns unix timestamp in milliseconds
		 *
		 * @return  real		time in milliseconds
		 */
		private function _getTime() {
			$mtime = microtime();
			$mtime = explode( ' ', $mtime );
			return (real)$mtime[1] + (real)$mtime[0];
		}
	}
?>