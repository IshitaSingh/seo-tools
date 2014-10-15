<?php
	class SEOWerxTestCase extends BaseUnitTestCase {

		// protected $fixtures = 'myrecord.xml';

		protected function prepare() {
			parent::prepare();

			// implement here
		}

		protected function cleanup() {
			parent::cleanup();

			// implement here
		}

		public function testPageTitleElementExists() {

			$analyzer = new \SEOWerx\SEOAnalyzer();
			$analyzer->analyze( 'http://google.ca', '<html><title</html>' );

			// test
		}
	}
?>