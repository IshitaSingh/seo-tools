<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 *
	 *
	 */
	namespace System\Web\WebControls;
	use \System\Collections\CollectionBase;


	/**
	 * Represents a Collection of GridViewColumn objects
	 *
	 * @package			PHPRum
	 * @subpackage		Web
	 *
	 */
	final class GridViewColumnCollection extends CollectionBase
	{
		/**
		 * GridView
		 * @var GridView
		 */
		protected $gridView;


		/**
		 * Constructor
		 * 
		 * @param  mixed	$collection		can be CollectionBase or array used to initialize Collection
		 * @return void
		 */
		public function __construct(GridView &$gridView, $collection = null )
		{
			parent::__construct($collection);
			$this->gridView = &$gridView;
		}


		/**
		 * sets object property
		 *
		 * @param  string	$field		name of field
		 * @param  mixed	$value		value of field
		 * @return void					string of variables
		 * @ignore
		 */
		public function __set( $field, $value )
		{
			if( $field === 'ajaxPostBack' )
			{
				trigger_error("GridViewColumnCollection::ajaxPostBack is deprecated", E_USER_DEPRECATED);
				foreach($this->items as $item)
				{
					$item->ajaxPostBack = (bool)$value;
					if($item->filter)
					{
						$item->filter->ajaxPostBack = (bool)$value;
					}
				}
			}
			else
			{
				return parent::__get($field);
			}
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				if( $item instanceof GridViewColumn )
				{
					$item->setGridView($this->gridView);
					$this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected object of type GridViewColumn in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add GridViewColumn to Collection before
		 *
		 * @param  GridViewColumn $item			GridViewColumn
		 * @param  string         $controlId	control id
		 *
		 * @return bool
		 */
		public function addBefore( $item, $controlId )
		{
			if( $item instanceof GridViewColumn )
			{
				$new_items = array();
				for( $i=0; $i<count($this->items); $i++ )
				{
					if( $this->items[$i]->controlId==$controlId )
					{
						$item->setGridView($this->gridView);
						$new_items[] = $item;
					}
					$new_items[] = $this->items[$i];
				}
				$this->items = $new_items;
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type GridViewColumn");
			}
		}


		/**
		 * add GridViewColumn to Collection
		 *
		 * @param  GridViewColumn $item
		 *
		 * @return bool
		 */
		public function add( $item )
		{
			if( $item instanceof GridViewColumn )
			{
				$item->setGridView($this->gridView);
				array_push( $this->items, $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type GridViewColumn");
			}
		}


		/**
		 * return GridViewColumn at a specified index
		 *
		 * @param  int		$index			index of GridViewColumn
		 *
		 * @return GridViewColumn				 GridViewColumn
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}


		/**
		 * handle load events
		 *
		 * @return void
		 */
		final public function load()
		{
			foreach($this->items as $column)
			{
				$column->load();
			}
		}


		/**
		 * handle request events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function requestProcessor( &$request )
		{
			foreach($this->items as $column)
			{
				$column->requestProcessor( $request );
			}
		}


		/**
		 * reset filters
		 *
		 * @return void
		 */
		final public function resetFilters()
		{
			foreach($this->items as $column)
			{
				$column->resetFilter();
			}
		}


		/**
		 * filter DataSet
		 *
		 * @param  DataSet	&$ds		DataSet
		 * @return void
		 */
		final public function filterDataSet(\System\DB\DataSet &$ds)
		{
			foreach($this->items as $column)
			{
				$column->filterDataSet($ds);
			}
		}


		/**
		 * handle post events
		 *
		 * @param  array	&$request	request data
		 * @return void
		 */
		final public function handlePostEvents( &$request )
		{
			foreach($this->items as $column)
			{
				$column->handlePostEvents( $request );
			}
		}


		/**
		 * handle render events
		 *
		 * @return void
		 */
		final public function render()
		{
			foreach($this->items as $column)
			{
				$column->render();
			}
		}


		/**
		 * returns index if value is found in collection
		 *
		 * @param  string		$controlId			control id
		 * @return GridViewColumn
		 */
		public function findColumn( $controlId )
		{
			for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
			{
				if( $this->items[$i]->controlId == $controlId )
				{
					return $this->items[$i];
				}
			}
			return null;
		}
	}
?>