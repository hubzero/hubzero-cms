<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Helpers;

include_once dirname(__DIR__) . DS . 'models' . DS . 'version.php';

/**
 * Tool version helper class
 */
class Version
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
		$db = \App::get('db');

		if (true)
		{
			$query = "SELECT instance FROM `#__tool_version`;";

			$db->setQuery($query);

			$result = $db->loadColumn();

			if ($result === false)
			{
				return false;
			}

			foreach ($result as $row)
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
		$db = \App::get('db');

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

		return \Components\Tools\Models\Version::getInstance($result);
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
		$db = \App::get('db');

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

		return \Components\Tools\Models\Version::getInstance($result);
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
		$db = \App::get('db');

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
					$db->Quote($toolid) . " AND v.state IN (1, 0) ORDER BY v.state DESC, v.revision DESC LIMIT 1";
			}
			else
			{
				$query = "SELECT instance FROM #__tool_version AS v, #__tool AS t WHERE t.toolname=" .
					$db->Quote($toolid) . " AND v.toolid=t.id AND v.state IN (1, 0) ORDER BY v.state DESC, v.revision " .
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

		return \Components\Tools\Models\Version::getInstance($result);
	}
}
