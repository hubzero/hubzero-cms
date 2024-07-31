<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Hubzero\Config\Registry;
use Components\Courses\Models\Assets\Handler;
use Components\Courses\Models\Assetgroup;
use Components\Courses\Models\Assets\Tool;
use App;
use Request;
use Date;
use stdClass;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetgroup.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assets' . DS . 'tool.php';

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetgroup.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'unit.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'offering.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'course.php';

/**
 * API controller for the course asset groups
 */
class Assetgroupv1_0 extends base
{
	/**
	 * Saves an asset group
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/assetgroup/save
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Asset group ID to edit",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Asset group title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "New asset group"
	 * }
	 * @apiParameter {
	 * 		"name":        "state",
	 * 		"description": "State of asset group",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "description",
	 * 		"description": "Short description",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "unit_id",
	 * 		"description": "ID of parent unit",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "parent",
	 * 		"description": "ID of parent asset group",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "params",
	 * 		"description": "Parameters related to the asset group",
	 * 		"type":        "array",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function saveTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Check for an incoming 'id'
		$id = Request::getInt('id', null);

		// Create an asset group instance
		$assetGroup = new Assetgroup($id);

		// Check to make sure we have an asset group object
		if (!is_object($assetGroup))
		{
			App::abort(500, 'Failed to create an asset group object');
		}

		// We'll always save the title again, even if it's just to the same thing
		$title = $assetGroup->get('title');
		$title = (!empty($title)) ? $title : 'New asset group';

		// Set our variables
		$assetGroup->set('title', Request::getString('title', $title));
		$assetGroup->set('alias', strtolower(str_replace(' ', '', $assetGroup->get('title'))));

		// Save the asset group
		if (!$assetGroup->get('title'))
		{
			App::abort(400, 'No title provided');
		}

		$state = Request::getInt('state', null);
		if (!is_null($state))
		{
			$assetGroup->set('state', $state);
		}

		$assetGroup->set('description', Request::getString('description', $assetGroup->get('description')));

		// When creating a new asset group
		if (!$id)
		{
			$assetGroup->set('unit_id', Request::getInt('unit_id', 0));
			$assetGroup->set('parent', Request::getInt('parent', 0));
			$assetGroup->set('created', Date::toSql());
			$assetGroup->set('created_by', App::get('authn')['user_id']);
		}

		if (($params = Request::getVar('params', false, 'post')) || !$id)
		{
			$p     = new Registry('');
			$db    = App::get('db');
			$query = $db->getQuery(true);

			$query->select('folder AS type, element AS name, params')
			      ->from('#__extensions')
			      ->where('enabled >= 1')
			      ->where('type =' . $db->quote('plugin'))
			      ->where('state >= 0')
			      ->where('folder =' . $db->quote('courses'))
			      ->order('ordering');

			if ($plugins = $db->setQuery($query)->loadObjectList())
			{
				foreach ($plugins as $plugin)
				{
					$default = new Registry($plugin->params);
					foreach ($default->toArray() as $k => $v)
					{
						if (substr($k, 0, strlen('default_')) == 'default_')
						{
							$p->set(substr($k, strlen('default_')), $default->get($k, $v));
						}
					}
				}
			}

			if ($params)
			{
				$p->parse($params);
			}

			$assetGroup->set('params', $p->toString());
		}

		// Save the asset group
		if (!$assetGroup->store())
		{
			App::abort(500, 'Asset group save failed');
		}

		// Return message
		$this->send(
			[
				'assetgroup_id'    => $assetGroup->get('id'),
				'assetgroup_title' => $assetGroup->get('title'),
				'assetgroup_state' => (int) $assetGroup->get('state'),
				'assetgroup_style' => 'display:none',
				'course_id'        => $this->course_id,
				'offering_alias'   => $this->offering_alias,
				'allow_tools'      => Tool::getToolDirectory()
			], ($id ? 200 : 201)
		);
	}

	/**
	 * Reorders asset groups
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/assetgroup/reorder
	 * @apiParameter {
	 * 		"name":        "assetgroupitem",
	 * 		"description": "Asset group items, in desired order",
	 * 		"type":        "array",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function reorderTask()
	{
		$groups = Request::getArray('assetgroupitem', []);

		$order = 1;

		foreach ($groups as $id)
		{
			if (!$assetGroup = new Assetgroup($id))
			{
				App::abort(500, 'Loading asset group {$id} failed');
			}

			// Set the new order
			$assetGroup->set('ordering', $order);

			// Save the asset group
			if (!$assetGroup->store())
			{
				App::abort(500, 'Asset group save failed');
			}

			$order++;
		}


		// Return message
		$this->send('New order saved');
	}


	// Creating that hierachy with an API
	// https://stage.stemedhub.org/api/courses/assetgroup/getAllCourses
	// COURSES - LEVEL 1
	public function getAllCoursesTask(){
		$database = \App::get('db');
		$query = "SELECT * FROM `#__courses`";

        $database->setQuery($query);
        $rows = $database->loadObjectList();

        $object = new stdClass();
        $object->courses = $rows;

        $this->send($object);
	}

	// COURSE OFFERING - LEVEL 2
	// https://stage.stemedhub.org/api/courses/assetgroup/getAllCourseOfferings?courseId=8
	public function getAllCourseOfferingsTask(){
		$courseId = Request::getInt('courseId', false);

		// Conditional Checks
		if (!$courseId) {
			throw new Exception("Course Id is required");
		}

		$database = \App::get('db');
		$query = "SELECT * FROM `#__courses_offerings` WHERE `course_id` = " . $courseId;

        $database->setQuery($query);
        $rows = $database->loadObjectList();

        $object = new stdClass();
        $object->course_offerings = $rows;

        $this->send($object);
	}

	// COURSE UNITS - LEVEL 3
	// https://stage.stemedhub.org/api/courses/assetgroup/getAllCourseUnits?offeringId=8
	public function getAllCourseUnitsTask(){
		$offeringId = Request::getInt('offeringId', false);

		// Conditional Checks
		if (!$offeringId) {
			throw new Exception("Offering Id is required");
		}

		$database = \App::get('db');
		$query = "SELECT * FROM `#__courses_units` WHERE `offering_id` = " . $offeringId;

        $database->setQuery($query);
        $rows = $database->loadObjectList();

        $object = new stdClass();
        $object->course_units = $rows;

        $this->send($object);
	}

	// ASSET GROUPS - LEVEL 4
	// https://stage.stemedhub.org/api/courses/assetgroup/getAllAssetGroups?unitId=18
	// https://stackoverflow.com/questions/21021863/creating-a-nested-array-form-a-flat-array-trough-parent-id
	public function getAllAssetGroupsTask(){
		$unitId = Request::getInt('unitId', false);

		// Conditional Checks
		if (!$unitId) {
			throw new Exception("Unit Id is required");
		}

		$database = \App::get('db');
		$query = "SELECT * FROM `#__courses_asset_groups` WHERE `unit_id` = " . $unitId;

		// Recursive
        $database->setQuery($query);
        $rows = $database->loadObjectList();

		$jsonArray = array();
        foreach ($rows as $v) {
			// Solve Error "Cannot use object of type stdClass as array"
            $assetGroupArray = (array) json_decode(json_encode($v),true);
            $jsonArray[] = $assetGroupArray;
        }

		$tree = $this->convertToTree($jsonArray);

		$object = new stdClass();
        $object->asset_groups = $tree;

        $this->send($object);
	}

	// This is a recursive function, needed to happen because levels can vary with depth and location of nodes, it's a tree.
    public function convertToTree($flat, $idField = 'id', $parentIdField = 'parent', $childNodesField = 'child', $curIdx = 0) {
        $indexed = array();

        foreach($flat as $row) {
            if ($row[$parentIdField] == $curIdx) {
                $indexed[$row[$idField]] = $row;
                $indexed[$row[$idField]]["child"] = $this->convertToTree($flat, $idField, $parentIdField, $childNodesField, $row[$idField]);
            }
        }

        return $indexed;
    }
}
