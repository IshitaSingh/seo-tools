<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\ActiveRecord;


	/**
	 * Represents a relationship type
	 *
	 * @package			PHPRum
	 * @subpackage		ActiveRecord
	 * @author			Darnell Shinbine
	 */
	final class RelationshipType
	{
		private $flags;

		private function __construct($flags)
		{
			$this->flags = (int)$flags;
		}

		/**
		 * Specifies the relationship is a one to many
		 * @return RelationshipType
		 */
		public static function HasMany() {return new RelationshipType(1);}

		/**
		 * Specifies the relationship is a many to one
		 * @return RelationshipType
		 */
		public static function BelongsTo() {return new RelationshipType(2);}

		/**
		 * Specifies the relationship is a many to many
		 * @return RelationshipType
		 */
		public static function HasManyAndBelongsTo() {return new RelationshipType(4);}

		/**
		 * Returns a string representing the type
		 * @return string
		 */
		final public function __toString()
		{
			switch($this->flags)
			{
				case 1: return "has_many"; break;
				case 2: return "belongs_to"; break;
				case 4: return "has_many_and_belongs_to"; break;
			}
		}
	}
?>