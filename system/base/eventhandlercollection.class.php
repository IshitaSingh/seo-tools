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
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class EventHandlerCollection extends CollectionBase
	{
		/**
		 * implement ArrayAccess methods
		 * @ignore 
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				if( $item instanceof EventHandlerBase )
				{
					$this->items[$index] = $item;
				}
				else
				{
					throw new \System\Base\TypeMismatchException("invalid index value expected object of type EventHandlerBase in ".get_class($this));
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
		 * @param  EventHandlerBase $item
		 *
		 * @return void
		 * @ignore
		 */
		public function add( $item )
		{
			if( $item instanceof EventHandlerBase )
			{
				array_push( $this->items, $item );
			}
			else
			{
				throw new \System\Base\InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type EventHandlerBase");
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
			return $this->add( $eventHandler );
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
			foreach($this->items as $eventHandler)
			{
				if($eventHandler->event === $event->name)
				{
					$eventHandler->raise($sender, $args);
				}
			}
		}
	}
?>