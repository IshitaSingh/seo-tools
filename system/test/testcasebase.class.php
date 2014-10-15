<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Test;
	use \UnitTestCase;

	/**
	 * include simpletest framework
	 */
	require_once __LIB_PATH__ . '/simpletest/unit_tester.php';


	/**
	 * Provides base functionality for the TestCase
	 *
	 * @property string $fixtures comma seperated string containing fixture filenames
	 *
	 * @package			PHPRum
	 * @subpackage		TestCase
	 * @author			Darnell Shinbine
	 */
	abstract class TestCaseBase extends UnitTestCase
	{
		/**
		 * fixtures to load
		 * @var string
		 */
		protected $fixtures				= '';


		/**
		 * Constructor
		 *
		 * @param   string			$testCase		Name of test case
		 * @return  void
		 * @ignore
		 */
		public function __construct( $testCase = '' ) {
			parent::__construct( $testCase );
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __set( $field, $value ) {
			throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
		}


		/**
		 * setup test module
		 *
		 * @return  void
		 * @ignore
		 */
		public function setUp() {
			parent::setUp();

			$this->loadFixtures( $this->fixtures );
			$this->prepare();
		}


		/**
		 * clean test module
		 *
		 * @return  void
		 * @ignore
		 */
		public function tearDown() {
			parent::tearDown();

			$this->cleanup();
		}


		/**
		 * run report
		 *
		 * @param   TestReporter	$reporter	TestReporter object
		 *
		 * @return  void
		 */
		public function run( &$reporter ) {
			$context = \SimpleTest::getContext();
			$context->setTest($this);
			$context->setReporter($reporter);
			$this->_reporter = &$reporter;
			$reporter->paintCaseStart($this->getLabel());
			$this->skip();
			if (! $this->_should_skip) {
				foreach ($this->getTests() as $method) {
					if ($reporter->shouldInvoke($this->getLabel(), $method)) {
						$invoker = &$this->_reporter->createInvoker($this->createInvoker());
						$invoker->before($method);
						$invoker->invoke($method);
						$invoker->after($method);
					}
				}
			}
			$reporter->setElapsedTime( \System\Base\ApplicationBase::getInstance()->timer->elapsed() );
			$reporter->paintCaseEnd($this->getLabel());
			unset($this->_reporter);
			return $reporter->getStatus();
		}


		/**
		 * returns an associative array of fixture variables
		 * used for dynamically inserting values inside fixtures
		 *
		 * @return  array
		 */
		protected function getFixtureVariables() {
			return array();
		}


		/**
		 * called before evey test
		 *
		 * @return  void
		 */
		protected function prepare() {
		}


		/**
		 * called after evey test
		 *
		 * @return  void
		 */
		protected function cleanup() {
		}


		/**
		 * load fixtures
		 *
		 * @param   array		$fixtures		array of fixtures
		 * @return  void
		 */
		protected function loadFixtures( $fixtures )
		{
			if($fixtures)
			{
				$tables = \System\Base\ApplicationBase::getInstance()->dataAdapter->buildSchema()->tableNames;

				while( count($tables) > 0 )
				{
					for($i=0; $i<count($tables); $i++)
					{
						$table = $tables[$i];
						if($table<>__DB_SCHEMA_VERSION_TABLENAME__)
						{
							try
							{
								\System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()->delete()->from($table)->execute();
								unset($tables[$i]);
								$tables = \array_values($tables);
							}
							catch(\System\DB\DatabaseException $e) {}
						}
						else
						{
							unset($tables[$i]);
							$tables = \array_values($tables);
						}
					}
				}

				if( strlen( $fixtures) > 0 )
				{
					$fixtures = explode( ',', $fixtures );

					foreach( $fixtures as $fixture )
					{
						if( strpos( $fixture, '.xml' ))
						{
							$this->loadXMLFixture( trim( $fixture ));
						}
						elseif( strpos( $fixture, '.csv' ))
						{
							$this->loadCSVFixture( trim( $fixture ));
						}
						elseif( strpos( $fixture, '.sql' ))
						{
							$this->loadSQLFixture( trim( $fixture ));
						}
						else
						{
							throw new \System\Base\InvalidOperationException( "fixture must be one of type .xml, .csv, or .sql" );
						}
					}
				}

				\System\Base\ApplicationBase::getInstance()->dataAdapter->rebuildSchema();
			}
		}


		/**
		 * load xml fixture
		 *
		 * @param   string		$fixture		name of fixture
		 * @return  void
		 */
		final protected function loadXMLFixture( $fixture )
		{
			if($fixture)
			{
				$tables = \System\Base\ApplicationBase::getInstance()->dataAdapter->buildSchema()->tableNames;
				$xmlParser = new \System\XML\XMLParser();
				$fail = false;

				$e = null;
				try
				{
					$xml = $xmlParser->parse( file_get_contents( \System\Base\ApplicationBase::getInstance()->config->fixtures . '/' . $fixture ));

					foreach( $xml->children as $record )
					{
						$ds = \System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()->select()->from($this->_arrayLsearch($record->name,$tables))->openDataSet();

						$fields = array_keys( $ds->row );
						$fieldnames = array();
						$fieldvalues = array();

						foreach( $record->children as $column )
						{
							$fieldnames[] = $this->_arrayLsearch( $column->name, $fields );

							if( isset( $column["EVAL"] ))
							{
								if( strtolower( $column["EVAL"] ) === 'true' )
								{
									eval('$column->value='.$column->value.';');
								}
							}

							$fieldvalues[] = $column->value;
						}

						try
						{
							\System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()
								->insertInto($this->_arrayLsearch( $record->name, $tables ), $fieldnames)
								->values($fieldvalues)
								->execute();
						}
						catch(SQLException $e)
						{
							throw new \System\Base\InvalidOperationException( "could not execute XML fixture " . $e->getMessage() );
						}
					}
				}
				catch(XMLException $e)
				{
					throw new \System\Base\XMLException( "could not parse XML fixture " . $e->getMessage() );
				}
			}
		}


		/**
		 * load csv fixture
		 *
		 * @param   string		$fixture		name of fixture
		 * @return  bool
		 */
		final protected function loadCSVFixture( $fixture )
		{
			if($fixture)
			{
				$table = str_replace('.csv', '', $fixture);

				$ds = \System\Base\ApplicationBase::getInstance()->dataAdapter->queryBuilder()->select()->from($table)->openDataSet();
				$fp = fopen( \System\Base\ApplicationBase::getInstance()->config->fixtures . '/' . $fixture, 'r' );
				$fields = fgetcsv($fp);

				while($row = fgetcsv($fp))
				{
					for($i=0; $i < count($fields); $i++)
					{
						$ds->row[$fields[$i]] = $row[$i];
					}
					$ds->insert();
				}
			}
		}


		/**
		 * load sql fixture
		 *
		 * @param   string		$fixture		name of fixture
		 * @return  void
		 */
		final protected function loadSQLFixture( $fixture )
		{
			if($fixture)
			{
				$sql = file_get_contents( \System\Base\ApplicationBase::getInstance()->config->fixtures . '/' . $fixture );
				$fail = false;

				foreach($this->getFixtureVariables() as $key=>$var)
				{
					$sql = str_replace("%{$key}%", $var, $sql);
				}

				if( $sql )
				{
					if(	\System\Base\ApplicationBase::getInstance()->dataAdapter instanceof \System\DB\MySQLi\MySQLiDataAdapter ||
						\System\Base\ApplicationBase::getInstance()->dataAdapter instanceof \System\DB\MySQL\MySQLDataAdapter)
					{
						\System\Base\ApplicationBase::getInstance()->dataAdapter->executeBatch( $sql );
					}
					else
					{
						\System\Base\ApplicationBase::getInstance()->dataAdapter->execute( $sql );
					}
				}
				else
				{
					throw new \System\Utils\FileLoadException( 'could not open fixture file ' . \System\Base\ApplicationBase::getInstance()->config->fixtures . '/' . $fixture );
				}
			}
		}


		/**
		 * search for string in array
		 *
		 * @param string $str
		 * @param array $array
		 * @return int index of element
		 */
		private function _arrayLsearch( $str, $array ) {
			foreach($array as $k=>$v){
				if(strtolower($v)===strtolower($str)){
					return $v;
				}
			}

			throw new \System\Base\InvalidOperationException("{$str} does not exist in array");
		}
	}
?>