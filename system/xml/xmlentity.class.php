<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\XML;
	use \ArrayAccess;


	/**
	 * XMLEntity
	 *
	 * Represents an XML entity
	 *
	 * @property string $name name of XML entity
	 * @property string $value value of XML entity
	 * @property string $cdata XML cdata
	 * @property XMLEntityAttributeCollection $attributes XML entity attributes
	 * @property XMLEntityCollection $children XML entity children
	 *
	 * @package			PHPRum
	 * @subpackage		XML
	 * @author			Darnell Shinbine
	 */
	class XMLEntity implements ArrayAccess {

		/**
		 * entity name
		 * @var string
		 */
		protected $name				= '';

		/**
		 * entity value
		 * @var string
		 */
		protected $value			= '';

		/**
		 * cdata value
		 * @var string
		 */
		protected $cdata			= '';

		/**
		 * entity attributes
		 * @var XMLEntityAttributeCollection
		 */
		protected $attributes		= array();

		/**
		 * entity children
		 * @var XMLEntityCollection
		 */
		protected $children			= null;

		/**
		 * set to force close tag
		 * @var bool
		 */
		protected $forceCloseTag	= false;


		/**
		 * Constructor
		 *
		 * @param   string	  $name	   Name of XML entity
		 *
		 * @return  void
		 */
		public function __construct( $name = '' )
		{
			$this->name = (string)$name;
			// $this->value = (string)$value;
			$this->children = new XMLEntityCollection();
			//$this->attributes = new XMLEntityAttributeCollection();
		}


		/**
		 * gets object property
		 *
		 * @param   string	$value	value of field
		 *
		 * @return  void
		 * @ignore
		 */
		public function __get( $value )
		{
			if( $value === 'name' )
			{
				return $this->name;
			}
			elseif( $value === 'value' )
			{
				return $this->value;
			}
			elseif( $value === 'cdata' )
			{
				return $this->cdata;
			}
			elseif( $value === 'attributes' )
			{
				return $this->attributes;
			}
			elseif( $value === 'children' )
			{
				return $this->children;
			}
			else
			{
				return $this->getChildByName( $value );
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
			if( $field === 'name' )
			{
				$this->name = (string) $value;
			}
			elseif( $field === 'value' )
			{
				$this->value = (string) $value;
			}
			elseif( $field === 'cdata' )
			{
				$this->cdata = (string) $value;
			}
			elseif( $field === 'attributes' )
			{
				if( $value instanceof XMLEntityAttributeCollection )
				{
					$this->attributes = $value;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid property value expected object of type XMLEntityAttributeCollection in ".get_class($this));
				}
			}
			elseif( $field === 'children' )
			{
				if( $value instanceof XMLEntityCollection )
				{
					$this->children = $value;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid property value expected object of type XMLEntityCollection in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetExists($index)
		{
			if( isset( $this->attributes[strtolower($index)] ))
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetGet($index)
		{
			if( isset( $this->attributes[strtolower($index)] ))
			{
				return $this->attributes[strtolower($index)];
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetSet($index, $value)
		{
			$this->attributes[strtolower($index)] = (string)$value;
		}

		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		function offsetUnset($index)
		{
			if( isset( $this->attributes[strtolower($index)] ))
			{
				unset( $this->attributes[strtolower($index)] );
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * get formatted entity value
		 *
		 * @param	string	$charset		character set
		 * @return  string					formatted value
		 */
		protected function getEntityValue( $charset = 'utf-8' ) {
			//$xml  = array('&#34;','&#38;','&#38;','&#60;','&#62;','&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;','&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;','&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;','&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;','&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;','&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;','&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;','&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;','&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;','&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;','&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;','&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;');
			//$html = array('&quot;','&amp;','&amp;','&lt;','&gt;','&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;');
			//$str  = str_replace( $html, $xml, $str );
			//$str  = str_ireplace( $html, $xml, $str );
			return \Rum::escape( $this->value, __QUOTE_STYLE__, $charset );
		}


		/**
		 * set entity attribute
		 *
		 * @param   string		$name	name of attribute
		 * @param   string		$value	value of attribute
		 * @return  void
		 */
		public function setAttribute( $name, $value ) {
			$this->attributes[strtolower((string)$name )] = (string)$value;
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
			if( isset( $this->attributes[strtolower((string)$name )] )) {
				$this->attributes[strtolower((string)$name )] .= (string)$value;
			}
			else {
				$this->attributes[strtolower((string)$name )] = (string)$value;
			}
		}


		/**
		 * get attribute of xml node
		 *
		 * @param  string		$name		name of attribute
		 * @return string					attribute value
		 */
		public function getAttribute( $name ) {
			return $this->attributes[strtolower((string)$name)];
		}


		/**
		 * remove an attribute from an xml node
		 *
		 * @param  string		$name		name of attribute
		 * @return void
		 */
		public function removeAttribute( $name ) {
			return $this->attributes->remove( strtolower( (string) $name ));
		}


		/**
		 * add a reference to a child element
		 *
		 * @param  XMLEntity	&$node		instance of an XMLEntity
		 * @return bool						true if successfull
		 */
		public function addChild( XMLEntity $node ) {
			return $this->children->add( $node );
		}


		/**
		 * returns a reference to a child element by index
		 *
		 * @param  int		$index			index of element
		 * @return XMLEntity				XMLEntity object
		 */
		public function getChild( $index ) {
			if( isset( $this->children[$index] )) {
				return $this->children[$index];
			}

			throw new \System\Base\ArgumentOutOfRangeException("child index `$index` does not exist");
		}


		/**
		 * returns a reference to a child element by element name
		 *
		 * @param  string	$name			name of element
		 * @return XMLEntity				XMLEntity object
		 */
		public function findChildByName( $name ) {
			foreach( $this->children as $child ) {
				if( strtolower( $child->name ) === strtolower( (string) $name )) {
					return $child;
				}
			}
			return null;
		}


		/**
		 * returns a reference to a child element by element name
		 *
		 * @param  string	$name			name of element
		 * @return XMLEntity				XMLEntity object
		 */
		public function getChildByName( $name ) {
			foreach( $this->children as $child ) {
				if( strtolower( $child->name ) === strtolower( (string) $name )) {
					return $child;
				}
			}

			throw new \System\Base\ArgumentOutOfRangeException("child element `$name` does not exist");
		}


		/**
		 * returns a reference to a child element by element attribute
		 *
		 * @param  string	$index			name of element
		 * @return XMLEntity				XMLEntity object
		 */
		public function getChildByAttribute( $attribute, $value ) {
			foreach( $this->children as $child ) {
				if( $child->getAttribute( (string) $attribute ) === (string) $value ) {
					return $child;
				}
			}

			throw new \System\Base\ArgumentOutOfRangeException("no child element with attribute `$attribute`=`$value` exists");
		}


		/**
		 * returns all children by name
		 *
		 * @param  string	$name			name of elements
		 * @return XMLEntityCollection					Collection of XMLEntity objects
		 */
		public function getChildrenByName( $name ) {
			$children = new XMLEntityCollection();
			foreach( $this->children as $child ) {
				if( strtolower( $child->name ) === strtolower( (string) $name )) {
					$children->add( $child );
				}
			}
			return $children;
		}


		/**
		 * remove a reference to a child element
		 *
		 * @param  XMLEntity	&$node		instance of a XMLEntity
		 * @return bool						true if successfull
		 */
		public function removeChild( XMLEntity &$node )
		{
			$numchildren = $this->children->count;
			for( $i = 0; $i < $numchildren; $i++ )
			{
				if( $this->children[$i] === $node )
				{
					return $this->children->remove( $i );
				}
			}
			return false;
		}


		/**
		 * replace a reference to a child element
		 *
		 * @param  XMLEntity	&$node1		instance of a XMLEntity
		 * @param  XMLEntity	&$node2		instance of a XMLEntity
		 * @return bool						true if successfull
		 */
		public function replaceChild( XMLEntity &$node1, XMLEntity &$node2 )
		{
			$numchildren = $this->children->count;
			for( $i = 0; $i < $numchildren; $i++ )
			{
				if( $this->children[$i] === $node1 )
				{
					$this->children[$i] = &$node2;
					return true;
				}
			}
			return false;
		}


		/**
		 * return XML string
		 *
		 * @return string			xml data
		 */
		public function getXMLString( $indent = 0 )
		{
			$charset = \System\Base\ApplicationBase::getInstance()->charset;

			/* create output buffer */
			$xml = '';

			/* indent */
			for( $i=0; $i < $indent; $i++ ) $xml .= ' ';

			/* open tag */
			$xml .= '<' . \Rum::escape( strtolower( $this->name ));

			/* add attributes */
			foreach( $this->attributes as $key => $value ) {
				$key   = \Rum::escape( strtolower( $key ));
				$value = trim(\Rum::escape( $value ));
				$xml  .= " $key=\"{$value}\"";
			}

			/* add elements */
			if( $this->children->count ) {

				/* close tag */
				$xml .= '>' . \System\Base\CARAGERETURN;

				/* add sub elements / set indent */
				foreach( $this->children as $child ) {
					$xml .= $child->getXMLString( $indent + \System\Base\INDENT );
				}

				/* indent */
				for( $i=0; $i < $indent; $i++ ) $xml .= ' ';

				/* close element */
				$xml .= '</' . \Rum::escape( \strtolower( $this->name )) . '>' . \System\Base\CARAGERETURN;
			}
			else {

				/* add text */
				$value = $this->getEntityValue($charset);

				if( strlen($value)>0 ) {
					$xml .= '>' . $value . '</' . \Rum::escape( \strtolower( $this->name )) . '>' . \System\Base\CARAGERETURN;
				}
				elseif( $this->cdata ) {
					$xml .= '><![CDATA[' . $this->cdata . ']]></' . \Rum::escape( \strtolower( $this->name )) . '>' . \System\Base\CARAGERETURN;
				}
				elseif( $this->forceCloseTag ) {
					$xml .= '></' . \Rum::escape( \strtolower( $this->name )) . '>' . \System\Base\CARAGERETURN;
				}
				else {
					/* close element */
					$xml .= ' />' . \System\Base\CARAGERETURN;
				}
			}

			return (string)$xml;
		}


		/**
		 * return XML node as string
		 *
		 * @param   array	$args			node parameters
		 * @return string					xhtml element
		 */
		final public function fetch( array $args = array() )
		{
			// set render time attributes
			foreach( $args as $key => $value )
			{
				$this->attributes[strtolower($key)] = $value;
			}

			return $this->getXMLString();
		}
	}
?>