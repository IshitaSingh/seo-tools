<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web;
	use System\Base\ModelBase;


	/**
	 * This class represents a form.
	 *
	 * The FormModelBase exposes 2 protected properties
	 * @property array $fields Contains an associative array of field names mapped to field types
	 * @property array $rules Contains an associative array of field names mapped to rules
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	abstract class FormModelBase extends ModelBase implements \System\Base\IBindable
	{
		/**
		 * Contains an associative array of field names mapped to field types
		 * @var array
		**/
		protected $fields			= array();

		/**
		 * Contains an associative array of field names mapped to rules
		 * @var array
		**/
		protected $rules			= array();

		/**
		 * Contains field mappings
		 * @var array
		 */
		static protected $field_mappings = array(
			'binary' => 'System\Web\WebControls\File',
			'blob' => 'System\Web\WebControls\TextArea',
			'boolean' => 'System\Web\WebControls\CheckBox',
			'date' => 'System\Web\WebControls\Date',
			'datetime' => 'System\Web\WebControls\DateTime',
			'email' => 'System\Web\WebControls\Email',
			'enum' => 'System\Web\WebControls\DropDownList',
			'integer' => 'System\Web\WebControls\Text',
			'numeric' => 'System\Web\WebControls\Text',
			'real' => 'System\Web\WebControls\Text',
			'ref' => 'System\Web\WebControls\DropDownList',
			'search' => 'System\Web\WebControls\Search',
			'string' => 'System\Web\WebControls\Text',
			'tel' => 'System\Web\WebControls\Tel',
			'time' => 'System\Web\WebControls\Time',
			'url' => 'System\Web\WebControls\URL'
		);

		/**
		 * Contains field mappings
		 * @var array
		 */
		static protected $column_mappings = array(
			'binary' => 'System\Web\WebControls\GridViewColumn',
			'blob' => 'System\Web\WebControls\GridViewTextArea',
			'boolean' => 'System\Web\WebControls\GridViewCheckBox',
			'date' => 'System\Web\WebControls\GridViewDate',
			'datetime' => 'System\Web\WebControls\GridViewDateTime',
			'email' => 'System\Web\WebControls\GridViewEmail',
			'enum' => 'System\Web\WebControls\GridViewDropDownMenu',
			'integer' => 'System\Web\WebControls\GridViewText',
			'numeric' => 'System\Web\WebControls\GridViewText',
			'real' => 'System\Web\WebControls\GridViewText',
			'ref' => 'System\Web\WebControls\GridViewDropDownMenu',
			'search' => 'System\Web\WebControls\GridViewSearch',
			'string' => 'System\Web\WebControls\GridViewText',
			'tel' => 'System\Web\WebControls\GridViewTel',
			'time' => 'System\Web\WebControls\GridViewTime',
			'url' => 'System\Web\WebControls\GridViewLink'
		);

		/**
		 * Contains rule mappings
		 * @var array
		 */
		static protected $rule_mappings = array(
			'boolean' => 'System\Validators\BooleanValidator',
			'compare' => 'System\Validators\CompareValidator',
			'datetime' => 'System\Validators\DateTimeValidator',
			'email' => 'System\Validators\EmailValidator',
			'enum' => 'System\Validators\EnumValidator',
			'filesize' => 'System\Validators\FileSizeValidator',
			'filetype' => 'System\Validators\FileTypeValidator',
			'integer' => 'System\Validators\IntegerValidator',
			'length' => 'System\Validators\LengthValidator',
			'numeric' => 'System\Validators\NumericValidator',
			'pattern' => 'System\Validators\PatternValidator',
			'range' => 'System\Validators\RangeValidator',
			'required' => 'System\Validators\RequiredValidator',
			'unique' => 'System\Validators\UniqueValidator',
			'url' => 'System\Validators\URLValidator'
		);

		/**
		 * row
		 * @var array
		**/
		private $row				= array();


		/**
		 * Constructor
		 *
		 * Read values from request, clean up variables, and merge get and post requests.
		 *
		 * @return  void
		 */
		protected function __construct()
		{
			foreach(array_keys($this->fields) as $key)
			{
				$this->row[$key] = null;
			}
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return mixed
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field == 'fields' )
			{
				return $this->fields;
			}
			elseif( $field == 'rules' )
			{
				return $this->rules;
			}
			elseif( array_key_exists( $field, $this->row ))
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
		function __set( $field, $value )
		{
			if( array_key_exists( (string)$field, $this->row ))
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
		function offsetExists($index)
		{
			if( array_key_exists( $index, $this->row ))
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
				$this->row[$index] = $value;
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
		 * returns fields as array
		 *
		 * @return array
		 */
		public function fields()
		{
			return $this->fields;
		}


		/**
		 * returns count
		 *
		 * @return int
		 */
		public function count()
		{
			return 1;
		}


		/**
		 * refresh model state
		 *
		 * @return void
		 */
		abstract public function refresh();


		/**
		 * save model state
		 *
		 * @return void
		 */
		abstract public function save();


		/**
		 * converts the form model into an array
		 *
		 * @return array
		 */
		public function toArray()
		{
			return $this->row;
		}


		/**
		 * static method to return a Form object
		 *
		 * @param  string		$controlId		form id
		 * @return Form
		 */
		static public function form( $controlId )
		{
			$type = self::getClass();
			$model = new $type();
//			$legend = \substr( strrchr( self::getClass(), '\\'), 1 );

			$form = new \System\Web\WebControls\Form( $controlId );
//			$form->fieldset->legend = \ucwords( \System\Web\WebApplicationBase::getInstance()->translator->get( $legend, $legend ));

			// create controls
			foreach( $model->fields as $field => $type )
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
//				$form->fieldset->getControl( $field )->label = ucwords( \System\Web\WebApplicationBase::getInstance()->translator->get( $field, str_replace( '_', ' ', $field )));

				// create list
				if($type === 'enum')
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

			// implement rules
			foreach( $model->rules as $field => $rules )
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
							$form->getControl($field)->addValidator($validator);
						}
					}
				}
			}

			return $form;
		}


		/**
		 * register new field type
		 *
		 * @param  string $field field type
		 * @param  string $type path to control
		 * @return void
		 */
		final static public function registerFieldType($field, $type)
		{
			self::$field_mappings[$field] = $type;
		}


		/**
		 * register new column type
		 *
		 * @param  string $field field type
		 * @param  string $type path to control
		 * @return void
		 */
		final static public function registerColumnType($field, $type)
		{
			self::$column_mappings[$field] = $type;
		}


		/**
		 * register new rule type
		 *
		 * @param  string $rule name of rule
		 * @param  string $type path to control
		 * @return void
		 */
		final static public function registerRuleType($rule, $type)
		{
			self::$rule_mappings[$rule] = $type;
		}
	}
?>