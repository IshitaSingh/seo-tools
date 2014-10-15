<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */


	/**
	 * Provides access to application services
	 *
	 * It contains such things as the session data, configuration data,
	 * server messages and application state.  It also provides methods for
	 * forwarding, message handling, URL rewriting and logging.
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class Rum
	{
		/**
		 * This method returns the application
		 *
		 * @return  WebApplicationBase
		 */
		static public function app()
		{
			return \System\Web\WebApplicationBase::getInstance();
		}


		/**
		 * returns the application base uri
		 *
		 * @return string
		 */
		static public function baseURI()
		{
			return \System\Web\WebApplicationBase::getInstance()->config->uri;
		}


		/**
		 * returns the current page breadcrumb trail as an HTML element
		 *
		 * @return string
		 */
		static public function breadcrumb()
		{
			$pages = array();
			$breadcrumb = '';
			$parent = '';
			foreach(explode('/', \System\Web\WebApplicationBase::getInstance()->currentPage) as $page)
			{
				$title = $page;
				if($parent) {
					$page = $parent . '/' . $page;
				}
				else {
					$page = $page;
				}
				$pages[$title] = $page;
				$parent = $page;
			}
			$i=0;
			foreach($pages as $title=>$page)
			{
				if($breadcrumb)
				{
					$breadcrumb .= '&nbsp;&raquo;&nbsp;';
				}
				if(count($pages)-1<>$i++)
				{
					$breadcrumb .= "<a href=\"".\System\Web\WebApplicationBase::getInstance()->getPageURI($page)."\">".ucwords($title)."</a>";
				}
				else
				{
					$breadcrumb .= ucwords($title);
				}
			}

			return $breadcrumb;
		}


		/**
		 * This method returns the configuration object
		 *
		 * @return  CacheBase
		 */
		static public function cache()
		{
			return \System\Base\ApplicationBase::getInstance()->cache;
		}


		/**
		 * This method returns the configuration object
		 *
		 * @return  AppConfiguration
		 */
		static public function config()
		{
			return \System\Base\ApplicationBase::getInstance()->config;
		}


		/**
		 * This method returns the application DataAdapter object
		 *
		 * @return  DataAdapter
		 */
		static public function db()
		{
			return \System\Base\ApplicationBase::getInstance()->dataAdapter;
		}


		/**
		 * This method returns an escaped string using the application charset for outputting
		 *
		 * @param	string	$string			string to escape
		 * @param	int		$quote_style	quote style
		 * @param	string	$charset		character set
		 * @return  string
		 */
		static public function escape($string, $quote_style = __QUOTE_STYLE__, $charset = '')
		{
			return \htmlentities($string, $quote_style, $charset?$charset:\System\Base\ApplicationBase::getInstance()->charset);
		}


		/**
		 * This method sets the next URI once the current controller is finished executing, the servlet will
		 * re-request the page with the requested action.  This method allows you to replace post data
		 * with keep friendly urls that can be bookmarked.
		 *
		 * @param   string				$nextPage					Name of requested page
		 * @param   array				$args						args for next action
		 * @param   ForwardMethodType	$method						forward method as constant of ForwardMethodType::URI() or ForwardMethodType::Request()
		 * @return  void
		 */
		static public function forward( $nextPage = '', array $args = array(), \System\Web\ForwardMethodType $method = null )
		{
			\System\Web\WebApplicationBase::getInstance()->setForwardPage( $nextPage, $args, $method );
		}


		/**
		 * this method adds message to message stack
		 *
		 * @param   string			$msg		Message content
		 * @param   AppMessageType	$type	   Message type as constant of AppMessageType::Success(), AppMessageType::Fail() or AppMessageType::Notice()
		 * @return  void
		 */
		static public function flash( $msg, \System\Base\AppMessageType $type = null )
		{
			$type = $type?$type:\System\Base\AppMessageType::Info();

			$msgObj = array();
			if( strtolower( substr( $msg, 0, 2 )) == 'i:' ) {
				$type = \System\Base\AppMessageType::Info();
				$msg  = substr( $msg, 2 );
			}
			elseif( strtolower( substr( $msg, 0, 2 )) == 'f:' ) {
				$type = \System\Base\AppMessageType::Fail();
				$msg  = substr( $msg, 2 );
			}
			elseif( strtolower( substr( $msg, 0, 2 )) == 's:' ) {
				$type = \System\Base\AppMessageType::Success();
				$msg  = substr( $msg, 2 );
			}
			if( strtolower( substr( $msg, 0, 2 )) == 'w:' ) {
				$type = \System\Base\AppMessageType::Warning();
				$msg  = substr( $msg, 2 );
			}

			\System\Web\WebApplicationBase::getInstance()->messages->add( new \System\Base\AppMessage( (string) $msg, $type ));
		}


		/**
		 * this method removes any displayed messages
		 *
		 * @return  void
		 */
		static public function clearMessages()
		{
			\Rum::app()->requestHandler->page->loadAjaxJScriptBuffer("var messages = document.getElementById('messages').childNodes;");
			\Rum::app()->requestHandler->page->loadAjaxJScriptBuffer("var len = messages.length;");
			\Rum::app()->requestHandler->page->loadAjaxJScriptBuffer("for(i=0;i<len;i++){messages[i].parentNode.removeChild(messages[i]);}");
		}


		/**
		 * returns an HTML anchor element
		 *
		 * @param string $title	title of link
		 * @param string $page name of page
		 * @param array $args array of args
		 * @param string $class css class
		 * @return string URL encoded HTML anchor element
		 */
		static public function link($title, $page, array $args = array(), $class = '')
		{
			$uri = \Rum::escape(\System\Web\WebApplicationBase::getInstance()->getPageURI($page, $args));
			return "<a href=\"{$uri}\" title=\"{$title}\" class=\"{$class}\">{$title}</a>";
		}


		/**
		 * This method automatically writes a string to a log stamped with the current time
		 *
		 * @param  string	$message		event to log
		 * @param  string	$category		log category
		 * @return void
		 */
		static public function log( $message, $category = 'events' )
		{
			\System\Base\ApplicationBase::getInstance()->logger->log($message, $category);
		}


		/**
		 * returns an array of que'd up messages
		 *
		 * @return array
		 */
		static public function messages()
		{
			return \System\Web\WebApplicationBase::getInstance()->messages;
		}


		/**
		 * This method returns the current controller
		 *
		 * @return  PageControllerBase
		 */
		static public function requestHandler()
		{
			return \System\Base\ApplicationBase::getInstance()->requestHandler;
		}


		/**
		 * This method traces a variable
		 *
		 * @param   mixed		$var			raw variable to trace
		 * @return  void
		 */
		static public function trace( $var )
		{
			return \System\Base\ApplicationBase::getInstance()->trace($var);
		}


		/**
		 * This method returns a URI based on the requested page and parameters
		 *
		 * @param   string		$page			name of page
		 * @param   array		$args			array of parameters
		 * @return  string						raw URI
		 */
		static public function uri( $page = '', array $args = array() )
		{
			return \System\Web\WebApplicationBase::getInstance()->getPageURI( $page, $args );
		}


		/**
		 * This method returns a URL based on the requested page and parameters
		 *
		 * @param   string		$page			name of the page
		 * @param   array		$args			array of parameters
		 * @return  string						raw URL
		 */
		static public function url( $page = '', array $args = array() )
		{
			return __PROTOCOL__ . '://' . __HOST__ . \System\Web\WebApplicationBase::getInstance()->getPageURI( $page, $args );
		}


		/**
		 * This method sends an HTTP status message to the client
		 *
		 * @param   int			$statuscode		HTTP status code
		 * @return void
		 */
		static public function sendHTTPError( $statuscode = 500 )
		{
			return \System\Web\WebApplicationBase::getInstance()->sendHTTPError( $statuscode );
		}


		/**
		 * This method translates a message into the appropriate language
		 *
		 * @param string $stringId string id to translate
		 * @param string $default string if not found
		 *
		 * @return string
		 */
		static public function tl($stringId, $default='')
		{
			return \System\Base\ApplicationBase::getInstance()->translator->get($stringId, $default);
		}
	}
?>