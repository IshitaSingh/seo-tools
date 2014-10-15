<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Provides base functionality for List Controls
	 *
	 * @property ListItemCollection $items Collection of list items
	 * @property bool $multiple Specifies whether multiple selections are allowed
	 * @property string $textField Specifies name of value text in datasource
	 * @property string $valueField Specifies name of value field in datasource
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	abstract class ListControlBase extends InputBase
	{
		/**
		 * collection of items
		 * @var ListItemCollection
		 */
		protected $items;

		/**
		 * Specifies whether multiple selections are allowed
		 * @var bool
		 */
		protected $multiple				= false;

		/**
		 * Specifies name of text field in datasource
		 * @var string
		 */
		protected $textField			= '';

		/**
		 * Specifies name of value field in datasource
		 * @var string
		 */
		protected $valueField			= '';


		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 * @param  string   $default	Default value
		 * @return void
		 */
		public function __construct( $controlId, $default = null )
		{
			parent::__construct( $controlId, $default );
			$this->items = new ListItemCollection();
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
			if( $field === 'items' )
			{
				return $this->items;
			}
			elseif( $field === 'multiple' )
			{
				return $this->multiple;
			}
			elseif( $field === 'textField' )
			{
				return $this->textField;
			}
			elseif( $field === 'valueField' )
			{
				return $this->valueField;
			}
			elseif( $field === 'minimumValue' || $field === 'maximumValue' )
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
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
			if( $field === 'multiple' )
			{
				$this->multiple = (bool)$value;
			}
			elseif( $field === 'textField' )
			{
				$this->textField = (string)$value;
			}
			elseif( $field === 'valueField' )
			{
				$this->valueField = (string)$value;
			}
			elseif( $field === 'minimumValue' || $field === 'maximumValue' )
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
			else
			{
				parent::__set($field,$value);
			}
		}


		/**
		 * validates control against validators, returns true on success
		 *
		 * @param  string		$errMsg		error message
		 * @return bool						true if control value is valid
		 */
		public function validate(&$errMsg = '')
		{
			if( $this->multiple )
			{
				if( is_array( $this->value ))
				{
					return parent::validate($errMsg);
				}
				else
				{
					$this->value = array();
				}
			}
			else
			{
				return parent::validate($errMsg);
			}
		}


		/**
		 * read view state from session
		 *
		 * @param  object	$viewState	session array
		 * @return void
		 */
		protected function onLoadViewState( array &$viewState )
		{
			parent::onLoadViewState( $viewState );

			if( !$this->value && $this->multiple )
			{
				$this->value = array();
			}
		}


		/**
		 * bind control to data
		 *
		 * @param  $default			value
		 * @return void
		 */
		protected function onDataBind()
		{
			$this->items->removeAll();

			// convert object into array
			if( $this->valueField && $this->textField )
			{
				foreach( $this->dataSource->toArray() as $row )
				{
					$this->items->add( (string) $row[$this->textField], (string) $row[$this->valueField] );
				}
			}
			else
			{
				throw new \System\Base\InvalidOperationException( 'ListControl::dataBind() called with no valueField or textField set' );
			}
		}


		/**
		 * process the HTTP request array
		 *
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			if( !$this->disabled )
			{
				if( $this->readonly )
				{
					$this->submitted = true;
				}

				if( isset( $request[$this->getHTMLControlId()] ))
				{
					$this->submitted = true;

					if( $this->value != $request[$this->getHTMLControlId()] )
					{
						$this->changed = true;
					}

					$this->value = $request[$this->getHTMLControlId()];
					unset( $request[$this->getHTMLControlId()] );
				}

				if( !$this->value && $this->multiple )
				{
					$this->value = array();
				}
				elseif( $this->value === '' )
				{
					$this->value = null;
				}
			}

//			if(( $this->ajaxPostBack || $this->ajaxValidation ) && $this->submitted)
//			{
//				if($this->validate($errMsg))
//				{
//					$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("Rum.clear('{$this->getHTMLControlId()}');");
//				}
//				else
//				{
//					$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("Rum.assert('{$this->getHTMLControlId()}', '".\addslashes($errMsg)."');");
//				}
//			}
		}
	}
?>