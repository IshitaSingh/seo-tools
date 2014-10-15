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
	 * Represents a collection of Application Messages
	 * 
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 */
	final class AppMessageCollection extends CollectionBase
	{
		/**
		 * convert Iterator object into string
		 *
		 * @return array
		 * @ignore
		 */
		public function __toString()
		{
			return $this->toString();
		}


		/**
		 * implement ArrayAccess methods
		 * @ignore
		 */
		public function offsetSet($index, $item)
		{
			if( array_key_exists( $index, $this->items ))
			{
				if( $item instanceof AppMessage )
				{
					$this->items[$index] = $item;
				}
				else
				{
					throw new TypeMismatchException("invalid index value expected object of type Message in ".get_class($this));
				}
			}
			else
			{
				throw new IndexOutOfRangeException("undefined index $index in ".get_class($this));
			}
		}


		/**
		 * add AppMessage to Collection
		 *
		 * @param  AppMessage $item
		 * @return bool
		 */
		public function add( $item )
		{
			if( $item instanceof AppMessage )
			{
				array_push( $this->items, $item );
			}
			else
			{
				throw new InvalidArgumentException("Argument 1 passed to ".get_class($this)."::add() must be an object of type AppMessage");
			}
		}


		/**
		 * return AppMessage at a specified index
		 *
		 * @param  int		$index			index of ActiveRecord
		 *
		 * @return AppMessage				AppMessage
		 */
		public function itemAt( $index )
		{
			return parent::itemAt($index);
		}


		/**
		 * return Collection of messages
		 *
		 * @param  constant		$type		Message type as constant of AppMessageType::Success(), AppMessageType::Fail() or AppMessageType::Notice()
		 * @return StringCollection
		 */
		public function getByType( AppMessageType $type = null )
		{
			$messages = new \System\Collections\StringCollection();
			foreach( $this->items as $message )
			{
				if( $type === null || $message->type == $type )
				{
					$messages->add( $message->message );
				}
			}

			return $messages;
		}


		/**
		 * convert Iterator object into string
		 *
		 * @return array
		 */
		public function toString()
		{
			$messages = '';
			foreach($this->items as $item)
			{
				$messages .= "{$item}\r\n";
			}
			return $messages;
		}
	}
?>