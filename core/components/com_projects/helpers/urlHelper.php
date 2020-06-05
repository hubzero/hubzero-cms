<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Helpers;

use Hubzero\Base\Obj;

class UrlHelper extends Obj
{
	/**
	 * Set URL based on user being a member of the project
	 *
	 * @param   string   $url
	 * @param   boolean  $userIsMember
	 * @return  string
	 */
	public static function updatePerMembership($url, $userIsMember)
	{
		if (!$userIsMember)
		{
			$url = self::_appendQueryCharacter($url);
			$url .= 'subdir=public';
		}

		return $url;
	}

	/**
	 * Append correct querystring delimiter
	 *
	 * @param   string   $url
	 * @return  string
	 */
	protected static function _appendQueryCharacter($url)
	{
		if (!preg_match('/\?.+$/', $url))
		{
			$url .= '?';
		}
		else
		{
			$url .= '&';
		}

		return $url;
	}
}
