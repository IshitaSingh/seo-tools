#php
	/**
	 * @package <Namespace>
	 */
	namespace <Namespace>;

	final class <ClassName> extends <BaseClassName>
	{
		public $version = <Version>;

		public function up()
		{
			return \Rum::db()->prepare("");
		}

		public function down()
		{
			return \Rum::db()->prepare("");
		}
	}
#end