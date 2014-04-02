<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'FixNames'
 * 
 * Long description (if any) ...
 */
class ResourcePathsCheck extends SystemHelperScript
{
	/**
	 * Description for '_description'
	 * 
	 * @var string
	 */
	protected $_description = 'Integrity check for resources.';

	/**
	 * Short description for 'run'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function run()
	{
		// hold missing 
		$missing = array();

		// get all resources
		$db  = JFactory::getDBO();
		$sql = "SELECT id, title, path FROM `#__resources` ORDER BY id";
		$db->setQuery($sql);
		$results = $db->loadObjectList();

		// get upload path
		$params = JComponentHelper::getParams('com_resources');
		$base = $params->get('uploadpath', '/site/resources');
		$base = JPATH_ROOT . DS . trim($base, DS) . DS;

		// loop through each resource
		foreach ($results as $result)
		{
			// make sure we have a path
			if (isset($result->path) && $result->path != '')
			{
				// trim our result
				$path = ltrim($result->path, DS);
				$path = trim($path);

				// checks
				if (is_dir($path))
				{	
					echo '<font color="yellow">#' . $result->id . ': Resource path is a directory ' . $path . '</font><br />';
				}
				elseif (JURI::isInternal($path) && !file_exists($base . $path))
				{
					$missing[] = $result;
					echo '<font color="red">#' . $result->id . ': missing resource at - ' .  $base . $path . '</font><br />';
				}
				else
				{
					echo '<font color="green">#' . $result->id . ': All is Good! - ' . $path . '</font><br />';
				}
			}
		}

		echo '<br /><hr />';
		echo '<strong>' . number_format(count($missing)) . ' Total Issues' . '</strong>';
	}
}
