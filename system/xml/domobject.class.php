<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\XML;


	/**
	 * Represents an XHTML DOM element
	 *
	 * @property string $nodeName name of XHTML DOM entity
	 * @property string $nodeValue value of XHTML DOM entity
	 * @property string $innerHtml inner html
	 * @property XMLEntityCollection $childNodes XHTML DOM entity children
	 * 
	 * @package			PHPRum
	 * @subpackage		XML
	 * @author			Darnell Shinbine
	 */
	final class DomObject extends XMLEntity
	{
		/**
		 * innerHtml value
		 * @var string
		 */
		protected $innerHtml		= '';


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'childNodes' ) {
				return $this->children;
			}
			elseif( $field === 'nodeName' ) {
				return $this->name;
			}
			elseif( $field === 'nodeValue' ) {
				return $this->value;
			}
			elseif( $field === 'innerHtml' ) {
				return $this->innerHtml;
			}
			else {
				return parent::__get( $field );
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string		$field			name of field
		 * @param  mixed		$value			value of field
		 * @return string						string of variables
		 * @ignore
		 */
		public function __set( $field, $value ) {
			if( $field === 'childNodes' ) {
				$this->children = $value;
			}
			elseif( $field === 'nodeName' ) {
				$this->name = (string) $value;
			}
			elseif( $field === 'nodeValue' ) {
				$this->value = (string) $value;
			}
			elseif( $field === 'innerHtml' ) {
				$this->innerHtml = (string) $value;
			}
			elseif( $field === 'name' ) {
				throw new \System\Base\BadMemberCallException("call to readonly property $field in ".get_class($this));
			}
			elseif( $field === 'value' ) {
				throw new \System\Base\BadMemberCallException("call to readonly property $field in ".get_class($this));
			}
			elseif( $field === 'children' ) {
				throw new \System\Base\BadMemberCallException("call to readonly property $field in ".get_class($this));
			}
			else {
				return parent::__set( $field, $value );
			}
		}


		/**
		 * return formatted entity value
		 *
		 * @param	string	$charset		character set
		 * @return  string					formatted value
		 */
		protected function getEntityValue( $charset = 'utf-8' )
		{
			if( strlen($this->innerHtml)>0 )
			{
				return $this->innerHtml;
			}
			else
			{
				return parent::getEntityValue( $charset );
			}
		}


		/**
		 * returns a reference to a child element by id
		 *
		 * @param  string	$id			id
		 * @return XMLEntity				XMLEntity object
		 */
		public function getChildById( $id ) {
			return $this->getChildByAttribute( 'id', $id );
		}


		/**
		 * output html node
		 *
		 * @param   array	$args			node parameters
		 * @return void
		 */
		public function render( array $args = array() )
		{
			\System\Web\HTTPResponse::write( $this->fetch( $args ));
		}


		/**
		 * return as XML string
		 *
		 * @return string			xml data
		 */
		final public function getXMLString( $indent = 0 )
		{
			// force end tag if xhtml type (html compatable)
			if( $this->name === 'script' ||
				$this->name === 'style' ||
				$this->name === 'title' ||
				$this->name === 'p' ||
				$this->name === 'a' ||
				$this->name === 'ul' ||
				$this->name === 'ol' ||
				$this->name === 'dl' ||
				$this->name === 'font' ||
				$this->name === 'table' ||
				$this->name === 'thead' ||
				$this->name === 'tbody' ||
				$this->name === 'tfoot' ||
				$this->name === 'th' ||
				$this->name === 'tr' ||
				$this->name === 'td' ||
				$this->name === 'form' ||
				$this->name === 'select' ||
				$this->name === 'textarea' ||
				$this->name === 'iframe' ||
				$this->name === 'span' ||
				$this->name === 'div' ||
				$this->name === 'label' ||
				$this->name === 'option' ) {

				// force end tag
				$this->forceCloseTag = true;
			}

			return parent::getXMLString( $indent );
		}
	}
?>