<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$name = null;

		// Is it numeric?
		if (is_numeric($et))
		{
			// Yes, then get contributor by ID
			$id = intval($et);
			$sql = "SELECT id, givenName, middleName, surname, name FROM `#__users` WHERE id=".$id;
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
				$cuser = User::getInstance($n);
				if (is_object($cuser))
				{
					$name = $cuser->get('name');
					$id   = $cuser->get('id');
				}
				else
				{
					return '(contributor:' . $et . ' not found)';
				}
			}
			else
			{
				$bits = explode(' ',$n);
				$sql = "SELECT id, givenName, middleName, surname, name FROM `#__users` WHERE givenName=" . $this->_db->quote($bits[0]) . " AND surname=" . $this->_db->quote(end($bits));
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
			return '<a href="' . \Route::url('index.php?option=com_members&id=' . $id) . '">' . $name . '</a>';
		}
		else
		{
			// Return error message
			return '(contributor:' . $et . ' not found)';
		}
	}
}

