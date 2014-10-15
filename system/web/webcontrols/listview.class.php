<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a ListView Control
	 *
	 * @property string $dataField Specifies the datafield represented by the list
	 * @property string $itemText Specifies the item text
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class ListView extends WebControlBase
	{
		/**
		 * specifies the datafield represented by the list
		 * @var string
		 */
		protected $dataField			= '';

		/**
		 * specifies the item text
		 * @var string
		 */
		protected $itemText				= '';

		/**
		 * contains tmp args array
		 * @var array
		 */
		private $_args				= array();


		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct( $controlId );
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
			if( $field === 'dataField' )
			{
				return $this->dataField;
			}
			elseif( $field === 'itemText' )
			{
				return $this->itemText;
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
			if( $field === 'dataField' )
			{
				$this->dataField = (string)$value;
			}
			elseif( $field === 'itemText' )
			{
				$this->itemText = (string)$value;
			}
			else
			{
				parent::__set($field,$value);
			}
		}


		/**
		 * renders form open tag
		 *
		 * @param   array	$args	attribute parameters
		 * @return void
		 */
		public function begin( array $args = array() )
		{
			$this->_args = $args;
			ob_start();
		}


		/**
		 * renders form close tag
		 *
		 * @return void
		 */
		public function end()
		{
			$this->itemText = '"' . ob_get_clean() . '"';
			\System\Web\HTTPResponse::write($this->getDomObject()->fetch( $this->_args ));
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			if(!$this->itemText) $this->itemText = "%{$this->dataField}%";

			// convert object into array
			$ul = new \System\XML\DomObject('div');
			$ul->setAttribute( 'id', $this->getHTMLControlId() );

			// convert object into array
			foreach( $this->dataSource->toArray() as $row )
			{
				// eval
				$values = array();
				$html = $this->itemText;

				foreach( $row as $field=>$value ) {
					$values[$field] = $value;
					$html = \str_replace( '%' . $field . '%', '$values["'.addslashes($field).'"]', $html );
				}

				$eval = eval( '$html = ' . $html . ';' );
				if($eval===false)
				{
					throw new \System\Base\InvalidOperationException("Could not run expression: \$html = " . ($html) . ';');
				}

				$ul->innerHtml .= $html;
			}

			return $ul;
		}


		/**
		 * called when control is loaded
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			parent::onLoad();
		}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			$page = $this->getParentByType('\System\Web\WebControls\Page');

			$page->loadAjaxJScriptBuffer('list1 = document.getElementById(\''.$this->getHTMLControlId().'\');');
			$page->loadAjaxJScriptBuffer('list2 = document.createElement(\'div\');');
			$page->loadAjaxJScriptBuffer('list2.innerHTML = \''.\addslashes(str_replace("\n", '', str_replace("\r", '', $this->fetch()))).'\';');
			$page->loadAjaxJScriptBuffer('list1.parentNode.insertBefore(list2, list1);');
			$page->loadAjaxJScriptBuffer('list1.parentNode.removeChild(list1);');
		}
	}
?>