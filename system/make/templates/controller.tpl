#php
	/**
	 * @package <Namespace>
	 */
	namespace <Namespace>;

	/**
	 * This class handles all requests for the /<PageURI> uri.
	 *
	 * The ControllerBase exposes 1 protected property
	 * @property int $outputCache Specifies how long to cache page output in seconds, 0 disables caching
	 *
	 * @package			<Namespace>
	 */
	final class <ClassName> extends <BaseClassName>
	{
		/**
		 * This method should return a view component for rendering
		 *
		 * @param   HTTPRequest		&$request	HTTPRequest object
		 * @return  View			view control
		 */
		public function getView( \System\Web\HTTPRequest &$request )
		{
			return new \System\Web\WebControls\View('view');
		}
	}
#end