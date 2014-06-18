<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a user's resources
 */
class modMyResources extends \Hubzero\Module\Module
{
	/**
	 * Display module content
	 *
	 * @return     void
	 */
	public function display()
	{
		$this->no_html = JRequest::getInt('no_html', 0);
		if (!$this->no_html)
		{
			// Push the module CSS to the template
			$this->css();
			$this->js();
		}

		$database = JFactory::getDBO();
		$juser = JFactory::getUser();

		$this->limit = intval($this->params->get('limit', 5));

		$this->sort = $this->params->get('sort', 'publish_up');

		// Get "published" contributions
		$query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype,
							AA.subtable, R.created, R.created_by, R.modified, R.published, R.publish_up, R.standalone,
							R.rating, R.times_rated, R.alias, R.ranking, rt.type AS typetitle, R.params ";
		if ($this->sort == 'usage')
		{
			$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=R.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
		}
		$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
		//$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
		$query .= "WHERE AA.authorid = ". $juser->get('id') ." ";
		$query .= "AND R.id = AA.subid ";
		$query .= "AND AA.subtable = 'resources' ";
		$query .= "AND R.standalone=1 AND R.type=rt.id AND R.published=1 ";
		$query .= "ORDER BY ";

		switch ($this->sort)
		{
			case 'usage':
				$query .= "users DESC";
			break;
			case 'title':
				$query .= "title ASC, publish_up DESC";
			break;
			case 'publish_up':
			default:
				$query .= "publish_up DESC, title ASC";
			break;
		}
		if ($this->limit > 0 && $this->limit != 'all')
		{
			$query .= " LIMIT " . $this->limit;
		}

		$database->setQuery($query);

		$this->contributions = $database->loadObjectList();

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}

