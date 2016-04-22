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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Hubzero\Database\Relational;
use User;

require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Wiki author model
 */
class Author extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'user_id' => 'positive|nonzero',
		'page_id' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->oneToOne('Components\Members\Models\Member', 'id', 'user_id');
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function page()
	{
		return $this->belongsToOne('Page', 'page_id');
	}

		/**
	 * Saves a string of comma-separated usernames or IDs to authors table
	 *
	 * @param   mixed    $authors  String or array of authors
	 * @param   integer  $page_id  The id of the page
	 * @return  boolean  True if authors successfully saved
	 */
	public static function setForPage($authors, $page_id)
	{
		if (!trim($authors))
		{
			return true;
		}

		// Get the list of existing authors
		$ids = array();

		$existing = self::all()
			->whereEquals('page_id', (int)$page_id)
			->rows();
		foreach ($existing as $ex)
		{
			$ids[] = $ex->get('user_id');
		}

		$auths = array();

		// Turn the comma-separated string of authors into an array and loop through it
		if ($authors)
		{
			if (is_string($authors))
			{
				$authors = explode(',', $authors);
				$authors = array_map('trim', $authors);
			}

			foreach ($authors as $author)
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
				$wpa = self::blank();
				$wpa->set('page_id', $page_id);
				$wpa->set('user_id', $targetuser->get('id'));

				if (!$wpa->save())
				{
					$err = $wpa->getError();
				}

				// Add them to the existing authors array
				$auths[] = $targetuser->get('id');
			}
		}

		// Loop through the old author list and check for nay entries not in the new list
		// Remove any entries not found in the new list
		foreach ($existing as $ex)
		{
			if (!in_array($ex->get('id'), $auths))
			{
				if (!$ex->destroy())
				{
					$err = $ex->getError();
				}
			}
		}

		if ($err)
		{
			return false;
		}

		return true;
	}
}
