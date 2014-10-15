<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Date Control
	 *
	 * @property string $dateFormat Specifies date format
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	class Date extends InputBase
	{
		/**
		 * type
		 * @ignore
		 */
		const type = 'date';

		/**
		 * specifies date format
		 * @var string
		 */
		protected $dateFormat				= 'Y-m-d';


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'dateFormat' ) {
				return $this->dateFormat;
			} else {
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
			if( $field === 'dateFormat' ) {
				$this->dateFormat = (string)$value;
			} else {
				parent::__set( $field, $value );
			}
		}


		/**
		 * process the HTTP request array
		 *
		 * @return void
		 * @access public
		 */
		protected function onRequest( array &$httpRequest )
		{
			parent::onRequest($httpRequest);

			if(strtotime($this->value)===false || $this->value=='0000-00-00') // PHP BUG
			{
				$this->value = null;
			}
		}

		/**
		 * getDomObject
		 *
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$input = $this->getInputDomObject();
			$input->setAttribute( 'value', date($this->dateFormat, strtotime($this->value)));
//			$input->setAttribute( 'class', ' '.self::type );
			$input->setAttribute( 'type', self::type );

			return $input;
		}
	}
?>