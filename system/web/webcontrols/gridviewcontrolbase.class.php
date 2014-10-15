<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 *
	 *
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView control
	 *
	 * @property string $parameter specifies the request parameter
	 * @property string $pkey specifies the primary key field
	 * @property bool $escapeOutput Specifies whether to escape the output
	 * @property bool $readonly Specifies whether control is readonly
	 * @property bool $disabled Specifies whether the control is disabled
	 * @property string $tooltip Specifies control tooltip
	 * @property string $default Specifies default value
	 * @property bool $disableAutoComplete Specifies whether to disable the browsers auto complete feature
	 * @property string $placeholder Specifies the text for the placeholder attribute
	 * @property string $value Specifies the value of control
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	abstract class GridViewControlBase extends GridViewColumn
	{
		/**
		 * request parameter
		 * @var string
		 */
		protected $parameter				= '';

		/**
		 * primary key field
		 * @var string
		 */
		protected $pkey						= '';

		/**
		 * determines whether to escape the output
		 * @var bool
		 */
		protected $escapeOutput				= true;

		/**
		 * Specifies whether the control is readonly, Default is false
		 * @var bool
		 */
		protected $readonly					= false;

		/**
		 * Specifies whether the control is disabled, Default is false
		 * @var bool
		 */
		protected $disabled					= false;

		/**
		 * specifies control tool tip
		 * @var string
		 */
		protected $tooltip					= '';

		/**
		 * specifies default value
		 * @var string
		 */
		protected $default					= '';

		/**
		 * contains a collection of validators
		 * @var ValidatorCollection
		 */
		protected $validators				= null;

		/**
		 * Specifies whether to disable the browsers auto complete feature
		 * @var bool
		 */
		protected $disableAutoComplete		= false;

		/**
		 * Specifies the text for the placeholder attribute
		 * @var string
		 */
		protected $placeholder				= '';

		/**
		 * Specifies the value of control
		 * @var string
		 */
		protected $value					= null;

		/**
		 * post back
		 * @var bool
		 */
		private $_handlePostBack			= false;


		/**
		 * @param  string		$dataField			field name
		 * @param  string		$pkey				primary key
		 * @param  string		$value				value of Control
		 * @param  string		$parameter			parameter
		 * @param  string		$headerText			header text
		 * @param  string		$footerText			footer text
		 * @param  string		$className			css class name
		 * @param  string		$tooltip			toolstip
		 * @param  string		$default			default value
		 * @return void
		 */
		public function __construct( $dataField, $pkey, $parameter='', $headerText='', $footerText='', $className='', $tooltip='', $default = '' )
		{
			parent::__construct( $dataField, $headerText, '', $footerText, $className );

			$this->parameter = $parameter?$parameter:str_replace(" ","_",$dataField);
			$this->pkey = $pkey;
			$this->tooltip = $tooltip;
			$this->default = $default;
			$this->validators  = new \System\Validators\ValidatorCollection($this);

			// event handling
			// default events
			$postEvent='on'.ucwords(str_replace(" ","_",$this->parameter)).'Post';
			$ajaxPostEvent='on'.ucwords(str_replace(" ","_",$this->parameter)).'AjaxPost';

			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $postEvent))
			{
				$this->events->registerEventHandler(new \System\Web\Events\GridViewColumnPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $postEvent));
			}
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $ajaxPostEvent))
			{
				$this->ajaxPostBack = true;
				$this->events->registerEventHandler(new \System\Web\Events\GridViewColumnAjaxPostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $ajaxPostEvent));
			}
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'parameter' ) {
				return $this->parameter;
			}
			elseif( $field === 'pkey' ) {
				return $this->pkey;
			}
			elseif( $field === 'escapeOutput' ) {
				return $this->escapeOutput;
			}
			elseif( $field === 'disableAutoComplete' ) {
				return $this->disableAutoComplete;
			}
			elseif( $field === 'placeholder' ) {
				return $this->placeholder;
			}
			elseif( $field === 'readonly' ) {
				return $this->readonly;
			}
			elseif( $field === 'disabled' ) {
				return $this->disabled;
			}
			elseif( $field === 'tooltip' ) {
				return $this->tooltip;
			}
			elseif( $field === 'default' ) {
				return $this->default;
			}
			elseif( $field === 'value' ) {
				return $this->value;
			}
			else {
				return parent::__get($field);
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
		public function __set( $field, $value ) {
			if( $field === 'parameter' ) {
				$this->parameter = (string)$value;
			}
			elseif( $field === 'pkey' ) {
				$this->pkey = (string)$value;
			}
			elseif( $field === 'escapeOutput' ) {
				$this->escapeOutput = (bool)$value;
			}
			elseif( $field === 'disableAutoComplete' ) {
				$this->disableAutoComplete = (bool)$value;
			}
			elseif( $field === 'placeholder' ) {
				$this->placeholder = (string)$value;
			}
			elseif( $field === 'readonly' ) {
				$this->readonly = (bool)$value;
			}
			elseif( $field === 'disabled' ) {
				$this->disabled = (bool)$value;
			}
			elseif( $field === 'tooltip' ) {
				$this->tooltip = (string)$value;
			}
			elseif( $field === 'default' ) {
				$this->default = (string)$value;
			}
			elseif( $field === 'value' ) {
				$this->value = $value;
			}
			else {
				parent::__set( $field, $value );
			}
		}


		/**
		 * adds a validator to the control
		 *
		 * @param  ValidatorBase
		 * @return void
		 */
		public function addValidator(\System\Validators\ValidatorBase $validator)
		{
			$validator->field = $this->dataField;
			$this->validators->add($validator);
		}


		/**
		 * validates control against validators, returns true on success
		 *
		 * @param  string		$errMsg		error message
		 * @return bool						true if control value is valid
		 */
		public function validate(&$errMsg = '')
		{
			$fail = false;
			if(!$this->disabled)
			{
				foreach($this->validators as $validator)
				{
					if($validator instanceof \System\Validators\CompareValidator)
					{
						if(!$validator->compare($this->value, $this->gridView->columns->findColumn($validator->fieldToCompare)->value))
						{
							$fail = true;
							if($errMsg) $errMsg .= ", ";
							$errMsg .= $validator->errorMessage;
						}
					}
					elseif(!$validator->validate($this->value))
					{
						$fail = true;
						if($errMsg) $errMsg .= ", ";
						$errMsg .= $validator->errorMessage;
					}
				}
			}

			return !$fail;
		}


		/**
		 * handle load events
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->validators->load();
		}


		/**
		 * handle request events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		public function onRequest( &$request )
		{
			$parameter = $this->formatParameter($this->parameter);
			if( isset( $request[$parameter] ))
			{
				$this->value = $request[$parameter];
				$this->_handlePostBack = true;
				unset( $request[$parameter] );
			}
		}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		public function onPost( &$request )
		{
			if( $this->_handlePostBack )
			{
				if($this->ajaxPostBack && \Rum::app()->requestHandler->isAjaxPostBack)
				{
					$this->events->raise(new \System\Web\Events\GridViewColumnAjaxPostEvent(), $this, \System\Web\HTTPRequest::$post);
				}
				else
				{
					$this->events->raise(new \System\Web\Events\GridViewColumnPostEvent(), $this, \System\Web\HTTPRequest::$post);
				}
			}
		}


		/**
		 * handle request events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		public function onRender()
		{
			$this->itemText = $this->fetchUpdateControl($this->dataField, $this->formatParameter($this->parameter));
//			$this->footerText = $this->fetchInsertControl($this->dataField, $this->formatParameter($this->parameter));
		}


		/**
		 * format parameter
		 * 
		 * @param string $parameter parameter to format
		 * @return string
		 */
		final protected function formatParameter( $parameter )
		{
			$parameter = str_replace( ' ', '_', (string)$parameter );
			$parameter = str_replace( '\'', '_', $parameter );
			$parameter = str_replace( '"', '_', $parameter );
			$parameter = str_replace( '/', '_', $parameter );
			$parameter = str_replace( '\\', '_', $parameter );
			$parameter = str_replace( '.', '_', $parameter );

			return $parameter;
		}


		/**
		 * escape
		 * 
		 * @param type $string string to escape
		 * @return string
		 */
		final protected function escape( $string )
		{
			if( $this->escapeOutput )
			{
				return \Rum::escape( $string );
			}
			else
			{
				return $string;
			}
		}


		final protected function getAttrs()
		{
			$attrs = "name=\"{$this->parameter}\"";
			if( $this->readonly )
			{
				$attrs .= ' readonly="readonly"';
			}

			if( $this->disabled )
			{
				$attrs .= ' disabled="disabled"';
			}

			if( $this->disableAutoComplete )
			{
				$attrs .= ' autocomplete="off"';
			}

//			if( $this->placeholder )
//			{
//				$attrs .= " placeholder=\"{$this->placeholder}\"";
//			}
			return $attrs;
		}


		/**
		 * get item text
		 *
		 * @return string
		 */
		abstract public function fetchUpdateControl();


		/**
		 * get footer text
		 *
		 * @return string
		 */
		abstract public function fetchInsertControl();
	}
?>