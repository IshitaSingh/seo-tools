<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Represents a database schema
	 *
	 * @property array $tableSchemas array of table schemas
	 * @property array $tableNames array of table names
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class DatabaseSchema
	{
		/**
		 * array of table schemas
		 * @var array
		 */
		private $tableSchemas		= array();


		/**
		 * Constructor
		 *
		 * @param  DataAdapter	$dataAdapter	instance of a DataAdapter
		 * @return void
		 */
		final public function __construct( array $properties, array $tableSchemas )
		{
			$this->tableSchemas = $tableSchemas;

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
					throw new \System\Base\InvalidOperationException("Property `{$key}` does not existx");
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
			if($field == 'tableNames')
			{
				$names = array();
				foreach($this->tableSchemas as $tableSchema)
				{
					$names[] = $tableSchema->name;
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
		final public function seek( $tableName )
		{
			foreach($this->tableSchemas as $tableSchema)
			{
				if(strtolower($tableSchema->name) === strtolower($tableName))
				{
					return $tableSchema;
				}
			}

			throw new SchemaException("TableSchema `{$tableName}` does not exist in schema");
		}
	}
?>