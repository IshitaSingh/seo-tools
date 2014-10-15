<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\I18N;


	/**
	 * Represents a message Translator using a Database
	 *
	 * @package			PHPRum
	 * @subpackage		I18N
	 * @author			Darnell Shinbine
	 */
	class DBTranslator extends TranslatorBase
	{
		/**
		 * data adapter
		 * @var DataAdapter
		 */
		private $db;

		/**
		 * data adapter
		 * @var DataAdapter
		 */
		private $table			= __LANGS_TABLENAME__;


		/**
		 * Constructor
		 *
		 * @param string $table table name
		 * @param string $dsn database connection string
		 */
		public function __construct($table = __LANGS_TABLENAME__, $dsn = '')
		{
			if($table)
			{
				$this->table = $table;
			}

			if($dsn)
			{
				$this->db = \System\DB\DataAdapter::create($dsn);
			}
			else
			{
				$this->db =& \System\Base\ApplicationBase::getInstance()->dataAdapter;
			}
		}


		/**
		 * get
		 *
		 * @param string $stringId string id to translate
		 * @param string $default string if not found
		 * 
		 * @return void
		 */
		public function get($stringId, $default = '')
		{
			$default = $default?$default:$stringId;

			try
			{
				$ds = $this->db->queryBuilder()
					->select($this->table, 'value')
					->from($this->table)
					->where($this->table, 'string_id', '=', $stringId)
					->where($this->table, 'lang', '=', $this->lang)
					->openDataSet();

				if($ds->count > 0)
				{
					return $ds->row["value"]?$ds->row["value"]:$default;
				}
				else
				{
					$this->db->queryBuilder()
						->insertInto($this->table, array('string_id', 'lang', 'charset', 'value'))
						->values(array((string)$stringId, $this->lang, $this->charset, (string)$default))
						->execute();

					return $default;
				}
			}
			catch(\System\DB\DatabaseException $e)
			{
				\System\Base\ApplicationBase::getInstance()->dataAdapter->addTableSchema(new \System\DB\TableSchema(
					array(
						'name' => $this->table,
						'primaryKey' => 'string_id'),
					array(),
					array(new \System\DB\ColumnSchema(array(
						'name' => 'string_id',
						'table' => $this->table,
						'type' => 'VARCHAR',
						'primaryKey' => true,
						'length' => 50,
						'notNull' => true,
						'string' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'lang',
						'table' => $this->table,
						'type' => 'CHAR',
						'primaryKey' => true,
						'length' => 2,
						'notNull' => true,
						'string' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'charset',
						'table' => $this->table,
						'type' => 'CHAR',
						'length' => 20,
						'notNull' => true,
						'string' => true)),
						new \System\DB\ColumnSchema(array(
						'name' => 'value',
						'table' => $this->table,
						'type' => 'CHAR',
						'length' => 255,
						'notNull' => true,
						'string' => true)))));
			}
		}
	}
?>