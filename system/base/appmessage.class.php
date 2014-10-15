<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;


	/**
	 * Represents a single application message
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class AppMessage
	{
		/**
		 * Specifies the message type
		 * @var int
		 */
		private $type					= '';

		/**
		 * The message content
		 * @var string
		 */
		private $message				= '';


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'type' ) {
				return $this->type;
			}
			elseif( $field === 'message' ) {
				return $this->message;
			}
			else {
				throw new BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * Constructor
		 *
		 * @param   string			$msg		Message content
		 * @param   AppMessageType	$type		Message type as constant of AppMessageType::Success(), AppMessageType::Fail() or AppMessageType::Notice()
		 * @return  void
		 */
		public function __construct( $msg, AppMessageType $type = null )
		{
			if( is_string( $msg ))
			{
				$type = $type?$type:AppMessageType::Info();

				if( $type == AppMessageType::Info() ||
					$type == AppMessageType::Fail() ||
					$type == AppMessageType::Success() ||
					$type == AppMessageType::Warning() )
				{
					$this->message = $msg;
					$this->type = $type;
				}
				else
				{
					throw new InvalidArgumentException("Argument 2 passed to AppMessage() must be a constant of AppMessageType::Fail(), AppMessageType::Success(), or AppMessageType::Notice()");
				}
			}
			else
			{
				throw new InvalidArgumentException("Argument 1 passed to AppMessage() must be a string");
			}
		}
	}
?>