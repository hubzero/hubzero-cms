<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models;

use Hubzero\Base\Obj;

/**
 * Tools Model
 */
class Tools extends Obj
{
	/**
	 * Get application tools
	 *
	 * @return  array
	 */
	public function getApplicationTools()
	{
		$dh = @opendir('/opt/trac/tools');
		$result = array();

		if (!empty($dh))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (is_dir('/opt/trac/tools/' . $file))
				{
					if (strncmp($file, '.', 1) != 0)
					{
						$result[] = $file;
					}
				}
			}

			closedir($dh);

			sort($result);

			if (count($result) > 0)
			{
				$database = \App::get('db');

				foreach ($result as $key => $val)
				{
					$result[$key] = $database->quote($val);
				}

				$query = "SELECT v.id, v.instance, v.toolname, v.title, MAX(v.revision), v.toolaccess, v.codeaccess, v.state, t.state AS tool_state
							FROM `#__tool` as t, `#__tool_version` as v
							WHERE v.toolname IN (" . implode(',', $result) . ") AND t.id=v.toolid
							AND v.state IN ('1','3')
							AND t.state != 9
							GROUP BY toolname
							ORDER BY v.toolname ASC";

				$database->setQuery($query);

				return $database->loadObjectList();
			}
		}

		return $result;
	}
}
