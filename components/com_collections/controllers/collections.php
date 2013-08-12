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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for bulletin boards
 */
class CollectionsControllerCollections extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->_authorize('collection');
		$this->_authorize('item');

		$this->dateFormat = '%d %b %Y';
		$this->timeFormat = '%I:%M %p';
		$this->tz = 0;
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$this->dateFormat = 'd M Y';
			$this->timeFormat = 'H:i p';
			$this->tz = true;
		}

		$this->registerTask('__default', 'recent');
		$this->registerTask('posts', 'recent');
		$this->registerTask('all', 'collections');

		parent::execute();
	}

	/**
	 * Display a list of latest whiteboard entries
	 * 
	 * @return     string
	 */
	/*public function popularTask()
	{
		$this->view->setLayout('posts');

		$this->view->dateFormat  = $this->dateFormat;
		$this->view->timeFormat  = $this->timeFormat;
		$this->view->tz          = $this->tz;
		$this->view->config     = $this->config;

		$this->_getStyles();

		$this->_getScripts('assets/js/jquery.masonry');
		$this->_getScripts('assets/js/jquery.infinitescroll');
		$this->_getScripts('assets/js/' . $this->_name);

		// Filters for returning results
		$this->view->filters = array();
		$this->view->filters['limit']  = JRequest::getInt('limit', 25);
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['id']     = JRequest::getInt('id', 0);
		$this->view->filters['user_id'] = $this->juser->get('id');
		$this->view->filters['sort']   = 'i.positive';
		$this->view->filters['state']   = 1;
		//$this->view->filters['trending'] = true;
		//$this->view->filters['board_id'] = 0;
		if ($this->view->filters['id'])
		{
			$this->view->filters['object_type'] = 'site';
		}
		$this->view->filters['access'] = 0;

		$this->view->collection = new CollectionsModelCollection();

		$this->view->filters['count'] = true;
		$this->view->total = $this->view->collection->posts($this->view->filters);

		$this->view->filters['count'] = false;
		$this->view->rows = $this->view->collection->posts($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}*/

	/**
	 * Display a list of latest whiteboard entries
	 * 
	 * @return     string
	 */
	public function recentTask()
	{
		$this->view->setLayout('posts');

		$this->view->dateFormat  = $this->dateFormat;
		$this->view->timeFormat  = $this->timeFormat;
		$this->view->tz          = $this->tz;
		$this->view->config     = $this->config;

		$this->_getStyles();

		$this->_getScripts('assets/js/jquery.masonry');
		$this->_getScripts('assets/js/jquery.infinitescroll');
		$this->_getScripts('assets/js/' . $this->_name);

		// Filters for returning results
		$this->view->filters = array();
		$this->view->filters['limit']  = JRequest::getInt('limit', 25);
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['id']     = JRequest::getInt('id', 0);
		$this->view->filters['user_id'] = $this->juser->get('id');
		$this->view->filters['sort']   = 'p.created';
		$this->view->filters['state']   = 1;
		$this->view->filters['is_default'] = 0;
		//$this->view->filters['trending'] = true;
		//$this->view->filters['board_id'] = 0;
		if ($this->view->filters['id'])
		{
			$this->view->filters['object_type'] = 'site';
		}
		$this->view->filters['access'] = 0;

		$this->view->collection = new CollectionsModelCollection();

		$this->view->filters['count'] = true;
		$this->view->total = $this->view->collection->posts($this->view->filters);

		$this->view->filters['count'] = false;
		$this->view->rows = $this->view->collection->posts($this->view->filters);

		$model = CollectionsModel::getInstance();

		$this->view->collections = $model->collections(array('count' => true, 'access' => 0, 'state' => 1, 'is_default' => 0));

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		$this->_buildTitle();
		$this->_buildPathway();

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Display a list of collections
	 * 
	 * @return     string
	 */
	public function collectionsTask()
	{
		$this->view->setLayout('collections');

		$this->view->dateFormat = $this->dateFormat;
		$this->view->timeFormat = $this->timeFormat;
		$this->view->tz         = $this->tz;
		$this->view->config     = $this->config;

		$this->_getStyles();

		$this->_getScripts('assets/js/jquery.masonry');
		$this->_getScripts('assets/js/jquery.infinitescroll');
		$this->_getScripts('assets/js/' . $this->_name);

		// Filters for returning results
		$this->view->filters = array();
		$this->view->filters['limit']  = JRequest::getInt('limit', 25);
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['id']     = JRequest::getInt('id', 0);
		$this->view->filters['user_id'] = $this->juser->get('id');
		//$this->view->filters['sort']   = 'p.created';
		$this->view->filters['state']   = 1;
		$this->view->filters['is_default'] = 0;
		//$this->view->filters['board_id'] = 0;
		if ($this->view->filters['id'])
		{
			$this->view->filters['object_type'] = 'site';
		}
		$this->view->filters['access'] = 0;

		$model = CollectionsModel::getInstance();

		$this->view->filters['count'] = true;
		$this->view->total = $model->collections($this->view->filters);

		$this->view->posts = $model->posts($this->view->filters);

		$this->view->filters['count'] = false;
		$this->view->rows = $model->collections($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		$this->_buildTitle();
		$this->_buildPathway();

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Display a form for creating an entry
	 * 
	 * @return     string
	 */
	public function aboutTask()
	{
		$this->view->setLayout('about');

		$this->_getStyles();

		// Filters for returning results
		$this->view->filters = array();
		$this->view->filters['id']      = JRequest::getInt('id', 0);
		$this->view->filters['user_id'] = $this->juser->get('id');
		$this->view->filters['search'] = JRequest::getVar('search', '');
		$this->view->filters['sort']    = 'p.created';
		$this->view->filters['state']   = 1;
		$this->view->filters['is_default'] = 0;
		if ($this->view->filters['id'])
		{
			$this->view->filters['object_type'] = 'site';
		}
		$this->view->filters['access'] = 0;

		$this->view->collection = new CollectionsModelCollection();

		$this->view->filters['count'] = true;
		$this->view->total = $this->view->collection->posts($this->view->filters);

		$model = CollectionsModel::getInstance();

		$this->view->collections = $model->collections(array('count' => true, 'access' => 0, 'state' => 1, 'is_default' => 0));

		$this->_buildTitle();
		$this->_buildPathway();

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Set the authorization level for the user
	 * 
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = $this->_option;
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($assetType == 'post' || $assetType == 'thread')
				{
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
				}
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
		}
	}
}
