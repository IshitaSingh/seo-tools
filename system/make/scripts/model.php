<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Make;


	/**
	 * Provides functionality to generate a model file
	 * 
	 * @package			PHPRum
	 * @subpackage		Make
	 */
	class Model extends MakeBase
	{
		/**
		 * Default namespace
		 * @var string
		 */
		const ModelNamespace    			= '\\Models';


		/**
		 * make
		 *
		 * @param string $target target
		 * @param array $options options
		 * @return void
		 */
		public function make($target, array $options = array())
		{
			$baseNamespace = Make::$namespace;
			$modelPath = __MODELS_PATH__ . '/' . strtolower( $target ) . __CLASS_EXTENSION__;
			$modelTestCasePath = __UNIT_TESTS_PATH__ . '/' . strtolower($target . __TESTCASE_SUFFIX__) . __CLASS_EXTENSION__;
			$modelFixturePath = __FIXTURES_PATH__ . '/' . strtolower($target) . '.xml';

			$modelTemplate = file_get_contents(\System\Base\ApplicationBase::getInstance()->config->root . "/system/make/templates/model.tpl");
			$modelTemplate = str_replace("<Namespace>", $baseNamespace . self::ModelNamespace, $modelTemplate);
			$modelTemplate = str_replace("<ClassName>", ucwords($target), $modelTemplate);

			$modelTestCaseTemplate = file_get_contents(\System\Base\ApplicationBase::getInstance()->config->root . "/system/make/templates/modeltestcase.tpl");
			$modelTestCaseTemplate = str_replace("<Namespace>", $baseNamespace . self::ModelNamespace, $modelTestCaseTemplate);
			$modelTestCaseTemplate = str_replace("<BaseNamespace>", $baseNamespace, $modelTestCaseTemplate);
			$modelTestCaseTemplate = str_replace("<ClassName>", ucwords($target), $modelTestCaseTemplate);

			/**
			 * Auto determine model properties via database
			 */
			$da = \System\Base\ApplicationBase::getInstance()->dataAdapter;

			if(is_null($da))
			{
				throw new \System\Base\InvalidOperationException("No DataAdapter configured");
			}

			$schema = $da->getSchema();

			/**
			 * Auto determine table name using SQLDataAdapter source
			 */

			$table = '';

			foreach( $schema->tableSchemas as $tableSchema )
			{
				if( strtolower($tableSchema->name) === strtolower( $target ))
				{
					$table = $tableSchema->name;
					break;
				}
			}

			if( !$table )
			{
				throw new \System\Base\InvalidOperationException("table `{$target}` not found in DataAdapter");
			}

			// Get table schema
			$tableSchema = $schema->seek($table);

			/**
			 * Auto determine the primary key
			 */
			$pkey = '';
			foreach($tableSchema->columnSchemas as $columnSchema)
			{
				if($columnSchema->primaryKey)
				{
					$pkey = $columnSchema->name;
					break;
				}
			}

			/**
			 * Auto determine table mappings using DataSet source
			 */
			$relationships = $this->mapRelationships( $schema, $tableSchema, $table, $pkey );
			$mappings = '';
			foreach($relationships as $relationship)
			{
				if($mappings)$mappings.=',';
				$mappings .= "
			array(
				'relationship' => '".$relationship["relationship"]."',
				'type' => '".$relationship["type"]."',
				'table' => '".$relationship["table"]."',
				'columnRef' => '".$relationship["columnRef"]."',
				'columnKey' => '".$relationship["columnKey"]."',
				'notNull' => '".$relationship["notNull"]."'
			)";
			}
			$mappings = "array({$mappings}\n\t\t)";

			/**
			 * Auto determine fields using DataSet source
			 */
			$fields = '';
			foreach($tableSchema->columnSchemas as $columnSchema)
			{
				$fields.=$fields?',':'';
				foreach( $relationships as $mapping )
				{
					// belongs_to
					if( $columnSchema->name === $mapping['columnKey'] &&
						$mapping['relationship'] == \System\ActiveRecord\RelationshipType::BelongsTo()->__toString() )
					{
						$fields .= "\n\t\t\t'{$columnSchema->name}' => 'ref'";
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
					$fields .= "\n\t\t\t'{$columnSchema->name}' => 'datetime'";
					continue;
				}
				elseif( $columnSchema->date )
				{
					$fields .= "\n\t\t\t'{$columnSchema->name}' => 'date'";
					continue;
				}
				elseif( $columnSchema->time )
				{
					$fields .= "\n\t\t\t'{$columnSchema->name}' => 'time'";
					continue;
				}
				elseif( $columnSchema->boolean )
				{
					$fields .= "\n\t\t\t'{$columnSchema->name}' => 'boolean'";
					continue;
				}
				elseif( $columnSchema->numeric )
				{
					$fields .= "\n\t\t\t'{$columnSchema->name}' => 'numeric'";
					continue;
				}
				elseif( $columnSchema->blob )
				{
					$fields .= "\n\t\t\t'{$columnSchema->name}' => 'blob'";
					continue;
				}
				else
				{
					$fields .= "\n\t\t\t'{$columnSchema->name}' => 'string'";
				}
			}
			$fields = "array({$fields}\n\t\t)";

			// auto determine rules using DataSet source
			$rules = '';
			foreach($tableSchema->columnSchemas as $columnSchema)
			{
				$rule = '';
				if($rules)$rules.=',';
//				if($columnSchema->notNull && !$columnSchema->boolean)
//				{
//					$rule .= ($rule?',':'')."'required'";
//				}
				if($columnSchema->datetime || $columnSchema->date || $columnSchema->time)
				{
					$rule .= ($rule?',':'')."'datetime'";
				}
				if($columnSchema->numeric)
				{
					$rule .= ($rule?',':'')."'numeric'";
				}
				if($columnSchema->unique)
				{
					$rule .= ($rule?',':'')."'unique'";
				}
				if($columnSchema->length > 0)
				{
					$rule .= ($rule?',':'')."'length(0, ".(int)$columnSchema->length.")'";
				}

				if($rule)
				{
					$rules .= "\n\t\t\t'{$columnSchema->name}' => array({$rule})";
				}
			}
			$rules = "array({$rules}\n\t\t)";

			$modelTemplate = str_replace("<TableName>", $table, $modelTemplate);
			$modelTemplate = str_replace("<PrimaryKey>", $pkey, $modelTemplate);
			$modelTemplate = str_replace("<Relationships>", $mappings, $modelTemplate);
			$modelTemplate = str_replace("<Fields>", $fields, $modelTemplate);
			$modelTemplate = str_replace("<Rules>", $rules, $modelTemplate);

			$this->export($modelPath, $modelTemplate);
			$this->export($modelTestCasePath, $modelTestCaseTemplate);
		}


		/**
		 * auto map object relationships
		 *
		 * @param  DataBaseSchema		$schema		database schema
		 * @return void
		 */
		private function mapRelationships( \System\DB\DatabaseSchema &$schema, \System\DB\TableSchema &$tableSchema, $table, $pkey )
		{
			/**
			 * complex mapping
			 * parse all the tables and map relationships "on the fly"
			 */
			$pkeys = array();
			$tables = array();
			$mappings = array();

			// loop through tables in database
			foreach( $schema->tableSchemas as $ftableSchema )
			{
				// ignore self
				if( $ftableSchema->name != $table )
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
				$namespace = Make::$namespace . self::ModelNamespace;

				// set true another table has pkey of this table
				$pKeyFound = false;

				// ignore self
				if( $ftableSchema->name != $table )
				{
					/**
					 * if this tables primary is found in another
					 * table, map the relationship 1:n
					 */
					foreach( $ftableSchema->columnSchemas as $columnSchema )
					{
						if( $columnSchema->name === $pkey )
						{
							$pKeyFound = true; // we have found this tables pkey in the foreign table
							if( isset( $pkeys[$ftableSchema->name] ))
							{
								$type = $namespace . '\\' . ucwords( $ftableSchema->name );

								$mapping = array(
									  'relationship' => \System\ActiveRecord\RelationshipType::HasMany()->__toString()
									, 'type' => $type
									, 'table' => $ftableSchema->name
									, 'columnRef' => $pkey
									, 'columnKey' => $pkey
									, 'notNull' => $columnSchema->notNull );

								$mappings[] = $mapping;
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
								$type = $namespace . '\\' . ucwords( $ftableSchema->name );

								$mapping = array(
									  'relationship' => \System\ActiveRecord\RelationshipType::BelongsTo()->__toString()
									, 'type' => $type
									, 'table' => $table
									, 'columnRef' => $pkeys[$ftableSchema->name]
									, 'columnKey' => $pkeys[$ftableSchema->name]
									, 'notNull' => $columnSchema->notNull );

								$mappings[] = $mapping;
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
								if( count( $ftableSchema->columnNames ) === 2 )
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
												$type = $namespace . '\\' . ucwords( $ftableSchema2->name );

												// found pkey of another table in foreign table
												$mapping = array(
													  'relationship' => \System\ActiveRecord\RelationshipType::HasManyAndBelongsTo()->__toString()
													, 'type' => $type
													, 'table' => $ftableSchema->name
													, 'columnRef' => $pkeys[$ftableSchema2->name]
													, 'columnKey' => $pkey
													, 'notNull' => $columnSchema->notNull );

												//$mappings[] = $mapping;
											}
										}
									}
								}
							}
						}
					}
				}
			}

			return $mappings;
		}
	}
?>