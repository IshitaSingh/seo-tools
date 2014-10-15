<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a TreeView Control
	 *
	 * @property bool $showIcons Specifies whether to show icons in tree
	 * @property bool $showRoot Specifies whether to show the root node
	 * @property rootNode $rootNode Contains the root node
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class TreeView extends WebControlBase
	{
		/**
		 * specifies whether to show icons in tree, Default is true
		 * @var bool
		 */
		protected $showIcons		= true;

		/**
		 * specifies whether to show the root node
		 * @var bool
		 */
		protected $showRoot			= true;

		/**
		 * contains the root node
		 * @var TreeNode
		 */
		protected $rootNode			= null;

		/**
		 * stores session date
		 * @var array
		 */
		private $_session			= array();

		/**
		 * stores request date
		 * @var array
		 */
		private $_request			= array();


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return void
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'showIcons' )
			{
				return $this->showIcons;
			}
			elseif( $field === 'showRoot' )
			{
				return $this->showRoot;
			}
			elseif( $field === 'rootNode' )
			{
				return $this->rootNode;
			}
			else
			{
				$treeNode = $this->findTreeItem( $field );
				if( !is_null( $treeNode ))
				{
					return $treeNode;
				}
				return parent::__get($field);
			}
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return void
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'showIcons' )
			{
				$this->showIcons = (bool)$value;
			}
			elseif( $field === 'showRoot' )
			{
				$this->showRoot = (bool)$value;
			}
			elseif( $field === 'rootNode' )
			{
				if( $value instanceof TreeNode )
				{
					$this->rootNode =& $value;
				}
				else
				{
					throw new \System\Base\BadMemberCallException("invalid property value expected object of type TreeNode in ".get_class($this));
				}
			}
			else
			{
				parent::__set($field,$value);
			}
		}


		/**
		 * traverses all tree nodes and returns true if a node is found
		 *
		 * @param  string	$id					id of node
		 * @return TreeNode						TreeNode
		 */
		public function findTreeItem( $id )
		{
			if( $this->rootNode )
			{
				return $this->rootNode->findChildNode( $id );
			}
			return null;
		}


		/**
		 * returns a DomObject representing control
		 *
		 * @return DomObject
		 */
		public function getDomObject()
		{
			if( $this->rootNode )
			{
				$rootNode = $this->createDomObject( 'ul' );
//				$rootNode->setAttribute( 'class', ' treeview' );

				if( $this->showRoot )
				{
					$node = $this->getTreeNodeDomObject( $this->rootNode );
					$rootNode->addChild( $node );
				}
				else
				{
					foreach( $this->rootNode->getChildren() as $childNode )
					{
						$node = $this->getTreeNodeDomObject( $childNode );
						$rootNode->addChild( $node );
					}
				}

				return $rootNode;
			}
			else
			{
				throw new \System\Base\InvalidOperationException( 'rootNode is null' );
			}
		}


		/**
		 * returns DomObject for the TreeNode and all child TreeNodes
		 * 
		 * @param  TreeNode		$treeNode		TreeNode object
		 * @return DomObject
		 */
		protected function getTreeNodeDomObject( TreeNode &$treeNode )
		{
			/*
			 * check if node is expandable
			 * if so set expandable flag
			 */
			$expandable = $treeNode->childNodes->count?TRUE:FALSE;
			$final = false;

			/*
			 * check if node is last node in list
			 * if so set final flag
			 */
			$siblings = $treeNode->getSiblings();
			if( $siblings->count > 0 )
			{
				$lastnode = $siblings[$siblings->count-1];
				$final = ($lastnode->id === $treeNode->id)?TRUE:FALSE;
			}
			else
			{
				$final = true;
			}

			/*
			 * create root node for current level
			 */
			$rootNode = new \System\XML\DomObject( 'li' );
			$rootNode->setAttribute( 'id', $this->getHTMLControlId() . '__node_' . $treeNode->id );

			/*
			 * create text
			 * this element contains the tree item content
			 * 
			 */
			$branchNode = new \System\XML\DomObject( 'a' );
			$branchNode->innerHtml = '&nbsp;';

			/**
			 * determine node class
			 * if node is expandable, node may be collapsed, expanded, fcollapsed, or fexpanded, 
			 * else node may be node, fnode
			 */
			if( $expandable )
			{
				$branchNode->setAttribute( 'class', ($final?'f':'') . ($treeNode->expanded?'expanded':'collapsed') );
				$branchNode->setAttribute( 'title', ($treeNode->expanded?'expanded':'collapsed') );

				$branchNode->setAttribute( 'onclick', 'Rum.treeviewToggleNode(\''.addslashes($this->getHTMLControlId()).'\',\'' . addslashes($treeNode->id) . '\',\'' . $this->ajaxCallback . '\',\'' . $this->getRequestData() . '\' );this.href=\'#\';' );
				$branchNode->setAttribute( 'href', $this->getQueryString(urlencode($this->getHTMLControlId().'__'.$treeNode->id).($treeNode->expanded?'_collapse':'_expand').'=1'));

				if( !$final )
				{
					$rootNode->setAttribute( 'class', 'expandable' );
				}
			}
			else
			{
				$branchNode->setAttribute( 'class', ($final?'f':'') . 'node' );
			}

			$rootNode->addChild( $branchNode );

			/*
			 * create image on text node
			 */
			if( $this->showIcons )
			{
				if( $treeNode->imgSrc )
				{
					$img = new \System\XML\DomObject('img');
					$img->setAttribute( 'class', 'icon' );
					$img->setAttribute( 'src', $treeNode->imgSrc );
					$img->setAttribute( 'alt', $treeNode->id );

					$img->setAttribute( 'onclick', 'Rum.treeviewToggleNode(\''.addslashes($this->getHTMLControlId()).'\',\'' . addslashes($this->getHTMLControlId().'__'.$treeNode->id) . '\',\'' . \Rum::config()->uri( '', array( $this->getHTMLControlId() . '_submitted' => '1' )) . '\');' );

					$rootNode->addChild( $img );
				}
				elseif( $expandable )
				{
					$img = new \System\XML\DomObject('img');
					$img->setAttribute( 'class', 'folder_' . ($treeNode->expanded?'expanded':'collapsed' ));
					$img->setAttribute( 'src', \System\Web\WebApplicationBase::getInstance()->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/css')) . '&asset=/treeview/spacer.gif' );
					$img->setAttribute( 'alt', $treeNode->id );

					$img->setAttribute( 'onclick', 'Rum.treeviewToggleNode(\''.addslashes($this->getHTMLControlId()).'\',\'' . addslashes($this->getHTMLControlId().'__'.$treeNode->id) . '\',\'' . \Rum::config()->uri( '', array( $this->getHTMLControlId() . '_submitted' => '1' )) . '\');' );

					$rootNode->addChild( $img );
				}
			}

			$htmlNode = new \System\XML\DomObject('span');
			$htmlNode->innerHtml = $treeNode->textOrHtml;
			/*
			if( $treeNode->selected ) {
				$htmlNode->setAttribute( 'class', 'selected' );
			}
			*/
			$rootNode->addChild( $htmlNode );

			/*
			 * create child nodes
			 * if node has children, put them here
			 */
			if( $treeNode->childNodes->count > 0 )
			{
				$childNodes = new \System\XML\DomObject( 'ul' );

				if( !$treeNode->expanded )
				{
					$childNodes->setAttribute( 'style', 'display:none;' );
				}

				foreach( $treeNode->childNodes as $node )
				{
					$dom = $this->getTreeNodeDomObject( $node );
					$childNodes->addChild( $dom );
				}

				$rootNode->addChild( $childNodes );
			}

			return $rootNode;
		}


		/**
		 * read view state from session
		 *
		 * @param  object	$viewState	session array
		 * @return void
		 */
		protected function onLoadViewState( array &$viewState )
		{
			if( $this->enableViewState )
			{
				$this->_session =& $viewState;
			}
		}


		/**
		 * called when control is loaded
		 *
		 * @return bool			true if successfull
		 */
		protected function onLoad()
		{
			$page = $this->getParentByType( '\System\Web\WebControls\Page' );

			$page->addScript( \System\Web\WebApplicationBase::getInstance()->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/javascript')) . '&asset=/treeview/treeview.js' );
			$page->addLink( \System\Web\WebApplicationBase::getInstance()->getPageURI(__MODULE_REQUEST_PARAMETER__, array('id'=>'core', 'type'=>'text/css')) . '&asset=/treeview/treeview.css' );
		}


		/**
		 * process the HTTP request array
		 *
		 * @return void
		 */
		protected function onRequest( array &$request )
		{
			$this->_request = $request;

			if($this->ajaxCallback)
			{
				// end buffer
				$this->getParentByType('\System\Web\WebControls\Page')->loadAjaxJScriptBuffer("");
			}
		}


		/**
		 * write view state to session
		 *
		 * @param  object	$viewState	session array
		 * @return void
		 */
		protected function onSaveViewState( array &$viewState )
		{
			// store selected/expanded attributes in persistant layer
			if( $this->enableViewState )
			{
				if( $this->rootNode )
				{
					// restore state of each node
					if( isset( $this->_session['r'] ))
					{
						$this->rootNode->loadViewState( $this->_session['r'] );
					}

					if( $this->_request )
					{
						// process request
						$this->rootNode->requestProcessor( $this->getHTMLControlId(), $this->_request );
					}

					// save state of each node
					$rootNode = array();
					$this->rootNode->saveViewState( $rootNode );
					$viewState['r'] = $rootNode;
				}
			}
		}
	}
?>