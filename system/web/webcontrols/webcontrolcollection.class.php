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
	 * Represents a Collection of WebControl objects
	 * 
	 * @package			PHPRum
	 * @subpackage		Web
	 * @author			Darnell Shinbine
	 */
	final class WebControlCollection extends CollectionBase
	{
		/**
		 * parent
		 * @var WebControl
		 */
		private $_parent;


		/**
		 * Constructor
		 *
		 * @param  WebControlBase	$parent		instance of a WebControlBase object
		 * 
		 * @return void
		 */
		public function __construct( WebControlBase &$parent ) {
			$this->_parent =& $parent;
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				if( $item instanceof WebControlBase )
				{
					if( $item->parent->controlId === $this->_parent->controlId )
					{
						$this->items[$index] = $item;
					}
					else
					{
						throw new \System\Base\InvalidOperationException("object parent is invalid");
					}
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected object of type WebControlBase in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add WebControl to Collection
		 *
		 * @param  WebControlBase $item
		 *
		 * @return void
		 */
		public function add( $item )
		{
			if( $item instanceof WebControlBase )
			{
				$item->setParent( $this->_parent );

				array_push( $this->items, $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type WebControlBase");
			}
		}


		/**
		 * replace a WebControl in a Collection
		 *
		 * @param string $controlId
		 * @param WebControlBase $item
		 * @return void
		 */
		public function replace( $controlId, $item )
		{
			if( $item instanceof WebControlBase )
			{
				$item->setParent( $this->_parent );
				$index = $this->indexOf( $controlId );

				if($index > -1)
				{
					//unset($this->items[$index]);
					$this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type WebControlBase");
			}
		}


		/**
		 * remove item from collection
		 *
		 * @param WebControlBase $item
		 *
		 * @return bool
		 */
		public function remove( $item )
		{
			return parent::remove( $item->controlId );
		}


		/**
		 * return WebControl at a specified index
		 *
		 * @param  int		$index			index of WebControlBase
		 * @return WebControlBase		   WebControl
		 */
		public function itemAt( $index )
		{
			return parent::itemAt( $index );
		}


		/**
		 * returns true if array item is found
		 *
		 * @param  string			$controlId		control id
		 * @return bool
		 */
		public function contains( $controlId )
		{
			foreach( $this->items as $item )
			{
				if( $item->isControl( $controlId ))
				{
					return true;
				}
			}
			return false;
		}


		/**
		 * returns index if value is found in collection
		 *
		 * @param  string		$controlId			control id
		 * @return int
		 */
		public function indexOf( $controlId )
		{
			for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
			{
				if( $this->items[$i]->isControl( $controlId ))
				{
					return $i;
				}
			}
			return -1;
		}
	}
?>