<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a ReportGroup
	 *
	 * @property string $groupName Contains the group name
	 * @property DataSet $dataSource Contains the DataSet for the current group
	 * @property ReportView $report Contains the ReportView object
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class ReportGroup
	{
		/**
		 * contains the group name
		 * @var string
		 */
		private $groupName			= '';

		/**
		 * contains bound DataSet
		 * @var DataSet
		 */
		private $dataSource			= null;

		/**
		 * contains the GroupView
		 * @var ReportView
		 */
		private $report				= null;


		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 *
		 * @return void
		 */
		public function __construct( ReportView &$report, $groupName, \System\DB\DataSet &$ds )
		{
			$this->groupName = (string)$groupName;
			$this->dataSource = clone $ds;
			$this->report = $report;
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
			if( $field === 'groupName' )
			{
				return $this->groupName;
			}
			elseif( $field === 'dataSource' )
			{
				return $this->dataSource;
			}
			elseif( $field === 'report' )
			{
				return $this->report;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}
	}
?>