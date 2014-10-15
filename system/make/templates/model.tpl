#php
	/**
	 * @package <Namespace>
	 */
	namespace <Namespace>;

	/**
	 * This class represents represents a <ClassName> table withing a database or an instance of a single
	 * record in the <ClassName> table and provides database abstraction
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
	 * @package			<Namespace>
	 */
	class <ClassName> extends \System\ActiveRecord\ActiveRecordBase
	{
		/**
		 * Specifies the table name
		 * @var string
		**/
		protected $table			= '<TableName>';

		/**
		 * Specifies the primary key (there can only be one primary key defined)
		 * @var string
		**/
		protected $pkey				= '<PrimaryKey>';

		/**
		 * Specifies field names mapped to field types
		 * @var array
		**/
		protected $fields			= <Fields>;

		/**
		 * Specifies field names mapped to field rules
		 * @var array
		**/
		protected $rules			= <Rules>;

		/**
		 * Specifies table relationships
		 * @var array
		**/
		protected $relationships	= <Relationships>;
	}
#end