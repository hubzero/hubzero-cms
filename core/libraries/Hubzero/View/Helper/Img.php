<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\View\Helper;

use Hubzero\Document\Asset\Image;
use Hubzero\Plugin\View;
use Request;

/**
 * Helper for generating paths to image assets.
 */
class Img extends AbstractHelper
{
	/**
	 * Generate asset path
	 *
	 * @param   string  $asset      Image to get path for
	 * @param   string  $extension  Extension name, e.g.: com_example, mod_example, plg_example_test
	 * @param   string  $element    Plugin element. Only used for plugins and if first argument is folder name.
	 * @return  object
	 */
	public function __invoke($asset = '', $extension = null, $element = null)
	{
		$extension = $extension ?: $this->_extension();

		if ($element)
		{
			$extension = 'plg_' . $extension . '_' . $element;
		}

		$asset = new Image($extension, $asset);

		return $asset->link();
	}

	/**
	 * Determine the extension the view is being called from
	 *
	 * @return  string
	 */
	private function _extension()
	{
		if ($this->getView() instanceof View)
		{
			return 'plg_' . $this->getView()->getFolder() . '_' . $this->getView()->getElement();
		}

		return $this->getView()->get('option', Request::getCmd('option'));
	}
}
