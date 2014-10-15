<?php
	/**
	 * @package SEOWerx\Models
	 */
	namespace SEOWerx\Models;

	/**
	 * This class represents represents a Websites table withing a database or an instance of a single
	 * record in the Websites table and provides database abstraction
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
	class Websites extends \System\ActiveRecord\ActiveRecordBase
	{
		/**
		 * Specifies the table name
		 * @var string
		**/
		protected $table			= 'websites';

		/**
		 * Specifies the primary key (there can only be one primary key defined)
		 * @var string
		**/
		protected $pkey				= 'website_id';

		/**
		 * Specifies field names mapped to field types
		 * @var array
		**/
		protected $fields			= array(
			'url' => 'url'
		);

		/**
		 * Specifies field names mapped to field rules
		 * @var array
		**/
		protected $rules			= array(
			'url' => array('required', 'url')
		);

		/**
		 * Specifies table relationships
		 * @var array
		**/
		protected $relationships	= array(
			array(
				'relationship' => 'has_many',
				'type' => 'SEOWerx\Models\Webpages',
				'table' => 'webpages',
				'columnRef' => 'website_id',
				'columnKey' => 'website_id',
				'notNull' => '1'
			)
		);
	}
?>