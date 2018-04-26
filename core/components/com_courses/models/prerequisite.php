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

namespace Components\Courses\Models;

use Components\Courses\Tables;

require_once(__DIR__ . DS . 'base.php');
require_once(__DIR__ . DS . 'gradebook.php');
require_once(__DIR__ . DS . 'asset.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'prerequisite.php');

/**
 * Courses model class for prerequisites
 */
class Prerequisite extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Prerequisites';

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
		$this->_db = \App::get('db');

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
						$asset = new Asset($prerequisite['scope_id']);

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
