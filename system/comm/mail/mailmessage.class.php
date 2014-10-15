<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 *
	 *
	 */
	namespace System\Comm\Mail;


	/**
	 * Represents an E-Mail Message
	 *
	 * @property string $to Recipient email address
	 * @property string $from Sender's email address
	 * @property string $subject Message subject
	 * @property string $body Message body
	 * @property string $charset Message character set
	 * @property string $contentType Message content-type
	 * @property string $encoding Message encoding
	 * @property bool $html Specifies whether to send as html
	 * @property array $cc array of email addresses to be copied
	 * @property array $bcc array of email addresses to be blind copied
	 *
	 * @package			PHPRum
	 * @subpackage		Mail
	 *
	 */
	class MailMessage
	{
		/**
		 * recipient email address
		 * @var string
		 */
		private $to					= '';

		/**
		 * email addresses to copy message to
		 * @var array
		 */
		private $cc					= array();

		/**
		 * email addresses to blind copy message to
		 * @var array
		 */
		private $bcc				= array();

		/**
		 * sender's email address
		 * @var string
		 */
		private $from				= '';

		/**
		 * message subject
		 * @var string
		 */
		private $subject			= '';

		/**
		 * message body
		 * @var string
		 */
		private $body				= '';

		/**
		 * message character set
		 * @var string
		 */
		private $charset			= "iso-8859-1";

		/**
		 * message content-type
		 * @var string
		 */
		private $contentType		= "text/plain";

		/**
		 * message encoding mode
		 * @var string
		 */
		private $encoding			= "7bit";

		/**
		 * specifies whether to send as html
		 * @var bool
		 */
		private $html				= true;

		/**
		 * message attachments
		 * @var array
		 */
		private $attachments		= array();

		/**
		 * unique content seperator
		 * @var string
		 */
		private $_unique_sep		= "";


		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_unique_sep = md5( uniqid( time() ));
		}


		/**
		 * returns an object property
		 *
		 * @return void
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'to' )
			{
				return $this->to;
			}
			elseif( $field === 'from' )
			{
				return $this->from;
			}
			elseif( $field === 'subject' )
			{
				return $this->subject;
			}
			elseif( $field === 'body' )
			{
				return $this->body;
			}
			elseif( $field === 'charset' )
			{
				return $this->charset;
			}
			elseif( $field === 'contentType' )
			{
				return $this->contentType;
			}
			elseif( $field === 'encoding' )
			{
				return $this->encoding;
			}
			elseif( $field === 'html' )
			{
				return $this->html;
			}
			elseif( $field === 'cc' )
			{
				return $this->cc;
			}
			elseif( $field === 'bcc' )
			{
				return $this->bcc;
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
			if( $field === 'to' )
			{
				$this->setTo( $value );
			}
			elseif( $field === 'from' )
			{
				$this->setFrom( $value );
			}
			elseif( $field === 'subject' )
			{
				$this->setSubject( $value );
			}
			elseif( $field === 'body' )
			{
				$this->setBody( $value );
			}
			elseif( $field === 'charset' )
			{
				$this->setCharset( $value );
			}
			elseif( $field === 'contentType' )
			{
				$this->setContentType( $value );
			}
			elseif( $field === 'encoding' )
			{
				$this->setEncoding( $value );
			}
			elseif( $field === 'html' )
			{
				$this->html = (bool)$value;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * set recipient email address
		 *
		 * @param string $to recipient email address
		 * @return void
		 */
		public function setTo( $to )
		{
			if( preg_match( '^.+@.+\\.[a-zA-Z]+^', $to ))
			{
				$this->to = $this->_sterilize( $to );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("invalid email address specified in Message::setTo()");
			}
		}


		/**
		 * set sender's email address
		 *
		 * @param string $from sender's email address
		 * @return void
		 */
		public function setFrom( $from )
		{
			if( preg_match( '^.+@.+\\.[a-zA-Z]+^', $from ))
			{
				$this->from = $this->_sterilize( $from );
			}
			else
			{
				throw new \System\Base\InvalidOperationException("invalid email address specified in Message::setFrom()");
			}
		}


		/**
		 * set message subject
		 *
		 * @param string $subject message subject
		 * @return void
		 */
		public function setSubject( $subject )
		{
			$this->subject = $this->_sterilize( $subject );
		}


		/**
		 * set message body
		 *
		 * @param string $body message body
		 * @return void
		 */
		public function setBody( $body )
		{
			$this->body = $body;
		}


		/**
		 * set character set
		 *
		 * @param string $charset character set
		 * @return void
		 */
		public function setCharset( $charset )
		{
			$this->charset = $this->_sterilize( $charset );
		}


		/**
		 * set content type
		 *
		 * @param string $contentType content type
		 * @return void
		 */
		public function setContentType( $contentType )
		{
			$this->contentType = $this->_sterilize( $contentType );
		}


		/**
		 * set character encoding
		 *
		 * @param string $encoding encoding
		 * @return void
		 */
		public function setEncoding( $encoding )
		{
			$this->encoding = $this->_sterilize( $encoding );
		}


		/**
		 * add email address to copy
		 *
		 * @param string $cc email address
		 * @return void
		 */
		public function addCc( $cc )
		{
			if( preg_match( '^.+@.+\\.[a-zA-Z]+^', $cc ))
			{
				array_push( $this->cc, $this->_sterilize( $cc ));
			}
			else
			{
				throw new \System\Base\InvalidOperationException("invalid email address specified in Message::addCc()");
			}
		}


		/**
		 * add email address to blind copy
		 *
		 * @param string $bcc email address
		 * @return void
		 */
		public function addBcc( $bcc )
		{
			if( preg_match( '^.+@.+\\.[a-zA-Z]+^', $bcc ))
			{
				array_push( $this->bcc, $this->_sterilize( $bcc ));
			}
			else
			{
				throw new \System\Base\InvalidOperationException("invalid email address specified in Message::addBcc()");
			}
		}


		/**
		 * add an attachment to the mail message
		 *
		 * @param string $file file path or content
		 * @param string $mimetype attachment mimetype
		 * @param string $filename optional filename (auto detected if adding by path)
		 * @param string $disposition specifies whether the attachment disposition is inline, default is false
		 * @param string $alternate specifies whether the attachment is part of a multipart/alternate message, default is false
		 * @param string $encoding optional encoding, default is 7bit
		 * @return void
		 */
		public function attach( $file, $mimetype = 'text/plain', $filename = '', $inline = false, $alternate = false, $encoding = '7bit' )
		{
			if( file_exists( $file ))
			{
				$content = \base64_encode(implode(file($file),''));
				// auto detect filename
				$filename = $filename?(string)$filename:substr( $file, strrpos( $file, '/' ) + 1, strlen( $file ));
				// suto set encoding
				$encoding = 'base64';
			}
			elseif(is_string($file))
			{
				$content = (string)$file;
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to attach() must be a string");
			}

			$this->attachments[] = array( 'content' => (string)$content, 'mimetype' => (string)$mimetype, 'filename' => (string)$filename, 'disposition' => $inline?'inline':'attachment', 'encoding' => (string)$encoding, 'alternate' => (bool)$alternate );
		}


		/**
		 * get message headers
		 *
		 * @return string message headers
		 */
		public function getHeaders()
		{
			$eol = $this->_getEOLSeperator();

			// build header
			$headers  = "MIME-Version: 1.0{$eol}";
			$headers .= 'To: ' . (string) $this->to . "{$eol}";
			$headers .= 'From: ' . (string) $this->from . "{$eol}";
			$headers .= 'Return-Path: ' . (string) $this->from . "{$eol}";
			$headers .= 'X-Sender: ' . (string) $this->from . "{$eol}";
			$headers .= 'Date: ' . gmdate('r') . "{$eol}";
			$headers .= 'X-Mailer: PHP/' . phpversion() . "{$eol}";
			$headers .= "Content-Type: multipart/mixed; boundary=\"{$this->_unique_sep}\"{$eol}";
			$headers .= 'Content-Transfer-Encoding: ' . (string) $this->encoding . "{$eol}";

			// recipients
			if( $this->cc ) {
				$headers .= 'Cc: ' . implode( ',', $this->cc ) . "$eol";
			}
			if( $this->bcc ) {
				$headers .= 'Bcc: ' . implode( ',', $this->bcc ) . "$eol";
			}

			return $headers;
		}


		/**
		 * get message content
		 *
		 * @return string message content
		 */
		public function getContent()
		{
			$eol = $this->_getEOLSeperator();

			// html content
			if( $this->html )
			{
				$this->contentType = 'text/html';
			}

			$alternate = false;
			$message = '';

			// add attachments
			if( sizeof( $this->attachments )) {

				// loop through attachments
				foreach( $this->attachments as $attachment ) {

					$message .= "--{$this->_unique_sep}{$eol}";

					if($attachment["alternate"])
					{
						// alternate
						$alternate = true;
						$message .= "Content-Type: multipart/alternative; boundary=\"alt-{$this->_unique_sep}\"{$eol}";
						$message .= "--alt-{$this->_unique_sep}{$eol}";
					}

					if($attachment["disposition"] == 'attachment') {
						$message .= "Content-Type: {$attachment['mimetype']}; name=\"{$attachment['filename']}\"{$eol}";
					}

					$message .= "Content-Transfer-Encoding: {$attachment["encoding"]}{$eol}";
					$message .= "Content-Disposition: {$attachment["disposition"]}{$eol}{$eol}";
					$message .= chunk_split($attachment["content"]);
				}
			}

			// send actual message
			if($alternate) {
				$message .= "--alt-{$this->_unique_sep}{$eol}";
			}
			else {
				$message .= "--{$this->_unique_sep}{$eol}";
			}

			$message .= 'Content-Type: ' . (string) $this->contentType . "; charset=" . (string) $this->charset . "{$eol}";
			$message .= 'Content-Transfer-Encoding: ' . (string) $this->encoding . "{$eol}{$eol}";
			$message .= $this->body . "{$eol}{$eol}";

			if($alternate) {
				$message .= "--alt-{$this->_unique_sep}--{$eol}";
			}

			$message .= "--{$this->_unique_sep}--{$eol}";

			return $message;
		}


		/**
		 * get all attachments
		 *
		 * @return array of attachments
		 */
		public function getAttachments()
		{
			return $this->attachments;
		}


		/**
		 * get end of line seperator
		 *
		 * @return string
		 */
		private function _getEOLSeperator() {
			if( strtoupper( substr( PHP_OS, 0, 3 ) === 'WIN' )) {
				return "\r\n";
			} elseif( strtoupper( substr( PHP_OS, 0, 3 ) === 'MAC' )) {
				return "\r";
			} else {
				return "\n";
			}
		}


		/**
		 * check for header insertion hacks and sterilize output (removes any linebreaks)
		 *
		 * @param string $str string to sterilize
		 * @return void
		 */
		private function _sterilize( $str )
		{
			// check for header insertion attacks
			if( preg_match( '/[\n\r]/', (string) $str )) {
				\System\Base\ApplicationBase::getInstance()->logger->log( 'header injection attack detected from IP ' . $_SERVER['REMOTE_ADDR'], 'security' );

				// clean output
				$str = str_replace( "\n", '', str_replace( "\r", '', (string) $str ));
			}

			return $str;
		}
	}
?>