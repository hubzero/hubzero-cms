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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'gradebook.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'prerequisite.php');

/**
 * Courses model class for prerequisites
 */
class CoursesModelPrerequisite extends CoursesModelAbstract
{
	/**
	 * JTable class name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'CoursesTablePrerequisites';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'prerequisite';

	/**
	 * Track the member id for whom the prereqs apply
	 *
	 * @var int
	 **/
	protected $member_id = null;

	/**
	 * Store the prereqs themselves
	 *
	 * @var array
	 **/
	protected $prerequisites = array();

	/**
	 * Unit progress
	 *
	 * @var array
	 **/
	protected $progress = null;

	/**
	 * Asset views
	 *
	 * @var array
	 **/
	protected $views = null;

	/**
	 * Grades
	 *
	 * @var array
	 **/
	protected $grades = null;

	/**
	 * Constructor
	 *
	 * @param  (int) $section_id
	 * @param  (obj) $gradebook
	 * @param  (int) $member_id
	 * @return void
	 */
	public function __construct($section_id, $gradebook, $member_id)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new $this->_tbl_name($this->_db);

		// Set vars
		$this->member_id = $member_id;
		$prerequisites   = $this->_tbl->loadAllBySectionId($section_id);

		$this->prerequisites = array();
		foreach ($prerequisites as $prerequisite)
		{
			$key = $prerequisite->item_scope.'.'.$prerequisite->item_id;
			$this->prerequisites[$key][] = array(
				'scope'    => $prerequisite->requisite_scope,
				'scope_id' => $prerequisite->requisite_id
			);
		}

		if (!isset($member_id))
		{
			return false;
		}

		$this->progress = $gradebook->progress($member_id);
		$this->views    = $gradebook->views($member_id);
		$grades         = $gradebook->_tbl->find(array('member_id'=>$member_id, 'scope'=>'asset'));

		if ($grades && count($grades) > 0)
		{
			$this->grades = array();
			foreach ($grades as $grade)
			{
				$this->grades[$grade->scope_id] = (!is_null($grade->override)) ? $grade->override : $grade->score;
			}
		}
	}

	/**
	 * Get prerequisites
	 *
	 * @return array
	 **/
	public function get($scope, $scope_id=null)
	{
		return (isset($this->prerequisites[$scope.'.'.$scope_id])) ? $this->prerequisites[$scope.'.'.$scope_id] : false;
	}

	/**
	 * See if item prerequisite has been fulfilled
	 *
	 * @TODO: For now, we're going to place all of the logic here for checking
	 * whether or not different types of items have been fulfilled.
	 * Eventually this should be abstracted out elsewhere.
	 *
	 * @return bool
	 **/
	public function hasMet($scope, $scope_id)
	{
		$return = true;

		switch ($scope)
		{
			case 'unit':
				$key = $scope.'.'.$scope_id;

				if (isset($this->prerequisites[$key]) && count($this->prerequisites[$key]) > 0)
				{
					foreach ($this->prerequisites[$key] as $prerequisite)
					{
						if (!isset($this->progress[$this->member_id])
							|| !isset($this->progress[$this->member_id][$prerequisite['scope_id']])
							|| $this->progress[$this->member_id][$prerequisite['scope_id']]['percentage_complete'] != 100)
						{
							$return = false;
							continue;
						}
					}
				}

				break;

			case 'asset':
				$key = $scope.'.'.$scope_id;

				if (isset($this->prerequisites[$key]) && count($this->prerequisites[$key]) > 0)
				{
					foreach ($this->prerequisites[$key] as $prerequisite)
					{
						$asset = new CoursesModelAsset($prerequisite['scope_id']);

						switch ($asset->get('type'))
						{
							case 'form':
								if (!isset($this->grades[$prerequisite['scope_id']]))
								{
									$return = false;
									continue;
								}
								break;

							default:
								if (!isset($this->views[$this->member_id])
								 || !is_array($this->views[$this->member_id])
								 || !in_array($prerequisite['scope_id'], $this->views[$this->member_id]))
								{
									$return = false;
									continue;
								}
								break;
						}
					}
				}
				break;
		}

		return $return;
	}
}