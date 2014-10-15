<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView Image
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class GridViewImage extends GridViewColumn
	{
		/**
		 * @param  string		$dataField			field name
		 * @param  string		$headerText			header text
		 * @param  string		$src				src (templating allowed)
		 * @param  string		$alt				alt text
		 * @param  string		$footerText			footer text
		 * @param  string		$className			css class name
		 * @return void
		 */
		public function __construct( $dataField, $headerText='', $src='', $alt='\'\'', $footerText='', $className='' )
		{
			parent::__construct($dataField, $headerText, "'<img src=\"'.{$src}.'\" alt=\"'.{$alt}.'\"/>'", $footerText, $className);
		}
	}
?>