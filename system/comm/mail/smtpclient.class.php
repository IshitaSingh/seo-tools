<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Comm\Mail;

	/**
	 * include lib
	 */
//	require_once __LIB_PATH__ . '/smtp/class.smtp.php';
	require_once __LIB_PATH__ . '/PHPMailer/class.phpmailer.php';
	require_once __LIB_PATH__ . '/PHPMailer/class.smtp.php';

	/**
	 * Handles email sending via smtp client
	 *
	 * @property string $host specifies the smtp host
	 * @property int $port specifies the smtp port
	 * @property int $timeout specifies the smtp connection timeout
	 * @property bool $keepAlive specifies whether to keep the connection alive
	 * @property string $helo specifies the smtp helo message
	 * @property string $authUsername specifies the smtp authentication username
	 * @property string $authPassword specifies the smtp authentication password
	 *
	 * @package			PHPRum
	 * @subpackage		Mail
	 * @author			Darnell Shinbine
	 */
	class SMTPClient implements IMailClient
	{
		/**
		 * smpt host
		 * @var string
		 */
		private $host = "localhost";

		/**
		 * smpt port
		 * @var int
		 */
		private $port = 25;

		/**
		 * smpt timeout
		 * @var int
		 */
		private $timeout = 30;

		/**
		 * smpt keep alive
		 * @var bool
		 */
		private $keepAlive = true;

		/**
		 * smpt helo message
		 * @var string
		 */
		private $helo = '';

		/**
		 * smpt authentication username
		 * @var string
		 */
		private $authUsername = '';

		/**
		 * smpt authentication password
		 * @var string
		 */
		private $authPassword = '';

		/**
		 * smpt client
		 * @var SMTP
		 * /
		private $smtp = null;
		 */


		/**
		 * Constructor
		 * @param string $host specifies the smtp host
		 */
		public function __construct( $host = '' )
		{
			if( strlen( $host ) > 0 )
			{
				$this->host = $host;
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
			if( $field === 'host' )
			{
				return $this->host;
			}
			elseif( $field === 'port' )
			{
				return $this->port;
			}
			elseif( $field === 'timeout' )
			{
				return $this->timeout;
			}
			elseif( $field === 'keepAlive' )
			{
				return $this->keepAlive;
			}
			elseif( $field === 'helo' )
			{
				return $this->helo;
			}
			elseif( $field === 'authUsername' )
			{
				return $this->authUsername;
			}
			elseif( $field === 'authPassword' )
			{
				return $this->authPassword;
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
			if( $field === 'host' )
			{
				$this->host = (string)$value;
			}
			elseif( $field === 'port' )
			{
				$this->port = (int)$value;
			}
			elseif( $field === 'timeout' )
			{
				$this->timeout = (int)$value;
			}
			elseif( $field === 'keepAlive' )
			{
				$this->keepAlive = (bool)$value;
			}
			elseif( $field === 'helo' )
			{
				$this->helo = (string)$value;
			}
			elseif( $field === 'authUsername' )
			{
				$this->authUsername = (string)$value;
			}
			elseif( $field === 'authPassword' )
			{
				$this->authPassword = (string)$value;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}

		/**
		 * Initiates a connection to an SMTP server
		 * operation failed.
		 * @return void
		 * /
		public function open()
		{
			if( $this->smtp == null )
			{
				$this->smtp = new \SMTP();
			}
			else
			{
				return;
			}

			$host = '';
			$port = 0;

			if( strstr( $this->host, ":" ))
			{
				list($host, $port) = explode( ":", $this->host );
			}
			else
			{
				$host = $this->host;
				$port = $this->port;
			}

			if( $this->smtp->Connect( $host, $port, $this->timeout ))
			{
				if( $this->helo !== '' )
				{
					$this->smtp->Hello( $this->helo );
				}
				else
				{
					$this->smtp->Hello( $_SERVER["HTTP_HOST"] );
				}

				if($this->authUsername)
				{
					if( !$this->smtp->Authenticate($this->authUsername, $this->authPassword ))
					{
						$this->smtp->Reset();
						throw new \System\Base\InvalidOperationException("Could not connect to SMPT server, authentication failed");
					}
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Could not connect to SMPT server, check smtp settings");
			}
		}
		*/


		/**
		 * Closes the active SMTP session if one exists.
		 * @return void
		 * /
		public function close()
		{
			if( $this->smtp != null )
			{
				if($this->smtp->Connected())
				{
					$this->smtp->Quit();
					$this->smtp->Close();
					$this->smtp = null;
				}
			}
		}
		*/


		/**
		 * sends a single email to all addresses in message
		 *
		 * @param MailMessage $message message to send
		 * @return void
		 */
		public function send(MailMessage $message)
		{
			//Create a new PHPMailer instance
			$mail = new \PHPMailer();
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = 0;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host = $this->host;
			//Set the SMTP port number - likely to be 25, 465 or 587
			$mail->Port = $this->port;
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
			//Username to use for SMTP authentication
			$mail->Username = $this->authUsername;
			//Password to use for SMTP authentication
			$mail->Password = $this->authPassword;
			//Set who the message is to be sent from
			$mail->setFrom($message->from);
			//Set an alternative reply-to address
			$mail->addReplyTo($message->from);

			//Set who the message is to be sent to
			$mail->addAddress($message->to);
			foreach($message->cc as $cc) {
				$mail->addCC($cc);
			}
			foreach($message->bcc as $bcc) {
				$mail->addBCC($bcc);
			}

			//Set the subject line
			$mail->Subject = $message->subject;
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($message->body);
			//Replace the plain text body with one created manually
//			$mail->AltBody = 'This is a plain-text message body';
			//Attach an image file
			foreach($message->getAttachments() as $attachment)
			{
				// TODO: write content to tmp file
				$mail->addAttachment($attachment["content"]);
			}

			//send the message, check for errors
			if (!$mail->send()) {
				throw new \System\Base\InvalidOperationException("Mail was not accepted for delivery, {$mail->ErrorInfo}");
			}
		}
	}
?>