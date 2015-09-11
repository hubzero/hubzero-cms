<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Pagination;

use Hubzero\View\View as AbstractView;

/**
 * Base class for a paginator View
 */
class View extends AbstractView
{
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
			$config['template_path'] = $this->_basePath . DS . 'Views';
		}

		$this->_setPath('template', $config['template_path']);
	}

	/**
	 * Method to get the view name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'pagination';
	}

	/**
	* Sets an entire array of search paths for templates or resources.
	*
	* @param   string  $type  The type of path to set, typically 'template'.
	* @param   mixed   $path  The new set of search paths.  If null or false, resets to the current directory only.
	* @return  void
	*/
	protected function _setPath($type, $path)
	{
		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Actually add the user-specified directories
		$this->_addPath($type, $path);

		// Always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
				// Set the alternative template search dir
				if (class_exists('\\App'))
				{
					$fallback = JPATH_BASE . DS . 'templates' . DS . \App::get('template')->template . DS . 'html' . DS . $this->getName();
					$this->_addPath('template', $fallback);
				}
			break;
		}
	}
}
