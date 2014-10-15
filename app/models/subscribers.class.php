<?php
	/**
	 * @package SEOWerx\Models
	 */
	namespace SEOWerx\Models;

	/**
	 * This class represents represents a Subscribers table withing a database or an instance of a single
	 * record in the Subscribers table and provides database abstraction
	 *
	 * The ActiveRecordBase exposes 5 protected properties, do not define these properties in the sub class
	 * to have the properties auto determined
	 * 
	 * @property string $table Specifies the table name
	 * @property string $pkey Specifies the primary key (there can only be one primary key defined)
	 * @property array $fields Specifies field names mapped to field types
	 * @property array $rules Specifies field names mapped to field rules
	 * @property array $relationahips Specifies table relationships
	 *
	 * @package			SEOWerx\Models
	 */
	class Subscribers extends \System\ActiveRecord\ActiveRecordBase
	{
		/**
		 * Specifies the table name
		 * @var string
		**/
		protected $table			= 'subscribers';

		/**
		 * Specifies the primary key (there can only be one primary key defined)
		 * @var string
		**/
		protected $pkey				= 'subscriber_id';

		/**
		 * Specifies field names mapped to field types
		 * @var array
		**/
		protected $fields			= array(
			'name' => 'string',
			'email' => 'string',
			'website' => 'string',
			'accept_terms' => 'boolean',
		);

		/**
		 * Specifies field names mapped to field rules
		 * @var array
		**/
		protected $rules			= array(
			'name' => array('length(0, 50)', 'required'),
			'email' => array('email', 'required'),
			'accept_terms' => array('required'),
		);

		/**
		 * Specifies table relationships
		 * @var array
		**/
		protected $relationships	= array(
		);

		protected function beforeInsert() {
			$this["registered"] = date('c');
		}

		protected function afterInsert() {
			$hash = md5($this["website"] . strtotime($this["registered"]));
			$full_name = $this["name"];
			$url = \Rum::url('report', array('q'=>$this["website"], 'id'=>$hash));

			$message = new \System\Comm\Mail\MailMessage();
			$message->to = $this["email"];
			$message->from = \Rum::config()->appsettings["reply_email"];
			$message->subject = "Your Free SEO Analysis and Report from Commerx";
			$message->body = str_replace('{full_name}', $full_name, str_replace('{url}', $url, file_get_contents(__ROOT__.'/app/layouts/email.tpl')));
			\Rum::app()->mailClient->send($message);
		}
	}
?>