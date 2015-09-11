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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Site\Controllers;

use Components\Collections\Models\Collection;
use Components\Collections\Models\Archive;
use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Request;
use Lang;
use User;

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

		Document::setTitle($this->_title);
	}
}
