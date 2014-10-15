<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Represents a table schema
	 *
	 * @property string $name table name
	 * @property string $primaryKey primary key
	 * @property array $foreignKeys array of foreign keys
	 * @property array $columnSchemas array of column schemas
	 * @property array $columnNames array of column names
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class TableSchema
	{
		/**
		 * table name
		 * @var string
		 */
		private $name				= '';

		/**
		 * primary key
		 * @var string
		 */
		private $primaryKey			= '';

		/**
		 * array of foreign keys
		 * @var string
		 */
		private $foreignKeys		= array();

		/**
		 * array of column schemas
		 * @var array
		 */
		private $columnSchemas		= array();


		/**
		 * Constructor
		 *
		 * @param  array $properties initial properties
		 * @param  array $foreignKeys array of foreign keys
		 * @param  array $columnSchemas array of column schemas
		 * @return void
		 */
		public function __construct( array $properties, array $foreignKeys, array $columnSchemas )
		{
			$this->foreignKeys = $foreignKeys;
			$this->columnSchemas = $columnSchemas;

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
			if($field == 'columnNames')
			{
				$names = array();
				foreach($this->columnSchemas as $columnSchema)
				{
					$names[] = $columnSchema->name;
				}
				return $names;
			}
			elseif(\array_key_exists($field, \get_object_vars($this)))
			{
				return $this->{$field};
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property `{$field}` in `".get_class($this)."`");
			}
		}


		/**
		 * seek
		 *
		 * @param  string	$tableName	table name
		 * @return TableSchema
		 */
		public function seek( $columnName )
		{
			foreach($this->columnSchemas as $columnSchema)
			{
				if($columnSchema->name === $columnName)
				{
					return $columnSchema;
				}
			}

			throw new SchemaException("ColumnSchema `{$columnName}` does not exist in `{$this->name}` schema");
		}
	}
?>