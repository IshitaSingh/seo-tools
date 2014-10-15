<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 *
	 *
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a Fieldset
	 *
	 * @property bool $ajaxPostBack specifies whether to perform ajax postback, Default is false
	 * @property bool $ajaxValidation specifies whether to perform ajax validation, Default is false
	 * @property string $legend fieldset legend
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @ignore
	 *
	 */
	class Fieldset extends WebControlBase
	{
		/**
		 * Fieldset legend
		 * @var string
		 */
		protected $legend				= '';


		/**
		 * Constructor
		 *
		 * @param  string   $controlId  Control Id
		 *
		 * @return void
		 */
		public function __construct( $controlId )
		{
			trigger_error("Fieldset is deprecated", E_USER_DEPRECATED);

			parent::__construct( $controlId );

			$this->legend = $controlId;
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
			if( $field === 'legend' )
			{
				$this->legend = (string)$value;
			}
			elseif( $field === 'ajaxPostBack' )
			{
				$this->setAjaxPostBack($value);
			}
			elseif( $field === 'ajaxValidation' )
			{
				$this->setAjaxValidation($value);
			}
			else
			{
				parent::__set( $field, $value );
			}
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
			if( $field === 'legend' )
			{
				return $this->legend;
			}
			else
			{
				return parent::__get($field);
			}
		}


		/**
		 * adds child control to collection
		 *
		 * @param  InputBase		&$control		instance of an InputBase
		 * @return void
		 */
		final public function add( DataFieldControlBase $control )
		{
			return parent::addControl($control);
		}


		/**
		 * called when control is loaded
		 *
		 * @return bool			true if successfull
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
			// loop through input controls
			foreach( $this->controls as $childControl )
			{
				$childControl->needsUpdating = true;
			}
		}


		/**
		 * sets focus to the control
		 *
		 * @return bool			True if changed
		 */
		final public function focus()
		{
			if( isset( $this->controls[0] ))
			{
				$childControl = $this->controls[0];
				$childControl->focus();
			}
		}


		/**
		 * validate all controls in form object
		 *
		 * @param  string $errMsg error message
		 * @return bool
		 */
		public function validate(&$errMsg = '')
		{
			$valid = true;
			for($i = 0; $i < $this->controls->count; $i++)
			{
				if( !$childControl = $this->controls[$i]->validate( $errMsg ))
				{
					$valid = false;
				}
			}

			return $valid;
		}


		/**
		 * update data source with Control data
		 *
		 * @param  \ArrayAccess $ds data source to fill
		 * @return void
		 */
		final public function fillDataSource( \ArrayAccess &$ds )
		{
			foreach( $this->controls as $childControl )
			{
				$childControl->fillDataSource( $ds );
			}
		}


		/**
		 * update Control with data from the data source
		 *
		 * @param \ArrayAccess $ds data source to read
		 * @return void
		 */
		public function readDataSource( \ArrayAccess &$ds )
		{
			foreach( $this->controls as $childControl )
			{
				$childControl->readDataSource( $ds );
			}
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$fieldset = $this->createDomObject( 'fieldset' );
			$fieldset->innerHtml .= "<legend><span>{$this->legend}</span></legend>";
			$dl = '<dl>';

			for( $i = 0, $count = $this->controls->count; $i < $count; $i++ )
			{
				$childControl = $this->controls->itemAt( $i );

				// create list item
				if( !$childControl->visible )
				{
					$dt = '<dt style="display:none;">';
					$dd = '<dd style="display:none;">';
				}
				else
				{
					$dt = '<dt>';
					$dd = '<dd>';
				}

				// create label
				$dt .= '<label class="'.($childControl->attributes->contains("class")?$childControl->attributes["class"]:'').'" for="'.$childControl->defaultHTMLControlId.'">' . $childControl->label . '</label>';

				// Get input control
				$dd .= $childControl->fetch();

				// create validation message span tag
				$errMsg = '';
				if( $this->getParentByType('\System\Web\WebControls\Form')->submitted )
				{
					$childControl->validate($errMsg);
				}

				$dd .= $childControl->fetchError();

				$dl .= $dt . '</dt>';
				$dl .= $dd . '</dd>';
			}

			$dl .= '</dl>';

			$fieldset->innerHtml .= $dl;

			return $fieldset;
		}


		/**
		 * set postback state on all child controls
		 *
		 * @param  bool $ajaxPostBack postback state
		 * @return void
		 */
		private function setAjaxPostBack($ajaxPostBack = true)
		{
			foreach( $this->controls as $childControl )
			{
				$childControl->ajaxPostBack = (bool)$ajaxPostBack;
			}
		}


		/**
		 * set ajax validation state on all child controls
		 *
		 * @param  bool $ajaxValidation ajax validation state
		 * @return void
		 */
		private function setAjaxValidation($ajaxValidation = true)
		{
			foreach( $this->controls as $childControl )
			{
				$childControl->ajaxValidation = (bool)$ajaxValidation;
			}
		}
	}
?>