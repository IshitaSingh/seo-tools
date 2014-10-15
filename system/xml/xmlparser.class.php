<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\XML;


	/**
	 * Provides a way to parse xml documents into XMLEntity objects
	 *
	 * @property string $error parse error
	 * @property array $data xml data
	 * 
	 * @package			PHPRum
	 * @subpackage		XML
	 * @author			Darnell Shinbine
	 */
	final class XMLParser {

		/**
		 * parse error
		 * @var string
		 */
		protected $error			= '';

		/**
		 * array of xml data
		 * @var array
		 */
		protected $data				= array();


		/**
		 * gets object property
		 *
		 * @param   string	$field	value of field
		 * @return  void
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'error' ) {
				return $this->error;
			}
			elseif( $field === 'data' ) {
				return $this->data;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property {$field} in ".get_class($this));
			}
		}


		/**
		 * parse xml data
		 *
		 * @param   string	$xml			raw xml data
		 * @param   bool	$attrs			specifies whether to return attributes in result
		 * @return  XMLEntity				XMLEntity
		 */
		function parse( $xml ) {

			// remove XML declaration
			$xml = preg_replace('`<\?xml.*?\?>`', '', $xml);

			// create xml parser resource
			$xml_parser = xml_parser_create();

			// read xml data into array
			$nodes = array();
			if( !xml_parse_into_struct( $xml_parser, $xml, $nodes )) {
				throw new XMLException( sprintf(
					"Configuration XML Parse Error: %s at line %d",
					xml_error_string(xml_get_error_code( $xml_parser )),
					xml_get_current_line_number( $xml_parser )));
			}

			// free resources
			xml_parser_free( $xml_parser );

			$index=0;
			return $this->_getXMLEntityCollection( $nodes, $index )->itemAt( 0 );
		}


		/**
		 * returns XMLEntityCollection object
		 *
		 * @param   array	$nodes	array of xml nodes
		 * @param   int		$index	array index
		 * @return  void
		 */
		private function _getXMLEntityCollection( &$nodes, &$index ) {

			$xmlEntityCollection = new XMLEntityCollection();

			while( $index < sizeof( $nodes )) {

				// get XML node data
				$node = $nodes[$index++];

				// create XMLEntity object
				$xmlEntity = new XMLEntity( strtoupper( $node['tag'] ));

				// set entity attributes
				if( isset( $node['attributes'] )) {
					foreach( $node['attributes'] as $attribute => $value ) {
						$xmlEntity->setAttribute( strtoupper( $attribute ), $value );
					}
				}

				// set node children
				if( $node['type'] === 'open' ) {
					$xmlEntity->children = $this->_getXMLEntityCollection( $nodes, $index );

					// add entity to collection
					$xmlEntityCollection->add( $xmlEntity );
				}
				// no children
				elseif( $node['type'] === 'complete' ) {

					// set node value
					if( isset( $node['value'] )) {
						$this->data[strtoupper($node['tag'])] = $node['value'];
						$xmlEntity->value					 = $node['value'];
					}

					// add entity to collection
					$xmlEntityCollection->add( $xmlEntity );
				}
				elseif( $node['type'] === 'close' ) {
					break;
				}
			}

			return $xmlEntityCollection;
		}
	}
?>