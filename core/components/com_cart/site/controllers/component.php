<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$extraParameter = Request::getString('p' . count($varNames), '');
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
			$value = Request::getString('p' . $i, '');
			if (!empty($value))
			{
				$params->$varName = $value;
			}
			else
			{
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
