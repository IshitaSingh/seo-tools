<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a ReportView Control
	 *
	 * @property string $title Report title
	 * @property DataSet $groupDataSource Contains the DataSet for the current group
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class ReportView extends WebControlBase
	{
		/**
		 * report title
		 * @var string
		 */
		private $title						= '';

		/**
		 * array holding report grouping fields
		 * @var array
		 */
		private $_grouping					= array();

		/**
		 * array holding report sorting fields
		 * @var array
		 */
		private $_sorting					= array();

		/**
		 * array holding report filtering fields and values
		 * @var array
		 */
		private $_filtering					= array();

		/**
		 * contains bound DataSet
		 * @var DataSet
		 */
		private $_data						= null;



		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 *
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct( $controlId );

			$this->title = $this->title?$this->title:$controlId;

			// event handling
			$this->events->add(new \System\Web\Events\ReportHeaderEvent());
			$this->events->add(new \System\Web\Events\ReportGroupHeaderEvent());
			$this->events->add(new \System\Web\Events\ReportDetailsEvent());
			$this->events->add(new \System\Web\Events\ReportGroupFooterEvent());
			$this->events->add(new \System\Web\Events\ReportFooterEvent());

			$method = 'on' . ucwords( $this->controlId ) . 'Header';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $method))
			{
				$this->events->registerEventHandler(new \System\Web\Events\ReportHeaderEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $method));
			}

			$method = 'on' . ucwords( $this->controlId ) . 'GroupHeader';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $method))
			{
				$this->events->registerEventHandler(new \System\Web\Events\ReportGroupHeaderEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $method));
			}

			$method = 'on' . ucwords( $this->controlId ) . 'Details';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $method))
			{
				$this->events->registerEventHandler(new \System\Web\Events\ReportDetailsEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $method));
			}

			$method = 'on' . ucwords( $this->controlId ) . 'GroupFooter';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $method))
			{
				$this->events->registerEventHandler(new \System\Web\Events\ReportGroupFooterEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $method));
			}

			$method = 'on' . ucwords( $this->controlId ) . 'Footer';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $method))
			{
				$this->events->registerEventHandler(new \System\Web\Events\ReportFooterEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $method));
			}
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'title' )
			{
				return $this->title;
			}
			else
			{
				return parent::__get( $field );
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return mixed
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'title' )
			{
				$this->title = (string)$value;
			}
			else
			{
				parent::__set( $field, $value );
			}
		}


		/**
		 * adds a grouping field to the report
		 *
		 * @return void
		 */
		public function addGrouping( $field, $ascending = true )
		{
			$this->events->add(new \System\Web\Events\ReportGroupHeaderEvent($field));
			$this->events->add(new \System\Web\Events\ReportGroupFooterEvent($field));

			$method = 'on' . ucwords( $this->controlId ) . ucwords( (string)$field ) . 'Header';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $method))
			{
				$this->events->registerEventHandler(new \System\Web\Events\ReportGroupHeaderEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $method, $field));
			}

			$method = 'on' . ucwords( $this->controlId ) . ucwords( (string)$field ) . 'Footer';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $method))
			{
				$this->events->registerEventHandler(new \System\Web\Events\ReportGroupFooterEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $method, $field));
			}

			$this->_grouping[$field] = $ascending;
		}


		/**
		 * adds a sorting field to the report
		 *
		 * @return void
		 */
		public function addSorting( $field, $ascending = true )
		{
			$this->_sorting[$field] = $ascending;
		}


		/**
		 * adds a filter to the report
		 *
		 * @return void
		 */
		public function addFilter( $field, $operator, $value )
		{
			$filter = array();
			$filter['field']	= $field;
			$filter['operator'] = $operator;
			$filter['value']	= $value;
			$this->_filtering[] = $filter;
		}


		/**
		 * returns an html string
		 *
		 * @param   array		$args			widget parameters
		 * @return  string
		 */
		public function fetch( array $array = array() )
		{
			ob_start();

			if( !$this->_data )
			{
				throw new \System\Base\InvalidOperationException("no valid DataSet object");
			}

			$this->_data->first();

			// filter
			foreach( $this->_filtering as $filter )
			{
				$this->_data->filter( $filter['field'], $filter['operator'], $filter['value'] );
			}

			// render report header
			$this->events->raise(new \System\Web\Events\ReportHeaderEvent(), $this, array('level'=>0));
			//$this->events->raise(new \System\Web\Events\ReportGroupHeaderEvent(), $this, array('level'=>0));

			// render details
			$this->_level( $this->_data );

			// render report footer
			//$this->events->raise(new \System\Web\Events\ReportGroupFooterEvent(), $this, array('level'=>0));
			$this->events->raise(new \System\Web\Events\ReportFooterEvent(), $this, array('level'=>0));

			return ob_get_clean();
		}


		/**
		 * returns a DomObject for rendering
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * bind control to data
		 *
		 * @param  $default			value
		 * @return void
		 */
		protected function onDataBind()
		{
			$this->_data = clone $this->dataSource;
		}


		/**
		 * return grouping array
		 *
		 * @return void
		 */
		protected function getGrouping()
		{
			return $this->_grouping;
		}


		/**
		 * render report grouping level
		 *
		 * @param  object	$param		DataSet object
		 * @param  level	$level		denotes the grouping level
		 * @return void
		 */
		private function _level( \System\DB\DataSet &$ds, $level = 0 )
		{
			/**
			 * the following code chunk will 
			 * get the field to group by
			 */
			$keys = array_keys( $this->_grouping );
			if( isset( $keys[$level] ))
			{
				if( \in_array($keys[$level], $ds->fields ))
				{
					// we now have the name of the field to group by so lets sort the data by field
					$field = $keys[ $level ];
					$ds->sort( $field, $this->_grouping[$field] );
				}
			}

			$value = false;

			if( isset( $field ))
			{
				// iterate through records
				while( !$ds->eof() )
				{
					if( in_array( $field, array_keys( $ds->row )))
					{
						// start a new group
						if( !( $value === $ds[$field] ))
						{
							// this grouping value
							$value = $ds[$field];

							/*
							 * this will filter out all records that 
							 * are not part of this grouping
							 */
							$group_rs = clone $ds;
							$group_rs->filter( $field, '=', $value );
							$group_rs->first();

							$group = new ReportGroup($this, $field, $group_rs);

							$this->events->raise(new \System\Web\Events\ReportGroupHeaderEvent($field), $group, array('level'=>$level));
							$this->events->raise(new \System\Web\Events\ReportGroupHeaderEvent(), $group, array('level'=>$level));

							/**
							 * if there is another level of grouping then repeat
							 * otherwise render
							 */
							if(( $level + 1 ) < sizeof( $keys ))
							{
								$this->_level( $group_rs, $level + 1 );
							}
							else
							{
								// sorting
								$sorting = array_reverse( $this->_sorting );
								foreach( $sorting as $key => $var )
								{
									$group_rs->sort( $key, $var );
								}

								// render details
								$this->events->raise(new \System\Web\Events\ReportDetailsEvent(), $group, array('level'=>$level));
							}

							// render group footer (only if not details)
							$group_rs->first();

							$this->events->raise(new \System\Web\Events\ReportGroupFooterEvent($field), $group, array('level'=>$level));
							$this->events->raise(new \System\Web\Events\ReportGroupFooterEvent(), $group, array('level'=>$level));
						}
					}

					// move record pointer
					$ds->next();
				}
			}
			else
			{
				// sorting
				$sorting = array_reverse( $this->_sorting );
				foreach( $sorting as $key => $var )
				{
					$ds->sort( $key, $var );
				}

				$this->events->raise(new \System\Web\Events\ReportDetailsEvent(), new ReportGroup($this, '', $ds), array('level'=>0));
			}
		}
	}
?>