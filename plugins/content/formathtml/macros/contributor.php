<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Macro class for linking contributor
 */
class Contributor extends Macro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'This macro will generate a link to a contributor\'s page with the contributor\'s name as the link text. It accepts either the contributor\'s ID, username, or name. NOTE: to use a name, it must be identical to their contributor page.';
		$txt['html'] = '<p>This macro will generate a link to a contributor\'s page with the contributor\'s name as the link text. It accepts either the contributor\'s ID, username, or name. NOTE: to use a name, it must be identical to their contributor page.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et)
		{
			return '';
		}
		$id = 0;
		// Is it numeric?
		if (is_numeric($et))
		{
			// Yes, then get contributor by ID
			$id = intval($et);
			$sql = "SELECT uidNumber, givenName, middleName, surname, name FROM `#__xprofiles` WHERE uidNumber=".$id;
			// Perform query
			$this->_db->setQuery($sql);
			$a = $this->_db->loadRow();

			// Did we get a result from the database?
			if ($a)
			{
				$id = ($id) ? $id : $a[0];
				// Build and return the link
				if ($a[4] != '')
				{
					$name = $a[4];
				}
				else
				{
					$name  = $a[1] . ' ';
					$name .= ($a[2]) ? $a[2] . ' ' : '';
					$name .= $a[3] . ' ';
				}
			}
		}
		else
		{
			// No, it could be username or name
			$n = trim($et);
			// Is there a space in it inidcating name ("First Last")?
			if (!strpos($n,' '))
			{
				// No, then we must have a username
				// Get user's name
				$cuser = JUser::getInstance($n);
				if (is_object($cuser))
				{
					$name = $cuser->get('name');
					$id   = $cuser->get('id');
				}
				else
				{
					return '(contributor:' . $et . ' not found)';
				}
			} else {
				$bits = explode(' ',$n);
				$sql = "SELECT uidNumber, givenName, middleName, surname, name FROM `#__xprofiles` WHERE givenName=" . $this->_db->quote($bits[0]) . " AND surname=" . $this->_db->quote(end($bits));
				// Perform query
				$this->_db->setQuery($sql);
				$a = $this->_db->loadRow();

				// Did we get a result from the database?
				if ($a)
				{
					$id = ($id) ? $id : $a[0];
					// Build and return the link
					if ($a[4] != '')
					{
						$name = $a[4];
					}
					else
					{
						$name  = $a[1] . ' ';
						$name .= ($a[2]) ? $a[2] . ' ' : '';
						$name .= $a[3] . ' ';
					}
				}
			}
		}

		// Did we get a result from the database?
		if ($name && $id)
		{
			return '<a href="' . \JRoute::_('index.php?option=com_members&id=' . $id) . '">' . $name . '</a>';
		}
		else
		{
			// Return error message
			return '(contributor:' . $et . ' not found)';
		}
	}
}

