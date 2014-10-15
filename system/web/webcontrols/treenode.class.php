<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;


	/**
	 * Represents a tree node
	 * 
	 * @property string $id Node id
	 * @property string $textOrHtml Node text
	 * @property string $imgSrc Specifies the path of the icon image
	 * @property bool $expanded Specifies if the node is expanded by default
	 * @property TreeNodeCollection $childNodes collection of child nodes
	 * @property TreeNode $parentNode Contains parent node
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	class TreeNode
	{
		/**
		 * node id
		 * @var string
		 */
		protected $id					= '';

		/**
		 * node text
		 * @var string
		 */
		protected $textOrHtml			= '';

		/**
		 * specifies the path of the icon image
		 * @var string
		 */
		protected $imgSrc				= '';

		/**
		 * Specifies if the node is expanded by default
		 * @var bool
		 */
		protected $expanded				= false;

		/**
		 * collection of child nodes
		 * @var TreeNodeCollection
		 */
		protected $childNodes			= null;

		/**
		 * contains parent node
		 * @var TreeNode
		 */
		protected $parentNode			= null;


		/**
		 * @param  string   $id			 TreeNode Id
		 * @param  string   $textOrHtml	 TreeNode text
		 * @param  bool	    $expanded	   Specifies whether to expand the TreeNode
		 * @param  string   $imgSrc		 Specifies the icon image source
		 * @return void
		 */
		public function __construct( $id, $textOrHtml = '', $expanded = false, $imgSrc = null )
		{
			$this->id		 = (string) $id;
			$this->textOrHtml = (string) $textOrHtml;
			$this->expanded   = (bool)   $expanded;
			$this->imgSrc	 = (string) $imgSrc;
			$this->childNodes = new TreeNodeCollection();
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return void
		 * @ignore
		 */
		public function __get( $field ) {
			if( $field === 'id' ) {
				return $this->id;
			}
			elseif( $field === 'textOrHtml' ) {
				return $this->textOrHtml;
			}
			elseif( $field === 'imgSrc' ) {
				return $this->imgSrc;
			}
			elseif( $field === 'expanded' ) {
				return $this->expanded;
			}
			elseif( $field === 'childNodes' ) {
				return $this->childNodes;
			}
			elseif( $field === 'parentNode' ) {
				return $this->parentNode;
			}
			else {
				$node = $this->findChildNode($field);
				if( !is_null( $node ))
				{
					return $node;
				}
				else
				{
					throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
				}
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
		public function __set( $field, $value ) {
			if( $field === 'textOrHtml' ) {
				$this->textOrHtml = (string) $value;
			}
			elseif( $field === 'imgSrc' ) {
				$this->imgSrc = (string) $value;
			}
			elseif( $field === 'expanded' ) {
				$this->expanded = (bool) $value;
			}
			else {
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
			}
		}


		/**
		 * read view state from session
		 *
		 * @param  object	$viewState	session array
		 * @return void
		 */
		public function loadViewState( array &$viewState )
		{
			if( isset( $viewState['e'] ))
			{
				$this->expanded = (bool) $viewState['e'];
			}

			if( isset( $viewState['n'] ))
			{
				for( $i = 0, $count = $this->childNodes->count; $i < $count; $i++ )
				{
					$childNode =& $this->childNodes[$i];

					if( isset( $viewState['n'][$childNode->id] ))
					{
						$childNode->loadViewState( $viewState['n'][$childNode->id] );
					}
				}
			}
		}


		/**
		 * process the HTTP request array
		 *
		 * @param string $treeViewControlId TreeView Control Id
		 * @param array $request Request Parameter Collection
		 *
		 * @return void
		 */
		public function requestProcessor( $treeViewControlId, array &$request )
		{
			if( isset( $request[$treeViewControlId.'__'.$this->id.'_expand'] ))
			{
				$this->expanded = true;
			}

			if( isset( $request[$treeViewControlId.'__'.$this->id.'_collapse'] ))
			{
				$this->expanded = false;
			}

			for( $i = 0, $count = $this->childNodes->count; $i < $count; $i++ )
			{
				$childNode =& $this->childNodes[$i];
				$childNode->requestProcessor( $treeViewControlId, $request );
			}
		}


		/**
		 * write view state to session
		 *
		 * @param  object	$viewState	session array
		 * @return void
		 */
		public function saveViewState( array &$viewState )
		{
			$viewState['e'] = $this->expanded;
			$nodes = array();

			for( $i = 0, $count = $this->childNodes->count; $i < $count; $i++ )
			{
				$childNode =& $this->childNodes[$i];
				$nodes[$childNode->id] = array();
				$childNode->saveViewState( $nodes[$childNode->id] );
			}

			$viewState['n'] = $nodes;
		}


		/**
		 * returns true if node is found in Collection
		 *
		 * @param  string	$id					id of TreeNode
		 * @return bool
		 */
		public function hasChildNode( $id )
		{
			$index = $this->childNodes->indexOf( $id );
			if( $index > -1 )
			{
				return true;
			}
			else
			{
				for( $i = 0, $count = $this->childNodes->count; $i < $count; $i++ )
				{
					$childNode = $this->childNodes->itemAt( $i );

					if( $childNode->hasChildNode( $id ))
					{
						return true;
					}
				}
			}

			return false;
		}


		/**
		 * returns TreeNode if node is found in Collection
		 *
		 * @param  string	$id					id of TreeNode
		 * @return TreeNode						reference to TreeNode object
		 */
		public function findChildNode( $id )
		{
			$index = $this->childNodes->indexOf( $id );
			if( $index > -1 )
			{
				return $this->childNodes->itemAt($index);
			}
			else
			{
				for( $i = 0, $count = $this->childNodes->count; $i < $count; $i++ )
				{
					$childNode = $this->childNodes->itemAt( $i );

					$node = $childNode->findChildNode( $id );
					if( !is_null( $node ))
					{
						return $node;
					}
				}
			}

			return null;
		}


		/**
		 * creates a child TreeNode in the current TreeNode
		 *
		 * @param  string	$textOrHTML				string value of node
		 * @param  bool		$expanded				expand node by default
		 * @param  string	$imgSrc					path to icon image
		 * @return TreeNode							handle to the newly added node
		 */
		public function createChild( $textOrHTML = '', $expanded = false, $imgSrc = '' )
		{
			$id = $this->id . '_' . (string) ( $this->childNodes->count + 1 );

			$node = new TreeNode( $id, (string) $textOrHTML, (bool) $expanded, (string) $imgSrc );
			$node->setParent( $this );

			$this->addChild( $node );
			return $node;
		}


		/**
		 * adds child TreeNode to current TreeNode
		 *
		 * @param  TreeNode		$treeNode			reference to an instance of a TreeNode object
		 * @return bool
		 */
		public function addChild( TreeNode $treeNode )
		{
			$treeNode->setParent( $this );
			return $this->childNodes->add( $treeNode );
		}


		/**
		 * returns true if child node exists
		 *
		 * @param  string	$id					id of node
		 * @return bool
		 */
		public function hasChild( $id )
		{
			for( $i = 0, $count = $this->childNodes->count; $i < $count; $i++ )
			{
				if( $this->childNodes[$i]->id === $id )
				{
					return true;
				}
			}

			return false;
		}


		/**
		 * returns child node by Id
		 *
		 * @param  string	$id					id of node
		 * @return TreeNode
		 */
		public function getChild( $id )
		{
			for( $i = 0, $count = $this->childNodes->count; $i < $count; $i++ )
			{
				if( $this->childNodes[$i]->id === $id )
				{
					return $this->childNodes[$i];
				}
			}

			throw new \System\Base\ArgumentOutOfRangeException("child id `$id` does not exist");
		}


		/**
		 * returns array of sibling nodes
		 *
		 * @return TreeNodeCollection			children
		 */
		public function getChildren()
		{
			return $this->childNodes;
		}


		/**
		 * returns array of sibling nodes
		 *
		 * @return XMLEntityCollection			siblings
		 */
		public function getSiblings()
		{
			if( $this->parentNode )
			{
				return $this->parentNode->childNodes;
			}
			return new TreeNodeCollection();
		}


		/**
		 * set parent node
		 *
		 * @param  TreeNode		$treeNode			reference to an instance of a TreeNode object
		 *
		 * @return void
		 */
		public function setParent( TreeNode &$parentNode )
		{
			$this->parentNode =& $parentNode;
		}


		/**
		 * get parent node
		 *
		 * @return TreeNode
		 */
		public function getParent()
		{
			return $this->parentNode;
		}


		/**
		 * returns node ancestry
		 *
		 * @return XMLEntityCollection			ancestry
		 */
		public function getAncestry()
		{
			$parents = new \System\XML\XMLEntityCollection();
			$node	= $this->parentNode;

			while( $node = $node->parentNode )
			{
				$parents->add( $node );
			}

			return $parents;
		}


		/**
		 * expand node
		 *
		 * @return void
		 */
		public function expand()
		{
			$this->expanded = true;
		}


		/**
		 * expands all children
		 *
		 * @return void
		 */
		public function expandAll()
		{
			$this->expand();
			foreach( $this->childNodes as $childNode )
			{
				$childNode->expandAll();
			}
		}


		/**
		 * collapse node
		 *
		 * @return void
		 */
		public function collapse()
		{
			$this->expanded = false;
		}


		/**
		 * collapses all children
		 *
		 * @return void
		 */
		public function collapseAll()
		{
			$this->collapse();
			foreach( $this->childNodes as $childNode )
			{
				$childNode->colapseAll();
			}
		}
	}
?>