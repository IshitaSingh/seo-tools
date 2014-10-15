<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a RadioButtonList Control
	 *
	 * @property string $onclick Specifies the action to take on click events
	 * @property string $ondblclick Specifies the action to take on double click events
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class RadioButtonList extends ListControlBase
	{
		/**
		 * Specifies the action to take on click events
		 */
		protected $onclick					= '';

		/**
		 * Specifies the action to take on double click events
		 */
		protected $ondblclick				= '';

		/**
		 * Size of listbox
		 */
		protected $multiple					= false;


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'onclick' )
			{
				return $this->onclick;
			}
			elseif( $field === 'ondblclick' )
			{
				return $this->ondblclick;
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
			if( $field === 'onclick' )
			{
				$this->onclick = (string)$value;
			}
			elseif( $field === 'ondblclick' )
			{
				$this->ondblclick = (string)$value;
			}
			elseif( $field === 'multiple' )
			{
				throw new \System\Base\BadMemberCallException("call to readonly property $field in ".get_class($this));
			}
			elseif( $field === 'attributes' )
			{
				throw new \System\Base\BadMemberCallException("call to readonly property attributes in ".get_class($this));
			}
			else
			{
				parent::__set($field,$value);
			}
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$fieldset = new \System\XML\DomObject( 'fieldset' );
			$fieldset->setAttribute( 'id', $this->getHTMLControlId() );
//			$fieldset->setAttribute( 'class', ' radiobuttonlist' );

			if( !$this->visible )
			{
				$fieldset->setAttribute( 'style', 'display:none;' );
			}

			for( $i = 0, $count = $this->items->count; $i < $count; $i++ )
			{
				$input = $this->createDomObject( 'input' );
				$input->setAttribute( 'id', $this->getHTMLControlId() . '__' . $i );
//				$input->setAttribute( 'class', 'radiobuttonlist_input' );
				$input->setAttribute( 'value', $this->items->itemAt( $i ));
				$input->setAttribute( 'title', $this->tooltip );

				if( $this->submitted && !$this->validate() )
				{
					$input->setAttribute( 'class', 'invalid' );
				}

				if( $this->autoPostBack )
				{
					$input->setAttribute( 'onclick', 'Rum.id(\''.$this->getParentByType('\System\Web\WebControls\Form')->getHTMLControlId().'\').submit();' );
				}

				if( $this->ajaxPostBack )
				{
					$input->setAttribute( 'onclick', 'Rum.evalAsync(\'' . $this->ajaxCallback . '\',\''.$this->getHTMLControlId().'=\'+encodeURIComponent(this.value)+\'&'.$this->getRequestData().'\',\'POST\','.\addslashes($this->ajaxStartHandler).','.\addslashes($this->ajaxCompletionHandler).');' );
				}

				if( $this->readonly )
				{
					$input->setAttribute( 'readonly', 'readonly' );
				}

				if( $this->disabled )
				{
					$input->setAttribute( 'disabled', 'disabled' );
				}

				if( $this->multiple )
				{
					$input->setAttribute( 'type', 'checkbox' );
					$input->setAttribute( 'name', $this->getHTMLControlId() .'[]' );

					if( in_array( $this->items->itemAt( $i ), $this->value, false ))
					{
						$input->setAttribute( 'checked', 'checked' );
					}
				}
				else
				{
					$input->setAttribute( 'type', 'radio' );
					$input->setAttribute( 'name', $this->getHTMLControlId() );

					if( $this->value === $this->items->itemAt( $i ))
					{
						$input->setAttribute( 'checked', 'checked' );
					}
				}

				$input->setAttribute( 'onclick',	str_replace( '%value%', $this->items->itemAt( $i ), $this->onclick ));
				$input->setAttribute( 'ondblclick', str_replace( '%value%', $this->items->itemAt( $i ), $this->ondblclick ));

				$label = new \System\XML\DomObject( 'label' );
				$label->addChild( $input );
				$label->addChild( new \System\XML\TextNode( $this->items->keyAt( $i )) );
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

			if( $this->items->count > 0 )
			{
				$this->defaultHTMLControlId = $this->getHTMLControlId() . "__0";
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