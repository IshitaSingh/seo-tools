<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a RadioButton Control
	 *
	 * @property bool $checked Specifies whether button is checked
	 * @property string $groupName Specifies group name
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class RadioButton extends InputBase
	{
		/**
		 * Specifies checked status
		 * @var bool
		 */
		protected $checked					= false;

		/**
		 * Specifies group name
		 * @var bool
		 */
		protected $groupName				= '';


		/**
		 * Constructor
		 *
		 * The constructor sets attributes based on session data, triggering events, and is responsible for
		 * formatting the proper request value and garbage handling
		 *
		 * @param  string   $controlId		Control Id
		 * @param  string   $groupName		Group name
		 * @param  string   $default		Default value
		 * @return void
		 */
		public function __construct( $controlId, $groupName = 'group', $default = false )
		{
			parent::__construct( $controlId, $controlId );
			if($groupName=='group') trigger_error ("RadioButton groupName name not specified, using default", E_USER_NOTICE);

			$this->checked = (bool)$default;
			$this->groupName = (string)$groupName;
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
			if( $field === 'checked' )
			{
				return $this->checked;
			}
			elseif( $field === 'groupName' )
			{
				return $this->groupName;
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
			if( $field === 'checked' )
			{
				$this->checked = (bool)$value;;
			}
			elseif( $field === 'groupName' )
			{
				$this->groupName = (string)$groupName;
			}
			else
			{
				return parent::__set( $field, $value );
			}
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			$input = $this->getInputDomObject();
			$input->setAttribute( 'value', $this->value );
			$input->setAttribute( 'name',  $this->groupName );
//			$input->setAttribute( 'class', ' radiobutton' );

			if( $this->visible )
			{
				$input->setAttribute( 'type', 'radio' );
			}

			if( $this->checked )
			{
				$input->setAttribute( 'checked', 'checked' );
			}

			return $input;
		}


		/**
		 * called when control is loaded
		 *
		 * @return bool			true if successfull
		 */
		protected function onLoad()
		{
			parent::onLoad();

			$this->autoPostBack = $this->getParentByType( '\System\Web\WebControls\RadioGroup' )->autoPostBack;
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
				else
				{
					if( isset( $request[$this->groupName] ))
					{
						// submitted
						$this->submitted = true;

						// changed
						if( $this->value === $request[$this->groupName] )
						{
							if( !$this->checked )
							{
								$this->changed = true;
							}
							$this->checked = true;
						}
						else
						{
							if( $this->checked )
							{
								$this->changed = true;
							}
							$this->checked = false;
						}
					}
				}
			}

			parent::onRequest( $request );
		}
	}
?>