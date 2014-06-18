<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$results = null;
$notes = $this->model->notes($this->filters);
if ($notes)
{
	foreach ($notes as $note)
	{
		$ky = $note->get('scope_id');
		if (!isset($results[$ky]))
		{
			$results[$ky] = array();
		}
		$results[$ky][] = $note;
	}
}

$base = $this->offering->link();

	if ($results)
	{
		foreach ($results as $id => $notes)
		{
			$lecture = new CoursesModelAssetgroup($id);
			$unit = CoursesModelUnit::getInstance($lecture->get('unit_id'));

 			echo $this->escape(stripslashes($lecture->get('title'))) . "\n";
			echo '--------------------------------------------------' . "\n";

			foreach ($notes as $note)
			{
				echo '#' . $note->get('id');

				if ($note->get('timestamp') != '00:00:00')
				{
					echo ' video time: ' . $this->escape($note->get('timestamp'));
				}
				echo "\n";
				echo $this->escape(stripslashes($note->get('content')));
				echo "\n";
			}

			echo "\n";
		}
	}
