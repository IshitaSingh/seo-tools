<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\I18N;


	/**
	 * Represents a message Translator using a File
	 *
	 * @package			PHPRum
	 * @subpackage		I18N
	 * @author			Darnell Shinbine
	 */
	class FileTranslator extends TranslatorBase
	{
		/**
		 * name of log file
		 * @var string
		 */
		private $file			= __LANGS_FILE__;

		/**
		 * langs
		 * @var XMLEntity
		 */
		private $langs			= null;


		/**
		 * Constructor
		 *
		 * @param string $flie file name
		 */
		public function __construct($file = '')
		{
			if($file)
			{
				$this->file = $file;
			}

			$xmlParser = new \System\XML\XMLParser();
			$this->langs = $xmlParser->parse(\file_get_contents($this->file));
		}


		/**
		 * get
		 *
		 * @param string $stringId string id to translate
		 * @param string $default string if not found
		 *
		 * @return void
		 */
		public function get($stringId, $default = '')
		{
			$default = $default?$default:$stringId;

			foreach($this->langs->children as $lang)
			{
				if($lang["lang"]==$this->lang)
				{
					foreach($lang->children as $string)
					{
						if($string["id"]==$stringId)
						{
							return $string["value"];
						}
					}
				}
			}

			// raise notice
			trigger_error("string id {$stringId} not found in langs.xml file, using default", E_USER_NOTICE);

			return $default;
		}
	}
?>