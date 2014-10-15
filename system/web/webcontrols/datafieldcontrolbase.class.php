<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Provides base functionality for Input Controls
	 *
	 * @property string $dataField Name of the data field in the datasource
	 * @property string $value Gets or sets value of control
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	abstract class DataFieldControlBase extends WebControlBase
	{
		/**
		 * Name of the data field in the datasource
		 * @var string
		 */
		protected $dataField				= '';

		/**
		 * Gets or sets value of control
		 * @var string
		 */
		protected $value					= null;


		/**
		 * Constructor
		 *
		 * The constructor sets attributes based on session data, triggering events, and is responcible for
		 * formatting the proper request value and garbage handling
		 *
		 * @param  string   $controlId	  Control Id
		 * @param  string   $default		Default value
		 * @return void
		 */
		public function __construct( $controlId, $default = null )
		{
			parent::__construct( $controlId );

			$this->dataField   = $controlId;
			$this->value       = $default;
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'dataField' ) {
				return $this->dataField;
			}
			elseif( $field === 'value' ) {
				return $this->value;
			}
			else {
				return parent::__get($field);
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
		public function __set( $field, $value ) {
			if( $field === 'dataField' ) {
				$this->dataField = (string)$value;
			}
			elseif( $field === 'value' ) {
				$this->value = $value;
			}
			else {
				parent::__set( $field, $value );
			}
		}


		/**
		 * update data source with Control value
		 *
		 * @param  \ArrayAccess $ds data source to fill
		 * @return void
		 */
		final public function fillDataSource( \ArrayAccess &$ds )
		{
			if( isset( $ds[$this->dataField] ))
			{
				$ds[$this->dataField] = $this->value;
			}
		}


		/**
		 * update Control value with data from the data source
		 *
		 * @param  \ArrayAccess $ds data source to read
		 * @return void
		 */
		final public function readDataSource( \ArrayAccess &$ds )
		{
			if( isset( $ds[$this->dataField] ))
			{
				if(!is_null($ds[$this->dataField]))
				{
					$this->value = $ds[$this->dataField];
				}
			}
		}


		/**
		 * bind control to datasource
		 * gets record from dataobject and sets the control value to datafield value
		 *
		 * @return bool			true if successfull
		 */
		protected function onDataBind()
		{
			if( isset( $this->dataSource[$this->dataField] ))
			{
				$this->value = $this->dataSource[$this->dataField];
			}
		}
	}
?>