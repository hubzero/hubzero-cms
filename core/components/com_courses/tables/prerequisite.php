<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses prerequisites table
 */
class Prerequisites extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
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
