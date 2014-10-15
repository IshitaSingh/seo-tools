<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a GridView Link
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class GridViewLink extends GridViewColumn
	{
		/**
		 * @param  string		$dataField			field name
		 * @param  string		$headerText			header text
		 * @param  string		$url				url (templating allowed)
		 * @param  string		$title				link title (templating allowed)
		 * @param  string		$target				link target
		 * @param  string		$alt				alt text
		 * @param  string		$footerText			footer text
		 * @param  string		$className			css class name
		 * @return void
		 */
		public function __construct( $dataField, $headerText='', $url='', $title='', $target='', $alt='', $footerText='', $className='' )
		{
			$title=$title?$title:$dataField;
			parent::__construct($dataField, $headerText, "'<a href=\"'.{$url}.'\" target=\"{$target}\" title=\"{$alt}\">'.{$title}.'</a>'", $footerText, $className);
		}
	}
?>