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

namespace Components\Citations\Site\Controllers;

use Components\Citations\Tables\Citation;
use Components\Citations\Tables\Author;
use Hubzero\Component\SiteController;
use Exception;
use User;

/**
 * Manage a citation's author entries
 */
class Authors extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Incoming member ID
		$id = Request::getInt('citation', 0);

		if ($id == 0)
		{
			$this->setError(Lang::txt('COM_CITATIONS_ERROR_MISSING_CITATION'));
		}

		$this->citation = new Citation($this->database);
		$this->citation->id = $id;
		if ($id > 0)
		{
			$this->citation->load($id);
		}

		if ($this->citation->id == 0)
		{
			$this->setError(Lang::txt('COM_CITATIONS_ERROR_INVALID_CITATION'));
		}

		parent::execute();
	}

	/**
	 * Add a user as a manager of a course
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if ($this->getError())
		{
			return $this->displayTask();
		}

		// Incoming host
		$m = Request::getVar('author', '');

		$mbrs = explode(',', $m);
		$mbrs = array_map('trim', $mbrs);

		foreach ($mbrs as $mbr)
		{
			$user = null;
			if (!strstr($mbr, ' '))
			{
				$user = User::getInstance($mbr);
			}

			// Make sure the user exists
			if (!is_object($user) || !$user->get('username'))
			{
				$mbr = trim($mbr);
				$mbr = preg_replace('/\s+/', ' ', $mbr);

				$user = new \Hubzero\User\User;
				$user->set('name', $mbr);

				$parts = explode(' ', $mbr);

				if (count($parts) > 1)
				{
					$surname = array_pop($parts);
					$user->set('surname', $surname);
					$givenName = array_shift($parts);
					$user->set('givenName', $givenName);
					if (!empty($parts))
					{
						$user->get('middleName', implode(' ', $parts));
					}
				}
			}

			$author = new Author($this->database);
			$author->cid          = $this->citation->id;
			$author->author       = $user->get('name');
			$author->uidNumber    = $user->get('id', 0);
			$author->organization = $user->get('organization');
			$author->givenName    = $user->get('givenName');
			$author->middleName   = $user->get('middleName');
			$author->surname      = $user->get('surname');
			$author->email        = $user->get('email');

			if (!$author->check())
			{
				$this->setError($author->getError());
				continue;
			}
			if (!$author->store())
			{
				$this->setError($author->getError());
				continue;
			}
		}

		$this->saveAuthorsList();

		// Push through to the view
		$this->displayTask();
	}

	/**
	 * Update author ordering
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if ($this->getError())
		{
			return $this->displayTask();
		}

		$mbrs = Request::getVar('author', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		$users = array();
		foreach ($mbrs as $i => $mbr)
		{
			$author = new Author($this->database);
			$author->load(intval($mbr));
			$author->ordering = ($i + 1);

			if (!$author->store())
			{
				$this->setError(Lang::txt('COM_CITATIONS_ERROR_UNABLE_TO_UPDATE') . ' ' . $mbr);
			}
		}

		// Push through to the view
		$this->displayTask();
	}

	/**
	 * Remove one or more authors from a citation
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if ($this->getError())
		{
			return $this->displayTask();
		}

		$author = new Author($this->database);

		$mbrs = Request::getVar('author', array());
		$mbrs = (!is_array($mbrs) ? array($mbrs) : $mbrs);

		$users = array();
		foreach ($mbrs as $mbr)
		{
			if (!$author->delete(intval($mbr)))
			{
				$this->setError(Lang::txt('COM_CITATIONS_ERROR_UNABLE_TO_REMOVE') . ' ' . $mbr);
			}
		}

		$this->saveAuthorsList();

		// Push through to the view
		$this->displayTask();
	}

	/**
	 * Display a list of authors for a citation
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->set('citation', $this->citation)
			->setLayout('display')
			->display();
	}

	/**
	 * Update authors string on the citations entry
	 * This is used primarily to make searching easier
	 *
	 * @return  void
	 */
	protected function saveAuthorsList()
	{
		// Reset the authors string
		$authors = $this->citation->authors();
		foreach ($authors as $author)
		{
			$auths[] = $author->author . ($author->uidNumber ? '{{' . $author->uidNumber . '}}' : '');
		}
		$this->citation->author = implode('; ', $auths);
		$this->citation->store();
	}
}
