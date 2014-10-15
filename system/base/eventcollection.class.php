<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Base;
	use \System\Collections\CollectionBase;


	/**
	 * Represents a Collection of EventHandlerBase objects
	 *
	 * @property EventHandlerCollection $eventHandlers event handler collection
	 * @property EventCollection $events event collection
	 *
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class EventCollection extends CollectionBase
	{
		/**
		 * event handler collection
		 * @var EventHandlerCollection
		 */
		private $eventHandlers		= null;


		/**
		 * Constructor
		 *
		 * @param  mixed	$collection		can be CollectionBase or array used to initialize Collection
		 * @return void
		 */
		public function __construct( $collection = null )
		{
			parent::__construct( $collection );

			$this->eventHandlers = new EventHandlerCollection();
		}


		/**
		 * gets object property
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @ignore
		 */
		public function __get( $field )
		{
			if( $field === 'eventHandlers' )
			{
				return $this->eventHandlers;
			}
			elseif( $field === 'events' )
			{
				return $this->items;
			}
			else
			{
				throw new \System\Base\BadMemberCallException("call to undefined property $field in ".get_class($this));
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
				if( $item instanceof EventBase )
				{
					$this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected object of type EventBase in ".get_class($this));
				}
			}
			else
			{
				throw new \System\Base\IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add event handler to collection
		 *
		 * @param  EventBase $item
		 *
		 * @return void
		 * @ignore
		 */
		public function add( $item )
		{
			if( $item instanceof EventBase )
			{
				array_push( $this->items, $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type EventBase");
			}
		}


		/**
		 * returns true if item array contains item
		 *
		 * @param  EventBase	$item		item
		 * @return bool						true if item found
		 */
		public function contains( $item )
		{
			if( $item instanceof EventBase )
			{
				foreach( $this->items as $event )
				{
					if( $event->name === $item->name )
					{
						return true;
					}
				}
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::contains() must be an object of type EventBase");
			}
		}


		/**
		 * returns first index of item found in Collection
		 *
		 * @param  EventBase	$item		item
		 * @return int						index of item
		 */
		public function indexOf( $item )
		{
			if( $item instanceof EventBase )
			{
				for( $i = 0, $count = count( $this->items ); $i < $count; $i++ )
				{
					if( $this->items[$i]->name === $item->name )
					{
						return $i;
					}
				}
				return -1;
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::indexOf() must be an object of type EventBase");
			}
		}


		/**
		 * register event handler
		 *
		 * @param  EventHandlerBase $eventHandler
		 *
		 * @return void
		 */
		final public function registerEventHandler( EventHandlerBase $eventHandler )
		{
			return $this->eventHandlers->registerEventHandler( $eventHandler );
		}


		/**
		 * raise all events
		 *
		 * @param  EventBase	$event		event
		 * @param  object		$sender		Sender object
		 * @param  array		$args		optional args
		 * @return void
		 */
		final public function raise(EventBase $event, &$sender, array $args = array())
		{
			$this->eventHandlers->raise($event, $sender, $args);
		}
	}
?>