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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Tools Model for Tools Component
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Tools Model
 */
class ToolsModelTools extends JModel
{
	/**
	 * Get application tools
	 *
	 * @return     array
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
				$aliases = implode("','", $result);

				$database = JFactory::getDBO();

				$query = "SELECT v.id, v.instance, v.toolname, v.title, MAX(v.revision), v.toolaccess, v.codeaccess, v.state, t.state AS tool_state
							FROM #__tool as t, #__tool_version as v
							WHERE v.toolname IN ('" . $aliases . "') AND t.id=v.toolid
							AND (v.state='1' OR v.state='3')
							GROUP BY toolname
							ORDER BY v.toolname ASC";

				$database->setQuery($query);

				return $database->loadObjectList();
			}
		}

		return $result;
	}
}
