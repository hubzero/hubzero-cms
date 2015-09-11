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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 *  Base component controller class
 */
class ComponentController extends \Hubzero\Component\SiteController
{
	/**
	 * Parse the URL parameters and map each parameter (in order) to the given array of names
	 *
	 * @param		array varNames: Array of names to map the URL parameters to
	 * @return		object: Object with properties named after var names mapped to URL parameters
	 */
	protected function getParams($varNames)
	{
		$i = 0;
		// Strict processing doesn't allow extra or missing parameters in the URL
		$strictProcessing = false;
		$params = false;

		// check if there are more parameters than needed
		$extraParameter = Request::getVar('p' . count($varNames), '');
		if ($strictProcessing && !empty($extraParameter))
		{
			// too many parameters in the URL
			throw new Exception(Lang::txt('Page Not Found'), 404);
		}

		// Go through each var name and assign a sequential URL parameter's value to it
		foreach ($varNames as $varName)
		{
			$value = Request::getVar('p' . $i, '');
			if (!empty($value))
			{
				$params->$varName = $value;
			}
			else {
				if ($strictProcessing)
				{
					// missing parameter in the URL
					throw new Exception(Lang::txt('Page Not Found'), 404);
				}
				break;
			}
			$i++;
		}
		return $params;
	}

}

