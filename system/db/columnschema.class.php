<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Represents a column schema
	 *
	 * @property string $name column name
	 * @property string $table table name
	 * @property string $type column type
	 * @property int $length column length
	 * @property bool $notNull specifies whether column is not null
	 * @property bool $primaryKey specifies whether column is a primary key
	 * @property bool $foreignKey specifies whether column is a foriegn key
	 * @property bool $autoIncrement specifies whether column is auto incrementing
	 * @property bool $unique specifies whether column is unique
	 * @property bool $numeric specifies whether column is numeric
	 * @property bool $string specifies whether column is a string
	 * @property bool $integer specifies whether column is an integer
	 * @property bool $real specifies whether column is a double
	 * @property bool $boolean specifies whether column is true/false
	 * @property bool $blob specifies whether column is a blob
	 * @property bool $date specifies whether column is date string
	 * @property bool $time specifies whether column is time string
	 * @property bool $datetime specifies whether column is a date/time string
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class ColumnSchema
	{
		/**
		 * column name
		 * @var string
		 */
		private $name					= '';

		/**
		 * table name
		 * @var string
		 */
		private $table					= '';

		/**
		 * column type
		 * @var string
		 */
		private $type					= '';

		/**
		 * column length
		 * @var int
		 */
		private $length					= 0;

		/**
		 * specifies whether field is not null
		 * @var bool
		 */
		private $notNull				= false;

		/**
		 * specifies whether field is primary key
		 * @var bool
		 */
		private $primaryKey				= false;

		/**
		 * specifies whether field is primary key
		 * @var bool
		 */
		private $foreignKey				= false;

		/**
		 * specifies whether field is auto incrementing
		 * @var bool
		 */
		private $autoIncrement			= false;

		/**
		 * specifies whether field is unique
		 * @var bool
		 */
		private $unique					= false;

		/**
		 * specifies whether field is numeric
		 * @var bool
		 */
		private $numeric				= false;

		/**
		 * specifies whether field is a string
		 * @var bool
		 */
		private $string					= false;

		/**
		 * specifies whether field is an integer
		 * @var bool
		 */
		private $integer				= false;

		/**
		 * specifies whether field is a double
		 * @var bool
		 */
		private $real					= false;

		/**
		 * specifies whether field is true/false
		 * @var bool
		 */
		private $boolean				= false;

		/**
		 * specifies whether field is a blob
		 * @var bool
		 */
		private $blob					= false;

		/**
		 * specifies whether field is date string
		 * @var bool
		 */
		private $date					= false;

		/**
		 * specifies whether field is time string
		 * @var bool
		 */
		private $time					= false;

		/**
		 * specifies whether field is date/time string
		 * @var bool
		 */
		private $datetime				= false;


		/**
		 * Constructor
		 *
		 * @param  array $properties initial properties
		 * @return void
		 */
		public function __construct( array $properties )
		{
			foreach($properties as $key => $value)
			{
				if(\array_key_exists($key, \get_object_vars($this)))
				{
					if(\is_scalar($value))
					{
						$this->{$key} = $value;
					}
					else
					{
						throw new \System\Base\InvalidOperationException("Property `{$key}` value must be a scaler value");
					}
				}
				else
				{
					throw new \System\Base\InvalidOperationException("Property `{$key}` does not exist");
				}
			}
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return mixed
		 */
		public function __get( $field )
		{
			if(\in_array($field, \get_class_vars(get_class($this))))
			{
				return $this->{$field};
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property `{$field}` in `".get_class($this)."`");
			}
		}
	}
?>