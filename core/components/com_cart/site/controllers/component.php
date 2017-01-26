<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cart\Site\Controllers;

/**
 *  Base component controller class
 */
class ComponentController extends \Hubzero\Component\SiteController
{
	/**
	 * Parse the URL parameters and map each parameter (in order) to the given array of names
	 *
	 * @param		mixed (array of strings or string): Array of names anr single name to map the URL parameter(s) to
	 * @return		object: Object with properties named after var names mapped to URL parameters
	 */
	protected function getParams($varNames)
	{
		$i = 0;
		// Strict processing doesn't allow extra or missing parameters in the URL
		$strictProcessing = false;
		$params = false;

		if (!is_array($varNames))
		{
			$varNames = array($varNames);
		}

		// check if there are more parameters than needed
		$extraParameter = Request::getVar('p' . count($varNames), '');
		if ($strictProcessing && !empty($extraParameter))
		{
			// too many parameters in the URL
			//throw new \Exception('Too many parameters');
			App::abort(404, Lang::txt('Page Not Found'));
		}

		$params = new \stdClass();

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
					//throw new \Exception('Too few parameters');
					App::abort(404, Lang::txt('Page Not Found'));
				}
				break;
			}
			$i++;
		}
		return $params;
	}

}
