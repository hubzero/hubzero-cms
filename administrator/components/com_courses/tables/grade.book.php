<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Courses grade book table
 */
class CoursesTableGradeBook extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $member_id = NULL;

	/**
	 * decimal(5,2)
	 * 
	 * @var decimal
	 */
	var $score = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $scope = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $scope_id = NULL;

	/**
	 * decimal(5,2)
	 * 
	 * @var decimal
	 */
	var $override = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_grade_book', 'id', $db);
	}

	/**
	 * Load gradebook entry by user and asset id
	 * 
	 * @param      string $member_id
	 * @param      string $asset_id
	 * @return     array
	 */
	public function loadByUserAndAssetId($member_id, $asset_id)
	{
		$db = $this->_db;
		$query  = 'SELECT *';
		$query .= ' FROM '.$this->_tbl;
		$query .= ' WHERE `scope` = "asset" AND `member_id` = ' . $db->quote($member_id) . ' AND `scope_id` = ' . $db->quote($asset_id);
		$db->setQuery( $query );

		if ($result = $db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Build a query based off of filters passed
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS gb";
		$query .= " LEFT JOIN `#__courses_assets` ca ON gb.scope_id = ca.id";
		$query .= " LEFT JOIN `#__courses_asset_associations` caa ON ca.id = caa.asset_id";
		$query .= " LEFT JOIN `#__courses_asset_groups` cag ON caa.scope_id = cag.id";

		$where = array();

		if (isset($filters['member_id']) && $filters['member_id'])
		{
			if(!is_array($filters['member_id']))
			{
				$filters['member_id'] = array($filters['member_id']);
			}
			$where[] = "member_id IN (" . implode(',', $filters['member_id']) . ")";
		}
		if (isset($filters['scope']) && $filters['scope'])
		{
			if(!is_array($filters['scope']))
			{
				$filters['scope'] = array($filters['scope']);
			}
			$where[] = "gb.scope IN ('" . implode('\',\'', $filters['scope']) . "')";
		}
		if (isset($filters['scope_id']) && $filters['scope_id'])
		{
			if(!is_array($filters['scope_id']))
			{
				$filters['scope_id'] = array($filters['scope_id']);
			}
			$where[] = "gb.scope_id IN (" . implode(',', $filters['scope_id']) . ")";
		}
		if (isset($filters['course_id']) && $filters['course_id'])
		{
			$where[] = "ca.course_id = " . $this->_db->Quote($filters['course_id']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get grade records
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function find($filters=array(), $key=null)
	{
		$query = "SELECT gb.*, cag.unit_id, ca.subtype" . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList($key);
	}

	/**
	 * Get passing info
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function passing($filters=array(), $key=null)
	{
		$query = "SELECT gb.member_id, score";
		$query .= " FROM $this->_tbl AS gb";
		$query .= " LEFT JOIN `#__courses_members` cm ON cm.id = gb.member_id";

		$where = array();

		if (isset($filters['member_id']) && $filters['member_id'])
		{
			if(!is_array($filters['member_id']))
			{
				$filters['member_id'] = array($filters['member_id']);
			}
			$where[] = "gb.member_id IN (" . implode(',', $filters['member_id']) . ")";
		}
		if (isset($filters['scope']) && $filters['scope'])
		{
			if(!is_array($filters['scope']))
			{
				$filters['scope'] = array($filters['scope']);
			}
			$where[] = "gb.scope IN ('" . implode('\',\'', $filters['scope']) . "')";
		}
		if (isset($filters['scope_id']) && $filters['scope_id'])
		{
			if(!is_array($filters['scope_id']))
			{
				$filters['scope_id'] = array($filters['scope_id']);
			}
			$where[] = "gb.scope_id IN (" . implode(',', $filters['scope_id']) . ")";
		}
		if (isset($filters['section_id']) && $filters['section_id'])
		{
			if(!is_array($filters['section_id']))
			{
				$filters['section_id'] = array($filters['section_id']);
			}
			$where[] = "cm.section_id IN (" . implode(',', $filters['section_id']) . ")";
		}
		$where[] = "cm.student = 1";

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList($key);
	}

	/**
	 * Query to sync exam scores with gradebook
	 * 
	 * @param      obj   $course
	 * @param      array $member_id
	 * @return     void
	 */
	public function syncGrades($course, $member_id=null)
	{
		if (!is_null($member_id) && !empty($member_id))
		{
			if (!is_array($member_id))
			{
				$member_id = (array) $member_id;
			}
		}
		else
		{
			// Pull all section members
			$members = $course->offering()->section()->members(array('student'=>1));
			$member_id = array();

			// Get member id's for refresh filter
			foreach ($members as $member)
			{
				$member_id[] = $member->get('id');
			}
		}

		// Get the assets
		$asset  = new CoursesTableAsset($this->_db);
		$assets = $asset->find(
			array(
				'w' => array(
					'course_id'  => $course->get('id'),
					'section_id' => $course->offering()->section()->get('id'),
					'asset_type' => 'form'
				)
			)
		);

		$values = array();

		if (count($assets) > 0)
		{
			foreach($assets as $asset)
			{
				// Add null values for unpublished forms that may have already been taken
				if ($asset->state != 1)
				{
					foreach ($member_id as $u)
					{
						$values[] = "('$u', NULL, 'asset', '{$asset->id}')";
					}
					continue;
				}

				$crumb = false;

				// Check for result for given student on form
				$crumb = $asset->url;

				if(!$crumb || strlen($crumb) != 20 || $asset->state != 1)
				{
					// Break foreach, this is not a valid form!
					continue;
				}

				$dep = PdfFormDeployment::fromCrumb($crumb, $course->offering()->section()->get('id'));

				$results = $dep->getResults(false, 'member_id');

				switch ($dep->getState())
				{
					// Form isn't available yet
					case 'pending':
						// Null value
						foreach ($member_id as $u)
						{
							$values[] = "('$u', NULL, 'asset', '{$asset->id}')";
						}
					break;

					// Form availability has expired - students either get a 0, or their score (no nulls)
					case 'expired':
						foreach ($member_id as $u)
						{
							$score = (isset($results[$u]['score'])) ? $results[$u]['score'] : '0.00';
							$values[] = "('{$u}', '{$score}', 'asset', '{$asset->id}')";
						}
					break;

					// Form is still active - students either get their score, or a null
					case 'active':
						foreach ($member_id as $u)
						{
							$resp = $dep->getRespondent($u);

							// Form is active and they have completed it!
							if($resp->getEndTime() && $resp->getEndTime() != '')
							{
								$score = (isset($results[$u]['score'])) ? '\''.$results[$u]['score'].'\'' : NULL;
								$values[] = "('{$u}', {$score}, 'asset', '{$asset->id}')";
							}
							// Form is active and they haven't finished it yet!
							else
							{
								$values[] = "('$u', NULL, 'asset', '{$asset->id}')";
							}
						}
					break;
				}
			}

			// Build query and run
			if (count($values) > 0)
			{
				$query  = "INSERT INTO `#__courses_grade_book` (`member_id`, `score`, `scope`, `scope_id`) VALUES\n";
				$query .= implode(",\n", $values);
				$query .= "\nON DUPLICATE KEY UPDATE score = VALUES(score);";

				$this->_db->execute($query);
			}
		}
	}

	/**
	 * Query to save unit and course totals to gradebook
	 * 
	 * @param      array $data - values to compose update query
	 * @param      int $course_id = course id
	 * @return     void
	 */
	public function saveGrades($data, $course_id)
	{
		$values = array();

		foreach ($data as $member_id=>$member)
		{
			foreach ($member['units'] as $unit_id=>$unit)
			{
				if (is_null($unit['unit_weighted']))
				{
					$values[] = "('$member_id', NULL, 'unit', '$unit_id')";
				}
				else
				{
					$values[] = "('$member_id', " . $this->_db->quote($unit['unit_weighted']) . ", 'unit', '$unit_id')";
				}
			}

			if (is_null($member['course_weighted']))
			{
				$values[] = "('$member_id', NULL, 'course', '$course_id')";
			}
			else
			{
				$values[] = "('$member_id', " . $this->_db->quote($member['course_weighted']) . ", 'course', '$course_id')";
			}
		}

		if (count($values) > 0)
		{
			$query  = "INSERT INTO `#__courses_grade_book` (`member_id`, `score`, `scope`, `scope_id`) VALUES\n";
			$query .= implode(",\n", $values);
			$query .= "\nON DUPLICATE KEY UPDATE score = VALUES(score);";

			$this->_db->execute($query);
		}
	}

	/**
	 * Clear grades for a given course/user combination
	 * 
	 * @param      array $member_id
	 * @param      object $course
	 * @return     void
	 */
	public function clearGrades($member_id, $course)
	{
		if (!is_object($course) || empty($member_id))
		{
			return false;
		}

		if (!is_array($member_id))
		{
			$member_id = (array) $member_id;
		}

		// Clear up course grades for given users
		$query  = "UPDATE `#__courses_grade_book` SET score = NULL";
		$query .= " WHERE scope = 'course' AND scope_id = " . $this->_db->quote($course->get('id'));
		$query .= " AND member_id IN (" . implode(',', $member_id) . ")";
		$this->_db->execute($query);

		// Clean up units as well...
		foreach ($course->offering()->units() as $unit)
		{
			$query  = "UPDATE `#__courses_grade_book` SET score = NULL";
			$query .= " WHERE scope = 'unit' AND scope_id = " . $this->_db->quote($unit->get('id'));
			$query .= " AND member_id IN (" . implode(',', $member_id) . ")";
			$this->_db->execute($query);
		}
	}

	/**
	 * Get asset completion count
	 * 
	 * @param      int $course_id
	 * @param      int $member_id
	 * @return     void
	 */
	public function getFormCompletionCount($course_id, $member_id=null)
	{
		$user = (!is_null($member_id)) ? "AND gb.member_id = {$member_id}" : '';
		$query   = "SELECT gb.member_id, ca.subtype, count(*) as count
					FROM $this->_tbl AS gb
					LEFT JOIN `#__courses_assets` ca ON gb.scope_id = ca.id
					WHERE scope='asset'
					AND ca.course_id = '{$course_id}'
					AND (score IS NOT NULL OR override IS NOT NULL)
					{$user}
					GROUP BY member_id, subtype";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get asset count
	 * 
	 * @return     void
	 */
	public function getFormCount()
	{
		$query   = "SELECT subtype, count(*) as count
					FROM `#__courses_assets`
					WHERE type = 'form'
					AND state = 1
					GROUP BY subtype;";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList('subtype');
	}
}