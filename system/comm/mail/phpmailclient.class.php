<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Comm\Mail;


	/**
	 * Handles email sending via PHP mail() function
	 * 
	 * @package			PHPRum
	 * @subpackage		Mail
	 * @author			Darnell Shinbine
	 */
	class PHPMailClient implements IMailClient
	{
		/**
		 * sends a single email to all addresses in message
		 *
		 * @param MailMessage $message message to send
		 * @return void
		 */
		public function send(MailMessage $message)
		{
			if( mail( $message->to, $message->subject, $message->getContent(), $message->getHeaders() ))
			{
				\Rum::log("Mail message sent via PHPMailClient from `{$message->from}` to `{$message->to}`, subject `{$message->subject}`", 'mail');
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Mail was not accepted for delivery, check php mail configuration");
			}
		}
	}
?>