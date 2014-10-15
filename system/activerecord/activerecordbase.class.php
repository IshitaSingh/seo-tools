<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\ActiveRecord;
	use System\Web\FormModelBase;


	/**
	 * This class represents a table withing a database or an instance of a single
	 * record in a table and provides database abstraction.
	 *
	 * The ActiveRecordBase exposes 5 protected properties, do not define these properties in the sub class
	 * to have the properties auto determined
	 * @property string $table Specifies the table name
	 * @property string $pkey Specifies the primary key (there can only be one primary key defined)
	 * @property array $fields Specifies field names mapped to field types
	 * @property array $rules Specifies field names mapped to field rules
	 * @property array $relationahips Specifies table relationships
	 *
	 * 2 Additional public readonly properties are exposed
	 * @property bool $isNull Specifies whether ActiveRecord is null
	 * @property bool $isEmpty Specifies whether ActiveRecord is empty
	 *
	 * @package			PHPRum
	 * @subpackage		ActiveRecord
	 * @author			Darnell Shinbine
	 */
	abstract class ActiveRecordBase extends FormModelBase
	{
		/**
		 * Specifies the table name
		 * @var string
		**/
		protected $table			= '';

		/**
		 * Specifies the primary key (there can only be one primary key defined)
		 * @var string
		**/
		protected $pkey				= '';

		/**
		 * Specifies the sort key
		 * @var string
		**/
		protected $sortKey			= '';

		/**
		 * Specifies table relationships
		 * @var array
		**/
		protected $relationships	= array();

		/**
		 * DataSet
		 * @var DataSet
		**/
		private $dataSet			= null;


		/**
		 * Constructor
		 *
		 * @return void
		 */
		final protected function __construct()
		{
			if(!$this->sortKey) $this->sortKey = $this->pkey;
			$this->init();
		}


		/**
		 * invokes a dynamic method
		 *
		 * @param  string   $function   name of the method
		 * @param  array	$args	   array of arguments
		 * @return mixed
		 * @ignore
		 */
		final public function __call( $function, array $args = array() )
		{
			$pos = 0;
			foreach( $this->relationships as $mapping )
			{
				if( strrchr( $mapping['type'], '\\') !== false )
				{
					$index = stripos( $function, substr( strrchr( $mapping['type'], '\\'), 1 ));
				}
				else
				{
					$index = stripos( $function, $mapping['type'] );
				}

				if( $index !== false )
				{
					$type = $mapping['type'];
					$pos  = $index;
				}
			}

			if( isset( $type ))
			{
				$subject = '';
				if( strrchr( $type, '\\') !== false )
				{
					$subject = substr( strrchr( $type, '\\'), 1 );
				}
				else
				{
					$subject = $type;
				}

				$prefix = (string)substr( $function, 0, $pos );
				$suffix = (string)substr( $function, $pos + strlen( $subject ), strlen( $function ) - $pos + strlen( $subject ));

				/**
				 * [prefix]	add, remove, removeAll, delete, deleteAll, find, findAll, getParent, getAll, get
				 * [type]
				 * [suffix]	ById, RecordById, Record, Records, DataSet
				 * [args]	ActiveRecordBase
				 */

				// add[Type]Record( [Type] $args[0] ) = addRecord( [Type] $args[0] )
				if( $prefix === 'create' && ( $suffix === 'Record' || $suffix === '' ))
				{
					if( count($args) <= 1 )
					{
						return $this->createRecordByType( $type, isset($args[0])?$args[0]:array() );
					}
					else
					{
						throw new \System\Base\MissingArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type array");
					}
				}

				// add[Type]Record( [Type] $args[0] ) = addRecord( [Type] $args[0] )
				if( $prefix === 'add' && ( $suffix === 'Record' || $suffix === '' ))
				{
					if( count($args) === 1 )
					{
						if( $args[0] instanceof $type )
						{
							return $this->addRecord( $args[0] );
						}
						else
						{
							throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type $type");
						}
					}
					else
					{
						throw new \System\Base\MissingArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type $type");
					}
				}

				// remove[Type]Record( [Type] $args[0] ) = removeRecord( [Type] $args[0] )
				elseif( $prefix === 'remove' && ( $suffix === 'Record' || $suffix === '' ))
				{
					if( count($args) === 1 )
					{
						if( $args[0] instanceof $type )
						{
							return $this->removeRecord( $args[0] );
						}
						else
						{
							throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type $type");
						}
					}
					else
					{
						throw new \System\Base\MissingArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type $type");
					}
				}

				// removeAll[Type]Records() = removeAllRecordsByType( string $type )
				elseif( $prefix === 'removeAll' && ( $suffix === 'Records' || $suffix === 's' || $suffix === '' ))
				{
					if( count($args) === 0 )
					{
						return $this->removeAllRecordsByType( $type );
					}
					else
					{
						throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects no parameters");
					}
				}

				// delete[Type]Record( [Type] $args[0] ) = addRecord( [Type] $args[0] )
				if( $prefix === 'delete' && ( $suffix === 'Record' || $suffix === '' ))
				{
					if( count($args) === 1 )
					{
						if( $args[0] instanceof $type )
						{
							return $this->deleteRecord( $args[0] );
						}
						else
						{
							throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type $type");
						}
					}
					else
					{
						throw new \System\Base\MissingArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type $type");
					}
				}

				// deleteAll[Type]Records() = deleteAllRecordsByType( string $type )
				elseif( $prefix === 'deleteAll' && ( $suffix === 'Records' || $suffix === 's' || $suffix === '' ))
				{
					if( count($args) === 0 )
					{
						return $this->deleteAllRecordsByType( $type );
					}
					else
					{
						throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects no parameters");
					}
				}

				// getAll[Type]Records() = getAllRecordsByType( string $type )
				elseif( $prefix === 'getAll' && ( $suffix === 'Records' || $suffix === 's' || $suffix === '' ))
				{
					if( count($args) <= 1 )
					{
						return $this->getAllRecordsByType( $type, isset($args[0])?$args[0]:array() );
					}
					else
					{
						throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type array");
					}
				}

				// getCount[Type]Records() = findAllRecordsByType( string $type )
				elseif( $prefix === 'getCount' && ( $suffix === 'Records' || $suffix === 's' || $suffix === '' ))
				{
					if( count($args) <= 1 )
					{
						return $this->getCountByType( $type, isset($args[0])?$args[0]:array() );
					}
					else
					{
						throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type array");
					}
				}

				// getParent[Type]Record() = findParentRecordByType( string $type )
				elseif( $prefix === 'findParent' && ( $suffix === 'Record' || $suffix === '' ))
				{
					if( count($args) === 0 )
					{
						return $this->findParentRecordByType( $type );
					}
					else
					{
						throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects no parameters");
					}
				}

				// get[Type]Record( int $args[0] ) = findRecordByType( string $type, int $args[0] )
				elseif( $prefix === 'find' && ( $suffix === 'Record' || $suffix === '' ))
				{
					if( count($args) === 1 )
					{
						return $this->findRecordByType( $type, $args[0] );
					}
					else
					{
						throw new \System\Base\MissingArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter");
					}
				}

				// findAll[Type]Records() = findAllRecordsByType( string $type )
				elseif( $prefix === 'findAll' && ( $suffix === 'Records' || $suffix === 's' || $suffix === '' ))
				{
					if( count($args) <= 1 )
					{
						return $this->findAllRecordsByType( $type, isset($args[0])?$args[0]:array() );
					}
					else
					{
						throw new \System\Base\InvalidArgumentException("Overloaded method ".get_class($this)."::$function expects one parameter of type array");
					}
				}
				else
				{
					throw new \System\Base\BadMethodCallException("call to undefined method $function in ".get_class( $this ));
				}
			}
			else
			{
				throw new \System\Base\BadMethodCallException("call to undefined method $function in ".get_class( $this ));
			}
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return mixed
		 * @ignore
		 */
		final public function __get( $field )
		{
			if( $field === 'isNull' )
			{
				return (bool) $this->dataSet === null;
			}
			elseif( $field === 'isEmpty' )
			{
				if( !$this->isNull )
				{
					return (bool) ( $this->dataSet->count === 0 );
				}
				else
				{
					return true;
				}
			}
			elseif( $field === 'table' )
			{
				return $this->table;
			}
			elseif( $field === 'pkey' )
			{
				return $this->pkey;
			}
			elseif( $field === 'associations' )
			{
				return $this->relationships;
			}
			elseif( array_key_exists( $field, $this->dataSet->row ))
			{
				return $this[$field];
			}
			else
			{
				return parent::__get($field);
			}
		}


		/**
		 * sets an object property
		 *
		 * @param  string	$field		name of the field
		 * @param  mixed	$value		value of the field
		 * @return bool					true on success
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( array_key_exists( (string)$field, $this->dataSet->row ))
			{
				$this[(string)$field] = $value;
			}
			else
			{
				parent::__set($field, $value);
			}
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		final function offsetExists($index)
		{
			if( array_key_exists( $index, $this->dataSet->row ))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		final function offsetGet($index)
		{
			if( array_key_exists( $index, $this->dataSet->row ))
			{
				return $this->dataSet->row[$index];
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
		final function offsetSet($index, $value)
		{
			if( array_key_exists( $index, $this->dataSet->row ))
			{
				$this->dataSet[$index] = $value;
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
		final function offsetUnset($index)
		{
			if( array_key_exists( $index, $this->dataSet->row ))
			{
				unset( $this->dataSet->row[$index] );
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * discard cahnges
		 *
		 * @return void
		 */
		final public function refresh()
		{
			$this->beforeRetrieve();
			$this->dataSet->requery();
			$this->afterRetrieve();
		}


		/**
		 * update DataAdapter
		 *
		 * @return void
		 */
		final public function save()
		{
			$this->beforeSave();

			if( !$this->isEmpty )
			{
				$this->beforeUpdate();
				$this->dataSet->update();
				$this->afterUpdate();
			}
			else
			{
				$this->beforeInsert();
				$this->dataSet->insert();
				$this->afterInsert();
			}

			$this->afterSave();
		}


		/**
		 * delete ActiveRecordBase
		 *
		 * @return void
		 */
		final public function delete()
		{
			if( !$this->isEmpty )
			{
				$this->beforeDelete();
				$this->dataSet->delete();
				$this->dataSet = null;
				$this->afterDelete();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot delete empty record");
			}
		}


		/**
		 * delete ActiveRecordBase and all associated ActiveRecordBases (cascading delete)
		 *
		 * @return void
		 */
		final public function deleteAll()
		{
			if( !$this->isEmpty )
			{
				foreach( $this->relationships as $mapping )
				{
					if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						$records = $this->findAllRecordsByType( $mapping['type'] );

						foreach( $records as $record )
						{
							$record->deleteAll();
						}

						continue;
					}

					elseif( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString() )
					{
						$this->dataSet->dataAdapter->prepare( '
							delete from
								`' . $mapping['table'] . '`
							where
								`' . $mapping['columnKey'] . '` = ' . $this[$mapping['columnKey']] )->execute();

						continue;
					}
				}

				$this->delete();
			}
			else
			{
				throw new \System\Base\InvalidOperationException ("cannot delete empty record");
			}
		}


		/**
		 * returns an array containing all row data
		 *
		 * @return array
		 */
		public function toArray()
		{
			return $this->dataSet->row;
		}


		/**
		 * public static factory methods
		 */


		/**
		 * static method to create new ActiveRecordBase of this type
		 *
		 * @param  array		$args		optional associative array of initial properties
		 * @return ActiveRecordBase
		 */
		static public function create( array $args = array() )
		{
			return ActiveRecordBase::addByType( self::getClass(), $args );
		}


		/**
		 * static method to find ActiveRecordBase of this type
		 *
		 * @param  array		$args		associative array of keys and values
		 * @return ActiveRecordBase
		 */
		static public function find( array $args )
		{
			return ActiveRecordBase::findByType( self::getClass(), $args );
		}


		/**
		 * static method to find ActiveRecordBase by primary key
		 *
		 * @param  mixed		$pid		key value to lookup
		 * @return ActiveRecordBase
		 */
		static public function findById( $pid )
		{
			$class = self::getClass();
			$activeRecord = new $class();

			return ActiveRecordBase::findByType( self::getClass(), array( $activeRecord->pkey => $pid ));
		}


		/**
		 * static method to find ActiveRecordBases of this type
		 *
		 * @param  array		$args		associative array of keys and values
		 * @return ActiveRecordCollection
		 */
		static public function findAll( array $args = array() )
		{
			trigger_error("ActiveRecord::findAll(args) is deprecated, use ::all instead", E_USER_DEPRECATED);
			return ActiveRecordBase::findAllByType( self::getClass(), $args );
		}


		/**
		 * static method to find the first ActiveRecordBase of this type
		 *
		 * @param  string		$args		associative array of keys and values
		 * @return ActiveRecordBase
		 */
		static public function first( array $args = array())
		{
			return ActiveRecordBase::firstByType( self::getClass(), $args );
		}


		/**
		 * static method to find the last ActiveRecordBase of this type
		 *
		 * @param  string		$args		associative array of keys and values
		 * @return ActiveRecordBase
		 */
		static public function last( array $args = array() )
		{
			return ActiveRecordBase::lastByType( self::getClass(), $args );
		}


		/**
		 * static method to return a DataSet of this type
		 *
		 * @param  array		$args		associative array of keys and values
		 * @return DataSet
		 */
		static public function all( array $columns = array(), array $filter = array(), array $sort_by = array(), $offset = 0, $limit = 0 )
		{
			return ActiveRecordBase::allByType( self::getClass(), $columns, $filter, $sort_by, $offset, $limit );
		}


		/**
		 * static method to return a filtered DataSet of this type
		 * 
		 * @param  array		$columns	array of column names to return
		 * @param  array		$filter		associative array of column names and values to filter
		 * @param  array		$sort_by	array of column names to sort by
		 * @param  int			$offset		number of records to offset
		 * @param  int			$limit		resultset limit
		 * @return DataSet
		 */
//		static public function filter( array $columns = array(), array $filter = array(), array $sort_by = array(), $offset = 0, $limit = 0 )
//		{
//			return ActiveRecordBase::allByType( self::getClass(), $columns, $filter, $sort_by, $offset, $limit );
//		}


		/**
		 * static method to return an extended DataSet of this type
		 *
		 * @param  array		$args		associative array of keys and values
		 * @return DataSet
		 */
		static public function more( array $args = array() )
		{
			return ActiveRecordBase::allExtendedByType( self::getClass(), $args );
		}


		/**
		 * static method to return a count of the number of records of this type
		 *
		 * @param  array		$args		associative array of keys and values
		 * @return DataSet
		 */
		static public function countAll( array $args = array() )
		{
			return ActiveRecordBase::countByType( self::getClass(), $args );
		}


		/**
		 * static method to return a Form object
		 *
		 * @param  string		$controlId		form id
		 * @return Form
		 */
		static public function form( $controlId )
		{
			$activeRecord = self::create();

			$form = new \System\Web\WebControls\Form( $controlId );

			// create controls
			foreach( $activeRecord->fields as $field => $type )
			{
				if(isset(self::$field_mappings[$type]))
				{
					$form->add(new self::$field_mappings[$type]($field));
				}
				else
				{
					throw new \System\Base\InvalidOperationException("No field mapping assigned to `{$type}`");
				}

				$control = $form->getControl( $field );
				$form->add(new \System\Web\WebControls\ValidationMessage($field.'_error', $control));
//				$control->label = ucwords( \System\Base\ApplicationBase::getInstance()->translator->get( $field, str_replace( '_', ' ', $field )));

				// create references
				if($type === 'ref')
				{
					foreach( $activeRecord->relationships as $mapping )
					{
						// belongs_to
						if( $mapping['columnKey'] === $field &&
							$mapping['relationship'] == RelationshipType::BelongsTo()->__toString() )
						{
							$class = $mapping['type'];
							$ds = $class::all();
//							$label = \substr( strrchr( $mapping['type'], '\\'), 1 );

							$control->textField = isset($mapping["columnText"])?$mapping["columnText"]:$mapping["columnRef"];
							$control->valueField = $mapping["columnKey"];
							$control->dataSource = $ds;
//							$control->label = ucwords( \System\Base\ApplicationBase::getInstance()->translator->get( $label, $label ));

							continue 2;
						}
					}
				}
				// create list
				else if($type === 'enum')
				{
					$options = array();
					foreach( $activeRecord->rules[$field] as $rule )
					{
						$type = \strstr($rule, '(', true);
						if($type === 'enum')
						{
							$type = \strstr($rule, '(', true);
							if(!$type)
							{
								$type = $rule;
							}
							$params = \strstr($rule, '(');
							if(!$params)
							{
								$params = '()';
							}
							eval("\$options = {$params};");

							foreach($options as $key=>$value) {
								$control->items->add($key, $value);
							}

							continue 2;
						}
					}
				}
			}

			/**
			// map has_many_and_belongs_to
			foreach( $activeRecord->relationships as $mapping )
			{
				if( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString())
				{
					$class = $mapping['type'];
					$ds = $class::all();
					$label = \substr( strrchr( $mapping['type'], '\\'), 1 );

					foreach( $ds->fields as $field )
					{
						if( !$field->primaryKey )
						{
							$textField = $field;
						}
					}

					$form->fieldset->add( new \System\Web\WebControls\ListBox( $mapping['columnRef'] ));
					$form->fieldset->getControl( $mapping['columnRef'] )->valueField = $mapping['columnRef'];
					$form->fieldset->getControl( $mapping['columnRef'] )->textField = $textField;
					$form->fieldset->getControl( $mapping['columnRef'] )->dataSource = $ds;
					$form->fieldset->getControl( $mapping['columnRef'] )->dataBind();
					$form->fieldset->getControl( $mapping['columnRef'] )->multiple  = true;
//					$form->fieldset->getControl( $mapping['columnRef'] )->label = ucwords( \System\Base\ApplicationBase::getInstance()->translator->get( $label, $label ));
				}
			}
			*/

			// implement rules
			foreach( $activeRecord->rules as $field => $rules )
			{
				$validators = array();
				if(\is_array($rules))
				{
					foreach($rules as $rule)
					{
						$type = \strstr($rule, '(', true);
						if(!$type)
						{
							$type = $rule;
						}
						$params = \strstr($rule, '(');
						if(!$params)
						{
							$params = '()';
						}

						if(isset(self::$rule_mappings[$type]))
						{
							$validators[] = self::$rule_mappings[$type].$params;
						}
						else
						{
							throw new \System\Base\InvalidOperationException("No rule mapping assigned to `{$type}`");
						}
					}
				}
				else
				{
					$type = \strstr($rules, '(', true);
					if(!$type)
					{
						$type = $rules;
					}
					$params = \strstr($rules, '(');
					if(!$params)
					{
						$params = '()';
					}

					if(isset(self::$rule_mappings[$type]))
					{
						$validators[] = self::$rule_mappings[$type].$params;
					}
					else
					{
						throw new \System\Base\InvalidOperationException("No rule mapping assigned to `{$type}`");
					}
				}

				foreach( $validators as $validator )
				{
					if($form->hasControl($field))
					{
						eval("\$validator = new {$validator};");
						if($validator instanceof \System\Validators\ValidatorBase)
						{
							if($validator instanceof \System\Validators\UniqueValidator) {
								$validator->dataSource = self::all();
							}
							$form->getControl($field)->addValidator($validator);
						}
					}
				}
			}

			return $form;
		}


		/**
		 * static method to return a GridView object
		 *
		 * @param  string		$controlId		form id
		 * @return GridView
		 */
		static public function gridview( $controlId )
		{
			$activeRecord = self::create();
			$class = get_class($activeRecord);

			$gridView = new \System\Web\WebControls\GridView( $controlId );

			// create controls
			foreach( $activeRecord->fields as $field => $type )
			{
				$column = null;
//				$header = ucwords( \System\Base\ApplicationBase::getInstance()->translator->get( $field, str_replace( '_', ' ', $field )));
				$param = $field;

				if(isset(self::$field_mappings[$type]))
				{
					// create references
					if($type === 'ref')
					{
						$options = array();
						foreach( $activeRecord->relationships as $mapping )
						{
							// belongs_to
							if( $mapping['columnKey'] === $field &&
								$mapping['relationship'] == RelationshipType::BelongsTo()->__toString() )
							{
								$class = $mapping['type'];
								$ds = $class::all();

								foreach($ds->rows as $row)
								{
									$options[$row[$mapping["columnRef"]]] = $row;
								}

								break;
							}
						}

						$column = new \System\Web\WebControls\GridViewDropDownList($field, $activeRecord->pkey, $options, $param, isset($mapping["columnText"])?$mapping["columnText"]:$mapping["columnRef"]);
						$column->textField = isset($mapping["columnText"])?$mapping["columnText"]:$mapping["columnRef"];
						$column->valueField = $mapping["columnRef"];
						$column->setFilter(new \System\Web\WebControls\GridViewListFilter($options));
						$column->default = $activeRecord[$field];
					}
					// create selection list
					else if($type === 'enum')
					{
						$options = array();
						foreach( $activeRecord->rules[$field] as $rule )
						{
							$type = \strstr($rule, '(', true);
							if($type === 'enum')
							{
								$type = \strstr($rule, '(', true);
								if(!$type)
								{
									$type = $rule;
								}
								$params = \strstr($rule, '(');
								if(!$params)
								{
									$params = '()';
								}
								eval("\$options = {$params};");
							}
						}

						$column = new \System\Web\WebControls\GridViewDropDownList($field, $activeRecord->pkey, $options, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewListFilter($options));
						$column->default = $activeRecord[$field];
					}
					else if($type === 'date')
					{
						$column = new \System\Web\WebControls\GridViewDate($field, $activeRecord->pkey, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewDateFilter());
						$column->default = $activeRecord[$field];
					}
					else if($type === 'time')
					{
						$column = new \System\Web\WebControls\GridViewTime($field, $activeRecord->pkey, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewTimeFilter());
						$column->default = $activeRecord[$field];
					}
					else if($type === 'datetime')
					{
						$column = new \System\Web\WebControls\GridViewDateTime($field, $activeRecord->pkey, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewDateTimeFilter());
						$column->default = $activeRecord[$field];
					}
					else if($type === 'boolean')
					{
						$column = new \System\Web\WebControls\GridViewCheckBox($field, $activeRecord->pkey, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewBooleanFilter());
						$column->default = $activeRecord[$field];
					}
					else if($type === 'blob')
					{
						$column = new \System\Web\WebControls\GridViewTextArea($field, $activeRecord->pkey, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewStringFilter());
						$column->default = $activeRecord[$field];
					}
					else if($type === 'search')
					{
						$column = new \System\Web\WebControls\GridViewSearch($field, $activeRecord->pkey, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewStringFilter());
						$column->default = $activeRecord[$field];
					}
					else
					{
						$column = new \System\Web\WebControls\GridViewText($field, $activeRecord->pkey, $param, $field);
						$column->setFilter(new \System\Web\WebControls\GridViewStringFilter());
						$column->default = $activeRecord[$field];
					}

					// Create the change event
//					$eventHandler = function ($sender, $args) use ($pkey, $param, $field, $class, $controlId) {
//
//						$entity = $class::findById($args[$pkey]);
//
//						if($entity)
//						{
//							$entity[$field] = $args[$param];
//							$entity->save();
//
//							if(\Rum::app()->requestHandler->isAjaxPostBack)
//							{
//								\Rum::app()->requestHandler->page->{$controlId}->refreshDataSource();
//								\Rum::app()->requestHandler->page->{$controlId}->updateAjax();
//							}
//						}
//						else {
//							throw new \System\Base\InvalidOperationException("Could not write to entity object");
//						}
//					};

					// Bind event handler method to the page controller
					// \Rum::app()->requestHandler->attachFunction("on{$field}Post", $eventHandler);

					// attach the event to the event listener
//					$column->events->registerEventHandler(new \System\Web\Events\GridViewColumnPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . "on{$field}Post"));
//					$column->events->registerEventHandler(new \System\Web\Events\GridViewColumnAjaxPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . "on{$field}Post"));
				}
				else
				{
					throw new \System\Base\InvalidOperationException("No field mapping assigned to `{$type}`");
				}

				$gridView->columns->add($column);
			}

			// implement rules
			foreach( $activeRecord->rules as $field => $rules )
			{
				$validators = array();
				if(\is_array($rules))
				{
					foreach($rules as $rule)
					{
						$type = \strstr($rule, '(', true);
						if(!$type)
						{
							$type = $rule;
						}
						$params = \strstr($rule, '(');
						if(!$params)
						{
							$params = '()';
						}

						if(isset(self::$rule_mappings[$type]))
						{
							$validators[] = self::$rule_mappings[$type].$params;
						}
						else
						{
							throw new \System\Base\InvalidOperationException("No rule mapping assigned to `{$type}`");
						}
					}
				}
				else
				{
					$type = \strstr($rules, '(', true);
					if(!$type)
					{
						$type = $rules;
					}
					$params = \strstr($rules, '(');
					if(!$params)
					{
						$params = '()';
					}

					if(isset(self::$rule_mappings[$type]))
					{
						$validators[] = self::$rule_mappings[$type].$params;
					}
					else
					{
						throw new \System\Base\InvalidOperationException("No rule mapping assigned to `{$type}`");
					}
				}

				foreach( $validators as $validator )
				{
					if($gridView->findColumn($field))
					{
						$validatorIntance = null;
						if(false===eval("\$validatorIntance = new {$validator};"))
						{
							throw new \System\Base\InvalidOperationException("Cannot create validator: new {$validator};");
						}

						if($validatorIntance instanceof \System\Validators\UniqueValidator) {
							$validatorIntance->dataSource = self::all();
						}
						$gridView->findColumn($field)->addValidator($validatorIntance);
					}
				}
			}

			return $gridView;
		}


		/**
		 * protected events
		 */


		/**
		 * event called before a new ActiveRecordBase is created
		 *
		 * @return void
		 */
		protected function beforeCreate() {}


		/**
		 * event called after a new ActiveRecordBase is created
		 *
		 * @return void
		 */
		protected function afterCreate() {}


		/**
		 * event called before every retrieve
		 *
		 * @return void
		 */
		protected function beforeRetrieve() {}


		/**
		 * event called after every retrieve
		 *
		 * @return void
		 */
		protected function afterRetrieve() {}


		/**
		 * event called before every save
		 *
		 * @return void
		 */
		protected function beforeSave() {}


		/**
		 * event called after every save
		 *
		 * @return void
		 */
		protected function afterSave() {}


		/**
		 * event called before every insert
		 *
		 * @return void
		 */
		protected function beforeInsert() {}


		/**
		 * event called after every insert
		 *
		 * @return void
		 */
		protected function afterInsert() {}


		/**
		 * event called before every update
		 *
		 * @return void
		 */
		protected function beforeUpdate() {}


		/**
		 * event called after every update
		 *
		 * @return void
		 */
		protected function afterUpdate() {}


		/**
		 * event called before every delete
		 *
		 * @return void
		 */
		protected function beforeDelete() {}


		/**
		 * event called after every delete
		 *
		 * @return void
		 */
		protected function afterDelete() {}


		/**
		 * private methods (used by __call)
		 */


		/**
		 * create new record of an ActiveRecordBase object
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 *
		 * @return void
		 */
		final private function createRecordByType( $type, array $args = array() )
		{
			if( !$this->isEmpty )
			{
				foreach( $this->relationships as $mapping )
				{
					if( strtolower( $mapping['type'] ) === strtolower( $type ))
					{
						if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
						{
							// set the foreign key of the foreign object...
							return self::addByType($mapping['type'], array_merge($args, array($mapping['columnRef']=>$this[$mapping['columnKey']])));
						}
					}
				}
				throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".get_class($this));
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." must be saved before creating a record");
			}
		}


		/**
		 * add association to another ActiveRecordBase object
		 *
		 * @param  ActiveRecordBase		&$activeRecord		reference to ActiveRecordBase object
		 * @return void
		 */
		final private function addRecord( ActiveRecordBase &$activeRecord )
		{
			if( !$this->isEmpty )
			{
				foreach( $this->relationships as $mapping )
				{
					if( strtolower( $mapping['type'] ) === strtolower( get_class( $activeRecord )))
					{
						if( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString() )
						{
							if( !$activeRecord->isEmpty )
							{
								$query = $activeRecord->dataSet->dataAdapter->queryBuilder();
								$query->select();
								$query->from( $mapping['table'] );
								$query->where( $mapping['table'], $mapping['columnKey'], '=', $this[$this->pkey] );
								$query->where( $mapping['table'], $mapping['columnRef'], '=', $activeRecord[$activeRecord->pkey] );

								$dsA = $this->dataSet->dataAdapter->openDataSet( $query );

								if( $dsA->count ) {
									throw new \System\Base\InvalidOperationException("association already exists");
								}

								$ds = $this->dataSet->dataAdapter->queryBuilder()->select()->from($mapping['table'])->openDataSet();
								$ds[$mapping['columnKey']] = $this[$mapping['columnKey']];
								if( !( $this instanceof $mapping['type'] ))
								{
									$ds[$mapping['columnRef']] = $activeRecord[$mapping['columnRef']];
								}
								else
								{
									$ds[$mapping['columnRef']] = $activeRecord[$mapping['columnKey']];
								}

								$ds->insert();
								return;
							}
							else
							{
								throw new \System\Base\InvalidOperationException(get_class($activeRecord)." must be saved before adding association");
							}
						}
						if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
						{
							if( $activeRecord[$mapping['columnRef']] )
							{
								if( $activeRecord[$mapping['columnRef']] === $this[$mapping['columnKey']] )
								{
									throw new \System\Base\InvalidOperationException(get_class($activeRecord)." is already a child of ".get_class($this));
								}
								else
								{
									throw new \System\Base\InvalidOperationException(get_class($activeRecord)." already has a parent");
								}
							}

							// set the foreign key of the foreign object...
							$activeRecord[$mapping['columnRef']] = $this[$mapping['columnKey']];
							$activeRecord->save();
							return;
						}
					}
				}
				throw new \System\Base\InvalidOperationException(get_class($activeRecord)." has no relationship to ".get_class($this));
			}
			else
			{
				throw new \System\Base\InvalidOperationException(get_class($this)." must be saved before adding another record");
			}
		}


		/**
		 * remove association with another ActiveRecordBase ActiveRecordBase
		 *
		 * @param  ActiveRecordBase		&$activeRecord		reference to ActiveRecordBase object
		 * @return void
		 */
		final private function removeRecord( ActiveRecordBase &$activeRecord )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( get_class( $activeRecord )))
				{
					if( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString() )
					{
						$query = $activeRecord->dataSet->dataAdapter->queryBuilder();
						$query->delete();
						$query->from( $mapping['table'] );
						$query->where( $mapping['table'], $mapping['columnKey'], '=', $this[$this->pkey] );
						$query->where( $mapping['table'], $mapping['columnRef'], '=', $activeRecord[$activeRecord->pkey] );

						$query->execute();

						if($this->dataSet->dataAdapter->getAffectedRows()!=1)
						{
							throw new \System\Base\InvalidOperationException(get_class($activeRecord)." is not associated with ".get_class($this));
						}
						return;
					}
					if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						if( $activeRecord[$mapping['columnRef']] != $this[$mapping['columnKey']] ) {
							if( !$activeRecord[$mapping['columnRef']] ) {
								throw new \System\Base\InvalidOperationException(get_class($activeRecord)." has no parent ".get_class($this));
							}
							else {
								throw new InvalidOperationException(get_class($activeRecord)." is not the child of".get_class($this));
							}
						}

						$activeRecord[$mapping['columnRef']] = null;
						$activeRecord->save();
						return;
					}
				}
			}

			throw new InvalidOperationException(get_class($this)." has no relationship to ".get_class($activeRecord));
		}


		/**
		 * remove all associations to another ActiveRecordBase object by type
		 *
		 * @param  string		$type		object type
		 * @return void
		 */
		final private function removeAllRecordsByType( $type )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( $type ))
				{
					if( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString() )
					{
						$query = $this->dataSet->dataAdapter->queryBuilder();
						$query->delete();
						$query->from  ( $mapping['table'] );
						$query->where ( $mapping['table'], $mapping['columnKey'], '=', $this[$this->pkey] );

						$query->execute();
						return;
					}
					if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						$query = $this->dataSet->dataAdapter->queryBuilder();
						$query->update( $mapping['table'] );
						$query->set   ( $mapping['table'], $mapping['columnRef'], null );
						$query->where ( $mapping['table'], $mapping['columnRef'], '=', $this[$mapping['columnKey']] );

						$query->execute();
						return;
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".$type);
		}


		/**
		 * delete a child ActiveRecordBase object
		 *
		 * @param  ActiveRecordBase		&$activeRecord		reference to ActiveRecordBase object
		 * @return void
		 */
		final private function deleteRecord( ActiveRecordBase &$activeRecord )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( get_class( $activeRecord )))
				{
					if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						if( $activeRecord[$mapping['columnRef']] != $this[$mapping['columnKey']] )
						{
							if( !$activeRecord[$mapping['columnRef']] )
							{
								throw new \System\Base\InvalidOperationException(get_class($activeRecord)." has no parent ".get_class($this));
							}
							else
							{
								throw new \System\Base\InvalidOperationException(get_class($activeRecord)." is not the child of ".get_class($this));
							}
						}
						$activeRecord->delete();
						return;
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".get_class($activeRecord));
		}


		/**
		 * delete all child ActiveRecordBase objects by type
		 *
		 * @param  string		$type		object type
		 * @return void
		 */
		final private function deleteAllRecordsByType( $type )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( $type ))
				{
					if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						$query = $this->dataSet->dataAdapter->queryBuilder();
						$query->delete();
						$query->from  ( $mapping['table'] );
						$query->where ( $mapping['table'], $mapping['columnRef'], '=', $this[$mapping['columnKey']] );

						$query->execute();
						return;
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".$type);
		}


		/**
		 * return a child ActiveRecordBase object by type
		 *
		 * @param  string		$type		object type
		 * @param  int			$id			value of pkey
		 * @return DataSet					reference to object
		 */
		final private function findRecordByType( $type, $id )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( $type ))
				{
					if( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						if( class_exists( $mapping['type'] ))
						{
							$activeRecord = ActiveRecordBase::addByType( $type );

							$joinTable = ($this->table==$mapping['table'])?$mapping['table'].'2':$mapping['table'];

							$query = $this->dataSet->dataAdapter->queryBuilder()
							->select( $joinTable, $activeRecord->pkey )
							->from( $this->table )
							->innerJoin( $mapping['table'], $mapping['columnRef'], $this->table, $mapping['columnKey'], $joinTable )
							->where( $joinTable, $mapping['columnRef'], '=', $this[$mapping['columnKey']] )
							->where( $joinTable, $activeRecord->pkey, '=', $id );

							$ds = $query->openDataSet();

							return ActiveRecordBase::findByType( $mapping['type'], array( $activeRecord->pkey => $ds[$activeRecord->pkey] ));
						}
						else
						{
							throw new \System\Base\InvalidOperationException("object `$type` is not defined");
						}
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".$type);
		}


		/**
		 * return a parent ActiveRecordBase object by type
		 *
		 * @param  string		$type		object type
		 * @return ActiveRecordBase
		 */
		final private function findParentRecordByType( $type )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( $type ))
				{
					if( $mapping['relationship'] == RelationshipType::BelongsTo()->__toString() )
					{
						if( class_exists( $mapping['type'] ))
						{
							return ActiveRecordBase::findByType( $mapping['type'], array( $mapping['columnKey'] => $this[$mapping['columnRef']] ));
						}
						else
						{
							throw new \System\Base\InvalidOperationException("object `$type` is not defined");
						}
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".$type);
		}


		/**
		 * return a collection of child ActiveRecordBase objects by type
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 *
		 * @return ActiveRecordCollection
		 */
		final private function findAllRecordsByType( $type, array $args = array() )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( $type ))
				{
					$activeRecord = new $type();

					if( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString() )
					{
						if( class_exists( $mapping['type'] ))
						{
							$query = $this->dataSet->dataAdapter->queryBuilder()
							->select( $mapping['table'], $mapping['columnRef'] )
							->from( $this->table );

							foreach( $args as $key => $value )
							{
								$query->where( $this->table, (string)$key, '=', $value );
							}

							$query->innerJoin( $mapping['table'], $mapping['columnKey'], $this->table, $mapping['columnKey'] );
							$query->where( $this->table, $mapping['columnKey'], '=', $this[$mapping['columnKey']] );

							$ds = $query->openDataSet();

							$records = array();
							foreach( $ds->rows as $row )
							{
								$records[] = ActiveRecordBase::findByType( $type, array_merge( $args, array( $activeRecord->pkey => $row[$activeRecord->pkey] )));
							}

							return new ActiveRecordCollection($records);
						}
						else
						{
							throw new \System\Base\InvalidOperationException("object `$type` is not defined");
						}
					}
					elseif( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						if( class_exists( $mapping['type'] ))
						{
							$joinTable = ($this->table==$mapping['table'])?$mapping['table'].'2':$mapping['table'];

							$query = $this->dataSet->dataAdapter->queryBuilder()
							->select( $joinTable, $activeRecord->pkey )
							->from( $this->table );

							foreach( $args as $key => $value )
							{
								$query->where( $activeRecord->table, (string)$key, '=', $value );
							}

							$query->innerJoin( $mapping['table'], $mapping['columnRef'], $this->table, $mapping['columnKey'], $joinTable );
							$query->where( $joinTable, $mapping['columnRef'], '=', $this[$mapping['columnKey']] );

							$ds = $query->openDataSet();

							$records = array();
							foreach( $ds->rows as $row )
							{
								$records[] = ActiveRecordBase::findByType( $type, array_merge( $args, array( $activeRecord->pkey => $row[$activeRecord->pkey] )));
							}

							return new ActiveRecordCollection($records);
						}
						else
						{
							throw new \System\Base\InvalidOperationException("object `$type` is not defined");
						}
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".$type);
		}


		/**
		 * return DataSet object with all child records by type
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 *
		 * @return DataSet					reference to object
		 */
		final private function getAllRecordsByType( $type, array $args = array() )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( $type ))
				{
					$activeRecord = new $type();

					if( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString() )
					{
						if( class_exists( $mapping['type'] ))
						{
							$query = $this->dataSet->dataAdapter->queryBuilder()
							->select( $activeRecord->table, '*' )
							->from( $mapping['table'] );

							foreach( $args as $key => $value )
							{
								$query->where( $activeRecord->table, (string)$key, '=', $value );
							}

							$query->innerJoin( $activeRecord->table, $activeRecord->pkey, $mapping['table'], $mapping['columnRef'] );

							if( !( $this instanceof $mapping['type'] ))
							{
								$query->innerJoin( $this->table, $mapping['columnKey'], $mapping['table'], $mapping['columnKey'] );
							}

							$query->where( $mapping['table'], $mapping['columnKey'], '=', $this[$mapping['columnKey']] );

							return $query->openDataSet();
						}
						else
						{
							throw new \System\Base\InvalidOperationException("object `$type` is not defined");
						}
					}
					elseif( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						$joinTable = ($this->table==$mapping['table'])?$this->table.'2':$this->table;

						$query = $this->dataSet->dataAdapter->queryBuilder()
						->select( $mapping['table'], '*' )
						->from( $mapping['table'] );

						foreach( $args as $key => $value )
						{
							$query->where( $activeRecord->table, (string)$key, '=', $value );
						}

						if( $mapping['notNull'] )
						{
							$query->innerJoin( $this->table, $mapping['columnKey'], $mapping['table'], $mapping['columnRef'], $joinTable );
						}
						else
						{
							$query->leftJoin( $this->table, $mapping['columnKey'], $mapping['table'], $mapping['columnRef'], $joinTable );
						}

						$query->where( $joinTable, $mapping['columnKey'], '=', $this[$mapping['columnKey']] );

						return $query->openDataSet();
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".$type);
		}


		/**
		 * return count of all child records by type
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 *
		 * @return int
		 */
		final private function getCountByType( $type, array $args = array() )
		{
			foreach( $this->relationships as $mapping )
			{
				if( strtolower( $mapping['type'] ) === strtolower( $type ))
				{
					$activeRecord = new $type();

					if( $mapping['relationship'] == RelationshipType::HasManyAndBelongsTo()->__toString() )
					{
						if( class_exists( $mapping['type'] ))
						{
							$query = $this->dataSet->dataAdapter->queryBuilder()
							->select( $activeRecord->table, $activeRecord->pkey )
							->from( $mapping['table'] );

							foreach( $args as $key => $value )
							{
								$query->where( $activeRecord->table, (string)$key, '=', $value );
							}

							$query->innerJoin( $activeRecord->table, $activeRecord->pkey, $mapping['table'], $mapping['columnRef'] );

							if( !( $this instanceof $mapping['type'] ))
							{
								$query->innerJoin( $this->table, $mapping['columnKey'], $mapping['table'], $mapping['columnKey'] );
							}

							$query->where( $mapping['table'], $mapping['columnKey'], '=', $this[$mapping['columnKey']] );

							return $query->openDataSet()->count;
						}
						else
						{
							throw new \System\Base\InvalidOperationException("object `$type` is not defined");
						}
					}
					elseif( $mapping['relationship'] == RelationshipType::HasMany()->__toString() )
					{
						$joinTable = ($this->table==$mapping['table'])?$this->table.'2':$this->table;

						$query = $this->dataSet->dataAdapter->queryBuilder()
						->select( $mapping['table'], $mapping['columnRef'] )
						->from( $mapping['table'] );

						foreach( $args as $key => $value )
						{
							$query->where( $activeRecord->table, (string)$key, '=', $value );
						}

						if( $mapping['notNull'] )
						{
							$query->innerJoin( $this->table, $mapping['columnKey'], $mapping['table'], $mapping['columnRef'], $joinTable );
						}
						else
						{
							$query->leftJoin( $this->table, $mapping['columnKey'], $mapping['table'], $mapping['columnRef'], $joinTable );
						}

						$query->where( $joinTable, $mapping['columnRef'], '=', $this[$mapping['columnKey']] );

						return $query->openDataSet()->count;
					}
				}
			}

			throw new \System\Base\InvalidOperationException(get_class($this)." has no relationship to ".$type);
		}


		/**
		 * static method to create new ActiveRecordBase by type
		 *
		 * @param  string		$type		object type
		 * @return ActiveRecordBase
		 */
		static private function addByType( $type, array $args = array() )
		{
			// create ActiveRecordBase
			$activeRecord = new $type();
			$activeRecord->beforeCreate();

			$da = \System\Base\ApplicationBase::getInstance()->dataAdapter;

			// create empty DataSet
			$query = $da->queryBuilder();
			$query->select( '*' );
			$query->from( $activeRecord->table );
			$query->empty = true;

			if(is_null($da))
			{
				throw new \System\Base\InvalidOperationException("AppServlet::dataAdapter is null");
			}

			$activeRecord->dataSet = $query->openDataSet();

			// set args
			foreach( $args as $key => $value )
			{
				$activeRecord[(string)$key] = $value;
			}

			$activeRecord->afterCreate();
			return $activeRecord;
		}


		/**
		 * static method to find ActiveRecordBase by type
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 * @return ActiveRecordBase
		 */
		static private function findByType( $type, array $args = array() )
		{
			$activeRecord = new $type();
			$activeRecord->beforeRetrieve();

			// build query
			$query = \System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()
			->select( '*' )
			->from( $activeRecord->table );

			// filter
			foreach( $args as $key => $value )
			{
				$query->where( $activeRecord->table, $key, '=', $value );
			}

			$activeRecord->dataSet = $query->openDataSet();

			if( $activeRecord->dataSet->count > 0 )
			{
				$activeRecord->afterRetrieve();
				return $activeRecord;
			}
			else
			{
				return null;
			}
		}


		/**
		 * static method to find ActiveRecordBases by type
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 * @return ActiveRecordCollection
		 */
		static private function findAllByType( $type, array $args = array() )
		{
			$activeRecord = new $type();
			$records = array();

			$ds = ActiveRecordBase::allByType( $type, $args );
			foreach( $ds->rows as $row )
			{
				$records[] = ActiveRecordBase::findByType( $type, array( $activeRecord->pkey => $row[$activeRecord->pkey] ));
			}

			return new ActiveRecordCollection($records);
		}


		/**
		 * static method to find the first ActiveRecordBase by type
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 * @return ActiveRecordBase
		 */
		static private function firstByType( $type, array $args = array() )
		{
			$activeRecord = new $type();

			$ds = ActiveRecordBase::allByType( $type, $args );
			$ds->first();

			return ActiveRecordBase::findByType( $type, array( $activeRecord->pkey=>$ds[$activeRecord->pkey] ));
		}


		/**
		 * static method to find the last ActiveRecordBase by type
		 *
		 * @param  string		$type		object type
		 * @param  string		$args		associative array of keys and values
		 *
		 * @return ActiveRecordBase
		 */
		static private function lastByType( $type, array $args = array() )
		{
			$activeRecord = new $type();

			$ds = ActiveRecordBase::allByType( $type, $args );
			$ds->last();

			return ActiveRecordBase::findByType( $type, array( $activeRecord->pkey=>$ds[$activeRecord->pkey] ));
		}


		/**
		 * static method to return a DataSet by type
		 * 
		 * @param  string		$type		object type
		 * @param  array		$columns	array of column names to return
		 * @param  array		$filter		associative array of column names and values to filter
		 * @param  array		$sort_by	array of column names to sort by
		 * @param  int			$offset		number of records to offset
		 * @param  int			$limit		resultset limit
		 * @return DataSet
		 */
		static private function allByType( $type, array $columns = array(), array $filter = array(), array $sort_by = array(), $offset = 0, $limit = 0 )
		{
			// TODO: rem backwards compatibility code
			if((bool)count(array_filter(array_keys($columns), 'is_string')) && !$filter && !$sort_by && !$offset && ~$limit) {
				$filter = $columns;
				$columns = array();
			}

			$activeRecord = new $type();

			// build query
			$query = \System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()->select();

			// columns
			foreach( $columns as $column )
			{
				$field = explode('.', $column);
				if(count($field)==2) {
					$query->column( $field[0], $field[1] );
				}
				else {
					$query->column( $activeRecord->table, $column );
				}
			}

			$query->from( $activeRecord->table );

			// filter
			foreach( $filter as $key => $value )
			{
				$field = explode('.', $key);
				if(count($field)==2) {
					$query->where( $field[0], $field[1], '=', $value );
				}
				else {
					$query->where( $activeRecord->table, $key, '=', $value );
				}
			}

			// sort
			if($sort_by)
			{
				foreach( $sort_by as $key )
				{
					$field = explode('.', $key);
					if(count($field)==2) {
						$query->orderBy( $field[0], $field[1] );
					}
					else {
						$query->orderBy( $activeRecord->table, $key );
					}
				}
			}
			elseif($activeRecord->sortKey)
			{
				$query->orderBy( $activeRecord->table, $activeRecord->sortKey );
			}

			if((int)$limit>0) {
//				$query->limit($limit); // TODO: implement
			}

			return $query->openDataSet();
		}


		/**
		 * static method to return an extended DataSet by type
		 * Extended datasets include table data from all mapped tables
		 *
		 * @param  string		$type		object type
		 * @param  array		$args		filter
		 * @return DataSet
		 */
		static private function allExtendedByType( $type, array $args = array() )
		{
			$activeRecord = new $type();

			// build query
			$query = \System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()
			->select( '*' )
			->from( $activeRecord->table );

			// inner join associated tables
			$i = 0;
			$tables = array($activeRecord->table);
			foreach( $activeRecord->relationships as $mapping )
			{
				if( $mapping['relationship'] == RelationshipType::BelongsTo()->__toString() )
				{
					// make sure class exists
					if( class_exists( $mapping['type'] ))
					{
						$ftype = new $mapping['type']();
						if( $ftype->table <> $activeRecord->table )
						{
							$jointable = '';
							if(in_array($ftype->table,$tables))
							{
								$jointable = "jointable{$i}";
							}
							else
							{
								$jointable = $ftype->table;
								$tables[] = $ftype->table;
							}
							$i++;

							$query->leftJoin( $ftype->table, $mapping['columnRef'], $activeRecord->table, $mapping['columnKey'], $jointable );
						}
					}
				}
			}

			// filter
			foreach( $args as $key => $value )
			{
				$field = explode('.', $key);
				if(count($field)==2) {
					$query->where( $field[0], $field[1], '=', $value );
				}
				else {
					$query->where( $activeRecord->table, $key, '=', $value );
				}
			}

			// sort
			if( $activeRecord->pkey )
			{
				$query->orderBy( $activeRecord->table, $activeRecord->pkey );
			}

			return $query->openDataSet();
		}


		/**
		 * static method to return a record count
		 *
		 * @param  string		$type		object type
		 * @param  array		$args		filter
		 * @return int
		 */
		static private function countByType( $type, array $args = array() )
		{
			$activeRecord = new $type();

			// build query
			$query = \System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()
			->select( $activeRecord->table, $activeRecord->pkey )
			->from( $activeRecord->table );

			// filter
			foreach( $args as $key => $value )
			{
				$query->where( $activeRecord->table, $key, '=', $value );
			}

			return $query->openDataSet()->count;
		}


		/**
		 * set object properties or verify that properties have been manuall defined
		 * this method requires an SQLDataAdapter
		 *
		 * @return void
		 */
		private function init()
		{
			if(__ACTIVERECORD_AUTO_MAP__)
			{
				// cache file
				$cache_id = 'mappings:' . get_class($this);
				$properties = \System\Base\Build::get($cache_id);

				if($properties)
				{
					$this->table = $properties['table'];
					$this->pkey	= $properties['pkey'];
					$this->relationships = $properties['relationships'];
					$this->fields = $properties['fields'];
					$this->rules = $properties['rules'];
				}
				else
				{
					$da = \System\Base\ApplicationBase::getInstance()->dataAdapter;

					if(is_null($da))
					{
						throw new \System\Base\InvalidOperationException("AppServlet::dataAdapter is null");
					}

					/**
					 * auto determine table name using SQLDataAdapter source
					 * can be overridden
					 */

					$schema = $da->getSchema();

					// auto determine table
					if( !$this->table )
					{
						$this->table = get_class( $this );

						if( strpos( $this->table, '\\' ) !== false )
						{
							$this->table = substr( strrchr( $this->table, '\\' ), 1 );
						}

						foreach( $schema->tableSchemas as $tableSchema )
						{
							if( strtolower($tableSchema->name) === strtolower( $this->table ))
							{
								$this->table = $tableSchema->name;
								break;
							}
						}

						if( !$this->table )
						{
							throw new \System\Base\InvalidOperationException("table `{$this->table}` does not exist in DataAdapter");
						}
					}

					// get table schema
					$tableSchema = $schema->seek($this->table);

					// auto determine the primary key
					if( !$this->pkey )
					{
						foreach($tableSchema->columnSchemas as $columnSchema)
						{
							if($columnSchema->primaryKey)
							{
								$this->pkey = $columnSchema->name;
								break;
							}
						}
					}

					// auto determine table mappings using DataSet source
					if( !$this->relationships )
					{
						$this->autoMap( $schema, $tableSchema );
					}

					// auto determine rules using DataSet source
					if( !$this->fields )
					{
						foreach($tableSchema->columnSchemas as $columnSchema)
						{
							foreach( $this->relationships as $mapping )
							{
								// belongs_to
								if( $columnSchema->name === $mapping['columnKey'] &&
									$mapping['relationship'] == RelationshipType::BelongsTo()->__toString() )
								{
									$this->fields[$columnSchema->name] = 'ref';
									continue 2;
								}
							}

							// create simple controls
							if( $columnSchema->autoIncrement )
							{
								continue;
							}
							elseif( $columnSchema->datetime )
							{
								$this->fields[$columnSchema->name] = 'datetime';
								continue;
							}
							elseif( $columnSchema->date )
							{
								$this->fields[$columnSchema->name] = 'date';
								continue;
							}
							elseif( $columnSchema->time )
							{
								$this->fields[$columnSchema->name] = 'time';
								continue;
							}
							elseif( $columnSchema->boolean )
							{
								$this->fields[$columnSchema->name] = 'boolean';
								continue;
							}
							elseif( $columnSchema->numeric )
							{
								$this->fields[$columnSchema->name] = 'numeric';
								continue;
							}
							elseif( $columnSchema->blob )
							{
								$this->fields[$columnSchema->name] = 'blob';
								continue;
							}
							else
							{
								$this->fields[$columnSchema->name] = 'string';
							}
						}
					}

					// auto determine rules using DataSet source
					if( !$this->rules )
					{
						foreach($tableSchema->columnSchemas as $columnSchema)
						{
							$rules = array();
//							if($columnSchema->notNull && !$columnSchema->boolean && !$columnSchema->binary)
//							{
//								$rules[] = 'required';
//							}
							if($columnSchema->datetime || $columnSchema->date || $columnSchema->time)
							{
								$rules[] = 'datetime';
							}
							if($columnSchema->numeric)
							{
								$rules[] = 'numeric';
							}
							if($columnSchema->unique)
							{
								$rules[] = 'unique';
							}
							if($columnSchema->length > 0)
							{
								$rules[] = 'length(0, '.$columnSchema->length.')';
							}

							if($rules)
							{
								$this->rules[$columnSchema->name] = $rules;
							}
						}
					}

					// write properties to cache file
					$properties = array();
					$properties['table'] = $this->table;
					$properties['pkey'] = $this->pkey;
					$properties['relationships'] = $this->relationships;
					$properties['fields'] = $this->fields;
					$properties['rules'] = $this->rules;

					\System\Base\Build::put( $cache_id, $properties );
				}
			}
		}


		/**
		 * auto map object relationships
		 *
		 * @param  DataBaseSchema		$schema		database schema
		 * @return void
		 */
		private function autoMap( \System\DB\DatabaseSchema &$schema, \System\DB\TableSchema &$tableSchema )
		{
			/**
			 * complex mapping
			 * parse all the tables and map relationships "on the fly"
			 */
			$pkeys = array();

			// loop through tables in database
			foreach( $schema->tableSchemas as $ftableSchema )
			{
				// ignore self
				if( $ftableSchema->name != $this->table )
				{
					// get an array of pkeys of all foreign tables
					$fComposite = false;
					foreach( $ftableSchema->columnSchemas as $columnSchema )
					{
						if( $columnSchema->primaryKey )
						{
							if( isset( $pkeys[$ftableSchema->name] ))
							{
								$fComposite=true;
								unset( $pkeys[$ftableSchema->name] );
							}
							elseif( !$fComposite )
							{
								$pkeys[$ftableSchema->name] = $columnSchema->name;
							}
						}
					}
				}
			}

			// loop through all tables in the database
			foreach( $schema->tableSchemas as $ftableSchema )
			{
				// get namespace of this object
				$namespace = substr(get_class($this), 0, strrpos(get_class($this), '\\')) . '\\';

				// set true another table has pkey of this table
				$pKeyFound = false;

				// ignore self
				if( $ftableSchema->name != $this->table )
				{
					/**
					 * if this tables primary is found in another
					 * table, map the relationship 1:n
					 */
					foreach( $ftableSchema->columnSchemas as $columnSchema )
					{
						if( $columnSchema->name === $this->pkey )
						{
							$pKeyFound = true; // we have found this tables pkey in the foreign table
							if( isset( $pkeys[$ftableSchema->name] ))
							{
								if( class_exists( $namespace . ucwords( $ftableSchema->name )))
								{
									$type = $namespace . ucwords( $ftableSchema->name );

									$mapping = array(
										  'relationship' => RelationshipType::HasMany()->__toString()
										, 'type' => $type
										, 'table' => $ftableSchema->name
										, 'columnRef' => $this->pkey
										, 'columnKey' => $this->pkey
										, 'notNull' => $columnSchema->notNull );

									$this->relationships[] = $mapping;
								}
							}
						}
					}

					/**
					 * if another tables primary key is found in this
					 * table, map the relationship n:1
					 */
					foreach( $tableSchema->columnSchemas as $columnSchema )
					{
						if( isset( $pkeys[$ftableSchema->name] ))
						{
							if( $columnSchema->name === $pkeys[$ftableSchema->name] )
							{
								if( class_exists( $namespace . ucwords( $ftableSchema->name )))
								{
									$type = $namespace . ucwords( $ftableSchema->name );

									$mapping = array(
										  'relationship' => RelationshipType::BelongsTo()->__toString()
										, 'type' => $type
										, 'table' => $this->table
										, 'columnRef' => $pkeys[$ftableSchema->name]
										, 'columnKey' => $pkeys[$ftableSchema->name]
										, 'notNull' => $columnSchema->notNull );

									$this->relationships[] = $mapping;
								}
							}
						}
					}

					/**
					 * if this tables primary key and another tables primary
					 * key is found in this table, map the relationship n:n
					 */
					foreach( $ftableSchema->columnSchemas as $columnSchema )
					{
						// found pkey of this table in foreign table
						if( $pKeyFound )
						{
							// loop through all foreign tables
							foreach( $schema->tableSchemas as $ftableSchema2 )
							{
								// ignore self
								if( $ftableSchema->name != $ftableSchema2->name )
								{
									// if foreign table has primary key?
									if( isset( $pkeys[$ftableSchema2->name] ))
									{
										// if foreign tables key = tables pkey
										if( $pkeys[$ftableSchema2->name] === $columnSchema->name )
										{
											// if there are only 2 columns in table
											if(count($ftableSchema->columnSchemas) === 2)
											{
												if( class_exists( $namespace . ucwords( $ftableSchema2->name )))
												{
													$type = $namespace . ucwords( $ftableSchema2->name );

													// found pkey of another table in foreign table
													$mapping = array(
														  'relationship' => RelationshipType::HasManyAndBelongsTo()->__toString()
														, 'type' => $type
														, 'table' => $ftableSchema->name
														, 'columnRef' => $pkeys[$ftableSchema2->name]
														, 'columnKey' => $this->pkey
														, 'notNull' => $columnSchema->notNull );

													$this->relationships[] = $mapping;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>