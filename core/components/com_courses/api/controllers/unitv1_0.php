<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Courses\Api\Controllers;

use Components\Courses\Models\Assetgroup;
use Components\Courses\Models\Unit;
use App;
use Config;
use Request;
use Date;
use Component;
use stdClass;

require_once __DIR__ . DS . 'base.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'unit.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'assetgroup.php';

/**
 * API controller for the course units
 */
class Unitv1_0 extends base
{
	/**
	 * Saves a course unit
	 *
	 * @apiMethod POST
	 * @apiUri    /courses/unit/save
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Unit ID to edit",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "section_id",
	 * 		"description": "Section ID of unit",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "offering_id",
	 * 		"description": "Offering ID of unit",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "title",
	 * 		"description": "Unit title",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "New Unit"
	 * }
	 * @apiParameter {
	 * 		"name":        "publish_up",
	 * 		"description": "Start publishing date",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "publish_down",
	 * 		"description": "Stop publishing date",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @return    void
	 */
	public function saveTask()
	{
		// Require authentication and authorization
		$this->authorizeOrFail();

		// Make sure we have an incoming 'id'
		$id = Request::getInt('id', null);

		// Create our unit model
		$unit = Unit::getInstance($id);

		// Check to make sure we have a unit object
		if (!is_object($unit))
		{
			App::abort(500, 'Failed to instantiate a unit object');
		}

		if ($section_id = Request::getInt('section_id', false))
		{
			$unit->set('section_id', $section_id);
		}

		// We'll always save the title again, even if it's just to the same thing
		$title = $unit->get('title');
		$title = (!empty($title)) ? $title : 'New Unit';

		// Set our values
		$unit->set('title', Request::getString('title', $title));
		$unit->set('alias', strtolower(str_replace(' ', '', $unit->get('title'))));

		$offset = Config::get('offset');

		// If we have dates coming in, save those
		if ($publish_up = Request::getVar('publish_up', false))
		{
			$unit->set('publish_up', Date::of($publish_up, $offset)->toSql());
		}
		if ($publish_down = Request::getVar('publish_down', false))
		{
			$unit->set('publish_down', Date::of($publish_down, $offset)->toSql());
		}

		// When creating a new unit
		if (!$id)
		{
			$unit->set('offering_id', Request::getInt('offering_id', 0));
			$unit->set('created', Date::toSql());
			$unit->set('created_by', App::get('authn')['user_id']);
		}

		// Save the unit
		if (!$unit->store())
		{
			App::abort(500, "Saving unit {$id} failed ({$unit->getError()})");
		}

		// Create a placeholder for our return object
		$assetGroups = [];

		// If this is a new unit, give it some default asset groups
		// Create a top level asset group for each of lectures, homework, and exam
		if (!$id)
		{
			// Get the courses config
			$config = Component::params('com_courses');
			$asset_groups = explode(',', $config->get('default_asset_groups', 'Lectures, Homework, Exam'));
			array_map('trim', $asset_groups);

			foreach ($asset_groups as $key)
			{
				// Get our asset group object
				$assetGroup = new Assetgroup(null);

				$assetGroup->set('title', $key);
				$assetGroup->set('alias', strtolower(str_replace(' ', '', $assetGroup->get('title'))));
				$assetGroup->set('unit_id', $unit->get('id'));
				$assetGroup->set('parent', 0);
				$assetGroup->set('created', Date::toSql());
				$assetGroup->set('created_by', App::get('authn')['user_id']);

				// Save the asset group
				if (!$assetGroup->store())
				{
					App::abort(500, 'Asset group save failed');
				}

				$return = new stdclass();
				$return->assetgroup_id    = $assetGroup->get('id');
				$return->assetgroup_title = $assetGroup->get('title');
				$return->course_id        = $this->course_id;
				$return->assetgroup_style = '';

				$assetGroups[] = $return;
			}
		}

		// Need to return the content of the prerequisites view (not sure of a better way to do this at the moment)
		// @FIXME: need to handle this another way...shouldn't be loading up views from API!
		/*$view = new \Hubzero\Plugin\View(array(
			'folder'  => 'courses',
			'element' => 'outline',
			'name'    => 'outline',
			'layout'  => '_prerequisites'
		));

		$view->set('scope', 'unit')
		     ->set('scope_id', $unit->get('id'))
		     ->set('section_id', $this->course->offering()->section()->get('id'))
		     ->set('items', clone($this->course->offering()->units()));*/

		// Return message
		$this->send(
			[
				'unit_id'        => $unit->get('id'),
				'unit_title'     => $unit->get('title'),
				'course_id'      => $this->course_id,
				'assetgroups'    => $assetGroups,
				'course_alias'   => $this->course->get('alias'),
				'offering_alias' => $this->offering_alias,
				'section_id'     => (isset($section_id) ? $section_id : $this->course->offering()->section()->get('id')),
				'prerequisites'  => ''//$view->loadTemplate()
			], ($id ? 200 : 201)
		);
	}
}