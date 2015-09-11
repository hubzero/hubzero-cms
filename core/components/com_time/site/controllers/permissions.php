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
 * @author    Sam Wilson <samwilson@purdue.edu
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Time\Site\Controllers;

/**
 * Permissions controller for time component
 */
class Permissions extends Base
{
	/**
	 * Default view function
	 *
	 * @return void
	 */
	public function displayTask()
	{
		// Get scope
		$this->view->scope    = Request::getWord('scope', 'Hub');
		$this->view->scope_id = Request::getInt('scope_id', 0);

		// Get permissions
		$access = new \JForm('permissions');
		$access->loadFile(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'forms' . DS . 'permissions.xml');

		// Bind existing rules if applicable
		$asset = new \JTableAsset($this->database);
		$name  = 'com_time.' . strtolower($this->view->scope) . '.' . $this->view->scope_id;
		$asset->loadByName($name);

		if ($asset->get('id'))
		{
			$access->setValue('asset_id', null, $asset->get('id'));
		}

		$this->view->permissions = $access->getField(strtolower($this->view->scope));

		// Display
		$this->view->display();
	}

	/**
	 * Save permissions to asset
	 *
	 * @return void
	 */
	public function saveTask()
	{
		$scope    = Request::getWord('scope', false);
		$scope_id = Request::getInt('scope_id', false);

		if (!$scope || !$scope_id)
		{
			echo json_encode(array('success'=>false));
			exit();
		}

		// Process Rules
		$data  = Request::getVar(strtolower($scope));
		$rules = array();

		if ($data && count($data) > 0)
		{
			foreach ($data as $rule => $parts)
			{
				if ($parts && count($parts) > 0)
				{
					foreach ($parts as $group => $perms)
					{
						if ($perms == '')
						{
							continue;
						}

						$rules[$rule][$group] = $perms;
					}
				}
			}
		}

		$class = 'Components\Time\Models\\' . $scope;
		$model = $class::oneOrFail($scope_id);
		$model->assetRules = new \JAccessRules($rules);
		$model->save();

		echo json_encode(array('success'=>true));
		exit();
	}
}