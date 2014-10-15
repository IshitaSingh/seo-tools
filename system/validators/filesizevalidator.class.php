<?php
	/**
	 * @license			see /docs/license.txt
	 * @package			PHPRum
	 * @author			Darnell Shinbine
	 * @copyright		Copyright (c) 2013
	 */
	namespace System\Validators;


	/**
	 * Provides basic validation for web controls
	 *
	 * @property string $controlId Control Id
	 *
	 * @package			PHPRum
	 * @subpackage		Validators
	 * @author			Darnell Shinbine
	 */
	class FileSizeValidator extends ValidatorBase
	{
		/**
		 * minimum fileszie
		 * @var double
		 */
		private $minSize;

		/**
		 * maximum fileszie
		 * @var double
		 */
		private $maxSize;


		/**
		 * FileSizeValidator
		 *
		 * @param  double	$maxSize maximum size in Bytes
		 * @param  double	$maxSize minimum size in Bytes, defaults to 0
		 * @param  string	$errorMessage error message
		 * @return void
		 */
		public function __construct( $maxSize, $minSize = 0, $errorMessage = '')
		{
			parent::__construct($errorMessage);

			$this->maxSize = (double) $maxSize;
			$this->minSize = (double) $minSize;
		}


		/**
		 * on load
		 *
		 * @return void
		 */
		protected function onLoad()
		{
			$this->errorMessage = $this->errorMessage?$this->errorMessage:$this->label.' '.str_replace('%n', "{$this->maxSize}KB", \System\Base\ApplicationBase::getInstance()->translator->get('must_be_less_than'));
		}


		/**
		 * validates the passed value
		 *
		 * @param  mixed $value value to validate
		 * @return bool
		 */
		public function validate($value)
		{
			if( $value['size'] > $this->minSize )
			{
				if(( ( (int) $this->maxSize ) < (int) $value['size'] ) && (int) $this->maxSize > 0 )
				{
					return false;
				}

				return true;
			}

			return false;
		}
	}
?>