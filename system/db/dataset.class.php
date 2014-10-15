<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Represents a DataSet
	 *
	 * @property int $page the current page
	 * @property int $pageCount the current page count
	 * @property int $pageSize the page size
  	 * @property int $cursor record pointer
	 * @property int $count number of records
	 * @property bool $eof specifies if end of record
	 * @property bool $bof specifies if beginning of record
	 * @property object $source data source object
	 * @property string $table source table
	 * @property array $fieldMeta array of field meta data
	 * @property array $fields array of fields
	 * @property array $rows collection of records
	 * @property array $row record data
	 * @property DataAdapter $dataAdapter data adapter
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	final class DataSet extends \System\Base\Object implements \System\Base\IBindable
	{
		/**
		 * array of FieldMeta objects
		 * @var array
		 */
		private $fieldMeta			= array();

		/**
		 * array of fields
		 * @var array
		 */
		private $fields				= array();

		/**
		 * array of rows
		 * @var array
		 */
		private $rows				= array();

		/**
		 * array of row data
		 * @var array
		 */
		private $row				= array();

		/**
		 * Sets or returns the maximum number of records allowed on a single page of a DataSet object
		 * @var int
		 */
		private $pageSize			= 20;

		/**
		 * internal record pointer
		 * @var int
		 */
		private $cursor				= 0;

		/**
		 * data source
		 * @var string
		 */
		private $source				= '';

		/**
		 * source table
		 * @var string
		 */
		private $table				= '';

		/**
		 * reference to the data adapter
		 * @var DataAdapter
		 */
		private $dataAdapter		= null;

		/**
		 * specifies if the DataSet is readonly
		 * @var int
		 */
		private $lockType;


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return mixed
		 */
		public function & __get( $field )
		{
			if( $field === 'page' )
			{
				$page = $this->page();
				return $page;
			}
			elseif( $field === 'pageCount' )
			{
				$pageCount = $this->pageCount();
				return $pageCount;
			}
			elseif( $field === 'pageSize' )
			{
				return $this->pageSize;
			}
			elseif( $field === 'cursor' )
			{
				return $this->cursor;
			}
			elseif( $field === 'count' )
			{
				$count = $this->count();
				return $count;
			}
			elseif( $field === 'eof' )
			{
				$eof = $this->eof();
				return $eof;
			}
			elseif( $field === 'bof' )
			{
				$bof = $this->bof();
				return $bof;
			}
			elseif( $field === 'source' )
			{
				return $this->source;
			}
			elseif( $field === 'table' )
			{
				return $this->table;
			}
			elseif( $field === 'dataAdapter' )
			{
				return $this->dataAdapter;
			}
			elseif( $field === 'fieldMeta' )
			{
				return $this->fieldMeta;
			}
			elseif( $field === 'fields' )
			{
				return $this->fields;
			}
			elseif( $field === 'rows' )
			{
				return $this->rows;
			}
			elseif( $field === 'row' )
			{
				return $this->row;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * sets an object property
		 *
		 * @param  string	$field		name of the field
		 * @param  mixed	$value		value of field
		 *
		 * @return bool					true on success
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'pageSize' )
			{
				$this->pageSize = (int)$value;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * Copy the DataSet to a static copy
		 *
		 * @return DataSet
		 * @ignore
		 */
		final public function __clone()
		{
			$this->lockType = DataSetType::OpenStatic();
		}


		/**
		 * create new DataSet
		 *
		 * @param   srray	$data				data source as array
		 *
		 * @return			DataSet
		 */
		static public function createFromArray( array $data )
		{
			$dataSet = new \System\DB\DataSet();

			// validate data
			if((bool)count(array_filter(array_keys($data[0]), 'is_string')))
			{
				$fields = array();
				$fieldMeta = array();
				foreach(array_keys($data[0]) as $field)
				{
					$fields[] = $field;
					$fieldMeta[] = new \System\DB\ColumnSchema(array(
						'name' => (string) $field,
						'type' => 'string',
						'notNull' => false,
						'primaryKey' => false,
						'foreignKey' => false,
						'unique' => false,
						'numeric' => false,
						'string' => true,
						'integer' => false,
						'real' => false,
						'date' => false,
						'time' => false,
						'datetime' => false,
						'boolean' => false,
						'autoIncrement' => false,
						'blob' => false));
				}

				$dataSet->setFields($fields);
				$dataSet->setFieldMeta($fieldMeta);
				$dataSet->setRows($data);
			}
			else
			{
				throw new \System\Base\InvalidOperationException("Invalid array passed to DataSet::createFromArray(array())");
			}

			return $dataSet;
		}


		/**
		 * create new DataSet
		 *
		 * @param   mixed		$source		data source
		 * @param   DataAdapter	$da			DataAdapter
		 * @param   DataSetType	$lock_type	lock type as constant of DataSetType::OpenDynamic(), DataSetType::OpenStatic(), or DataSetType::OpenReadonly()
		 *
		 * @return  	DataSet
		 */
		static public function addNew( $source, DataAdapter &$da, DataSetType $lock_type = null )
		{
			$ds = new DataSet();
			$ds->source = $source;
			$ds->lockType = $lock_type?$lock_type:DataSetType::OpenDynamic();

			if( $ds->lockType == DataSetType::OpenDynamic() )
			{
				$ds->dataAdapter =& $da;
			}

			$da->fill( $ds );
			$ds->updateRowData();
			return $ds;
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetExists($index)
		{
			return array_key_exists( $index, $this->row );
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetGet($index)
		{
			if( array_key_exists( $index, $this->row ))
			{
				return $this->row[$index];
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetSet($index, $value)
		{
			if( array_key_exists( $index, $this->row ))
			{
				return $this->row[$index] = $value;
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetUnset($index)
		{
			if( array_key_exists( $index, $this->row ))
			{
				unset( $this->row[$index] );
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * implement Countable methods
		 * @ignore
		 */
		public function count()
		{
			return count($this->rows);
		}


		/**
		 * set fields
		 *
		 * @param  array		$fields		fields to add to DataSet
		 * @return void
		 */
		public function setFields( array $fields )
		{
			if( empty( $this->fields ))
			{
				$this->fields = $fields;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot set fields, already set");
			}
		}


		/**
		 * set fields
		 *
		 * @param  array		$fields		fields to add to DataSet
		 * @return void
		 */
		public function setFieldMeta( array $fieldMeta )
		{
			if( empty( $this->fieldMeta ))
			{
				$this->fieldMeta = $fieldMeta;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot set fieldMeta, already set");
			}
		}


		/**
		 * set records
		 *
		 * @param  array		$records	records to add to DataSet
		 * @return void
		 */
		public function setRows( array $rows )
		{
			if( empty( $this->rows ))
			{
				$this->rows = $rows;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot set rows, already set");
			}
		}


		/**
		 * set table
		 *
		 * @param  string		$table		source table
		 * @return void
		 */
		public function setTable( $table )
		{
			if( !$this->table )
			{
				$this->table = $table;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot set table, already set");
			}
		}


		/**
		 * update accessor with data from current record
		 *
		 * @return void
		 */
		public function updateRowData()
		{
			$this->row = array();

			// check that cursor points to valid record
			if( $this->cursor < count($this->rows) && $this->cursor > -1 )
			{
				// fill record values with values from records using cursor
				$this->row = $this->rows[$this->cursor];
			}
			else
			{
				// fill record fields with empty values
				foreach( $this->fields as $field )
				{
					$this->row[$field] = null;
				}
			}
		}


		/**
		 * sorts DataSet
		 *
		 * @param string	$column				column name
		 * @param bool 		$reverse			specifies reverse sort order
		 * @param bool		$case_insensitive	specifies if case sensitive
		 * @return void
		 */
		public function sort( $column, $reverse = false, $case_insensitive = false )
		{
			$field = null;
			foreach($this->fieldMeta as $meta)
			{
				if($meta->name==$column)
				{
					$field = $meta;
					break;
				}
			}

			if( $field )
			{
				/* get column number */
				$CSort = new ColumnCompare( $column );
				$rows = $this->rows;

				/* sort column by numerical */
				if( $field->numeric )
				{
					usort( $rows, array( &$CSort, 'compareNumeric' ));
				}
				/* sort column by date */
				elseif( $field->datetime ||
					$field->date ||
					$field->time )
				{
					usort( $rows, array( &$CSort, 'compareDateString' ));
				}
				/* sort column by string */
				else
				{
					if( $case_insensitive )
					{
						usort( $rows, array( &$CSort, 'compareStringi' ));
					}
					else
					{
						usort( $rows, array( &$CSort, 'compareString' ));
					}
				}

				/* reverse order */
				if( (bool) $reverse ) $rows = array_reverse( $rows );

				$this->rows = $rows;
				$this->updateRowData();
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("Field `{$column}` does not exist in DataSet");
			}
		}


		/**
		 * Moves result pointer to next record
		 *
		 * @return void
		 */
		public function next()
		{
			$this->cursor++;
			$this->updateRowData();
		}


		/**
		 * Moves result pointer to previous record
		 *
		 * @return void
		 */
		public function prev()
		{
			$this->cursor--;
			$this->updateRowData();
		}


		/**
		 * Moves result pointer to first record
		 *
		 * @return void
		 */
		public function first()
		{
			$this->cursor = 0;
			$this->updateRowData();
		}


		/**
		 * Moves result pointer to last record
		 *
		 * @return void
		 */
		public function last()
		{
			$this->cursor = count($this->rows) - 1;
			$this->updateRowData();
		}


		/**
		 * Moves record pointer to specified record
		 *
		 * @param  int	$cursor		new location of result pointer
		 *
		 * @return void
		 */
		public function move( $cursor )
		{
			// check for existing rows
			if( count($this->rows) > 0 && $cursor < count($this->rows) && $cursor > -1 )
			{
				$this->cursor = (int)$cursor;
				$this->updateRowData();
				return;
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("record index {$cursor} does not in DataSet");
			}
		}


		/**
		 * sets result pointer to record based on search criteria
		 *
		 * @param  string	$field				field name to search on
		 * @param  string	$value				field value to search for
		 * @param  bool		$case_insensitive	specifies case insentitive
		 * @return bool							true if successful
		 */
		public function seek( $field, $value, $case_insensitive = 0 )
		{
			// loop through rows
			for( $i=0; $i < count($this->rows); $i++ )
			{
				// if field exists in row
				if( $this->rows[$i][$field] )
				{
					// compare value
					if( $this->rows[$i][$field] == $value )
					{
						// set result pointer
						$this->move( $i );
						return true;
					}
					// compare value ignoring case if specified
					elseif( strtolower( $this->rows[$i][$field] ) === strtolower( $value ) && $case_insensitive )
					{
						// set result pointer
						$this->move( $i );
						return true;
					}
				}
			}
			return false;
		}


		/**
		 * Moves record pointer to first record on specified page
		 *
		 * @param  int		$page		page
		 * @return int					current page
		 */
		public function page( $page = null )
		{
			if( !is_null( $page ))
			{
				$cursor = ( $this->pageSize * ( (int) $page -1 ));

				// check for existing rows
				if( $cursor < count($this->rows) && $cursor > -1 )
				{
					// set pointer to first record of page
					$this->cursor=$cursor;

					// fill record with values from row
					$this->updateRowData();

					return (int) $page;
				}
			}
			else
			{
				if( $this->pageSize > 0 )
				{
					return (int)( $this->cursor / $this->pageSize ) + 1;
				}
				else
				{
					return 1;
				}
			}
		}


		/**
		 * Returns the number of pages with data in a DataSet object
		 *
		 * @return int						no of pages
		 */
		public function pageCount()
		{
			if( count($this->rows) )
			{
				if( $this->pageSize ) {
					// get current page
					if( count($this->rows) % $this->pageSize ) {
						return (int)( count($this->rows) / $this->pageSize ) + 1;
					}
					else {
						return (int)( count($this->rows) / $this->pageSize );
					}
				}
				return 1;
			}
			return 0;
		}


		/**
		 * filter DataSet by removing all records
		 *
		 * @param  string	$column				column name to filter
		 * @param  string	$value				field value to search for
		 * @param  string	$operator			operation to perform
		 * @param  int		$case_insensitive	specifies case insentitive
		 * @return void
		 */
		public function filter( $column, $operator, $value, $case_insensitive = false )
		{
			// alias
			if( $operator === '=' ) $operator = '==';
			if( $operator === '!=' ) $operator = '<>';

			// validate operator
			if( $operator === '==' ||					// equal
				$operator === '<=' ||					// smaller or equal
				$operator === '>=' ||					// greater or equal
				$operator === '<'  ||					// smaller than
				$operator === '>'  ||					// greater than
				$operator === '<>'  ||					// not equal
				strtolower($operator) === 'is null' ||	// null
				strtolower($operator) === 'not null' ||	// not null
				strtolower($operator) === 'contains' ||	// contains
				strtolower($operator) === 'begins' ||	// begins with
				strtolower($operator) === 'ends'		// ends with
				)
			{
				$field = null;

				foreach($this->fieldMeta as $meta)
				{
					if($meta->name==$column)
					{
						$field = $meta;
						break;
					}
				}

				if( $field )
				{
					$tmp_array = array();

					// loop through rows
					for( $i=0, $count=count($this->rows); $i < $count; $i++ )
					{
						// if field exists in row
						if(!( array_search( $column, array_keys( $this->rows[$i] )) === false ))
						{
							$cmp_value = '';
							if( $case_insensitive )
							{
								$cmp_value = strtolower( $this->rows[$i][$field->name] );
								if(is_array($value))
								{
									$tmp_value = array();
									foreach($value as $var)
									{
										$tmp_value[] = strtolower($var);
									}
									$value = $tmp_value;
								}
								else
								{
									$value = strtolower( $value );
								}
							}
							else
							{
								$cmp_value = $this->rows[$i][$field->name];
							}

							if( strtolower( $operator ) === 'is null' )
							{
								// search for string
								if( is_null( $this->rows[$i][$field->name] ))
								{
									$tmp_array[] = $this->rows[$i];
								}
							}
							elseif( strtolower( $operator ) === 'not null' )
							{
								// search for string
								if( !is_null( $this->rows[$i][$field->name] ))
								{
									$tmp_array[] = $this->rows[$i];
								}
							}
							elseif( strtolower( $operator ) === 'contains' )
							{
								// search for string
								if( !( strpos( (string) $cmp_value, (string) $value ) === false ))
								{
									$tmp_array[] = $this->rows[$i];
								}
							}
							elseif( strtolower( $operator ) === 'begins' )
							{
								// search for string
								if(( strpos( (string) $cmp_value, (string) $value ) === 0 ))
								{
									$tmp_array[] = $this->rows[$i];
								}
							}
							elseif( strtolower( $operator ) === 'ends' )
							{
								// search for string
								if(( strpos( (string) $cmp_value, (string) $value ) === strlen( $cmp_value ) - strlen( $value )))
								{
									$tmp_array[] = $this->rows[$i];
								}
							}
							elseif( $field->numeric )
							{
								// compare numbers
								eval(
									'if( $cmp_value' . $operator . (int) $value . ')
									{
										$tmp_array[] = $this->rows[$i];
									}'
								);
							}
							elseif( $field->datetime || $field->date || $field->time )
							{
								// compare dates
								eval(
									'if( strtotime($cmp_value)' . $operator . (int) strtotime($value) . ')
									{
										$tmp_array[] = $this->rows[$i];
									}'
								);
							}
							else
							{
								if(is_array($value))
								{
									foreach($value as $var)
									{
										// compare strings
										eval(
											'if( $cmp_value' . $operator . '"' . (string) $var . '")
											{
												$tmp_array[] = $this->rows[$i];
											}'
										);
									}
								}
								else
								{
									// compare strings
									eval(
										'if( $cmp_value' . $operator . '"' . (string) $value . '")
										{
											$tmp_array[] = $this->rows[$i];
										}'
									);
								}
							}
						}
						else
						{
							throw new \System\Base\ArgumentOutOfRangeException("field `{$column}` does not exist in Row");
						}
					}

					// replace DataSet
					$this->rows = $tmp_array;
					$this->updateRowData();
				}
				else
				{
					throw new \System\Base\ArgumentOutOfRangeException("field `{$column}` does not exist in DataSet");
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException("invalid operator `$operator` in expression");
			}
		}


		/**
		 * Returns true on end of file
		 *
		 * @return bool					true if end of file
		 */
		public function eof()
		{
			if( count($this->rows) > 0 )
			{
				return $this->cursor >= count($this->rows);
			}
			return true;
		}


		/**
		 * returns true at beggining of file
		 *
		 * @return bool					true if beggining of file
		 */
		public function bof()
		{
			if( count($this->rows) > 0 )
			{
				return $this->cursor < 0;
			}
			return true;
		}


		/**
		 * returns average of values in column
		 *
		 * @param  string	$column		name of column
		 * @return real
		 */
		public function getAvg( $column )
		{
			$field = null;
			for($i = 0; $i < count($this->fieldMeta); $i++)
			{
				if($this->fieldMeta[$i]->name==$column) $field = $this->fieldMeta[$i];
			}

			if( $field )
			{
				if( $field->numeric )
				{
					if( count($this->rows) > 0 )
					{
						return $this->getSum( $column ) / count($this->rows);
					}
					else
					{
						return 0;
					}
				}
				else
				{
					throw new \System\Base\InvalidOperationException("cannot perform operation `getAvg` on column `$column`");
				}
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("field `$column` does not exist in DataSet");
			}
		}


		/**
		 * returns max of values in column
		 *
		 * @param  string	$column		name of column
		 * @return real
		 */
		public function getMax( $column )
		{
			$max = 0;
			$field = null;
			for($i = 0; $i < count($this->fieldMeta); $i++)
			{
				if($this->fieldMeta[$i]->name==$column) $field = $this->fieldMeta[$i];
			}

			if( $field )
			{
				if( $field->numeric )
				{
					// loop through rows
					for( $i=0, $count=count($this->rows); $i < $count; $i++ )
					{
						// if field exists in row
						if( isset($this->rows[$i][$column] ))
						{
							if( $max < (real)$this->rows[$i][$column] )
							{
								$max = (real)$this->rows[$i][$column];
							}
						}
						else
						{
							throw new \System\Base\ArgumentOutOfRangeException("column `$column` does not exist in Row");
						}
					}

					return $max;
				}
				elseif( $this->fieldMeta[$index]->datetime || $this->fieldMeta[$index]->date || $this->fieldMeta[$index]->time )
				{
					for( $i=0, $count=count($this->rows); $i < $count; $i++ )
					{
						// if field exists in row
						if( isset($this->rows[$i][$column] ))
						{
							if( $max < strtotime( $this->rows[$i][$column] ))
							{
								$max = strtotime( $this->rows[$i][$column] );
							}
						}
						else
						{
							throw new \System\Base\ArgumentOutOfRangeException("column `$column` does not exist in Row");
						}
					}

					return date('Y-m-d H:i:s',$max);
				}
				else
				{
					throw new \System\Base\InvalidOperationException("cannot perform operation `getMax` on column `$column`");
				}
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("field `$column` does not exist in DataSet");
			}
		}


		/**
		 * returns min of values in column
		 *
		 * @param  string	$column		name of column
		 * @return real
		 */
		public function getMin( $column )
		{
			$min = false;
			$field = null;
			for($i = 0; $i < count($this->fieldMeta); $i++)
			{
				if($this->fieldMeta[$i]->name==$column) $field = $this->fieldMeta[$i];
			}

			if( $field )
			{
				// loop through rows
				if( $field->numeric )
				{
					for( $i = 0, $count = count($this->rows); $i < $count; $i++ )
					{
						// if field exists in row
						if( isset($this->rows[$i][$column] ))
						{
							if( $min === false || $min > (real)$this->rows[$i][$column] )
							{
								$min = (real)$this->rows[$i][$column];
							}
						}
						else
						{
							throw new \System\Base\ArgumentOutOfRangeException("column `$column` does not exist in Row");
						}
					}

					return $min;
				}
				elseif( $field->datetime || $field->date || $field->time )
				{
					for( $i = 0, $count = count($this->rows); $i < $count; $i++ )
					{
						// if field exists in row
						if( isset($this->rows[$i][$column] ))
						{
							if( $min === false || $min > strtotime( $this->rows[$i][$column] ))
							{
								$min = strtotime( $this->rows[$i][$column] );
							}
						}
						else
						{
							throw new \System\Base\ArgumentOutOfRangeException("column `$column` does not exist in Row");
						}
					}

					return date('Y-m-d H:i',$min);
				}
				else
				{
					throw new \System\Base\InvalidOperationException("cannot perform operation `getMin` on column `$column`");
				}
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("field `$column` does not exist in DataSet");
			}
		}


		/**
		 * returns sum of all values in column
		 *
		 * @param  string	$column		name of column
		 * @return int
		 */
		public function getSum( $column )
		{
			$field = null;
			for($i = 0; $i < count($this->fieldMeta); $i++)
			{
				if($this->fieldMeta[$i]->name==$column) $field = $this->fieldMeta[$i];
			}

			if( $field )
			{
				if( $field->numeric )
				{
					$sum = 0;

					// loop through rows
					for( $i = 0, $count = count($this->rows); $i < $count; $i++ )
					{
						// if field exists in row
						if( isset($this->rows[$i][$column] ))
						{
							$sum += (real) $this->rows[$i][$column];
						}
					}

					return $sum;
				}
				else
				{
					throw new \System\Base\InvalidOperationException("cannot perform operation `getSum` on column `$column`");
				}
			}
			else
			{
				throw new \System\Base\ArgumentOutOfRangeException("field `$column` does not exist in DataSet");
			}
		}


		/**
		 * Requery datasource
		 *
		 * @return void
		 */
		public function requery()
		{
			trigger_error("DataSet::requery() is deprecated, use DataSet::refresh() instead", E_USER_DEPRECATED);
			$this->refresh();
		}


		/**
		 * refresh data from data source
		 *
		 * @return void
		 */
		public function refresh()
		{
			if( $this->lockType == DataSetType::OpenDynamic() )
			{
				$this->table = '';
				$this->fieldMeta = array();
				$this->fields = array();
				$this->rows = array();
				if($this->dataAdapter->closed)
				{
					$this->dataAdapter->open();
				}
				$this->dataAdapter->fill( $this );
				$this->updateRowData();
				$this->first();
			}
		}


		/**
		 * write data to data source
		 *
		 * @return void
		 */
		public function save()
		{
			if( isset( $this->rows[$this->cursor] ))
			{
				return $this->update();
			}
			else
			{
				return $this->insert();
			}
		}


		/**
		 * return fields as array
		 *
		 * @return void
		 */
		public function fields()
		{
			return $this->fields;
		}


		/**
		 * Inserts record into DataSource
		 *
		 * @return void
		 */
		public function insert()
		{
			if( $this->lockType == DataSetType::OpenDynamic() )
			{
				$this->dataAdapter->insert( $this );

				$this->rows[] = $this->row;
				$this->last();
			}
			elseif( $this->lockType == DataSetType::OpenStatic() )
			{
				$this->rows[] = $this->row;
				$this->last();
			}
			elseif( $this->lockType == DataSetType::OpenReadonly() )
			{
				throw new \System\Base\InvalidOperationException("Cannot perform insert operation on readonly DataSet");
			}
		}


		/**
		 * Update DataSource
		 *
		 * @return void
		 */
		public function update()
		{
			if( $this->lockType == DataSetType::OpenDynamic() )
			{
				$this->dataAdapter->update( $this );

				$this->rows[$this->cursor] = $this->row;
				$this->updateRowData();
			}
			elseif( $this->lockType == DataSetType::OpenStatic() )
			{
				$this->rows[$this->cursor] = $this->row;
				$this->updateRowData();
			}
			elseif( $this->lockType == DataSetType::OpenReadonly() )
			{
				throw new \System\Base\InvalidOperationException("Cannot perform update operation on readonly DataSet");
			}
		}


		/**
		 * Delete record from DataSource
		 *
		 * @return void
		 */
		public function delete()
		{
			if( $this->lockType == DataSetType::OpenDynamic() )
			{
				if( count($this->rows) > 0 )
				{
					$this->dataAdapter->delete( $this );

					unset( $this->rows[$this->cursor] );
					$this->rows = array_values( $this->rows );
					$this->updateRowData();
				}
				else
				{
					throw new \System\Base\InvalidOperationException("Cannot delete null record");
				}
			}
			elseif( $this->lockType == DataSetType::OpenStatic() )
			{
				if( count($this->rows) > 0 )
				{
					unset( $this->rows[$this->cursor] );
					$this->rows = array_values( $this->rows );
					$this->updateRowData();
				}
				else
				{
					throw new \System\Base\InvalidOperationException("Cannot delete null record");
				}
			}
			elseif( $this->lockType == DataSetType::OpenReadonly() )
			{
				throw new \System\Base\InvalidOperationException("Cannot perform delete operation on readonly DataSet");
			}
		}


		/**
		 * Returns an array containg all rows
		 *
		 * @return array
		 */
		public function toArray()
		{
			return $this->rows;
		}


		/**
		 * Returns a DataSet as a CSV string
		 *
		 * @param  char		$enclosure		string enclosure
		 * @param  char		$fdelimiter		column dilemeter
		 * @param  char		$rdelimiter		row dilemeter
		 * @param  string	$nullexpr		null expression
		 * @param  bool		$fieldnames		include fields names in string
		 * @param  int		$n				no of rows
		 * @return string					CSV string
		 */
		public function getCSVString( $enclosure="\"", $fdelimiter="\t", $rdelimiter="\n", $nullexpr = 'NULL', $fieldnames=true, $n=0 )
		{
			$_string = '';
			$_writerdelimiter=false;

			/*
			 * create field objects
			 *
			 * this code loops through fields of fieldset
			 * adds all field names to an array
			 *
			 */
			if( $fieldnames )
			{
				$_writedelimiter=false;
				$_writerdelimiter=true;

				for( $i = 0, $count = count($this->fieldMeta); $i < $count; $i++ )
				{
					$field_object = $this->fieldMeta[ $i ];
					if( $_writedelimiter )
					{
						$_string .= $fdelimiter;
					}
					else
					{
						$_writedelimiter=true;
					}
					$_string .= $enclosure . addslashes( $field_object->name ) . $enclosure;
				}
			}

			/*
			 * create record objects
			 *
			 * this code loops through all records
			 * adds array to csv
			 */
			$newline = chr(10);
			$rowcount = count($this->rows);
			$colcount = count($this->fields);
			for( $row=0; $row < $rowcount && ( $row < $n || $n === 0 ); $row++ )
			{
				if( $_writerdelimiter )
				{
					$_string .= $rdelimiter;
				}
				else
				{
					$_writerdelimiter=true;
				}

				$_writedelimiter=false;

				for( $col=0; $col < $colcount; $col++ )
				{
					$field_object = $this->fieldMeta[$col];

					if( $_writedelimiter )
					{
						$_string .= $fdelimiter;
					}
					else
					{
						$_writedelimiter=true;
					}

					if( $this->rows[$row][$field_object->name] === null )
					{
						$_string .= $nullexpr;
					}
					else
					{
						$_string .= $enclosure . str_replace( '"', '""', str_replace( "\n", $newline, str_replace( "\r", $newline, $this->rows[$row][$field_object->name] ))) . $enclosure;
					}
				}
			}
			return $_string;
		}


		/**
		 * Returns a DataSet as an XML string
		 *
		 * @since  3.5.5
		 * @return string					XML string
		 */
		public function getXMLString()
		{
			$xmlDataSet = new \System\XML\XMLEntity( 'DataSet' );
			$xmlFields = new \System\XML\XMLEntity( 'fields' );

			foreach( $this->fields as $field )
			{
				$xmlField = new \System\XML\XMLEntity( 'field' );

				$xmlFieldName = new \System\XML\XMLEntity( 'name' );
				$xmlFieldName->value = $field->name;

				$xmlFieldLength = new \System\XML\XMLEntity( 'length' );
				$xmlFieldLength->value = $field->length;

				$xmlFieldType = new \System\XML\XMLEntity( 'type' );
				$xmlFieldType->value = $field->type;

				$xmlFieldNumeric = new \System\XML\XMLEntity( 'numeric' );
				$xmlFieldNumeric->value = $field->numeric?'true':'false';

				$xmlFieldDateTime = new \System\XML\XMLEntity( 'datetime' );
				$xmlFieldDateTime->value = ($field->datetime||$field->date||$field->time)?'true':'false';

				$xmlFieldBoolean = new \System\XML\XMLEntity( 'boolean' );
				$xmlFieldBoolean->value = $field->boolean?'true':'false';

				$xmlFieldPrimaryKey = new \System\XML\XMLEntity( 'primaryKey' );
				$xmlFieldPrimaryKey->value = $field->primaryKey?'true':'false';

				$xmlField->addChild( $xmlFieldName );
				$xmlField->addChild( $xmlFieldLength );
				$xmlField->addChild( $xmlFieldType );
				$xmlField->addChild( $xmlFieldNumeric );
				$xmlField->addChild( $xmlFieldDateTime );
				$xmlField->addChild( $xmlFieldBoolean );
				$xmlField->addChild( $xmlFieldPrimaryKey );

				$xmlFields->addChild( $xmlField );
			}

			$xmlRecords = new \System\XML\XMLEntity( 'records' );
			foreach( $this->rows as $record )
			{
				$xmlRecord = new \System\XML\XMLEntity( 'record' );

				foreach( $this->fields as $field )
				{
					$xmlField = new \System\XML\XMLEntity( 'field' );
					$xmlField->setAttribute( 'name', $field->name );
					if( $field->boolean )
					{
						$xmlField->value = base64_encode( $record[$field->name] );
					}
					else
					{
						$xmlField->value = $record[$field->name];
					}

					$xmlRecord->addChild( $xmlField );
				}

				$xmlRecords->addChild( $xmlRecord );
			}

			$xmlDataSet->addChild( $xmlFields );
			$xmlDataSet->addChild( $xmlRecords );

			return $xmlDataSet->getXMLString();
		}
	}
?>