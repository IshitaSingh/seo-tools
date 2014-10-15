<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
    namespace System\Make;


	/**
	 * Provides functionality to generate controller/view files
	 *
	 * @package			PHPRum
	 * @subpackage		Make
	 */
	class Controller extends MakeBase
	{
		/**
		 * Default namespace
		 * @var string
		 */
		const ControllerNamespace       = '\\Controllers';


		/**
		 * make
		 *
		 * @param string $target target
		 * @param array $options options
		 * @return void
		 */
		public function make($target, array $options = array())
		{
			$path = substr($target, 0, strrpos($target, '/'));

			$className = ucwords(substr(strrchr('/'.$target, '/'), 1));
			$baseNamespace = Make::$namespace;
			$namespace = str_replace('/', '\\', $baseNamespace . self::ControllerNamespace . ($path?'\\'.ucwords($path):''));
			$baseClassName = '\\System\\Web\\ControllerBase';
			$pageURI = $target;

			$controllerPath = \System\Base\ApplicationBase::getInstance()->config->controllers . '/' . strtolower($target) . __CONTROLLER_EXTENSION__;
			$testCasePath = __FUNCTIONAL_TESTS_PATH__ . '/' . strtolower($target) . strtolower(__CONTROLLER_TESTCASE_SUFFIX__) . __CLASS_EXTENSION__;

			$controllerTemplate = file_get_contents(\System\Base\ApplicationBase::getInstance()->config->root . "/system/make/templates/controller.tpl");
			$controllerTemplate = str_replace("<Namespace>", $namespace, $controllerTemplate);
			$controllerTemplate = str_replace("<BaseNamespace>", $baseNamespace, $controllerTemplate);
			$controllerTemplate = str_replace("<ClassName>", $className, $controllerTemplate);
			$controllerTemplate = str_replace("<BaseClassName>", $baseClassName, $controllerTemplate);
			$controllerTemplate = str_replace("<PageURI>", $pageURI, $controllerTemplate);
			$controllerTemplate = str_replace("<TemplateExtension>", __TEMPLATE_EXTENSION__, $controllerTemplate);

			$testCaseTemplate = $template = file_get_contents(\System\Base\ApplicationBase::getInstance()->config->root . "/system/make/templates/controllertestcase.tpl");
			$testCaseTemplate = str_replace("<Namespace>", $namespace, $testCaseTemplate);
			$testCaseTemplate = str_replace("<BaseNamespace>", $baseNamespace, $testCaseTemplate);
			$testCaseTemplate = str_replace("<ClassName>", $className, $testCaseTemplate);
			$testCaseTemplate = str_replace("<BaseClassName>", $baseClassName, $testCaseTemplate);
			$testCaseTemplate = str_replace("<PageURI>", $pageURI, $testCaseTemplate);
			$testCaseTemplate = str_replace("<TemplateExtension>", __TEMPLATE_EXTENSION__, $testCaseTemplate);
			$testCaseTemplate = str_replace("<Fixture>", strtolower($className).'.sql', $testCaseTemplate);

			$this->export($controllerPath, $controllerTemplate);
			$this->export($testCasePath, $testCaseTemplate);
		}
	}
?>