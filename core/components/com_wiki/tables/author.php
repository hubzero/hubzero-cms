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

namespace Components\Wiki\Tables;

use Lang;
use User;

/**
 * Wiki table for associating authors to a page
 */
class Author extends \JTable
{
	/**
	 * Object constructor to set table and key field
	 *
	 * @param   object  $db  Database object
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wiki_page_author', 'id', $db);
	}

	/**
	 * Method for checking that fields are valid before sending to the database
	 *
	 * @return  boolean  True if all fields are valid
	 */
	public function check()
	{
		$this->page_id = intval($this->page_id);
		if (!$this->page_id)
		{
			$this->setError(Lang::txt('Author entry must have a page ID.'));
		}

		$this->user_id = intval($this->user_id);
		if (!$this->user_id)
		{
			$this->setError(Lang::txt('Author entry must have a user ID.'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Returns the record ID for a given page ID/user ID combo
	 *
	 * @param   integer  $page_id
	 * @param   integer  $user_id
	 * @return  integer
	 */
	public function getId($page_id=NULL, $user_id=NULL)
	{
		$page_id = $page_id ?: $this->page_id;
		$user_id = $user_id ?: $this->user_id;

		if (!$page_id || !$user_id)
		{
			$this->setError(Lang::txt("Missing argument (page_id: $page_id, user_id: $user_id)."));
			return false;
		}

		if (!$this->id)
		{
			$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE page_id=" . $this->_db->quote($page_id) . " AND user_id=" . $this->_db->quote($user_id));
			$this->id = $this->_db->loadResult();
		}
		return $this->id;
	}

	/**
	 * Returns whether a user is an author for a given page
	 *
	 * @param   integer  $page_id
	 * @param   integer  $user_id
	 * @return  boolean  True if user is an author
	 */
	public function isAuthor($page_id=NULL, $user_id=NULL)
	{
		$id = $this->getId($page_id, $user_id);
		if ($id)
		{
			return true;
		}
		return false;
	}

	/**
	 * Returns an array of user IDs for a given page ID
	 *
	 * @param   integer  $page_id
	 * @return  array
	 */
	public function getAuthorIds($page_id=NULL)
	{
		$page_id = $page_id ?: $this->page_id;

		if (!$page_id)
		{
			$this->setError(Lang::txt('Missing page ID.'));
			return false;
		}
		$this->_db->setQuery("SELECT user_id FROM $this->_tbl WHERE page_id=" . $this->_db->quote($page_id));
		return $this->_db->loadColumn();
	}

	/**
	 * Returns an array of objects of user data for a given page ID
	 *
	 * @param   integer  $page_id
	 * @return  array
	 */
	public function getAuthors($page_id=NULL)
	{
		$page_id = $page_id ?: $this->page_id;

		if (!$page_id)
		{
			$this->setError(Lang::txt('Missing page ID.'));
			return false;
		}
		$this->_db->setQuery("SELECT wa.user_id, u.username, u.name FROM $this->_tbl AS wa, `#__users` AS u WHERE wa.page_id=" . $this->_db->quote($page_id) . " AND u.id=wa.user_id");
		return $this->_db->loadObjectList();
	}

	/**
	 * Removes an author for a page
	 *
	 * @param   integer  $page_id
	 * @param   integer  $user_id
	 * @return  boolean  True if entry successfully removed
	 */
	public function removeAuthor($page_id=NULL, $user_id=NULL)
	{
		$page_id = $page_id ?: $this->page_id;
		$user_id = $user_id ?: $this->user_id;

		if (!$page_id || !$user_id)
		{
			$this->setError(Lang::txt("Missing argument (page_id: $page_id, user_id: $user_id)."));
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE page_id=" . $this->_db->quote($page_id) . " AND user_id=" . $this->_db->quote($user_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Removes all author for a page
	 *
	 * @param   integer  $page_id
	 * @return  boolean  True if all entries successfully removed
	 */
	public function removeAuthors($page_id=NULL)
	{
		$page_id = $page_id ?: $this->page_id;

		if (!$page_id)
		{
			$this->setError(Lang::txt('Missing page ID.'));
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE page_id=" . $this->_db->quote($page_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Saves a string of comma-separated usernames or IDs to authors table
	 *
	 * @param   integer  $page_id the id of the page
	 * @param   string   $authors string of authors
	 * @return  boolean  True if authors successfully saved
	 */
	public function updateAuthors($authors=NULL, $page_id=NULL)
	{
		$page_id = $page_id ?: $this->page_id;

		if (!$page_id)
		{
			$this->setError(Lang::txt("Missing argument (page_id: $page_id)."));
			return false;
		}

		// Get the list of existing authors
		$ids = $this->getAuthorIds($page_id);

		$auths = array();

		// Turn the comma-separated string of authors into an array and loop through it
		if ($authors)
		{
			$authArray = explode(',', $authors);
			$authArray = array_map('trim', $authArray);
			foreach ($authArray as $author)
			{
				// Attempt to load each user
				$targetuser = User::getInstance($author);

				// Ensure we found an account
				if (!is_object($targetuser))
				{
					// No account found for this username/ID
					// Move on to next record
					continue;
				}
				// Check if they're already an existing author
				if (in_array($targetuser->get('id'), $ids))
				{
					// Add them to the existing authors array
					$auths[] = $targetuser->get('id');
					// Move on to next record
					continue;
				}
				// Create a new author object and attempt to save the record
				$wpa = new self($this->_db);
				$wpa->page_id = $page_id;
				$wpa->user_id = $targetuser->get('id');
				if ($wpa->check())
				{
					if (!$wpa->store())
					{
						$this->setError($wpa->getError());
					}
					// Add them to the existing authors array
					$auths[] = $targetuser->get('id');
				}
				else
				{
					$this->setError("Error adding page author: (page_id: $wpa->page_id, user_id: $wpa->user_id).");
				}
			}
		}
		// Loop through the old author list and check for nay entries not in the new list
		// Remove any entries not found in the new list
		foreach ($ids as $id)
		{
			if (!in_array($id, $auths))
			{
				$wpa = new self($this->_db);
				if (!$wpa->removeAuthor($page_id, $id))
				{
					$this->setError($wpa->getError());
				}
			}
		}
		if ($this->getError())
		{
			return false;
		}
		return true;
	}

	/**
	 * Transition old author strings to table
	 *
	 * @return  boolean  True on success, false on error
	 */
	public function transitionAuthors()
	{
		$this->_db->setQuery("SELECT id, authors FROM `#__wiki_page` WHERE authors!='' AND authors IS NOT NULL");
		if ($pages = $this->_db->loadObjectList())
		{
			foreach ($pages as $page)
			{
				$authors = explode(',', $page->authors);
				$authors = array_map('trim', $authors);
				foreach ($authors as $author)
				{
					$targetuser = User::getInstance($author);

					// Ensure we found an account
					if (is_object($targetuser))
					{
						$wpa = new self($this->_db);
						$wpa->page_id = $page->id;
						$wpa->user_id = $targetuser->get('id');
						if ($wpa->check())
						{
							$wpa->store();
						}
						else
						{
							$this->setError("Error adding page author: (page_id: $wpa->page_id, user_id: $wpa->user_id).");
						}
					}
				}
			}
		}
		if (!$this->getError())
		{
			$this->_db->setQuery("ALTER TABLE $this->_tbl DROP COLUMN `authors`");
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
			}
		}
		if (!$this->getError())
		{
			return true;
		}
		return false;
	}
}
