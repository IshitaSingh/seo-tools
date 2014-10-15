<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Web\WebControls;
	use \System\Collections\CollectionBase;


	/**
	 * Represents a Collection of TreeNode objects
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class TreeNodeCollection extends CollectionBase
	{
		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				if( $item instanceof TreeNode )
				{
					$this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected object of type TreeNode in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add TreeNode to Collection
		 *
		 * @param  TreeNode $item
		 * 
		 * @return bool
		 */
		public function add( $item )
		{
			if( $item instanceof TreeNode )
			{
				array_push( $this->items, $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type TreeNode");
			}
		}


		/**
		 * return TreeNode at a specified index
		 *
		 * @param  int		$index			index of TreeNode
		 *
		 * @return TreeNode				 TreeNode
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}


		/**
		 * returns true if array item is found
		 *
		 * @param  string			$id		TreeNode id
		 * @return bool
		 */
		public function contains( $id )
		{
			foreach( $this->items as $item )
			{
				if( $item->id === $id )
				{
					return true;
				}
			}
			return false;
		}


		/**
		 * returns index if value is found in collection
		 *
		 * @param  string			$id		TreeNode id
		 * @return int
		 */
		public function indexOf( $id )
		{
			for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
			{
				if( $this->items[$i]->id === $id )
				{
					return $i;
				}
			}
			return -1;
		}
	}
?>