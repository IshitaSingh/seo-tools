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
     * @property int $yearMin year minimum
     * @property int $yearMax year maximum
	 * @property bool $allowNull specifies whether to allow null values
     *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class DateTimeSelector extends InputBase
	{
		/**
		 * specifies the year range
		 * @access protected
		 */
		protected $yearMin				= 1900;

		/**
		 * specifies the year range
		 * @access protected
		 */
		protected $yearMax				= 2020;

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
			trigger_error("DateTimeSelector is deprecated, use DateTime instead", E_USER_DEPRECATED);

			$this->yearMin = (int) date( 'Y', time() ) - 70;
			$this->yearMax = (int) date( 'Y', time() ) + 4;
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
			if( $field == 'yearMin' ) {
				return $this->yearMin;
			}
			elseif( $field == 'yearMax' ) {
				return $this->yearMax;
			}
			elseif( $field == 'allowNull' ) {
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
			if( $field == 'yearMin' ) {
				$this->yearMin = (int)$value;
			}
			elseif( $field == 'yearMax' ) {
				$this->yearMax = (int)$value;
			}
			elseif( $field == 'allowNull' ) {
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

			if( isset( $httpRequest[$this->getHTMLControlId().'__month'] ) &&
				isset( $httpRequest[$this->getHTMLControlId().'__day'] ) &&
				isset( $httpRequest[$this->getHTMLControlId().'__year'] ) &&
				isset( $httpRequest[$this->getHTMLControlId().'__hour'] ) &&
				isset( $httpRequest[$this->getHTMLControlId().'__minute'] ) &&
				isset( $httpRequest[$this->getHTMLControlId().'__meridiem'] ))
			{
				$this->submitted = true;

				if( isset( $httpRequest[$this->getHTMLControlId().'__null'] ) || !$this->allowNull )
				{
					if( $this->value != $httpRequest[$this->getHTMLControlId().'__year'] . '-' .
							$httpRequest[$this->getHTMLControlId().'__month'] . '-' .
							$httpRequest[$this->getHTMLControlId().'__day'] .
							date( 'H:i:s', strtotime(
							$httpRequest[$this->getHTMLControlId().'__hour'] . ':' .
							$httpRequest[$this->getHTMLControlId().'__minute'] .
							$httpRequest[$this->getHTMLControlId().'__meridiem'] ))) {
						$this->changed = true;
					}

					$this->value = $httpRequest[$this->getHTMLControlId().'__year'] . '-' .
						$httpRequest[$this->getHTMLControlId().'__month'] . '-' .
						$httpRequest[$this->getHTMLControlId().'__day'] .
						date( 'H:i:s', strtotime(
						$httpRequest[$this->getHTMLControlId().'__hour'] . ':' .
						$httpRequest[$this->getHTMLControlId().'__minute'] .
						$httpRequest[$this->getHTMLControlId().'__meridiem'] ));

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

					$this->value = null; // bug fix: changed to null
				}

				unset( $httpRequest[$this->getHTMLControlId().'__month'] );
				unset( $httpRequest[$this->getHTMLControlId().'__day'] );
				unset( $httpRequest[$this->getHTMLControlId().'__year'] );
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
			// create widgets
			$editRegion = $this->createDomObject( 'span' );
			$editRegion->setAttribute( 'class', ' datetimeselector' );

			$select_day = new \System\XML\DomObject( 'select' );
			$select_month = new \System\XML\DomObject( 'select' );
			$select_year = new \System\XML\DomObject( 'select' );
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

			$select_day->setAttribute( 'class', 'datetimeselector_day' );
			$select_month->setAttribute( 'class', 'datetimeselector_month' );
			$select_year->setAttribute( 'class', 'datetimeselector_year' );
			$select_hour->setAttribute( 'class', 'datetimeselector_hour' );
			$select_minute->setAttribute( 'class', 'datetimeselector_minute' );
			$select_meridiem->setAttribute( 'class', 'datetimeselector_meridiem' );
			$null->setAttribute( 'class', 'dateselector_null' );

			$select_day->setAttribute( 'name', $this->getHTMLControlId() . '__day' );
			$select_month->setAttribute( 'name', $this->getHTMLControlId() . '__month' );
			$select_year->setAttribute( 'name', $this->getHTMLControlId() . '__year' );
			$select_hour->setAttribute( 'name', $this->getHTMLControlId() . '__hour' );
			$select_minute->setAttribute( 'name', $this->getHTMLControlId() . '__minute' );
			$select_meridiem->setAttribute( 'name', $this->getHTMLControlId() . '__meridiem' );

			$select_day->setAttribute( 'id', $this->getHTMLControlId() . '__day' );
			$select_month->setAttribute( 'id', $this->getHTMLControlId() . '__month' );
			$select_year->setAttribute( 'id', $this->getHTMLControlId() . '__year' );
			$select_hour->setAttribute( 'id', $this->getHTMLControlId() . '__hour' );
			$select_minute->setAttribute( 'id', $this->getHTMLControlId() . '__minute' );
			$select_meridiem->setAttribute( 'id', $this->getHTMLControlId() . '__meridiem' );

			$select_month->setAttribute( 'tabIndex', $this->tabIndex++ );
			$select_day->setAttribute( 'tabIndex', $this->tabIndex++ );
			$select_year->setAttribute( 'tabIndex', $this->tabIndex++ );
			$select_hour->setAttribute( 'tabIndex', $this->tabIndex++ );
			$select_minute->setAttribute( 'tabIndex', $this->tabIndex++ );
			$select_meridiem->setAttribute( 'tabIndex', $this->tabIndex++ );
			$null->setAttribute( 'tabIndex', $this->tabIndex );

			// set date to today if no date set
			$value=$this->value?strtotime($this->value)?$this->value:date('m/d/y g:ia', time()):date('m/d/y g:ia', time());

			// auto set on date
			if( $this->allowNull )
			{
				$select_day->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
				$select_month->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
				$select_year->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
				$select_hour->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
				$select_minute->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
				$select_meridiem->setAttribute( 'onchange', 'Rum.id(\'' . $this->getHTMLControlId() . '__null\').checked = true;' );
			}

			// set onchange attribute
			if( $this->autoPostBack )
			{
				$select_day->setAttribute( 'onchange', 'submit();' );
				$select_month->setAttribute( 'onchange', 'submit();' );
				$select_year->setAttribute( 'onchange', 'submit();' );
				$select_hour->setAttribute( 'onchange', 'submit();' );
				$select_minute->setAttribute( 'onchange', 'submit();' );
				$select_meridiem->setAttribute( 'onchange', 'submit();' );
				$null->setAttribute( 'onchange', 'Rum.id(\''.$this->getParentByType('\System\Web\WebControls\Form')->getHTMLControlId().'\').submit();' );
			}

			if( $this->ajaxPostBack )
			{
				$js = '\'' . $this->getHTMLControlId() . '__day=\' + Rum.id(\'' . $this->getHTMLControlId() . '__day\').value + ';
				$js .= '\'&' . $this->getHTMLControlId() . '__month=\' + Rum.id(\'' . $this->getHTMLControlId() . '__month\').value + ';
				$js .= '\'&' . $this->getHTMLControlId() . '__year=\' + Rum.id(\'' . $this->getHTMLControlId() . '__year\').value + ';
				$js .= '\'&' . $this->getHTMLControlId() . '__hour=\' + Rum.id(\'' . $this->getHTMLControlId() . '__hour\').value + ';
				$js .= '\'&' . $this->getHTMLControlId() . '__minute=\' + Rum.id(\'' . $this->getHTMLControlId() . '__minute\').value + ';
				$js .= '\'&' . $this->getHTMLControlId() . '__meridiem=\' + Rum.id(\'' . $this->getHTMLControlId() . '__meridiem\').value + ';
				if($this->allowNull) $js .= '\'&' . $this->getHTMLControlId() . '__null=\' + Rum.id(\'' . $this->getHTMLControlId() . '__null\').value + ';
				$js .= '\'';

				$select_day->setAttribute( 'onchange',      'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$select_month->setAttribute( 'onchange',    'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$select_year->setAttribute( 'onchange',     'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$select_hour->setAttribute( 'onchange',     'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$select_minute->setAttribute( 'onchange',   'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$select_meridiem->setAttribute( 'onchange', 'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
				$null->setAttribute( 'onchange',            'Rum.evalAsync(\'' . $this->ajaxCallback . '\','.$js.'&'.$this->getRequestData().'\',\'POST\');' );
			}

			// set invalid class
			if( $this->submitted && !$this->validate() ) {
				$select_day->setAttribute( 'class', 'invalid' );
				$select_month->setAttribute( 'class', 'invalid' );
				$select_year->setAttribute( 'class', 'invalid' );
				$select_hour->setAttribute( 'class', 'invalid' );
				$select_minute->setAttribute( 'class', 'invalid' );
				$select_meridiem->setAttribute( 'class', 'invalid' );
			}

			// set readonly attribute
			if( $this->readonly )
			{
				$select_day->setAttribute( 'disabled', 'disabled' );
				$select_month->setAttribute( 'disabled', 'disabled' );
				$select_year->setAttribute( 'disabled', 'disabled' );
				$select_hour->setAttribute( 'disabled', 'disabled' );
				$select_minute->setAttribute( 'disabled', 'disabled' );
				$select_meridiem->setAttribute( 'disabled', 'disabled' );
				$null->setAttribute( 'disabled', 'disabled' );
			}

			// set readonly attribute
			if( $this->disabled )
			{
				$select_day->setAttribute( 'disabled', 'disabled' );
				$select_month->setAttribute( 'disabled', 'disabled' );
				$select_year->setAttribute( 'disabled', 'disabled' );
				$select_hour->setAttribute( 'disabled', 'disabled' );
				$select_minute->setAttribute( 'disabled', 'disabled' );
				$select_meridiem->setAttribute( 'disabled', 'disabled' );
				$null->setAttribute( 'disabled', 'disabled' );
			}

			// set tooltip attribute
			if( $this->tooltip )
			{
				$select_day->setAttribute( 'title', $this->tooltip );
				$select_month->setAttribute( 'title', $this->tooltip );
				$select_year->setAttribute( 'title', $this->tooltip );
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
				$day      = (int)date( 'd', $timestamp );
				$month    = (int)date( 'm', $timestamp );
				$year     = (int)date( 'Y', $timestamp );
				$hour     = (int)date( 'g', $timestamp );
				$minute   = (int)date( 'i', $timestamp );
				$meridiem = date( 'a', $timestamp );

				// create month element
				for( $i=1; $i <= 12; $i++ )
				{
					$option = new \System\XML\DomObject( 'option' );
					$option->setAttribute( 'value', $i );
					$option->nodeValue = date( 'M', strtotime( "$i/01/01" ));
 
					if( $i == $month )
					{
						$option->setAttribute( 'selected', 'selected' );
					}

					$select_month->addChild( $option );
					unset( $option );
				}

				// create day element
				for( $i=1; $i <= 31; $i++ )
				{
					$option = new \System\XML\DomObject( 'option' );
					$option->setAttribute( 'value', $i );
					$option->nodeValue = $i;

					if( $i == $day )
					{
						$option->setAttribute( 'selected', 'selected' );
					}

					$select_day->addChild( $option );
					unset( $option );
				}

				$thisyear = (int) date( 'Y', time() );

				// create year element
				for( $i=($this->yearMin); $i <= ($this->yearMax); $i++ )
				{
					$option = new \System\XML\DomObject( 'option' );
					$option->setAttribute( 'value', $i );
					$option->nodeValue = $i;

					if( $i == $year )
					{
						$option->setAttribute( 'selected', 'selected' );
					}

					$select_year->addChild( $option );
					unset( $option );
				}

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

				$editRegion->addChild( $select_month );
				$editRegion->addChild( $select_day );
				$editRegion->addChild( $select_year );
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
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("console.log('DateTimeSelector has no update ajax implementation');");
		}
	}
?>