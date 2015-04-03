<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Collections\Site\Controllers;

use Components\Collections\Models\Collection;
use Components\Collections\Models\Archive;
use Hubzero\Component\SiteController;

/**
 * Controller class for collections and posts
 */
class Collections extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		// This needs to be called to ensure scripts are pushed to the document
		if (!User::isGuest())
		{
			$foo = \JFactory::getEditor()->display('description', '', '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer'));
		}

		$this->_authorize('collection');
		$this->_authorize('item');

		$this->registerTask('__default', 'posts');
		$this->registerTask('all', 'collections');

		parent::execute();
	}

	/**
	 * Display a list of latest whiteboard entries
	 *
	 * @return  void
	 */
	public function postsTask()
	{
		$this->view->config  = $this->config;

		// Filters for returning results
		$this->view->filters = array(
			'limit'   => Request::getInt('limit', 25),
			'start'   => Request::getInt('limitstart', 0),
			'search'  => Request::getVar('search', ''),
			'id'      => Request::getInt('id', 0),
			'user_id' => User::get('id'),
			'sort'    => 'p.created',
			'state'   => 1,
			'access'  => (!User::isGuest() ? array(0, 1) : 0)
		);
		if ($this->view->filters['id'])
		{
			$this->view->filters['object_type'] = 'site';
		}

		$this->view->collection = new Collection();

		$this->view->filters['count'] = true;
		$this->view->total = $this->view->collection->posts($this->view->filters);

		$this->view->filters['count'] = false;
		$this->view->rows = $this->view->collection->posts($this->view->filters);

		$model = Archive::getInstance();

		$this->view->collections = $model->collections(array(
			'count'      => true,
			'access'     => (!User::isGuest() ? array(0, 1) : 0),
			'state'      => 1
		));

		$this->_buildTitle();
		$this->_buildPathway();

		$this->view
			->setLayout('posts')
			->display();
	}

	/**
	 * Display a list of collections
	 *
	 * @return  void
	 */
	public function collectionsTask()
	{
		$this->view->config = $this->config;

		// Filters for returning results
		$this->view->filters = array(
			'limit'   => Request::getInt('limit', 25),
			'start'   => Request::getInt('limitstart', 0),
			'search'  => Request::getVar('search', ''),
			'id'      => Request::getInt('id', 0),
			'state'   => 1,
			'access'  => (!User::isGuest() ? array(0, 1) : 0)
		);
		if ($this->view->filters['id'])
		{
			$this->view->filters['object_type'] = 'site';
		}

		$model = Archive::getInstance();

		$this->view->filters['count'] = true;
		$this->view->total = $model->collections($this->view->filters);

		$this->view->filters['user_id'] = User::get('id');

		$this->view->posts = $model->posts($this->view->filters);
		unset($this->view->filters['user_id']);

		$this->view->filters['count'] = false;
		$this->view->rows = $model->collections($this->view->filters);

		$this->_buildTitle();
		$this->_buildPathway();

		$this->view
			->setLayout('collections')
			->display();
	}

	/**
	 * Display information about collections
	 *
	 * @return  void
	 */
	public function aboutTask()
	{
		$this->view;

		// Filters for returning results
		$this->view->filters = array(
			'id'      => Request::getInt('id', 0),
			'search'  => Request::getVar('search', ''),
			'sort'    => 'p.created',
			'state'   => 1,
			'access'  => (!User::isGuest() ? array(0, 1) : 0)
		);
		if ($this->view->filters['id'])
		{
			$this->view->filters['object_type'] = 'site';
		}

		$this->view->collection = new Collection();

		$this->view->filters['count'] = true;
		$this->view->total = $this->view->collection->posts($this->view->filters);

		$model = Archive::getInstance();

		$this->view->collections = $model->collections(array(
			'count'  => true,
			'state'  => 1,
			'access' => (!User::isGuest() ? array(0, 1) : 0)
		));

		$this->_buildTitle();
		$this->_buildPathway();

		$this->view
			->setLayout('about')
			->display();
	}

	/**
	 * Set the authorization level for the user
	 *
	 * @param   string   $assetType
	 * @param   integer  $assetId
	 * @return  void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!User::isGuest())
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
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if ($this->_task)
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}

		\JFactory::getDocument()->setTitle($this->_title);
	}
}
