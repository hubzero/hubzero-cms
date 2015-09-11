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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

/**
 * Courses prerequisites table
 */
class Prerequisites extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_prerequisites', 'id', $db);
	}

	/**
	 * Get all prereqs for a given section
	 *
	 * @param  int   $section_id
	 * @return array $results
	 **/
	public function loadAllBySectionId($section_id)
	{
		$query = "SELECT * FROM `{$this->_tbl}` WHERE `section_id` = '{$section_id}'";

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		return $results;
	}

	/**
	 * Get all prereqs for a given scope/scope_id
	 *
	 * @param  string $scope
	 * @param  int    $scope_id
	 * @param  int    $section_id
	 * @return array  $results
	 **/
	public function loadAllByScope($scope, $scope_id, $section_id)
	{
		$query = "SELECT * FROM `{$this->_tbl}` WHERE `item_scope` = '{$scope}' AND `item_id` = '{$scope_id}' AND `section_id` = '{$section_id}' ORDER BY `requisite_id` ASC";

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		return $results;
	}
}