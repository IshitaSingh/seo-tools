<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\XML;


	/**
	 * Represents a TextNode XML entity
	 * 
	 * @package			PHPRum
	 * @subpackage		XML
	 * @author			Darnell Shinbine
	 */
	class TextNode extends XMLEntity
	{
		/**
		 * Constructor
		 *
		 * @param   string	  $text	   Node text
		 *
		 * @return  void
		 */
		public function __construct( $text = '')
		{
			$this->value = (string)$text;
		}


		/**
		 * gets object property
		 *
		 * @param   string	$value	value of field
		 * @return  void
		 * @ignore
		 */
		public function __get( $value )
		{
			if( $value === 'value' )
			{
				return $this->value;
			}
			else
			{
				return parent::__get($value);
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'value' )
			{
				$this->value = (string) $value;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to readonly property $field in ".get_class($this));
			}
		}


		/**
		 * set attribute
		 *
		 * @param   string		$name	name of attribute
		 * @param   string		$value	value of attribute
		 * @return  void
		 */
		public function setAttribute( $name, $value ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * append attribute of xml node
		 * overwrite attribute with same name
		 *
		 * @param  string		$name		name of attribute
		 * @param  string		$value		value of attribute
		 * @return void
		 */
		public function appendAttribute( $name, $value ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * get attribute of xml node
		 *
		 * @param  string		$name		name of attribute
		 * @return string					attribute value
		 */
		public function getAttribute( $name ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * remove an attribute from an xml node
		 *
		 * @param  string		$name		name of attribute
		 * @return void
		 */
		public function removeAttribute( $name ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * add a reference to a child element
		 *
		 * @param  DomObject	&$node		instance of a DomObject
		 * @return bool						true if successfull
		 */
		public function addChild( XMLEntity $node ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * returns a reference to a child element by index
		 *
		 * @param  int		$index			index of element
		 * @return XMLEntity				XMLEntity object
		 */
		public function getChild( $index ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * returns a reference to a child element by element name
		 *
		 * @param  string	$name			name of element
		 * @return XMLEntity				XMLEntity object
		 */
		public function getChildByName( $name ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * returns a reference to a child element by element attribute
		 *
		 * @param  string	$index			name of element
		 * @return XMLEntity				XMLEntity object
		 */
		public function getChildByAttribute( $attribute, $value ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * returns all children by name
		 *
		 * @param  string	$name			name of elements
		 * @return XMLEntityCollection					Collection of XMLEntity objects
		 */
		public function getChildrenByName( $name ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * remove a reference to a child element
		 *
		 * @param  DomObject	&$node		instance of a DomObject
		 * @return bool						true if successfull
		 */
		public function removeChild( XMLEntity &$node ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * replace a reference to a child element
		 *
		 * @param  DomObject	&$node1		instance of a DomObject
		 * @param  DomObject	&$node2		instance of a DomObject
		 * @return bool						true if successfull
		 */
		public function replaceChild( XMLEntity &$node1, XMLEntity &$node2 ) {
			throw new \System\Base\MethodNotImplementedException();
		}


		/**
		 * return XML string
		 *
		 * @return string			xml data
		 */
		public function getXMLString( $indent = 0 ) {
			return $this->getEntityValue(\System\Base\ApplicationBase::getInstance()->charset);
		}
	}
?>