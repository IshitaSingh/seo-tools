<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\DB;


	/**
	 * Provides access to a generic data source
	 *
	 * @property bool $opened Specifies whether the connection is open
	 * @property bool $closed Specifies whether the connection is closed
	 *
	 * @package			PHPRum
	 * @subpackage		DB
	 * @author			Darnell Shinbine
	 */
	abstract class DataAdapter
	{
		/**
		 * A collection of connection variables
		 * @var array
		 */
		protected $args					= array();

		/**
		 * Specifies the db collation
		 * @var string
		 */
		protected $collation			= 'utf8_general_ci';

		/**
		 * Specifies the character set
		 * @var string
		 */
		protected $charset				= 'utf-8';

		/**
		 * Specifies whether caching is enabled
		 * @var bool
		 */
		protected $cacheEnabled			= false;

		/**
		 * Specifies the caching expiration in seconds
		 * @var int
		 */
		protected $cacheExpires			= 300;

		/**
		 * last query executed
		 * @var string
		 */
		private $lastQuery				= '';

		/**
		 * last query execution time in ms
		 * @var int
		 */
		private $lastQueryTime		= 0;

		/**
		 * total query time in seconds
		 * @var int
		 */
		private $queryTime			= 0;

		/**
		 * query count
		 * @var int
		 */
		private $queryCount			= 0;

		/**
		 * Specifies whether to keep statistics on queries
		 * @var bool
		 */
		private $stats				= false;

		/**
		 * Specifies the elapsed time for stats
		 * @var int
		 */
		private $timer				= 0;

		/**
		 * Specifies whether to keep statistics on queries
		 * @var int
		 */
		private $schema;


		/**
		 * Constructor
		 *
		 * @param   array	$args	connection arguments
		 * @return void
		 */
		protected function __construct( array $args )
		{
			$this->stats = (bool)\System\Base\ApplicationBase::getInstance()->debug;

			if(isset($args["charset"]))
			{
				$this->charset = (string)$args["charset"];
			}

			if(isset($args["collation"]))
			{
				$this->collation = (string)$args["collation"];
			}

			if(isset($args["cache"]))
			{
				if($args["cache_enabled"]=='true' || $args["cache"]=='1')
				{
					$this->cacheEnabled = true;
				}
			}

			if(isset($args["cache_expires"]))
			{
				if($args["cache_expires"]>0)
				{
					$this->cacheExpires = (int)$args["cache_expires"];
				}
			}

			$this->args = $args;
			$this->open();
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return bool					true on success
		 * @ignore
		 */
		final public function __get( $field ) {
			if( $field === 'opened' ) {
				return (bool) $this->opened();
			}
			elseif( $field === 'closed' ) {
				return (bool) !$this->opened();
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property `{$field}` in `".get_class($this)."`");
			}
		}


		/**
		 * creates a connection object to a datasource
		 *
		 * Examples:
		 * <code>
		 * adapter=mysql;uid=root;pwd=;server=localhost;database=northwind;charset=utf8;pconnect=false;cache_enabled=false;cache_expires=300;
		 * adapter=mssql;uid=root;pwd=;server=localhost;database=northwind;charset=utf8;
		 * adapter=text;format=TabDelimited;source=/northwind.csv;charset=utf8;
		 * adapter=dir;source=/northwind;
		 * </code>
		 *
		 * @param   string   $dsn   connection string
		 * @param   string   $username   database username
		 * @param   string   $password   database password
		 * @return	DataAdapter
		 */
		final static public function create( $dsn, $username = '', $password = '' )
		{
			$dsn = ltrim($dsn);
			// detect PDO connection DSN
			if(strpos($dsn, 'adapter')===0 || strpos($dsn, 'driver')===0)
			{
				// Rum connection string detected
				$args = array( 'server' => 'localhost' );
				$var = explode( ';', (string) $dsn );
				$count = sizeof( $var );
				for( $i=0; $i < $count; $i++ ) {
					$pair = explode( '=', $var[$i] );
					if( isset( $pair[1] )) {
						$args[strtolower(trim($pair[0]))] = trim($pair[1]);
					}
				}

				if( isset( $args['adapter'] ) || isset( $args['driver'] )) {

					// get driver
					$adapter = isset( $args['adapter'] )?$args['adapter']:$args['driver'];
					$da = null;

					/**
					 * Create an object to handle this type of
					 * connection based on the specified driver
					 */

					/* MySQL adapter */
					if( $adapter === 'mysql' ) {
						include_once __SYSTEM_PATH__ . '/db/mysql/mysqldataadapter' . __CLASS_EXTENSION__;
						$da = new MySQL\MySQLDataAdapter( $args );
					}
					/* MySQLi improved adapter */
					elseif( $adapter === 'mysqli' ) {
						include_once __SYSTEM_PATH__ . '/db/mysqli/mysqlidataadapter' . __CLASS_EXTENSION__;						
						$da = new MySQLi\MySQLiDataAdapter( $args );
					}
					/* MSSQL adapter */
					elseif( $adapter === 'mssql' ) {
						include_once __SYSTEM_PATH__ . '/db/mssql/mssqldataadapter' . __CLASS_EXTENSION__;
						$da = new MSSQL\MSSQLDataAdapter( $args );
					}
					/* PostgreSQL adapter * /
					elseif( $adapter === 'pssql' ) {
						include_once __SYSTEM_PATH__ . '/db/pssql/pssqldataadapter' . __CLASS_EXTENSION__;
						$da = new PSSQL\PSSQLDataAdapter( $args );
					}
					/* Text File adapter */
					elseif( $adapter === 'text' ) {
						include_once __SYSTEM_PATH__ . '/db/text/textdataadapter' . __CLASS_EXTENSION__;
						$da = new Text\TextDataAdapter( $args );
					}
					/* File System adapter */
					elseif( $adapter === 'dir' ) {
						include_once __SYSTEM_PATH__ . '/db/dir/dirdataadapter' . __CLASS_EXTENSION__;
						$da = new Dir\DirDataAdapter( $args );
					}
					else {
						if( class_exists( $adapter )) {
							$da = new $adapter( $args );
						}
						else {
							throw new DataAdapterException("DataAdapter `{$adapter}` adapter not found");
						}
					}

					// return object
					return $da;
				}
				else
				{
					throw new DataAdapterException("no DataAdapter specified");
				}
			}
			else
			{
				// PDO connection string detected
				include_once __SYSTEM_PATH__ . '/db/pdo/pdodataadapter' . __CLASS_EXTENSION__;
				return new PDO\PDODataAdapter($dsn, $username, $password);
			}
		}


		/**
		 * opens a DataSet specified by the source
		 *
		 * @param  object		$source		source object
		 * @param  DataSetType	$lock_type	lock type as constant of DataSetType::OpenDynamic(), DataSetType::OpenStatic(), or DataSetType::OpenReadonly()
		 * @return DataSet					return DataSet
		 */
		final public function openDataSet( $source = '', DataSetType $lock_type = null )
		{
			if($this->cacheEnabled)
			{
				$id = $source;
				$ds = \System\Base\ApplicationBase::getInstance()->cache->get($id);
				if($ds)
				{
					return $ds;
				}
				else
				{
					$ds = DataSet::addNew( $source, $this, $lock_type );
					\System\Base\ApplicationBase::getInstance()->cache->put($id, $ds, $this->cacheExpires);
					return $ds;
				}
			}

			return DataSet::addNew( $source, $this, $lock_type );
		}


		/**
		 * enable caching
		 *
		 * @param  int	 $expires		cache expiration in seconds
		 * @return void
		 */
		final public function enableCaching( $expires = null )
		{
			$this->cacheEnabled = true;

			if( $expires > 0 )
			{
				$this->cacheExpires = (int)$expires;
			}
		}


		/**
		 * disable caching
		 *
		 * @param  int	 $expires		cache expiration in seconds
		 * @return void
		 */
		final public function disableCaching()
		{
			$this->cacheEnabled = false;
		}


		/**
		 * Executes a query procedure on the current connection
		 *
		 * @param  SQLStatement	$query	SQL statement
		 * @return void
		 */
		final public function execute( $query )
		{
			$this->query($query);
		}


		/**
		 * Executes a query batch or procedure on the current connection
		 *
		 * @param  string		$batch		sql batch query
		 * @return void
		 */
		final public function executeBatch( $batch )
		{
			// TODO: very bad!!! avoid!
//			trigger_error("executeBatch is deprecated!", E_USER_DEPRECATED);
			$queries = explode( ";", $batch );

			foreach( $queries as $query )
			{
				if(trim($query))
				{
					$this->execute($query);
				}
			}
		}


		/**
		 * return last executed query
		 *
		 * @return string
		 */
		final public function getLastQuery()
		{
			return $this->lastQuery;
		}


		/**
		 * return last executed query time in milliseconds
		 *
		 * @return real
		 */
		final public function getLastQueryTime()
		{
			return $this->lastQueryTime;
		}


		/**
		 * returns the query count
		 *
		 * @return int
		 */
		final public function getQueryCount()
		{
			return $this->queryCount;
		}


		/**
		 * returns the total query time
		 *
		 * @return int
		 */
		final public function getQueryTime()
		{
			return $this->queryTime;
		}


		/**
		 * returns a DataBaseSchema object
		 *
		 * @return DatabaseSchema
		 */
		final public function getSchema()
		{
			if( !$this->schema )
			{
				$id = 'schema:'.implode('', $this->args);
				$this->schema = \System\Base\Build::get( $id );

				if( !$this->schema )
				{
					$this->schema = $this->buildSchema();
					\System\Base\Build::put($id, $this->schema);
				}
			}

			return $this->schema;
		}


		/**
		 * rebuilds the DataBaseSchema object
		 *
		 * @return void
		 */
		final public function rebuildSchema()
		{
			$this->schema = $this->buildSchema();
		}


		/**
		 * builds a DataBaseSchema object
		 *
		 * @return DatabaseSchema
		 */
		abstract public function buildSchema();


		/**
		 * creats a TableSchema object
		 *
		 * @return DatabaseSchema
		 */
		abstract public function addTableSchema( TableSchema &$tableSchema );


		/**
		 * alters a TableSchema object
		 *
		 * @return DatabaseSchema
		 */
		abstract public function alterTableSchema( TableSchema &$tableSchema );


		/**
		 * drops a TableSchema object
		 *
		 * @return DatabaseSchema
		 */
		abstract public function dropTableSchema( TableSchema &$tableSchema );


		/**
		 * prepare an SQL statement
		 * Creates a prepared statement bound to parameters specified by the @symbol
		 * e.g. SELECT * FROM `table` WHERE user=@user
		 *
		 * @param  string	$statement	SQL statement
		 * @param  array	$parameters	array of parameters to bind
		 * @return SQLStatement
		 */
		abstract public function prepare($statement, array $parameters = array());


		/**
		 * fetches DataSet from datasource string using source string
		 *
		 * @param  DataSet	$ds			reference to a DataSet object
		 * @return void
		 */
		abstract public function fill( DataSet &$ds );


		/**
		 * opens a connection to a datasource
		 *
		 * @return bool					true if successfull
		 */
		abstract public function open();


		/**
		 * closes an open connection
		 *
		 * @return bool					true if successfull
		 */
		abstract public function close();


		/**
		 * returns true if a connection to a datasource is currently open
		 *
		 * @return bool					true if connection open
		 */
		abstract public function opened();


		/**
		 * attempt to insert a record into the datasource
		 *
		 * @param  DataSet	$ds				reference to a DataSet
		 * @return void
		 */
		abstract public function insert( DataSet &$ds );


		/**
		 * attempt to update a record in the datasource
		 *
		 * @param  DataSet	&$ds			reference to a DataSet
		 * @return void
		 */
		abstract public function update( DataSet &$ds );


		/**
		 * attempt to delete a record in the datasource
		 *
		 * @param  DataSet	&$ds			reference to a DataSet
		 * @return void
		 */
		abstract public function delete( DataSet &$ds );


		/**
		 * creats a QueryBuilder object
		 *
		 * @return SQLStatementBase
		 */
		abstract public function queryBuilder();


		/**
		 * creats a Transaction object
		 *
		 * @return TransactionBase
		 */
		abstract public function beginTransaction();


		/**
		 * Executes a query procedure on the current connection and return the result
		 *
		 * @param  string		$query		query to execute
		 * @return resource
		 */
		final protected function query( $query )
		{
			if( $this->stats )
			{
				$mtime = microtime();
				$mtime = explode( ' ', $mtime );
				$this->timer = (real)$mtime[1] + (real)$mtime[0];
				$this->lastQuery = $query;
				$this->lastQueryTime = 0;
			}

			// Add support for passing prepared statements
			if($query instanceof SQLStatementBase) {
				$result = $query->query();
			}
			else {
				// Backwards compatible support
//				trigger_error("Use of unprepared SQL statements is not recommended, use DataAdapter::prepare() instead", E_USER_WARNING);
				$statement = $this->prepare($query);
				$result = $statement->query();
			}

			if( $this->stats )
			{
				$mtime = microtime();
				$mtime = explode( ' ', $mtime );
				$this->lastQueryTime = number_format( (real)$mtime[1] + (real)$mtime[0] - $this->timer, 8 );
				$this->queryCount++;
				$this->queryTime += $this->lastQueryTime;
			}

			return $result;
		}
	}
?>