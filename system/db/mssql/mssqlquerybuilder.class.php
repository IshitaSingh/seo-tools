<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB\MSSQL;


	/**
	 * Represents a generic SQL Query
	 *
	 * @property bool $empty specifies whether to return empty result set
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	class MSSQLQueryBuilder extends MSSQLStatement
	{
		/**
		 * object opening delimiter
		 * @var string
		**/
		protected $objectOpeningDelimiter	= "`";

		/**
		 * object closing delimiter
		 * @var string
		**/
		protected $objectClosingDelimiter	= "`";

		/**
		 * string delimiter
		 * @var string
		**/
		protected $stringDelimiter	= "'";

		/**
		 * main clause
		 * @var string
		**/
		protected $mainClause		= '';

		/**
		 * specifies whether to return empty resultset
		 * @var bool
		**/
		protected $empty			= false;

		/**
		 * array of select columns
		 * @var array
		**/
		private $columns			= array();

		/**
		 * array of values
		 * @var array
		**/
		private $values				= array();

		/**
		 * name of table
		 * @var array
		**/
		private $tables				= array();

		/**
		 * array of join tables
		 * @var array
		**/
		private $joins				= array();

		/**
		 * array of where clauses
		 * @var array
		**/
		private $whereClauses		= array();

		/**
		 * array of order by clauses
		 * @var array
		**/
		private $orderByClauses		= array();

		/**
		 * array of having clauses
		 * @var array
		**/
		private $havingClauses		= array();

		/**
		 * array of group by clauses
		 * @var array
		**/
		private $groupByClauses		= array();


		/**
		 * Constructor
		 * 
		 * @param DataAdapter	$dataAdapter	instance of a DataAdapter
		 * @param resource $connection mssql connection object
		 * @param string $objectOpeningDelimiter object opening delimiter
		 * @param string $objectClosingDelimiter object closing delimiter
		 * @param string $stringDelimiter string delimiter
		 */
		public function __construct(\System\DB\DataAdapter &$dataAdapter, $connection, $objectOpeningDelimiter = null, $objectClosingDelimiter = null, $stringDelimiter = null)
		{
			parent::__construct($dataAdapter, $connection);

			if($objectOpeningDelimiter) $this->objectOpeningDelimiter = $objectOpeningDelimiter;
			if($objectClosingDelimiter) $this->objectClosingDelimiter = $objectClosingDelimiter;
			if($stringDelimiter) $this->stringDelimiter = $stringDelimiter;
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return bool					true on success
		 * @ignore
		 */
		final public function __get( $field ) {
			if( $field === 'empty' ) {
				return $this->empty;
			}
			else {
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
		final public function __set( $field, $value ) {
			if( $field === 'empty' ) {
				$this->empty = (bool) $value;
			}
			else {
				parent::__set($field, $value);
			}
		}


		/**
		 * impliments `select` statement
		 *
		 * @param  string		$table			table name
		 * @param  string		$column			column name
		 * @param  string		$alias			column alias
		 * @return QueryBuilder
		 */
		final public function select( $table = '*', $column = '*', $alias = '' ) {
			$this->setMainClause( 'select' );
			if($table) {
				$this->addColumn( $table, $column, $alias );
			}
			return $this;
		}


		/**
		 * add column
		 *
		 * @return void
		 */
		final public function column( $table = '*', $column = '*', $alias = '' ) {
			$this->columns[] = array(
				  'table'  => (string) $table
				, 'column' => (string) $column
				, 'alias'  => $alias?(string)$alias:(string)$column );
		}


		/**
		 * impliments `insert into` statement
		 *
		 * @param  string		$table			table name
		 * @param  array		$columns		array of columns
		 * @return QueryBuilder
		 */
		final public function insertInto( $table, array $columns ) {
			$this->setMainClause( 'insert' );
			$this->addTable( $table );
			foreach( $columns as $columnname ) {
				$this->addColumn( $table, $columnname );
			}
			return $this;
		}


		/**
		 * impliments `update` statement
		 *
		 * @param  string		$table			table name
		 * @return QueryBuilder
		 */
		final public function update( $table ) {
			$this->setMainClause( 'update' );
			$this->addTable( $table );
			return $this;
		}


		/**
		 * impliments `truncate` statement
		 *
		 * @param  string		$table			table name
		 * @return QueryBuilder
		 */
		final public function truncate($table) {
			$this->setMainClause( 'truncate' );
			$this->addTable( $table, $table );
			return $this;
		}


		/**
		 * impliments `delete` statement
		 *
		 * @return QueryBuilder
		 */
		final public function delete() {
			$this->setMainClause( 'delete' );
			return $this;
		}


		/**
		 * impliments `from` statement
		 *
		 * @param  string		$table			table name
		 * @param  string		$alias			table alias
		 * @return QueryBuilder
		 */
		final public function from( $table, $alias = '' ) {
			$this->checkMainClause( 'select', 'delete' );
			$this->addTable( $table, $alias );
			return $this;
		}


		/**
		 * impliments `values` statement
		 *
		 * @param  array		$values			array of column values
		 * @return QueryBuilder
		 */
		final public function values( array $values ) {
			$this->checkMainClause( 'insert' );
			foreach( $values as $value ) {
				$this->addValue( $value );
			}
			return $this;
		}


		/**
		 * impliments `set` statement
		 *
		 * @param  string		$table			table name
		 * @param  string		$column			column name
		 * @param  string		$value			column value
		 * @return QueryBuilder
		 */
		final public function set( $table, $column, $value ) {
			$this->checkMainClause( 'update' );
			$this->addColumn( $table, $column );
			$this->addValue ( $value );
			return $this;
		}


		/**
		 * impliments `set` statement
		 *
		 * @param  string		$table			table name
		 * @param  array		$columns		column names
		 * @param  array		$values			values
		 * @return QueryBuilder
		 */
		final public function setColumns( $table, array $columns, array $values ) {
			$this->checkMainClause( 'update' );
			foreach( $columns as $column ) {
				$this->addColumn( $table, $column );
			}
			foreach( $values as $value ) {
				$this->addValue( $value );
			}
			return $this;
		}


		/**
		 * impliments `inner join` statement
		 *
		 * @param  string		$lefttable			left table name
		 * @param  string		$leftcolumn			left column name
		 * @param  string		$righttable			right table name
		 * @param  string		$rightcolumn		right column name
		 * @param  string		$alias				left table alias
		 * @return QueryBuilder
		 */
		final public function innerJoin( $lefttable, $leftcolumn, $righttable, $rightcolumn, $alias = '' ) {
			$this->checkMainClause( 'select', 'delete' );
			$this->addJoin( 'inner', $lefttable, $leftcolumn, $righttable, $rightcolumn, $alias );
			return $this;
		}


		/**
		 * impliments `left join` statement
		 *
		 * @param  string		$lefttable			left table name
		 * @param  string		$leftcolumn			left column name
		 * @param  string		$righttable			right table name
		 * @param  string		$rightcolumn		right column name
		 * @param  string		$alias				left table alias
		 * @return QueryBuilder
		 */
		final public function leftJoin( $lefttable, $leftcolumn, $righttable, $rightcolumn, $alias = '' ) {
			$this->checkMainClause( 'select', 'delete' );
			$this->addJoin( 'left', $lefttable, $leftcolumn, $righttable, $rightcolumn, $alias );
			return $this;
		}


		/**
		 * impliments `right join` statement
		 *
		 * @param  string		$lefttable			left table name
		 * @param  string		$leftcolumn			left column name
		 * @param  string		$righttable			right table name
		 * @param  string		$rightcolumn		right column name
		 * @param  string		$alias				left table alias
		 * @return QueryBuilder
		 */
		final public function rightJoin( $lefttable, $leftcolumn, $righttable, $rightcolumn, $alias = '' ) {
			$this->checkMainClause( 'select', 'delete' );
			$this->addJoin( 'right', $lefttable, $leftcolumn, $righttable, $rightcolumn, $alias );
			return $this;
		}


		/**
		 * impliments `where` statement
		 *
		 * @param  string		$table			table name
		 * @param  string		$column			column name
		 * @param  string		$operand		operation to perform
		 * @param  string		$value			column value
		 * @return QueryBuilder
		 */
		final public function where( $table, $column, $operand, $value ) {
			$this->checkMainClause( 'select', 'update', 'delete' );
			$this->addWhereClause( $table, $column, $operand, $value );
			return $this;
		}


		/**
		 * impliments `order by` statement
		 *
		 * @param  string		$table			table name
		 * @param  string		$column			column name
		 * @param  string		$direction		order by direction
		 * @return QueryBuilder
		 */
		final public function orderBy( $table, $column, $direction = 'asc' ) {
			$this->checkMainClause( 'select' );
			$this->addOrderByClause( $table, $column, $direction );
			return $this;
		}


		/**
		 * impliments `having` statement
		 *
		 * @param  string		$column			column name
		 * @param  string		$operand		operation to perform
		 * @param  string		$value			column value
		 * @return QueryBuilder
		 */
		final public function having( $column, $operand, $value ) {
			$this->checkMainClause( 'select', 'update', 'delete' );
			$this->addHavingClause( $column, $operand, $value );
			return $this;
		}


		/**
		 * impliments `group by` statement
		 *
		 * @param  string		$table			table name
		 * @param  string		$column			column name
		 * @return QueryBuilder
		 */
		final public function groupBy( $table, $column ) {
			$this->checkMainClause( 'select' );
			$this->addGroupByClause( $table, $column );
			return $this;
		}


		/**
		 * add column
		 *
		 * @return void
		 */
		protected function addColumn( $table = '*', $column = '*', $alias = '' ) {
			$this->columns[] = array(
				  'table'  => (string) $table
				, 'column' => (string) $column
				, 'alias'  => $alias?(string)$alias:(string)$column );
		}


		/**
		 * add value
		 *
		 * @return void
		 */
		protected function addValue( $value ) {
			$this->values[] = $value;
		}


		/**
		 * add table
		 *
		 * @return void
		 */
		protected function addTable( $table, $alias = '' ) {
			$this->tables[] = array(
				  'table' => (string) $table
				, 'alias' => $alias?(string)$alias:(string)$table );
		}


		/**
		 * add join
		 *
		 * @return void
		 */
		protected function addJoin( $type, $lefttable, $leftcolumn, $righttable, $rightcolumn, $alias = '' ) {
			$this->joins[] = array(
				  'type'		=> (string) $type
				, 'lefttable'   => (string) $lefttable
				, 'leftcolumn'  => (string) $leftcolumn
				, 'righttable'  => (string) $righttable
				, 'rightcolumn' => (string) $rightcolumn
				, 'alias'	   => $alias?(string)$alias:(string)$lefttable );
		}


		/**
		 * add where clause
		 *
		 * @return void
		 */
		protected function addWhereClause( $table, $column, $operand, $value ) {
			$this->whereClauses[] = array(
				  'table'   => (string) $table
				, 'column'  => (string) $column
				, 'operand' => (string) $operand
				, 'value'   => $value );
		}


		/**
		 * add order by clause
		 *
		 * @return void
		 */
		protected function addOrderByClause( $table, $column, $direction = 'asc' ) {
			$this->orderByClauses[] = array(
				  'table'	 => (string) $table
				, 'column'	=> (string) $column
				, 'direction' => (string) $direction=='desc'?'desc':'asc' );
		}


		/**
		 * add having clause
		 *
		 * @return void
		 */
		protected function addHavingClause( $column, $operand, $value ) {
			$this->havingClauses[] = array(
				  'column'  => (string) $column
				, 'operand' => (string) $operand
				, 'value'   => $value );
		}


		/**
		 * add group by clause
		 *
		 * @return void
		 */
		protected function addGroupByClause( $table, $column ) {
			$this->groupByClauses[] = array(
				  'table'	 => (string) $table
				, 'column'	=> (string) $column );
		}


		/**
		 * check statement
		 *
		 * @return bool
		 */
		private function checkMainClause( $statement1, $statement2 = '', $statement3 = '', $statement4 = '' ) {
			if( $this->mainClause ) {

				if( $this->mainClause === (string) $statement1 ) {
					return true;
				}
				elseif( $this->mainClause === (string) $statement2 ) {
					return true;
				}
				elseif( $this->mainClause === (string) $statement3 ) {
					return true;
				}
				elseif( $this->mainClause === (string) $statement4 ) {
					return true;
				}
			}

			throw new QueryException("unexpected clause in `{$this->mainClause}` statement");
		}


		/**
		 * set statement
		 *
		 * @return bool
		 */
		private function setMainClause( $statement ) {
			if( !$this->mainClause || $this->mainClause === (string) $statement ) {
				$this->mainClause = (string) $statement;
				return;
			}

			throw new QueryException( 'unexpected statement `' . (string) $statement . '` on `' . $this->mainClause . '` statement' );
		}


		/**
		 * get prepared SQL statement as string
		 *
		 * @param  array	$parameters	array of parameters to bind
		 * @return string
		 */
		public function getPreparedStatement(array $parameters = array()) {

			// select
			if( $this->mainClause === 'select' ) {
				$sql = 'select';

				// columns
				$columns = '';
				foreach( $this->columns as $column ) {

					if( strlen( $columns ) > 0 ) {
						$columns .= '
	, ';
					}
					else {
						$columns = ' ';
					}

					if( $column['table'] === '*' ) {
						$columns .= '*';
					}
					else {
						$columns .= ''.$this->objectOpeningDelimiter.'' . $column['table'] . ''.$this->objectClosingDelimiter.'';

						if( $column['column'] === '*' ) {
							$columns .= '.*';
						}
						else {
							$columns .= '.'.$this->objectOpeningDelimiter.'' . $column['column'] . ''.$this->objectClosingDelimiter.'';
							$columns .= ' as '.$this->objectOpeningDelimiter.'' . $column['alias'] . ''.$this->objectClosingDelimiter.'';
						}
					}
				}

				$sql .= isset( $columns )?$columns:'';

				// from
				$tables = '';
				foreach( $this->tables as $table ) {
					if( strlen( $tables ) > 0 ) {
						$tables .= '
	, '.$this->objectOpeningDelimiter.'' . $table['table'] . ''.$this->objectClosingDelimiter.'' . ' as '.$this->objectOpeningDelimiter.'' . $table['alias'] . ''.$this->objectClosingDelimiter.'';
					}
					else {
						$tables = '
	from '.$this->objectOpeningDelimiter.'' . $table['table'] . ''.$this->objectClosingDelimiter.'' . ' as '.$this->objectOpeningDelimiter.'' . $table['alias'] . ''.$this->objectClosingDelimiter.'';
					}
				}

				$sql .= isset( $tables )?$tables:'';
			}

			// insert
			elseif( $this->mainClause === 'insert' ) {
				$sql = 'insert';

				$tables = $this->tables;

				$sql .= '
	into '.$this->objectOpeningDelimiter.'' . $tables[0]['table'] . ''.$this->objectClosingDelimiter.' (';

				// columns
				$columns = '';
				foreach( $this->columns as $column ) {
					if( strlen( $columns ) > 0 ) {
						$columns .= ','.$this->objectOpeningDelimiter.'' . $column['column'] . ''.$this->objectClosingDelimiter.'';
					}
					else {
						$columns = ''.$this->objectOpeningDelimiter.'' . $column['column'] . ''.$this->objectClosingDelimiter.'';
					}
				}

				$sql .= isset( $columns )?$columns:'';
				$sql .= ')';

				$sql .= '
	values(';

				// values
				$values = '';
				foreach( $this->values as $value ) {

					if( strlen( $values ) > 0 ) {
						$values .= ',';
					}
					else {
						$values = '';
					}
					if( is_null( $value )) {
						$values .= 'null';
					}
					elseif( is_bool( $value )) {
						$values .= $value?'true':'false';
					}
					elseif( is_int( $value )) {
						$values .= (int)$value;
					}
					elseif( is_float( $value )) {
						$values .= (real)$value;
					}
					else {
						$value = $this->dataAdapter->escapeString( $value );
						if(strpos($value, '0x' )===0) {
							$values .= $value;
						}
						else {
							$values .= $this->stringDelimiter . $value . $this->stringDelimiter;
						}
					}
				}

				$sql .= $values . ')';
			}

			// update
			elseif( $this->mainClause === 'update' ) {
				$sql = 'update';

				$tables = $this->tables;
				$sql .= ' '.$this->objectOpeningDelimiter.'' . $tables[0]['table'] . ''.$this->objectClosingDelimiter.'';

				// set
				$columns = $this->columns;
				$values = $this->values;
				$setClause = '';
				for( $i = 0; $i < count( $columns ); $i++ ) {
					if( strlen( $setClause ) > 0 ) {
						$setClause .= '
	, ';
					}
					else {
						$setClause = '
	set ';
					}

					if( is_null( $values[$i] )) {
						$setClause .= ''.$this->objectOpeningDelimiter.'' . $columns[$i]['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $columns[$i]['column'] . ''.$this->objectClosingDelimiter.' = null';
					}
					elseif( is_bool( $values[$i] )) {
						$setClause .= ''.$this->objectOpeningDelimiter.'' . $columns[$i]['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $columns[$i]['column'] . ''.$this->objectClosingDelimiter.' = ' . ($values[$i]?'true':'false');
					}
					elseif( is_int( $values[$i] )) {
						$setClause .= ''.$this->objectOpeningDelimiter.'' . $columns[$i]['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $columns[$i]['column'] . ''.$this->objectClosingDelimiter.' = ' . (int)$values[$i];
					}
					elseif( is_float( $values[$i] )) {
						$setClause .= ''.$this->objectOpeningDelimiter.'' . $columns[$i]['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $columns[$i]['column'] . ''.$this->objectClosingDelimiter.' = ' . (real)$values[$i];
					}
					else {
						$value = $this->dataAdapter->escapeString( $values[$i] );
						if(strpos( $value, '0x' )===0) {
							$setClause .= ''.$this->objectOpeningDelimiter.'' . $columns[$i]['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $columns[$i]['column'] . ''.$this->objectClosingDelimiter.' = ' . $value;
						}
						else {
							$setClause .= ''.$this->objectOpeningDelimiter.'' . $columns[$i]['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $columns[$i]['column'] . ''.$this->objectClosingDelimiter.' = ' . $this->stringDelimiter . $value . $this->stringDelimiter;
						}
					}
				}

				$sql .= isset( $setClause )?$setClause:'';
			}

			// delete
			elseif( $this->mainClause === 'delete' ) {
				$sql = 'delete';

				// from
				$tables = '';
				foreach( $this->tables as $table ) {
					if( strlen( $tables ) > 0 ) {
						$tables .= '
	, '.$this->objectOpeningDelimiter.'' . $table['table'] . ''.$this->objectClosingDelimiter.'';
					}
					else {
						$tables = '
	from '.$this->objectOpeningDelimiter.'' . $table['table'] . ''.$this->objectClosingDelimiter.'';
					}
				}

				$sql .= isset( $tables )?$tables:'';
			}

			// delete
			elseif( $this->mainClause === 'truncate' ) {
				$sql = 'truncate';

				// from
				$tables = '';
				foreach( $this->tables as $table ) {
					if( strlen( $tables ) > 0 ) {
						$tables .= ', '.$this->objectOpeningDelimiter.'' . $table['table'] . ''.$this->objectClosingDelimiter.'';
					}
					else {
						$tables = ' '.$this->objectOpeningDelimiter.'' . $table['table'] . ''.$this->objectClosingDelimiter.'';
					}
				}

				$sql .= isset( $tables )?$tables:'';
			}

			// joins
			foreach( $this->joins as $join ) {
				$sql .= '
' . $join['type'] . '
	join '.$this->objectOpeningDelimiter.'' . $join['lefttable'] . ''.$this->objectClosingDelimiter.' as '.$this->objectOpeningDelimiter.'' . $join['alias'] . ''.$this->objectClosingDelimiter.'
		on '.$this->objectOpeningDelimiter.'' . $join['alias'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $join['leftcolumn'] . ''.$this->objectClosingDelimiter.' = '.$this->objectOpeningDelimiter.'' . $join['righttable'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $join['rightcolumn'] . ''.$this->objectClosingDelimiter.'';


			}

			// where
			$whereClause = '';
			foreach( $this->whereClauses as $where ) {
				if( strlen( $whereClause ) > 0 ) {
					$whereClause .= '
and';
				}
				else {
					$whereClause = '
where';
				}
				if( is_null( $where['value'] )) {
					$whereClause .= '
	'.$this->objectOpeningDelimiter.'' . $where['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $where['column'] . ''.$this->objectClosingDelimiter.' is null';
				}
				elseif( is_bool( $where['value'] )) {
					$whereClause .= '
	'.$this->objectOpeningDelimiter.'' . $where['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $where['column'] . ''.$this->objectClosingDelimiter.' = ' . ($where['value']?'true':'false');
				}
				elseif( is_int( $where['value'] )) {
					$whereClause .= '
	'.$this->objectOpeningDelimiter.'' . $where['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $where['column'] . ''.$this->objectClosingDelimiter.' ' . $where['operand'] . ' ' . (int)$where['value'] . '';
				}
				elseif( is_float( $where['value'] )) {
					$whereClause .= '
	'.$this->objectOpeningDelimiter.'' . $where['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $where['column'] . ''.$this->objectClosingDelimiter.' ' . $where['operand'] . ' ' . (real)$where['value'] . '';
				}
				else {
					$value = $this->dataAdapter->escapeString( $where['value'] );
					if(strpos( $value, '0x' )===0) {
						$whereClause .= '
	'.$this->objectOpeningDelimiter.'' . $where['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $where['column'] . ''.$this->objectClosingDelimiter.' ' . $where['operand'] . ' ' . $value;
					}
					else {
						$whereClause .= '
	'.$this->objectOpeningDelimiter.'' . $where['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $where['column'] . ''.$this->objectClosingDelimiter.' ' . $where['operand'] . ' ' . $this->stringDelimiter . $value . $this->stringDelimiter;
					}
				}
			}

			if( $this->empty ) {
				if( strlen( $whereClause ) === 0 ) {
					$whereClause = '
where
	0';
				}
			}

			$sql .= isset( $whereClause )?$whereClause:'';

			// orderby
			$orderByClause = '';
			foreach( $this->orderByClauses as $orderby ) {
				if( strlen( $orderByClause ) > 0 ) {
					$orderByClause .= '
	, '.$this->objectOpeningDelimiter.'' . $orderby['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $orderby['column'] . ''.$this->objectClosingDelimiter.' ' . $orderby['direction'];
				}
				else {
					$orderByClause = '
order
	by '.$this->objectOpeningDelimiter.'' . $orderby['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $orderby['column'] . ''.$this->objectClosingDelimiter.' ' . $orderby['direction'];
				}
			}

			$sql .= isset( $orderByClause )?$orderByClause:'';

			// groupby
			$groupByClause = '';
			foreach( $this->groupByClauses as $groupby ) {
				if( strlen( $groupByClause ) > 0 ) {
					$groupByClause .= '
	, '.$this->objectOpeningDelimiter.'' . $groupby['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $groupby['column'] . ''.$this->objectClosingDelimiter.'';
				}
				else {
					$groupByClause = '
group
	by '.$this->objectOpeningDelimiter.'' . $groupby['table'] . ''.$this->objectClosingDelimiter.'.'.$this->objectOpeningDelimiter.'' . $groupby['column'] . ''.$this->objectClosingDelimiter.'';
				}
			}

			$sql .= isset( $groupByClause )?$groupByClause:'';

			// having
			$havingClause = '';
			foreach( $this->havingClauses as $having ) {
				if( strlen( $havingClause ) > 0 ) {
					$havingClause .= '
and';
				}
				else {
					$havingClause = '
having';
				}
				$value = $this->dataAdapter->escapeString( $having['value'] );
				if(strpos( $value, '0x' )===0) {
					$havingClause .= '
	'.$this->objectOpeningDelimiter.'' . $having['column'] . ''.$this->objectClosingDelimiter.' ' . $having['operand'] . ' ' . $value;
				}
				else {
					$havingClause .= '
	'.$this->objectOpeningDelimiter.'' . $having['column'] . ''.$this->objectClosingDelimiter.' ' . $having['operand'] . ' ' . $this->stringDelimiter . $value . $this->stringDelimiter;
				}
			}

			$sql .= isset( $havingClause )?$havingClause:'';
			$this->prepare($sql);

			return parent::getPreparedStatement($parameters);
		}
	}
?>