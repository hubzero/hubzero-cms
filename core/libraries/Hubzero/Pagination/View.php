<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Pagination;

use Hubzero\View\View as AbstractView;

/**
 * Base class for a paginator View
 */
class View extends AbstractView
{
	/**
	 * The name of the view
	 *
	 * @var  array
	 */
	protected $_name = 'pagination';

	/**
	 * Layout name
	 *
	 * @var  string
	 */
	protected $_layout = 'paginator';

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Set a base path for use by the view
		if (!array_key_exists('base_path', $config))
		{
			$config['base_path'] = __DIR__;
		}

		$this->_basePath = $config['base_path'];

		// Set the default template search path
		if (!array_key_exists('template_path', $config))
		{
			$config['template_path'] = $this->_basePath . DIRECTORY_SEPARATOR . 'Views';
		}

		$this->setPath('template', $config['template_path']);
	}

	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @param   string  $type  The type of path to set, typically 'template'.
	 * @param   mixed   $path  The new set of search paths.  If null or false, resets to the current directory only.
	 * @return  void
	 */
	protected function setPath($type, $path)
	{
		$type = strtolower($type);

		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Actually add the user-specified directories
		$this->addPath($type, $path);

		// Always add the fallback directories as last resort
		if ($type == 'template' && $this->_overridePath)
		{
			// Set the alternative template search dir
			$path = $this->_overridePath . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $this->getName();

			$this->addPath($type, $path);
		}
	}
}
