<?php
	/**
	 * @package SEOWerx\Models
	 */
	namespace SEOWerx\Models;

	/**
	 * This class represents represents a Webpages table withing a database or an instance of a single
	 * record in the Webpages table and provides database abstraction
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
	class Webpages extends \System\ActiveRecord\ActiveRecordBase
	{
		/**
		 * Specifies the table name
		 * @var string
		**/
		protected $table			= 'webpages';

		/**
		 * Specifies the primary key (there can only be one primary key defined)
		 * @var string
		**/
		protected $pkey				= 'webpage_id';

		/**
		 * Specifies field names mapped to field types
		 * @var array
		**/
		protected $fields			= array(
			'website_id' => 'ref',
			'url' => 'string',
			'http_status' => 'string',
			'response_headers' => 'string',
			'response' => 'string',
			'time' => 'numeric',
			'last_crawled' => 'datetime'
		);

		/**
		 * Specifies field names mapped to field rules
		 * @var array
		**/
		protected $rules			= array(
			'url' => array('length(0, 765)'),
			'http_status' => array('length(0, 150)'),
			'response_headers' => array('length(0, 50331645)'),
			'response' => array('length(0, 50331645)'),
			'time' => array('numeric','length(0, 12)'),
			'last_crawled' => array('datetime','length(0, 19)')
		);

		/**
		 * Specifies table relationships
		 * @var array
		**/
		protected $relationships	= array(
			array(
				'relationship' => 'has_many',
				'type' => 'SEOWerx\Models\Keyphrases',
				'table' => 'keyphrases',
				'columnRef' => 'webpage_id',
				'columnKey' => 'webpage_id',
				'notNull' => '1'
			),
			array(
				'relationship' => 'belongs_to',
				'type' => 'SEOWerx\Models\Websites',
				'table' => 'webpages',
				'columnRef' => 'website_id',
				'columnKey' => 'website_id',
				'notNull' => '1'
			)
		);
	}
?>