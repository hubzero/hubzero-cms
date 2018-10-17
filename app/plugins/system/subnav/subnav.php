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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin checking for missing/required registration fields
 */
class plgSystemSubnav extends \Hubzero\Plugin\Plugin
{
	/**
	 * Hook for after parsing route
	 *
	 * @return void
	 */
	public function onSubnavRequest()
	{
		// parse mappings
		$comMappings = $this->params->get('component_mappings');
		$urlMappings = $this->params->get('url_mappings');

		$comMappings = explode("\n", $comMappings);
		$urlMappings = explode("\n", $urlMappings);

		$componentsMap = array();
		foreach ($comMappings as $mapping)
		{
			$mapping = explode(',', $mapping);
			if (count($mapping) === 2) {
				$componentsMap[trim($mapping[0])] = trim($mapping[1]);
			}
		}

		$urlsMap = array();
		foreach ($urlMappings as $mapping)
		{
			$mapping = explode(',', $mapping);
			if (count($mapping) === 2) {
				$urlsMap[trim($mapping[0])] = trim($mapping[1]);
			}
		}

		$map = array('com' => $componentsMap, 'url' => $urlsMap);

		return $map;
	}
}
