<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\I18N;


	/**
	 * Represents a message Translator
	 *
	 * @property string		$lang		specifies the language
	 * @property string		$charset	specifies character set
	 *
	 * @package			PHPRum
	 * @subpackage		I18N
	 * @author			Darnell Shinbine
	 */
	abstract class TranslatorBase
	{
		/**
		 * specifies the language
		 * @var string
		 */
		private $lang = 'en';

		/**
		 * specifies the character set
		 * @var string
		 */
		private $charset = 'utf-8';


		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->charset = \System\Base\ApplicationBase::getInstance()->charset;
		}


		/**
		 * returns an object property
		 *
		 * @param  string	$field		name of the field
		 * @return mixed
		 * @ignore
		 */
		final public function __get( $field ) {
			if( $field === 'lang' ) {
				return $this->lang;
			}
			elseif( $field === 'charset' ) {
				return $this->charset;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property `{$field}` in `".get_class($this)."`");
			}
		}


		/**
		 * sets an object property
		 *
		 * @param  string	$field		name of the field
		 * @return void
		 * @ignore
		 */
		final public function __set( $field, $value ) {
			if( $field === 'lang' ) {
				$this->setLang($value);
			}
			elseif( $field === 'charset' ) {
				$this->setCharset($value);
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property `{$field}` in `".get_class($this)."`");
			}
		}


		/**
		 * set language
		 *
		 * @param string $lang language
		 * @return void
		 */
		final public function setLang($lang)
		{
			$this->lang = (string)$lang;
		}


		/**
		 * set character set
		 *
		 * @param string $charset character set
		 * @return void
		 */
		final public function setCharset($charset)
		{
			$this->charset = (string)$charset;
		}


		/**
		 * get
		 *
		 * @param string $stringId string id to translate
		 * @param string $default string if not found
		 * 
		 * @return void
		 */
		abstract public function get($stringId, $default = '');
	}
?>