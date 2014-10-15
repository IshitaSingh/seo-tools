<?php
	/**
	 * @package SEOWerx
	 */
	namespace SEOWerx;

	include_once __ROOT__ . '/libs/string.inc';
	include_once __ROOT__ . '/libs/html.inc';

	define( '__DENOMINATOR__', 10 );

	class SEOAnalyzer {

		protected $warnings			= array();
		protected $recommendations	= array();
		protected $keyworddensity	= 0;
		protected $score			= __DENOMINATOR__;
		private   $_parsed_content	= '';


		/**
		 * __construct
		 *
		 * @return void
		 * @access public
		 */
		public function __construct() {
		}


		/**
		 * __get
		 *
		 * @param  string	$field		name of field
		 * @return string				string of variables
		 * @access protected
		 */
		public function __get( $field ) {
			if( $field == 'warnings' ) {
				return $this->warnings;
			}
			elseif( $field == 'recommendations' ) {
				return $this->recommendations;
			}
			elseif( $field == 'keyworddensity' ) {
				return $this->keyworddensity;
			}
			elseif( $field == 'score' ) {
				return $this->getScore();
			}
		}

		// return score from 0 to 10
		public function getScore() {
			$score = ( $this->score / __DENOMINATOR__ ) * 10;
			if( (int) $score < 0 ) return 0;
			if( (int) $score > __DENOMINATOR__ ) return __DENOMINATOR__;
			return (int) $score;
		}


		/**
		 * analyze
		 *
		 * analyze pagecrawler
		 *
		 * @param  string		$url			url
		 * @param  string		$response		response
		 * @param  array		$keywords		keywords
		 * 
		 * @return bool
		 * @access public
		 */
		public function analyze( $url, $response, array $keywords ) {

			// reset errors
			$this->warnings = array();

			// reset scoring
			$this->score = __DENOMINATOR__;

			// parse content
			$this->_parsed_content = parse_content( $response, $url, false );

			// run all tests
			foreach( get_class_methods( $this ) as $method ) {
				if( strpos( $method, 'test' ) === 0 ) {
					$this->{$method}( $url, $response, $keywords );
				}
			}
		}

		/* meta analysis */

		protected function testPageTitleElementExists( $url, $response, array $keywords ) {
			// page title element must exist
			$pagetitle = extract_segment_from_chunk( $response, '<title>', '</title>' );

			if( strlen( $pagetitle ) < 0 ) {
				// page title must exist
				$this->warnings[] = 'Page title element not found';
				$this->recommendations[] = 'Add a descriptive page title';
				$this->score--;
			}
		}

		protected function testPageTitleElementLength( $url, $response, array $keywords ) {
			// page title element must exist
			$pagetitle = extract_segment_from_chunk( $response, '<title>', '</title>' );

			if( str_word_count( $pagetitle ) < 3 ) {
				// page title must exist
				$this->warnings[] = 'Page title element less than 3 words';
				$this->recommendations[] = 'Use a descriptive page title specific to the page';
				$this->score--;
			}
			elseif( strlen( $pagetitle ) > 64 ) {
				// page title must exist
				$this->warnings[] = 'Page title element greater than 64 characters';
				$this->recommendations[] = 'Shorten the page title to less than 64 characters';
				$this->score--;
			}
		}

		protected function testPageTitleElementIsUnique( $url, $response, array $keywords ) {
			// page title element should be unique
			$pagetitle = extract_segment_from_chunk( $response, '<title>', '</title>' );

			if( !defined( '__PAGE_TITLE_ELEMENT' . $pagetitle )) {
				define( '__PAGE_TITLE_ELEMENT' . $pagetitle, TRUE );
			}
			else {
				$this->warnings[] = 'Page title element should be unique';
				$this->recommendations[] = 'Use a unique page title that is specific to the page';
				$this->score--;
			}
		}

		protected function testMetaDescriptionTagExists( $url, $response, array $keywords ) {
			// page description must exist

			if( !get_meta_content( $response, 'description' )) {
				$this->warnings[] = 'No meta description tag found';
				$this->recommendations[] = 'Add a meta description tag that describes the content on the page';
				$this->score--;
			}
		}

		protected function testMetaKeywordsExistInContent( $url, $response, array $keywords ) {
			// any keywords must exist in content

			$metaKeywords = get_meta_content( $response, 'keywords' );
			if( $metaKeywords ) {
				if( $keywords = explode( ',', $metaKeywords )) {
					foreach( $keywords as $keyword ) {
						if( stripos( $this->_parsed_content, trim( $keyword )) === false ) {
							$this->warnings[] = 'There are meta keywords that do not exist in the content';
							$this->recommendations[] = 'Remove any meta keywords that are not present on the page';
							$this->score--;
							return;
						}
					}
				}
			}
		}

		/* structure/technical analysis */

		protected function testPageContainsInternalLink( $url, $response, array $keywords ) {
			// page should contain at least 1 internal link

			if( stripos( parse_content( $response, $url ), get_base_url( $url )) === false ) {
				$this->warnings[] = 'Page does not contain internal links';
				$this->recommendations[] = 'Add internal links to help distribute page rank more effeciently';
				$this->score--;
			}
		}

		protected function testPageContentLength( $url, $response, array $keywords ) {
			// content should be at least 160 chars
			$response     = $this->_parsed_content;
			$wordcount    = str_word_count( $response );

			if( $wordcount < 160 ) {
				$this->warnings[] = 'Page content contains less than 160 words';
				$this->recommendations[] = 'Add more relevant content';
				$this->score--;
			}
		}

		/* keyword analysis */

		/*
		protected function testHeadingTagContainsKeyword( $url, $response, array $keywords ) {
			// page should contain at least 1 h1

			$keywordfound = false;
			foreach( $keywords as $keyword ) {
				if( stripos( extract_segment_from_chunk( $response, '<h1>', '</h1>' ), $keyword ) !== false ) {
					$keywordfound = true;
					$this->score++;
				}
			}

			if( !$keywordfound ) {
				$this->recommendations[] = 'Add a heading tag containing a keyphrase';
			}
		}
		*/

		/*
		protected function testPageNameContainsKeyword( $url, $response, array $keywords ) {
			// page name should contain a phrase
			$pagename = str_ireplace( '-', ' ', str_ireplace( '_', ' ', basename( $url )));
			$keywordfound = false;

			foreach( $keywords as $keyword ) {
				if( stripos( $pagename, $keyword ) !== false ) {
					$keywordfound = true;
					$this->score++;
				}
			}

			if( !$keywordfound ) {
				$this->recommendations[] = 'Use a descriptive page name based on your keywords';
			}
		}
		*/

		protected function testPageTitleElementContainsKeyword( $url, $response, array $keywords ) {
			// check that page title contains a keyphrase
			$keywordfound = false;
			foreach( $keywords as $keyword ) {
				if( stripos( extract_segment_from_chunk( $response, '<title>', '</title>' ), $keyword ) !== false ) {
					$keywordfound = true;
				}
			}

			if( !$keywordfound ) {
				$this->warnings[] = "The page title element does not contain any keyphrases";
				$this->recommendations[] = "Incorporate keyphrases into the page title";
				$this->score--;
			}
		}

		protected function testBodyContainsKeywords( $url, $response, array $keywords ) {
			// check that page body contains all keyphrases
			foreach( $keywords as $keyword ) {
				if( stripos( $this->_parsed_content, $keyword ) === false ) {
					$this->warnings[] = "The keyphrase \"{$keyword}\" was not found";
					$this->score--;
				}
			}
		}

		protected function testKeyWordDensity( $url, $response, array $keywords ) {
			// keyword density should be 10%
			$response     = $this->_parsed_content;
			$wordcount    = str_word_count( $response );
			$keywordcount = 0;

			foreach( $keywords as $keyword ) {
				preg_match_all( "/$keyword/i", $response, $matches );
				$keywordcount += count( $matches[0] );
			}

			if( $wordcount ) {
				$this->keyworddensity = $keywordcount / $wordcount;
			}

			// defined keywords
			if( !count( $keywords )) {
				$this->warnings[] = "No keyphrases have been defined";
				$this->score-=2;
			}
			elseif( count( $keywords ) < 3 ) {
				$this->warnings[] = "Less than 3 keyphrases were defined";
				$this->score--;
			}
			elseif( count( $keywords ) > 5 ) {
				$this->warnings[] = "More than 5 keyphrases were defined";
				$this->score--;
			}

			// keyword density
			if( $this->keyworddensity < 0.02 ) {
				$this->warnings[] = "Keyword density is less than 2%";
				$this->recommendations[] = "Incorporate more keyphrases into the content";
				$this->score-=2;
			}
			elseif( $this->keyworddensity < 0.05 ) {
				$this->warnings[] = "Keyword density is less than 5%";
				$this->recommendations[] = "Incorporate more keyphrases into the content";
				$this->score--;
			}
			elseif( $this->keyworddensity > 0.10 ) {
				// no warning
				$this->score++;
			}
		}
	}
?>