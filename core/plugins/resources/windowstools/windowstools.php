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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for Windows tools
 */
class plgResourcesWindowstools extends \Hubzero\Plugin\Plugin
{
	/**
	 * Generate a Windows tool invoke URL to redirect to
	 *
	 * @param   string  $option  Name of the component
	 * @return  void
	 */
	public function invoke($option)
	{
		$url = $this->generateInvokeUrl($option);

		if ($url)
		{
			App::redirect($url);
		}

		App::abort(404, Lang::txt('No invoke URL found.'));
	}

	/**
	 * Generate a Windows tool invoke URL to redirect to
	 *
	 * @param   string  $option  Name of the component
	 * @return  string
	 */
	public function generateInvokeUrl($option, $uuid = null)
	{
		$uuid = ($uuid ? $uuid : Request::getVar('uuid'));

		if (!$uuid)
		{
			return '';
		}

		$url = 'https://hubzero.org';

		return $url;
	}
}
