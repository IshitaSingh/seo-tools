<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a ListBox Control
	 *
	 * @property int $listSize Size of listbox
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class ListBox extends ListControlBase
	{
		/**
		 * Size of listbox, default is 6
		 * @var int
		 */
		protected $listSize				= 6;


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'listSize' )
			{
				return $this->listSize;
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
			if( $field === 'listSize' )
			{
				$this->listSize = (int)$value;
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
			$select = $this->createDomObject( 'select' );
			$select->setAttribute( 'id', $this->getHTMLControlId());
			$select->setAttribute( 'title', $this->tooltip );
//			$select->setAttribute( 'class', ' listbox' );
			$select->setAttribute( 'size', $this->listSize );

			if( $this->multiple )
			{
				$select->setAttribute( 'multiple', 'multiple' );
				$select->setAttribute( 'name', $this->getHTMLControlId() .'[]' );
			}
			else
			{
				$select->setAttribute( 'name', $this->getHTMLControlId());
			}

			if( $this->submitted && !$this->validate() )
			{
				$select->setAttribute( 'class', 'invalid' );
			}

			if( $this->autoPostBack )
			{
				$select->setAttribute( 'onchange', 'Rum.id(\''.$this->getParentByType( '\System\Web\WebControls\Form')->getHTMLControlId().'\').submit();' );
			}

			if( $this->ajaxPostBack )
			{
				if( !$this->multiple )
				{
					$select->setAttribute( 'onchange', 'Rum.evalAsync(\'' . $this->ajaxCallback . '\',\''.$this->getHTMLControlId().'__validate=1&'.$this->getHTMLControlId().'=\'+encodeURIComponent(this.value)+\'&'.$this->getRequestData().'\',\'POST\','.\addslashes($this->ajaxStartHandler).','.\addslashes($this->ajaxCompletionHandler).');' );
				}
			}

			if( $this->readonly )
			{
				$select->setAttribute( 'disabled', 'disabled' );
			}

			if( $this->disabled )
			{
				$select->setAttribute( 'disabled', 'disabled' );
			}

			if( !$this->visible )
			{
				$select->setAttribute( 'style', 'display: none;' );
			}

			// create options
			$keys = $this->items->keys;
			$values = $this->items->values;

			for( $i = 0, $count = $this->items->count; $i < $count; $i++ )
			{
				$option = '<option';

				if( is_array( $this->value ))
				{
					if( array_search( $values[$i], $this->value ) !== false )
					{
						$option .= ' selected="selected"';
					}
				}
				else
				{
					if( $this->value == $values[$i])
					{
						$option .= ' selected="selected"';
					}
				}

				$option .= ' value="' . $values[$i] . '">';
				$option .= $keys[$i] . '</option>';

				$select->innerHtml .= $option;
			}

			return $select;
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


		/**
		 * Event called on ajax callback
		 *
		 * @return void
		 */
		protected function onUpdateAjax()
		{
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("Rum.id('{$this->getHTMLControlId()}').length=0;");
			foreach($this->items as $key=>$value)
			{
				$key = str_replace('\'', '\\\'', $key);
				$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("Rum.id('{$this->getHTMLControlId()}').options.add(new Option('{$key}', '{$value}'));");
			}
			$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("Rum.id('{$this->getHTMLControlId()}').value='{$this->value}';");
		}
	}
?>