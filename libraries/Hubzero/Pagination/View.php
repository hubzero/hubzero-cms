<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Set a base path for use by the view
		if (array_key_exists('base_path', $config))
		{
			$this->_basePath = $config['base_path'];
		}
		else
		{
			$this->_basePath = __DIR__;
		}

		// set the default template search path
		if (array_key_exists('template_path', $config))
		{
			// user-defined dirs
			$this->_setPath('template', $config['template_path']);
		}
		else
		{
			$this->_setPath('template', $this->_basePath . DS . 'Views');
		}
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
		$app = \JFactory::getApplication();

		// Clear out the prior search dirs
		$this->_path[$type] = array();

		// Actually add the user-specified directories
		$this->_addPath($type, $path);

		// Always add the fallback directories as last resort
		switch (strtolower($type))
		{
			case 'template':
				// Set the alternative template search dir
				if (isset($app))
				{
					$fallback = JPATH_BASE . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . $this->getName();
					$this->_addPath('template', $fallback);
				}
			break;
		}
	}
}
