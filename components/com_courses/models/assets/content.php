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
		require_once JPATH_ROOT . DS . 'components'    . DS . 'com_courses' . DS . 'models'      . DS . 'asset.php';
		require_once JPATH_ROOT . DS . 'administrator' . DS . 'components'  . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php';

		// Create our asset table object
		$asset = new CoursesModelAsset();

		// Grab the incoming content
		$content = JRequest::getVar('content', '', 'default', 'none', 2);

		// Get everything ready to store
		// Check if vars are already set (i.e. by a sub class), before setting them here
		$asset->set('title',        ((!empty($this->asset['title']))        ? $this->asset['title']        : strip_tags(substr($content, 0, 25))));
		$asset->set('type',         ((!empty($this->asset['type']))         ? $this->asset['type']         : 'text'));
		$asset->set('subtype',      ((!empty($this->asset['subtype']))      ? $this->asset['subtype']      : 'content'));
		$asset->set('content',      ((!empty($this->asset['content']))      ? $this->asset['content']      : $content));
		$asset->set('url',          ((!empty($this->asset['url']))          ? $this->asset['url']          : ''));
		$asset->set('graded',       ((!empty($this->asset['graded']))       ? $this->asset['graded']       : 0));
		$asset->set('grade_weight', ((!empty($this->asset['grade_weight'])) ? $this->asset['grade_weight'] : ''));
		$asset->set('created',      JFactory::getDate()->toSql());
		$asset->set('created_by',   JFactory::getApplication()->getAuthn('user_id'));
		$asset->set('course_id',    JRequest::getInt('course_id', 0));
		$asset->set('state',        0);

		// Check whether asset should be graded
		if ($graded = JRequest::getInt('graded', false))
		{
			$asset->set('graded', $graded);
			$asset->set('grade_weight', 'homework');
		}

		// Save the asset
		if (!$asset->store())
		{
			return array('error' => 'Asset save failed');
		}

		// If we're saving progress calculation var
		if ($progress = JRequest::getInt('progress_factors', false))
		{
			$asset->set('progress_factors', array('asset_id'=>$asset->get('id'), 'section_id'=>JRequest::getInt('section_id', 0)));
			$asset->store();
		}

		// Create asset assoc object
		$assocObj = new CoursesTableAssetAssociation($this->db);

		$this->assoc['asset_id'] = $asset->get('id');
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
		$course->offering($offering_alias);

		$url = JRoute::_($course->offering()->link() . '&asset=' . $asset->get('id'));

		$files = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'asset_state'    => $asset->get('state'),
			'scope_id'       => $this->assoc['scope_id']
		);

		$return_info = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'course_id'      => $asset->get('course_id'),
			'offering_alias' => $offering_alias,
			'scope_id'       => $this->assoc['scope_id'],
			'files'          => array($files)
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
		require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php';

		// Create our asset object
		$id    = JRequest::getInt('id', null);
		$asset = new CoursesModelAsset($id);

		// Grab the incoming content
		$content = JRequest::getVar('content', '', 'default', 'none', 2);

		// Get everything ready to store
		// Check if vars are already set (i.e. by a sub class), before setting them here
		$asset->set('title',   ((!empty($this->asset['title']))   ? $this->asset['title']   : strip_tags(substr($content, 0, 25))));
		$asset->set('type',    ((!empty($this->asset['type']))    ? $this->asset['type']    : 'text'));
		$asset->set('subtype', ((!empty($this->asset['subtype'])) ? $this->asset['subtype'] : 'content'));
		$asset->set('content', ((!empty($this->asset['content'])) ? $this->asset['content'] : $content));

		// If we have a state coming in as an int
		if ($graded = JRequest::getInt('graded', false))
		{
			$asset->set('graded', $graded);
			// By default, weight asset as a 'homework' type
			$grade_weight = $asset->get('grade_weight');
			if (empty($grade_weight))
			{
				$asset->set('grade_weight', 'homework');
			}
			else
			{
				$asset->set('grade_weight', $grade_weight);
			}
		}
		elseif ($graded = JRequest::getInt('edit_graded', false))
		{
			$asset->set('graded', 0);
		}

		// If we're saving progress calculation var
		if ($progress = JRequest::getInt('progress_factors', false))
		{
			$asset->set('progress_factors', array('asset_id'=>$asset->get('id'), 'section_id'=>JRequest::getInt('section_id', 0)));
		}
		elseif (JRequest::getInt('edit_progress_factors', false))
		{
			$asset->set('section_id', JRequest::getInt('section_id', 0));
			$asset->set('progress_factors', 'delete');
		}

		// Save the asset
		if (!$asset->store())
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
			require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_courses' . DS . 'tables' . DS . 'asset.association.php';
			$assoc = new CoursesTableAssetAssociation($this->db);

			if (!$assoc->loadByAssetScope($asset->get('id'), $original_scope_id, $scope))
			{
				return array('error' => 'Failed to load asset association');
			}

			// Save the asset association
			if (!$assoc->save(array('scope_id'=>$scope_id)))
			{
				return array('error' => 'Asset association save failed');
			}
		}

		// Get the url to return to the page
		$course_id      = JRequest::getInt('course_id', 0);
		$offering_alias = JRequest::getCmd('offering', '');
		$course         = new CoursesModelCourse($course_id);
		$course->offering($offering_alias);

		$url = JRoute::_($course->offering()->link() . '&asset=' . $asset->get('id'));

		$files = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'asset_state'    => $asset->get('state'),
			'scope_id'       => $scope_id,
		);

		$return_info = array(
			'asset_id'       => $asset->get('id'),
			'asset_title'    => $asset->get('title'),
			'asset_type'     => $asset->get('type'),
			'asset_subtype'  => $asset->get('subtype'),
			'asset_url'      => $url,
			'course_id'      => $asset->get('course_id'),
			'offering_alias' => $offering_alias,
			'scope_id'       => $scope_id,
			'files'          => array($files)
		);

		// Return info
		return array('assets' => $return_info);
	}
}