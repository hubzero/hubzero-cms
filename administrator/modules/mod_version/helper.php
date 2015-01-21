<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Version;

use Hubzero\Module\Module;
use JVersion;

/**
 * Module class for displaying CMS version
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		$format  = $this->params->get('format', 'short');
		$product = $this->params->get('product', 0);
		$method  = 'get' . ucfirst($format) . 'Version';

		// Get the joomla version
		$instance = new JVersion();
		$version  = call_user_func(array($instance, $method));

		if ($format == 'short' && !empty($product))
		{
			// Add the product name to short format only (in long format it's included)
			$version = $instance->PRODUCT . ' ' . $version;
		}

		$version .= ' ' . \Hubzero\Version\Version::VERSION;

		// Get the view
		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
