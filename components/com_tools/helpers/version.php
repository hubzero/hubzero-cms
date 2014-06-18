<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(__DIR__ . '/../models/version.php');

/**
 * Tool version helper class
 */
class ToolsHelperVersion
{
	/**
	 * Short description for 'iterate'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $func Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public function iterate($func)
	{
		$db = \JFactory::getDBO();

		if (true)
		{
			$query = "SELECT instance FROM #__tool_version;";

			$db->setQuery($query);

			$result = $db->loadResultArray();

			if ($result === false)
			{
				return false;
			}

			foreach($result as $row)
			{
				call_user_func($func, $row);
			}
		}

		return true;
	}

	/**
	 * Short description for 'getCurrentToolVersion'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $toolid Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public static function getCurrentToolVersion($toolid)
	{
		$db = \JFactory::getDBO();

		if (is_numeric($toolid))
		{
			$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
				$db->Quote($toolid) . " AND v.state=1 ORDER BY v.revision DESC LIMIT 1";
		}
		else
		{
			$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
				$db->Quote($toolid) . " AND v.toolid=t.id AND v.state=1 ORDER BY v.revision " .
				" DESC LIMIT 1";
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		if (empty($result))
		{
			return false;
		}

		return ToolsModelVersion::getInstance($result);
	}

	/**
	 * Short description for 'getDevelopmentToolVersion'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $toolid Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public static function getDevelopmentToolVersion($toolid)
	{
		$db = \JFactory::getDBO();

		if (is_numeric($toolid))
		{
			$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
				$db->Quote($toolid) . " AND v.state=3 ORDER BY v.revision DESC LIMIT 1";
		}
		else
		{
			$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
				$db->Quote($toolid) . " AND v.toolid=t.id AND v.state=3 ORDER BY v.revision " .
				" DESC LIMIT 1";
		}

		$db->setQuery($query);
		$result = $db->loadResult();

		if (empty($result))
		{
			return false;
		}

		return ToolsModelVersion::getInstance($result);
	}

	/**
	 * Short description for 'getToolRevision'
	 *
	 * Long description (if any) ...
	 *
	 * @param	  unknown $toolid Parameter description (if any) ...
	 * @param	  string $revision Parameter description (if any) ...
	 * @return	 boolean Return description (if any) ...
	 */
	public static function getToolRevision($toolid, $revision)
	{
		$db = \JFactory::getDBO();

		if ($revision == 'dev' || $revision == 'development')
		{
			if (is_numeric($toolid))
			{
				$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
					$db->Quote($toolid) . " AND v.state=3 ORDER BY v.revision DESC LIMIT 1";
			}
			else
			{
				$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
					$db->Quote($toolid) . " AND v.toolid=t.id AND v.state=3 ORDER BY v.revision " .
					" DESC LIMIT 1";
			}
		}
		else if ($revision == 'current')
		{
			if (is_numeric($toolid))
			{
				$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
					$db->Quote($toolid) . " AND v.state=1 ORDER BY v.revision DESC LIMIT 1";
			}
			else
			{
				$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
					$db->Quote($toolid) . " AND v.toolid=t.id AND v.state=1 ORDER BY v.revision " .
					" DESC LIMIT 1";
			}
		}
		else
		{
			if (is_numeric($toolid))
			{
				$query = "SELECT instance FROM #__tool_version AS v WHERE v.toolid=" .
					$db->Quote($toolid) . " AND v.state<>'3' AND v.revision=" . $db->Quote($revision) . "  LIMIT 1";
			}
			else
			{
				$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
				   	$db->Quote($toolid) . " AND v.toolid=" .  $db->Quote($toolid) . " AND v.state<>'3' AND " .
					" v.revision=" . $db->Quote($revision) . "  LIMIT 1";
			}
		}

		$db->setQuery($query);

		$result = $db->loadResult();

		if (empty($result))
		{
			return false;
		}

		return ToolsModelVersion::getInstance($result);
	}
}
