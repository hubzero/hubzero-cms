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

use Hubzero\Database\Table;

/**
 * Courses grade book table
 */
class GradeBook extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
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

		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$query .= " LEFT JOIN `#__courses_members` cm ON gb.member_id = cm.id";
		}

		$where = array();

		if (isset($filters['asset_id']) && $filters['asset_id'])
		{
			if (!is_array($filters['asset_id']))
			{
				$filters['asset_id'] = array($filters['asset_id']);
			}
			$where[] = "ca.id IN (" . implode(',', $filters['asset_id']) . ")";
		}
		if (isset($filters['member_id']) && $filters['member_id'])
		{
			if (!is_array($filters['member_id']))
			{
				$filters['member_id'] = array($filters['member_id']);
			}
			$where[] = "member_id IN (" . implode(',', $filters['member_id']) . ")";
		}
		if (isset($filters['scope']) && $filters['scope'])
		{
			if (!is_array($filters['scope']))
			{
				$filters['scope'] = array($filters['scope']);
			}
			$where[] = "gb.scope IN ('" . implode('\',\'', $filters['scope']) . "')";
		}
		if (isset($filters['graded']) && $filters['graded'])
		{
			$where[] = "ca.graded = '1'";
		}
		if (isset($filters['scope_id']) && $filters['scope_id'])
		{
			if (!is_array($filters['scope_id']))
			{
				$filters['scope_id'] = array($filters['scope_id']);
			}
			$where[] = "gb.scope_id IN (" . implode(',', $filters['scope_id']) . ")";
		}
		if (isset($filters['course_id']) && $filters['course_id'])
		{
			$where[] = "ca.course_id = " . $this->_db->quote($filters['course_id']);
		}
		if (isset($filters['section_id']) && $filters['section_id'])
		{
			$where[] = "cm.section_id = " . $this->_db->quote($filters['section_id']);
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
		$query = "SELECT gb.*, ca.grade_weight" . $this->_buildQuery($filters);

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
			if (!is_array($filters['member_id']))
			{
				$filters['member_id'] = array($filters['member_id']);
			}
			$where[] = "gb.member_id IN (" . implode(',', $filters['member_id']) . ")";
		}
		if (isset($filters['scope']) && $filters['scope'])
		{
			if (!is_array($filters['scope']))
			{
				$filters['scope'] = array($filters['scope']);
			}
			$where[] = "gb.scope IN ('" . implode('\',\'', $filters['scope']) . "')";
		}
		if (isset($filters['scope_id']) && $filters['scope_id'])
		{
			if (!is_array($filters['scope_id']))
			{
				$filters['scope_id'] = array($filters['scope_id']);
			}
			$where[] = "gb.scope_id IN (" . implode(',', $filters['scope_id']) . ")";
		}
		if (isset($filters['section_id']) && $filters['section_id'])
		{
			if (!is_array($filters['section_id']))
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
	 * Query to sync form scores with gradebook
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

		if (count($member_id) == 0)
		{
			return;
		}

		// Get the assets
		$asset  = new Asset($this->_db);
		$assets = $asset->find(
			array(
				'w' => array(
					'course_id'   => $course->get('id'),
					'section_id'  => $course->offering()->section()->get('id'),
					'offering_id' => $course->offering()->get('id'),
					'asset_type'  => 'form'
				)
			)
		);

		// Query for existing data
		$query = "SELECT * FROM `#__courses_grade_book` WHERE `member_id` IN (".implode(',', $member_id).") AND `scope` IN ('asset')";
		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		$existing_grades = array();
		foreach ($results as $r)
		{
			$existing_grades[$r->member_id.'.'.$r->scope_id] = array('id'=>$r->id, 'score'=>$r->score);
		}

		$inserts = array();
		$updates = array();
		$deletes = array();

		if (count($assets) > 0)
		{
			foreach ($assets as $asset)
			{
				// Add null values for unpublished forms that may have already been taken
				if ($asset->state != 1)
				{
					$deletes[] = $asset->id;

					continue;
				}

				$crumb = false;

				// Check for result for given student on form
				$crumb = $asset->url;

				if (!$crumb || strlen($crumb) != 20 || $asset->state != 1)
				{
					// Break foreach, this is not a valid form!
					continue;
				}

				include_once(dirname(__DIR__) . DS . 'models' . DS . 'formDeployment.php');
				$dep = \Components\Courses\Models\PdfFormDeployment::fromCrumb($crumb, $course->offering()->section()->get('id'));

				$results = $dep->getResults('member_id', $member_id);

				switch ($dep->getState())
				{
					// Form isn't available yet
					case 'pending':
						// Null value
						foreach ($member_id as $u)
						{
							$key = $u.'.'.$asset->id;
							if (!array_key_exists($key, $existing_grades))
							{
								$inserts[] = "('{$u}', NULL, 'asset', '{$asset->id}', NULL)";
							}
							else if (!is_null($existing_grades[$key]['score']))
							{
								$updates[] = "UPDATE `#__courses_grade_book` SET `score` = NULL WHERE `id` = '".$existing_grades[$key]['id']."'";
							}
						}
					break;

					// Form availability has expired - students either get a 0, or their score (no nulls)
					case 'expired':
						foreach ($member_id as $u)
						{
							$score    = (isset($results[$u]['score'])) ? $results[$u]['score'] : '0.00';
							$finished = (isset($results[$u]['finished'])) ? '\''.$results[$u]['finished'].'\'' : 'NULL';

							$key = $u.'.'.$asset->id;
							if (!array_key_exists($key, $existing_grades))
							{
								$inserts[] = "('{$u}', '{$score}', 'asset', '{$asset->id}', {$finished})";
							}
							else if ($existing_grades[$key]['score'] != $score)
							{
								$updates[] = "UPDATE `#__courses_grade_book` SET `score` = '{$score}', `score_recorded` = {$finished} WHERE `id` = '".$existing_grades[$key]['id']."'";
							}
						}
					break;

					// Form is still active - students either get their score, or a null
					case 'active':
						foreach ($member_id as $u)
						{
							$resp = $dep->getRespondent($u);

							// Form is active and they have completed it!
							if ($resp->getEndTime() && $resp->getEndTime() != '')
							{
								$score = (isset($results[$u]['score'])) ? '\''.$results[$u]['score'].'\'' : 'NULL';

								$key = $u.'.'.$asset->id;
								if (!array_key_exists($key, $existing_grades))
								{
									$inserts[] = "('{$u}', {$score}, 'asset', '{$asset->id}', '" . $results[$u]['finished'] . "')";
								}
								else if ($existing_grades[$key]['score'] != $score)
								{
									$updates[] = "UPDATE `#__courses_grade_book` SET `score` = {$score}, `score_recorded` = '" . $results[$u]['finished'] . "' WHERE `id` = '".$existing_grades[$key]['id']."'";
								}
							}
							// Form is active and they haven't finished it yet!
							else
							{
								$key = $u.'.'.$asset->id;
								if (!array_key_exists($key, $existing_grades))
								{
									$inserts[] = "('{$u}', NULL, 'asset', '{$asset->id}', NULL)";
								}
								else if (!is_null($existing_grades[$key]['score']))
								{
									$updates[] = "UPDATE `#__courses_grade_book` SET `score` = NULL, `score_recorded` = NULL WHERE `id` = '".$existing_grades[$key]['id']."'";
								}
							}
						}
					break;
				}
			}

			// Build query and run
			if (count($inserts) > 0)
			{
				$query  = "INSERT INTO `#__courses_grade_book` (`member_id`, `score`, `scope`, `scope_id`, `score_recorded`) VALUES\n";
				$query .= implode(",\n", $inserts);

				$this->_db->setQuery($query);
				$this->_db->query();
			}

			if (count($updates) > 0)
			{
				foreach ($updates as $update)
				{
					$query = $update;
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}

			if (count($deletes) > 0)
			{
				$query = "DELETE FROM `#__courses_grade_book` WHERE `scope` = 'asset' AND `scope_id` IN (".implode(',', $deletes).")";

				$this->_db->setQuery($query);
				$this->_db->query();
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
		$values          = array();
		$member_ids      = array();
		$existing_grades = array();

		if (!empty($data))
		{
			// Get member id's
			foreach ($data as $member_id => $member)
			{
				$member_ids[] = $member_id;
			}

			// Query for existing data
			$query = "SELECT * FROM `#__courses_grade_book` WHERE `member_id` IN (".implode(',', $member_ids).") AND `scope` IN ('course', 'unit')";
			$this->_db->setQuery($query);
			$results = $this->_db->loadObjectList();

			foreach ($results as $r)
			{
				$existing_grades[$r->member_id.'.'.$r->scope.'.'.$r->scope_id] = array('id'=>$r->id, 'score'=>$r->score);
			}
		}

		$inserts = array();
		$updates = array();

		foreach ($data as $member_id => $member)
		{
			foreach ($member['units'] as $unit_id => $unit)
			{
				// Check for empty unit_id
				// This is a hack for storing "extra" grades added via the gradebook - they come in with unit_id of NULL
				if (empty($unit_id))
				{
					$unit_id = 0;
				}

				if (is_numeric($unit['unit_weighted']))
				{
					if (array_key_exists($member_id.'.unit.'.$unit_id, $existing_grades))
					{
						$key = $member_id.'.unit.'.$unit_id;

						if ((is_null($existing_grades[$key]['score']) && !is_null($unit['unit_weighted'])) || $existing_grades[$key]['score'] != $unit['unit_weighted'])
						{
							$updates[] = "UPDATE `#__courses_grade_book` SET `score` = '{$unit['unit_weighted']}' WHERE `id` = '".$existing_grades[$key]['id']."'";
						}
					}
					else
					{
						$inserts[] = "('{$member_id}', " . $this->_db->quote($unit['unit_weighted']) . ", 'unit', '{$unit_id}')";
					}
				}
				else if (is_null($unit['unit_weighted']))
				{
					if (array_key_exists($member_id.'.unit.'.$unit_id, $existing_grades))
					{
						$key = $member_id.'.unit.'.$unit_id;

						if (!is_null($existing_grades[$key]['score']))
						{
							$updates[] = "UPDATE `#__courses_grade_book` SET `score` = NULL WHERE `id` = '".$existing_grades[$key]['id']."'";
						}
					}
					else
					{
						$inserts[] = "('{$member_id}', NULL, 'unit', '{$unit_id}')";
					}
				}
			}

			if (is_numeric($member['course_weighted']))
			{
				if (array_key_exists($member_id.'.course.'.$course_id, $existing_grades))
				{
					$key = $member_id.'.course.'.$course_id;

					if ((is_null($existing_grades[$key]['score']) && !is_null($member['course_weighted'])) || $existing_grades[$key]['score'] != $member['course_weighted'])
					{
						$updates[] = "UPDATE `#__courses_grade_book` SET `score` = '{$member['course_weighted']}' WHERE `id` = '".$existing_grades[$key]['id']."'";
					}
				}
				else
				{
					$inserts[] = "('{$member_id}', " . $this->_db->quote($member['course_weighted']) . ", 'course', '{$course_id}')";
				}
			}
			else if (is_null($member['course_weighted']))
			{
				if (array_key_exists($member_id.'.course.'.$course_id, $existing_grades))
				{
					$key = $member_id.'.course.'.$course_id;

					if (!is_null($existing_grades[$key]['score']))
					{
						$updates[] = "UPDATE `#__courses_grade_book` SET `score` = NULL WHERE `id` = '".$existing_grades[$key]['id']."'";
					}
				}
				else
				{
					$inserts[] = "('{$member_id}', NULL, 'course', '{$course_id}')";
				}
			}
		}

		if (count($updates) > 0)
		{
			foreach ($updates as $update)
			{
				$query = $update;
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}

		if (count($inserts) > 0)
		{
			$query  = "INSERT INTO `#__courses_grade_book` (`member_id`, `score`, `scope`, `scope_id`) VALUES\n";
			$query .= implode(",\n", $inserts);

			$this->_db->setQuery($query);
			$this->_db->query();
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
		$this->_db->setQuery($query);
		$this->_db->query();

		// Clean up units as well...
		foreach ($course->offering()->units() as $unit)
		{
			$query  = "UPDATE `#__courses_grade_book` SET score = NULL";
			$query .= " WHERE scope = 'unit' AND scope_id = " . $this->_db->quote($unit->get('id'));
			$query .= " AND member_id IN (" . implode(',', $member_id) . ")";
			$this->_db->setQuery($query);
			$this->_db->query();
		}
	}

	/**
	 * Clear units if they once had a grade but should no longer
	 *
	 * @param      array $data - info to process
	 * @return     void
	 */
	public function clearUnits($data)
	{
		if (is_array($data) && count($data) > 0)
		{
			foreach ($data as $unit_id => $members)
			{
				if (is_array($members) && count($members) > 0)
				{
					$query  = "UPDATE `#__courses_grade_book` SET score = NULL";
					$query .= " WHERE scope = 'unit' AND scope_id = " . $this->_db->quote($unit_id);
					$query .= " AND member_id IN (" . implode(',', $members) . ")";
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
	}
}
