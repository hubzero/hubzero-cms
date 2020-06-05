<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Forum\Models\UsersCategory;
use Request;
use User;

require_once dirname(dirname(__DIR__)) . '/models/usersCategory.php';

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
	public function createTask()
	{
		$userId = Request::getInt('userId');
		$currentUserId = User::get('id');

		$this->_requiresMatchingUser($currentUserId, $userId);

		$categoriesIds = Request::getArray('categoriesIds');

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

		$this->send($result);
	}

	/**
	 * Instantiate user's categories
	 *
	 * @param   array    $categoriesIds
	 * @param   integer  $currentUserId
	 * @return  array
	 */
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

	/**
	 * Save user's categories
	 *
	 * @param   array  $usersCategories
	 * @return  array
	 */
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
		$userId = Request::getInt('userId');
		$currentUserId = User::get('id');

		$this->_requiresMatchingUser($currentUserId, $userId);

		$categoriesIds = Request::getArray('categoriesIds');

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

		$this->send($result);
	}

	/**
	 * Destroy user's categories
	 *
	 * @param   array  $usersCategories
	 * @return  array
	 */
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

	/**
	 * Check user's id
	 *
	 * @param   integer  $currentUserId
	 * @param   integer  $userId
	 * @return  void
	 */
	protected function _requiresMatchingUser($currentUserId, $userId)
	{
		if ($currentUserId !== $userId)
		{
			$error = array(
				'status' => 'error',
				'error' => 'User ID mismatch, unable to proceed.'
			);

			$this->send($result);
		}
	}
}
