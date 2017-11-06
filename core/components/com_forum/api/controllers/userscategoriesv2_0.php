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

namespace Components\Forum\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Forum\Models\UsersCategory;
use Request;

require_once Component::path('com_forum') . '/models/usersCategory.php';

class UsersCategoriesv2_0 extends ApiController
{

	/**
	 * Create user's categories
	 *
	 * @apiMethod POST
	 * @apiUri    /api/v2.0/forum/userscategories/create
	 * @apiParameter {
	 * 		"name":          "category_id",
	 * 		"description":   "Forum category's ID",
	 * 		"type":          "integer",
	 * 		"required":      true
	 * }
	 * @apiParameter {
	 * 		"name":          "user_id",
	 * 		"description":   "User's ID",
	 * 		"type":          "integer",
	 * 		"required":      true
	 * }
	 * @return    TODO
	 */
	function createTask()
	{
		$userId = Request::getVar('userId');
		$currentUserId = User::get('id');

		$this->_requiresMatchingUser($currentUserId, $userId);

		$categoriesIds = Request::getVar('categoriesIds');

		$usersCategories = $this->_instantiateUsersCategories($categoriesIds, $currentUserId);

		$errors = $this->_saveUsersCategories($usersCategories);

		$arrayCategories = array_map(function($usersCategory) {
			return $usersCategory->toArray();
		}, $usersCategories);

		$result = array(
			'records' => $arrayCategories,
			'errors' => $errors
		);

		$result['status'] = empty($errors) ? 'success' : 'error';

		echo json_encode($result);
		exit();
	}

	protected function _instantiateUsersCategories($categoriesIds, $currentUserId)
	{
		$usersCategories = array_map(function($categoryId) use ($currentUserId) {
			$usersCategory = UsersCategory::blank();
			$usersCategory->set(array(
				'category_id' => $categoryId,
				'user_id' => $currentUserId
			));
			return $usersCategory;
		}, $categoriesIds);

		return $usersCategories;
	}

	protected function _saveUsersCategories($usersCategories)
	{
		$errors = array();

		foreach ($usersCategories as $usersCategory)
		{
			if (!$usersCategory->save())
			{
				$instanceErrors = $usersCategory->getErrors();
				array_push($errors, $instanceErrors);
			}
		}

		return $errors;
	}

	/**
	 * Destroys user's categories
	 *
	 * @apiMethod DELETE
	 * @apiUri    /api/v2.0/forum/userscategories/destroy
	 * @apiParameter {
	 * 		"name":          "category_id",
	 * 		"description":   "Forum category's ID",
	 * 		"type":          "integer",
	 * 		"required":      true
	 * }
	 * @apiParameter {
	 * 		"name":          "user_id",
	 * 		"description":   "User's ID",
	 * 		"type":          "integer",
	 * 		"required":      true
	 * }
	 * @return    TODO
	 */
	public function destroyTask()
	{
		$userId = Request::getVar('userId');
		$currentUserId = User::get('id');

		$this->_requiresMatchingUser($currentUserId, $userId);

		$categoriesIds = Request::getVar('categoriesIds');

		$usersCategories = UsersCategory::all()
			->whereEquals('user_id', $userId)
			->whereIn('category_id', $categoriesIds);

		$errors = $this->_destroyUsersCategories($usersCategories);

		$arrayCategories = $usersCategories->rows()->toArray();

		$result = array(
			'records' => $arrayCategories,
			'errors' => $errors
		);

		$result['status'] = empty($errors) ? 'success' : 'error';

		echo json_encode($result);
		exit();
	}

	protected function _destroyUsersCategories($usersCategories)
	{
		$errors = array();

		foreach ($usersCategories as $usersCategory)
		{
			if (!$usersCategory->destroy())
			{
				$instanceErrors = $usersCategory->getErrors();
				array_push($errors, $instanceErrors);
			}
		}

		return $errors;
	}

	protected function _requiresMatchingUser($currentUserId, $userId)
	{
		if ($currentUserId !== $userId)
		{
			$error = array(
				'status' => 'error',
				'error' => 'User ID mismatch, unable to proceed.'
			);
			echo json_encode($error);
			exit();
		}
	}

}
