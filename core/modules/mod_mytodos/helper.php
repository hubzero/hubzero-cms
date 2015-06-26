<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shaun Einolf <einolfs@mail.nih.gov>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\MyTodos;

use Hubzero\Module\Module;
use User;
use App;

/**
 * Module class for displaying a user's to do items
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$this->rows = array();

		// Find the user's most recent to do items
		if (!User::isGuest())
		{
			$database = App::get('db');
			$database->setQuery(
				"SELECT a.id, a.content, b.title, b.alias
				FROM `#__project_todo` a
				INNER JOIN `#__projects` b ON b.id=a.projectid
				WHERE a.assigned_to=" . $database->escape(User::get('id')) . " AND a.state=0 LIMIT 0, " . intval($this->params->get('limit', 10))
			);
			$this->rows = $database->loadObjectList();
		}

		// Push the module CSS to the template
		$this->css();

		require $this->getLayoutPath();
	}
}
