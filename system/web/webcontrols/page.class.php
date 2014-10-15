<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents an Page Control
	 *
	 * @property string $onload Specifies jscript events for page load
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class Page extends View
	{
		/**
		 * specifies content-Type
		 * @var string
		 */
		protected $contentType			= 'text/html';

		/**
		 * specify javascript to execute on page load
		 * @var string
		 */
		protected $onload				= '';

		/**
		 * array of script elements
		 * @var array
		 */
		protected $scriptElements		= array();

		/**
		 * array of meta elements
		 * @var array
		 */
		protected $metaElements			= array();

		/**
		 * array of link elements
		 * @var array
		 */
		protected $linkElements			= array();

		/**
		 * array of object elements
		 * @var array
		 */
		protected $objectElements		= array();

		/**
		 * specifies whether object is currently rendering
		 * @var bool
		 */
		private $_rendering				= false;

		/**
		 * contains the buffer
		 * @var array
		 */
		static private $ajaxBuffer		= array();


		/**
		 * sets the controlId and prepares the control attributes
		 *
		 * @param  string   $controlId  Control Id
		 * @return void
		 */
		public function __construct( $controlId )
		{
			parent::__construct($controlId);

			// event handling
			$this->events->add(new \System\Web\Events\PageRequestEvent());
			$this->events->add(new \System\Web\Events\PagePostEvent());

			$onRequest = 'on'.ucwords($this->controlId).'Request';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onRequest))
			{
				$this->events->registerEventHandler(new \System\Web\Events\PageRequestEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onRequest));
			}
			$onPost = 'on'.ucwords($this->controlId).'Post';
			if(\method_exists(\System\Web\WebApplicationBase::getInstance()->requestHandler, $onPost))
			{
				$this->events->registerEventHandler(new \System\Web\Events\PagePostEventHandler('\System\Web\WebApplicationBase::getInstance()->requestHandler->' . $onPost));
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
			if( $field === 'onload' ) {
				return $this->onload;
			}
			elseif( $field === 'submit' )
			{
				if(null===$this->findControl('submit')) {
					throw new \System\Base\InvalidOperationException("ActiveRecordBase::form()->submit is no longer generated");
				}
				else {
					return $this->findControl('submit');
				}
			}
			else {
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
		public function __set( $field, $value ) {
			if( $field === 'onload' ) {
				$this->onload = (string)$value;
			}
			else {
				parent::__set($field,$value);
			}
		}


		/**
		 * add metatag
		 *
		 * @param   string		$name			the meta tag name to add
		 * @param   string		$content		the meta tag content value to add
		 * @return  int
		 */
		public function addMetaTag( $name, $content )
		{
			if(!$this->_rendering)
			{
				$metaElement = array( 'name' => (string) $name
									, 'content' => (string) $content );

				if( !in_array( $metaElement, $this->metaElements ))
				{
					return (int) array_push( $this->metaElements, $metaElement );
				}
				return 0;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot add meta data while rendering");
			}
		}


		/**
		 * add script element
		 *
		 * @param   string		$src			source
		 * @param   string		$type			type, default is text/javascript
		 * @param   string		$language		script language, default is 'Javascript'
		 * @param   string		$charset		character set, default is iso-8859-1
		 * @return  int
		 */
		public function addScript( $src, $type='text/javascript', $language='Javascript', $charset='utf-8' )
		{
			if(!$this->_rendering)
			{
				$scriptElement = array( 'src' => (string) $src
									  , 'type' => (string) $type
									  , 'language' => (string) $language
									  , 'charset' => (string) $charset );

				if( !in_array( $scriptElement, $this->scriptElements ))
				{
					return (int) array_push( $this->scriptElements, $scriptElement );
				}
				return 0;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot add script while rendering");
			}
		}


		/**
		 * add link element
		 *
		 * @param   string		$href			source
		 * @param   string		$rel			relationship, default is stylesheet
		 * @param   string		$type			type, default is text/css
		 * @param   string		$media			media, default is 'all'
		 * @param   string		$charset		character set, default is iso-8859-1
		 * @return  int
		 */
		public function addLink( $href, $rel='stylesheet', $type='text/css', $media='all', $charset='iso-8859-1' )
		{
			if(!$this->_rendering)
			{
				$linkElement = array( 'href' => (string) $href
									, 'rel' => (string) $rel
									, 'type' => (string) $type
									, 'media' => (string) $media
									, 'charset' => (string) $charset );

				if( !in_array( $linkElement, $this->linkElements ))
				{
					return (int) array_push( $this->linkElements, $linkElement );
				}
				return 0;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot add link while rendering");
			}
		}


		/**
		 * load javascript content into JScript buffer
		 *
		 * @param  string	$javaScript		javascript to execute
		 * @return void
		 */
		final public function loadJScript( $javaScript )
		{
			$this->onload .= $javaScript . ';';
			$this->loadAjaxJScriptBuffer($javaScript);
		}


		/**
		 * load content into ajax output buffer
		 *
		 * @param  string	$output		output content
		 * @return void
		 */
		final protected function loadAjaxBuffer( $output )
		{
			self::$ajaxBuffer[] = $output;
		}


		/**
		 * load content into ajax jscript output buffer
		 *
		 * @param  string	$javaScript		javascript to execute
		 * @return void
		 */
		final public function loadAjaxJScriptBuffer( $javaScript )
		{
			$this->loadAjaxBuffer(\str_replace("\n", "\\n", $javaScript));
		}


		/**
		 * return ajax output buffer as a string
		 *
		 * @return string
		 */
		final public function getAjaxBuffer()
		{
			$output = \implode("\n", self::$ajaxBuffer);
			$this->clearAjaxBuffer();
			return $output;
		}


		/**
		 * clear ajax output buffer
		 *
		 * @return void
		 */
		final public function clearAjaxBuffer()
		{
			self::$ajaxBuffer = array();
		}


		/**
		 * specifies whether the buffer contains output
		 *
		 * @return bool
		 */
		final public function isAjaxBufferReady()
		{
			return (count(self::$ajaxBuffer) > 0);
		}


		/**
		 * returns an html string
		 *
		 * @param   array		$args			widget parameters
		 * @return  string
		 */
		public function fetch( array $args = array() )
		{
			if(!$this->_rendering)
			{
				$this->_rendering = true;
				$content = str_replace('</head>', $this->fetchPageHeader() . '</head>', parent::fetch($args));
				$this->_rendering = false;

				return $content;
			}
			else
			{
				throw new \System\Base\InvalidOperationException("cannot fetch while rendering");
			}
		}


		/**
		 * Event called when request is processed
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			parent::onRequest($request);
			$this->events->raise(new \System\Web\Events\PageRequestEvent(), $this, $request);
		}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		protected function onPost( array &$request )
		{
			parent::onPost($request);
			$this->events->raise(new \System\Web\Events\PagePostEvent(), $this, $request);
		}


		/**
		 * return page header
		 *
		 * @return  string
		 */
		protected function fetchPageHeader()
		{
			ob_start();
?>
<?php foreach( $this->metaElements as $metaElement ) : ?>
<meta name="<?php echo $this->escape( $metaElement['name'] ) ?>" content="<?php echo $metaElement['content'] ?>" />
<?php endforeach; ?>
<?php foreach( $this->linkElements as $linkElement ) : ?>
<link href="<?php echo $this->escape( $linkElement['href'] ) ?>" rel="<?php echo $this->escape( $linkElement['rel'] ) ?>" type="<?php echo $this->escape( $linkElement['type'] ) ?>" media="<?php echo $this->escape( $linkElement['media'] ) ?>" />
<?php endforeach; ?>
<?php foreach( $this->scriptElements as $scriptElement ) : ?>
<script src="<?php echo $this->escape( $scriptElement['src'] ) ?>" type="<?php echo $this->escape( $scriptElement['type'] ) ?>"></script>
<?php endforeach; ?>
<?php if( $this->onload ) : ?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
window.onload = function () {<?php echo $this->onload ?>};
//--><!]]>
</script>
<?php endif; ?>
<?php
			return ob_get_clean();
		}
	}
?>