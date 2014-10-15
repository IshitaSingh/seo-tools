<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Comm\Mail;


	/**
	 * Handles email sending via the $sendmail program
	 *
	 * @property string $path specifies the path to the $sendmail program
	 *
	 * @package			PHPRum
	 * @subpackage		Mail
	 * @author			Darnell Shinbine
	 */
	class SendMailClient implements IMailClient
	{
		/**
		 * path to sendmail
		 * @var string
		 */
		private $path = "/usr/sbin/sendmail";


		/**
		 * Constructor
		 * @param string $path specifies the path to the $sendmail program
		 */
		public function __construct( $path = '' )
		{
			if( strlen( $path ) > 0 )
			{
				$this->path = (string)$path;
			}
		}


		/**
		 * returns an object property
		 *
		 * @return void
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'path' )
			{
				return $this->path;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * sets an object property
		 *
		 * @return void
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'path' )
			{
				return $this->path = $value;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * sends a single email to all addresses in message
		 *
		 * @param MailMessage $message message to send
		 * @return void
		 */
		public function send(MailMessage $message)
		{
			if ($message->from != "")
			{
				$sendmail = sprintf( "%s -oi -f %s -t", $this->path, $message->from );
			}
			else
			{
				$sendmail = sprintf( "%s -oi -t", $this->path );
			}

			$mail = popen( $sendmail, "w" );

			if( !$mail )
			{
				throw new \System\Base\InvalidOperationException("could not open \$sendmail, check sendmail configuration");
			}

			fputs( $mail, $message->getHeaders() );
			fputs( $mail, $message->getContent() );

			$result = pclose( $mail ) >> 8 & 0xFF;

			if( $result != 0 )
			{
				throw new \System\Base\InvalidOperationException("could not open \$sendmail, check sendmail configuration");
			}
			else
			{
				\Rum::log("Mail message sent via SendMailClient from `{$message->from}` to `{$message->to}`, subject `{$message->subject}`", 'mail');
			}
		}
	}
?>