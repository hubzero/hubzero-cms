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
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Content based asset handler (i.e. things like notes, wiki, html, etc...)
*/
class ContentAssetHandler extends AssetHandler
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
			'action_message' => 'As textual content',
			'responds_to'    => array('text')
		);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		// Include needed files
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
		require_once(JPATH_ROOT . DS . 'components'    . DS . 'com_courses' . DS . 'models'      . DS . 'asset.php');

		// Create our asset table object
		$assetObj = new CoursesTableAsset($this->db);

		// Grab the incoming content
		$content = JRequest::getVar('content', '', 'default', 'none', 2);

		// Get everything ready to store
		// Check if vars are already set (i.e. by a sub class), before setting them here
		$this->asset['title']      = (!empty($this->asset['title']))   ? $this->asset['title']   : substr($content, 0, 25);
		$this->asset['type']       = (!empty($this->asset['type']))    ? $this->asset['type']    : 'text';
		$this->asset['subtype']    = (!empty($this->asset['subtype'])) ? $this->asset['subtype'] : 'content';
		$this->asset['content']    = (!empty($this->asset['content'])) ? $this->asset['content'] : $content;
		$this->asset['created']    = JFactory::getDate()->toSql();
		$this->asset['created_by'] = JFactory::getApplication()->getAuthn('user_id');
		$this->asset['course_id']  = JRequest::getInt('course_id', 0);

		// Check whether asset should be graded
		if ($graded = JRequest::getInt('graded', false))
		{
			$this->asset['graded']       = $graded;
			$this->asset['grade_weight'] = 'homework';
		}

		// Save the asset
		if (!$assetObj->save($this->asset))
		{
			return array('error' => 'Asset save failed');
		}

		// Create asset assoc object
		$assocObj = new CoursesTableAssetAssociation($this->db);

		$this->assoc['asset_id'] = $assetObj->get('id');
		$this->assoc['scope']    = JRequest::getCmd('scope', 'asset_group');
		$this->assoc['scope_id'] = JRequest::getInt('scope_id', 0);

		// Save the asset association
		if (!$assocObj->save($this->assoc))
		{
			return array('error' => 'Asset association save failed');
		}

		// Get the url to return to the page
		$course_id      = JRequest::getInt('course_id', 0);
		$offering_alias = JRequest::getCmd('offering', '');
		$course         = new CoursesModelCourse($course_id);

		$url = JRoute::_('index.php?option=com_courses&controller=offering&gid='.$course->get('alias').'&offering='.$offering_alias.'&asset='.$assetObj->get('id'));

		$return_info = array(
			'asset_id'       => $this->assoc['asset_id'],
			'asset_title'    => $this->asset['title'],
			'asset_type'     => $this->asset['type'],
			'asset_subtype'  => $this->asset['subtype'],
			'asset_url'      => $url,
			'course_id'      => $this->asset['course_id'],
			'offering_alias' => JRequest::getCmd('offering', ''),
			'scope_id'       => $this->assoc['scope_id']
		);

		// Return info
		return array('assets' => $return_info);
	}

	/**
	 * Save method for this handler
	 * // @FIXME: reduce code duplication here
	 *
	 * @return array of assets created
	 **/
	public function save()
	{
		// Include needed files
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php');
		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.php');
		require_once(JPATH_ROOT . DS . 'components'    . DS . 'com_courses' . DS . 'models'      . DS . 'asset.php');

		// Create our asset table object
		$assetObj = new CoursesTableAsset($this->db);
		$assetObj->load(JRequest::getInt('id'));

		// Grab the incoming content
		$content = JRequest::getVar('content', '', 'default', 'none', 2);

		// Get everything ready to store
		// Check if vars are already set (i.e. by a sub class), before setting them here
		$this->asset['title']      = (!empty($this->asset['title']))   ? $this->asset['title']   : strip_tags(substr($content, 0, 25));
		$this->asset['type']       = (!empty($this->asset['type']))    ? $this->asset['type']    : 'text';
		$this->asset['subtype']    = (!empty($this->asset['subtype'])) ? $this->asset['subtype'] : 'content';
		$this->asset['content']    = (!empty($this->asset['content'])) ? $this->asset['content'] : $content;
		$this->asset['created']    = $assetObj->created;
		$this->asset['created_by'] = $assetObj->created_by;
		$this->asset['course_id']  = $assetObj->course_id;
		$this->asset['state']      = $assetObj->state;

		// If we have a state coming in as an int
		if ($graded = JRequest::getInt('graded', false))
		{
			$this->asset['graded'] = $graded;
			// By default, weight asset as a 'homework' type
			$grade_weight = $assetObj->grade_weight;
			if (empty($grade_weight))
			{
				$this->asset['grade_weight'] = 'homework';
			}
			else
			{
				$this->asset['grade_weight'] = $grade_weight;
			}
		}
		elseif ($graded = JRequest::getInt('edit_graded', false))
		{
			$this->asset['graded'] = 0;
			$this->asset['grade_weight'] = $assetObj->grade_weight;
		}

		// Save the asset
		if (!$assetObj->save($this->asset))
		{
			return array('error' => 'Asset save failed');
		}

		$scope_id          = JRequest::getInt('scope_id', null);
		$original_scope_id = JRequest::getInt('original_scope_id', null);
		$scope             = JRequest::getCmd('scope', 'asset_group');

		// Only worry about this if scope id is changing
		if (!is_null($scope_id) && !is_null($original_scope_id) && $scope_id != $original_scope_id)
		{
			// Create asset assoc object
			$assocObj = new CoursesTableAssetAssociation($this->db);

			if (!$assocObj->loadByAssetScope($assetObj->id, $original_scope_id, $scope))
			{
				return array('error' => 'Failed to load asset association');
			}

			// Set new scope id
			$row->scope_id  = $scope_id;

			// Save the asset association
			if (!$assocObj->save($row))
			{
				return array('error' => 'Asset association save failed');
			}
		}

		// Get the url to return to the page
		$course_id      = JRequest::getInt('course_id', 0);
		$offering_alias = JRequest::getCmd('offering', '');
		$course         = new CoursesModelCourse($course_id);

		$url = JRoute::_('index.php?option=com_courses&controller=offering&gid='.$course->get('alias').'&offering='.$offering_alias.'&asset='.$assetObj->get('id'));

		$return_info = array(
			'asset_id'       => (int) $this->assoc['asset_id'],
			'asset_title'    => $this->asset['title'],
			'asset_type'     => $this->asset['type'],
			'asset_subtype'  => $this->asset['subtype'],
			'asset_url'      => $url,
			'course_id'      => $this->asset['course_id'],
			'offering_alias' => $offering_alias,
			'scope_id'       => $this->assoc['scope_id']
		);

		// Return info
		return array('assets' => $return_info);
	}
}