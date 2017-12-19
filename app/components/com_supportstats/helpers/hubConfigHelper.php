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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Supportstats\Helpers;

require_once Component::path('com_supportstats') . '/vendor/autoload.php';

use Dotenv\Dotenv;

class HubConfigHelper
{

	/**
	 * Gets the access token for the hub with the given name
	 *
	 * @param   string   $hubName		Name of hub
	 * @return  string
	 */
	public static function getAccessToken($hubName)
	{
		self::_loadApiCredentials($hubName);
		$envVarName = self::_getEnvVarName($hubName, 'ACCESS_TOKEN');
		$accessToken = getenv($envVarName);

		return $accessToken;
	}

	/**
	 * Loads hub's environment variables
	 *
	 * @param   string   $hubName		Name of hub
	 * @return  null
	 */
	protected static function _loadApiCredentials($hubName)
	{
		$directory = __DIR__ . '/../';
		$envFileName = self::_getEnvFileName($hubName);
		$dotenv = new Dotenv($directory, $envFileName);

		$dotenv->load();
	}

	/**
	 * Determines file name that stores the hub's environment variables
	 *
	 * @param   string   $hubName		Name of hub
	 * @return  string
	 */
	protected static function _getEnvFileName($hubName)
	{
		$formattedName = self::_getFormattedName($hubName);
		$envFileName = '.env-' . strtolower($formattedName);

		return $envFileName;
	}

	/**
	 * Determines environment variable name
	 *
	 * @param   string   $hubName		Name of hub
	 * @param   string   $envVar		Generic environment variable name
	 * @return  string
	 */
	protected static function _getEnvVarName($hubName, $envVar)
	{
		$formattedName = self::_getFormattedName($hubName);
		$envVarName = strtoupper($hubName) . "_$envVar";

		return $envVarName;
	}

	/**
	 * Formats hub's name to match environment variable naming conventions
	 *
	 * @param   string   $hubName		Name of hub
	 * @return  string
	 */
	protected static function _getFormattedName($hubName)
	{
		return preg_replace("/ /", '', $hubName);
	}

}
