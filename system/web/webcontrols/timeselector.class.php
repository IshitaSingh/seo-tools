<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
     * handles date control element creation (string)
	 * abstracts away the presentation logic and data access layer
     * the server-side control for WebWidgets
	 *
	 * @property bool $allowNull specifies whether to allow null values
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class TimeSelector extends InputBase
	{
		/**
		 * specifies whether to allow nulls
		 * @access protected
		 */
		protected $allowNull			= false;


		/**
		 * Constructor
		 *
		 * @return void
		 * @access public
		 */
		public function __construct( $controlId, $default = null )
		{
			parent::__construct( $controlId, $default );
			trigger_error("TimeSelector is deprecated, use Time instead", E_USER_DEPRECATED);
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @access protected
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field == 'allowNull' ) {
				return $this->allowNull;
			}
			else {
				return parent::__get( $field );
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 *
		 * @return mixed
		 * @access protected
		 * @ignore
		 */
		public function __set( $field, $value ) {
			if( $field == 'allowNull' ) {
				$this->allowNull = (bool)$value;
			}
			else {
				parent::__set($field,$value);
			}
		}


		/**
		 * called when control is loaded
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			parent::onLoad();

			$this->defaultHTMLControlId = $this->getHTMLControlId().'__hour';
		}


		/**
		 * process the HTTP request array
		 *
		 * @return void
		 * @access public
		 */
		protected function onRequest( array &$httpRequest ) {

			if( $this->readonly ) {
				$this->submitted = true;

				return;
			}

			if( isset( $httpRequest[$this->getHTMLControlId().'__hour'] ) &&
				isset( $httpRequest[$this->getHTMLControlId().'__minute'] ) &&
				isset( $httpRequest[$this->getHTMLControlId().'__meridiem'] ))
			{
				$this->submitted = true;
				if( isset( $httpRequest[$this->getHTMLControlId().'__null'] ) || !$this->allowNull )
				{
					if( $this->value != date( 'H:i:s', strtotime(	$httpRequest[$this->getHTMLControlId().'__hour']
																	. ':'
																	. $httpRequest[$this->getHTMLControlId().'__minute']
																	. $httpRequest[$this->getHTMLControlId().'__meridiem'] ))) {
						$this->changed = true;
					}

					$this->value = date( 'H:i:s', strtotime(	$httpRequest[$this->getHTMLControlId().'__hour']
																. ':'
																. $httpRequest[$this->getHTMLControlId().'__minute']
																. $httpRequest[$this->getHTMLControlId().'__meridiem'] ));

					if( isset( $httpRequest[$this->getHTMLControlId().'__null'] ))
					{
						unset( $httpRequest[$this->getHTMLControlId().'__null'] );
					}
				}
				else
				{
					if( $this->value ) {
						$this->changed = true;
					}

					$this->value = null;
				}

				unset( $httpRequest[$this->getHTMLControlId().'__hour'] );
				unset( $httpRequest[$this->getHTMLControlId().'__minute'] );
				unset( $httpRequest[$this->getHTMLControlId().'__meridiem'] );
			}

			parent::onRequest($httpRequest);
		}


		/**
		 * returns widget object
		 *
		 * @param  none
		 * @return void
		 * @access public
		 */
		public function getDomObject()
		{
			$editRegion = $this->createDomObject( 'span' );
			$editRegion->setAttribute( 'class', ' timeselector' );

			$select_hour = new \System\XML\DomObject( 'select' );
			$select_minute = new \System\XML\DomObject( 'select' );
			$select_meridiem = new \System\XML\DomObject( 'select' );
			$null = new \System\XML\DomObject( 'input' );

			$null->setAttribute( 'type', 'checkbox' );
			$null->setAttribute( 'name', $this->getHTMLControlId() . '__null' );
			$null->setAttribute( 'id', $this->getHTMLControlId() . '__null' );

			if( $this->value )
			{
				$null->setAttribute( 'checked', 'checked' );
			}

			$select_hour->setAttribute( 'class', 'timeselector_hour' );
			$select_minute->setAttribute( 'class', 'timeselector_minute' );
			$select_meridiem->setAttribute( 'class', 'timeselector_meridiem' );
			$null->setAttribute( 'class', 'timeselector_null' );

			$select_hour->setAttribute( 'name', $this->getHTMLControlId() . '__hour' );
			$select_minute->setAttribute( 'name', $this->getHTMLControlId() . '__minute' );
			$select_meridiem->setAttribute( 'name', $this->getHTMLControlId() . '__meridiem' );

			$select_hour->setAttribute( 'id', $this->getHTMLControlId() . '__hour' );
			$select_minute->setAttribute( 'id', $this->getHTMLControlId() . '__minute' );
			$select_meridiem->setAttribute( 'id', $this->getHTMLControlId() . '__meridiem' );

			$select_hour->setAttribute( 'tabIndex', $this->tabIndex++ );
			$select_minute->setAttribute( 'tabIndex', $this->tabIndex++ );
			$select_meridiem->setAttribute( 'tabIndex', $this->tabIndex++ );
			$null->setAttribute( 'tabIndex', $this->tabIndex );

			// set time to now if no time set
			$value=$this->value?strtotime($this->value)?$this->value:date('g:ia', time()):date('g:ia', time());

			// auto set on date
			if( $this->allowNull )
			{
				$select_hour->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
				$select_minute->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
				$select_meridiem->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
			}

			// set onchange attribute
			if( $this->autoPostBack )
			{
				$select_hour->setAttribute( 'onchange', 'submit();' );
				$select_minute->setAttribute( 'onchange', 'submit();' );
				$select_meridiem->setAttribute( 'onchange', 'submit();' );
				$null->setAttribute( 'onchange', 'Rum.id(\''.$this->getParentByType('\System\Web\WebControls\Form')->getHTMLControlId().'\').submit();' );
			}

			if( $this->ajaxPostBack )
			{
				$js = '\'' . $this->getHTMLControlId() . '__hour=\' + Rum.id(\'' . $this->getHTMLControlId() . '__hour\').value + ';
				$js .= '\'&' . $this->getHTMLControlId() . '__minute=\' + Rum.id(\'' . $this->getHTMLControlId() . '__minute\').value + ';
				$js .= '\'&' . $this->getHTMLControlId() . '__meridiem=\' + Rum.id(\'' . $this->getHTMLControlId() . '__meridiem\').value + ';
				if($this->allowNull) $js .= '\'&' . $this->getHTMLControlId() . '__null=\' + Rum.id(\'' . $this->getHTMLControlId() . '__null\').value + ';
				$js .= '\'';
				$select_hour->setAttribute( 'onchange',     'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$select_minute->setAttribute( 'onchange',   'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$select_meridiem->setAttribute( 'onchange', 'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$null->setAttribute( 'onchange',            'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
			}

			// set invalid class
			if( $this->submitted && !$this->validate() ) {
				$select_hour->setAttribute( 'class', 'invalid' );
				$select_minute->setAttribute( 'class', 'invalid' );
				$select_meridiem->setAttribute( 'class', 'invalid' );
			}

			// set readonly attribute
			if( $this->readonly )
			{
				$select_hour->setAttribute( 'disabled', 'disabled' );
				$select_minute->setAttribute( 'disabled', 'disabled' );
				$select_meridiem->setAttribute( 'disabled', 'disabled' );
				$null->setAttribute( 'disabled', 'disabled' );
			}

			// set readonly attribute
			if( $this->disabled )
			{
				$select_hour->setAttribute( 'disabled', 'disabled' );
				$select_minute->setAttribute( 'disabled', 'disabled' );
				$select_meridiem->setAttribute( 'disabled', 'disabled' );
				$null->setAttribute( 'disabled', 'disabled' );
			}

			// set tooltip attribute
			if( $this->tooltip )
			{
				$select_hour->setAttribute( 'title', $this->tooltip );
				$select_minute->setAttribute( 'title', $this->tooltip );
				$select_meridiem->setAttribute( 'title', $this->tooltip );
				$null->setAttribute( 'title', $this->tooltip );
			}

			// set visibility attribute
			if( !$this->visible )
			{
				$editRegion->setAttribute( 'style', 'display: none;' );
			}

			// select initial items
			$timestamp = strtotime( $value );
			if( $timestamp )
			{
				$hour     = (int)date( 'g', $timestamp );
				$minute   = (int)date( 'i', $timestamp );
				$meridiem = date( 'a', $timestamp );

				// create hour element
				for( $i=1; $i <= 12; $i++ )
				{
					$option = new \System\XML\DomObject( 'option' );
					$option->setAttribute( 'value', $i );
					$option->nodeValue = $i;

					if( $i == $hour )
					{
						$option->setAttribute( 'selected', 'selected' );
					}

					$select_hour->addChild( $option );
					unset( $option );
				}

				// create day element
				for( $i=0; $i <= 45; $i+=15 )
				{
					$option = new \System\XML\DomObject( 'option' );
					$option->setAttribute( 'value', $i?$i:'00' );
					$option->nodeValue = $i?$i:'00';

					if( $i <= $minute && $i > $minute - 15 )
					{
						$option->setAttribute( 'selected', 'selected' );
					}

					$select_minute->addChild( $option );
					unset( $option );
				}

				$option = new \System\XML\DomObject( 'option' );
				$option->setAttribute( 'value', 'am' );
				$option->nodeValue = 'AM';
				if( 'am' == $meridiem )
				{
					$option->setAttribute( 'selected', 'selected' );
				}
				$select_meridiem->addChild( $option );
				unset( $option );

				$option = new \System\XML\DomObject( 'option' );
				$option->setAttribute( 'value', 'pm' );
				$option->nodeValue .= 'PM';
				if( 'pm' == $meridiem )
				{
					$option->setAttribute( 'selected', 'selected' );
				}
				$select_meridiem->addChild( $option );
				unset( $option );

				$editRegion->addChild( $select_hour );
				$editRegion->addChild( $select_minute );
				$editRegion->addChild( $select_meridiem );

				if( $this->allowNull )
				{
					$editRegion->addChild( $null );
				}
			}

			return $editRegion;
		}


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("console.log('TimeSelector has no update ajax implementation');");
		}
	}
?>