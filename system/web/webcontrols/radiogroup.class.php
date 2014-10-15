<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a RadioGroup Control
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class RadioGroup extends InputBase
	{
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
			trigger_error("RadioGroup is deprecated", E_USER_DEPRECATED);
			parent::__construct( $controlId, $default );
		}


		/**
		 * adds child control to collection
		 *
		 * @param  RadioButton		&$control		instance of a RadioButton
		 * @return void
		 */
		final public function add( RadioButton $control )
		{
			return parent::addControl($control);
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$fieldset = $this->createDomObject( 'fieldset' );
//			$fieldset->setAttribute( 'class', ' radiogroup' );
			$legend = new \System\XML\DomObject( 'legend' );
			$legend->innerHtml = $this->label; // deprecated

			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$label = new \System\XML\DomObject( 'label' );
				$span = new \System\XML\DomObject( 'span' );
				$span->nodeValue = $this->controls->itemAt( $i )->label;
				$input = $this->controls->itemAt( $i )->getDomObject();

				$label->addChild( $input );
				$label->addChild( $span );
				$fieldset->addChild( $label );
			}

			return $fieldset;
		}


		/**
		 * called when control is loaded
		 * 
		 * @return bool			true if successfull
		 */
		protected function onLoad()
		{
			parent::onLoad();

			if( $this->value )
			{
				for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
				{
					$childControl = $this->controls->itemAt( $i );

					if( $childControl->value === $this->value )
					{
						$childControl->checked = true;
					}
					else {
						$childControl->checked = false;
					}
				}
			}

			if( isset( $this->controls[0] ))
			{
				$this->defaultHTMLControlId = $this->controls[0]->getHTMLControlId();
			}
			else
			{
				throw new \System\Base\InvalidOperationException("RadioGroup must have child RadioButton controls");
			}
		}


		/**
		 * process the HTTP request array
		 * 
		 * @param  array		&$request	request data
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
				elseif( isset( $request[$this->getHTMLControlId()] ))
				{
					// submitted
					$this->submitted = true;

					// changed
					if( $this->value != $request[$this->getHTMLControlId()] )
					{
						$this->changed = true;
					}

					// set value
					$this->value = $request[$this->getHTMLControlId()];
					unset( $request[$this->getHTMLControlId()] );
				}
			}

			parent::onRequest( $request );
		}
	}
?>