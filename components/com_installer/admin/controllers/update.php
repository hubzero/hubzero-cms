<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 */
class InstallerControllerUpdate extends JControllerLegacy
{
	/**
	 * Update a set of extensions.
	 *
	 * @since	1.6
	 */
	public function update()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Lang::txt('JINVALID_TOKEN'));

		$model = $this->getModel('update');
		$uid   = Request::getVar('cid', array(), '', 'array');

		\Hubzero\Utility\Arr::toInteger($uid, array());
		if ($model->update($uid))
		{
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
		}

		$redirect_url = User::getState('com_installer.redirect_url');
		if (empty($redirect_url))
		{
			$redirect_url = Route::url('index.php?option=com_installer&view=update', false);
		}
		else
		{
			// wipe out the user state when we're going to redirect
			User::setState('com_installer.redirect_url', '');
			User::setState('com_installer.message', '');
			User::setState('com_installer.extension_message', '');
		}

		$this->setRedirect($redirect_url);
	}

	/**
	 * Find new updates.
	 *
	 * @since	1.6
	 */
	public function find()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Lang::txt('JINVALID_TOKEN'));

		// Get the caching duration
		jimport('joomla.application.component.helper');
		$component = Component::load('com_installer');
		$params = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;

		// Find updates
		$model  = $this->getModel('update');
		$result = $model->findUpdates(0, $cache_timeout);

		$this->setRedirect(Route::url('index.php?option=com_installer&view=update', false));
		//$view->display();
	}

	/**
	 * Purges updates.
	 *
	 * @since	1.6
	 */
	public function purge()
	{
		// Purge updates
		// Check for request forgeries
		Session::checkToken() or jexit(Lang::txt('JINVALID_TOKEN'));

		$model = $this->getModel('update');
		$model->purge();
		$model->enableSites();

		$this->setRedirect(Route::url('index.php?option=com_installer&view=update', false), $model->_message);
	}

	/**
	 * Fetch and report updates in JSON format, for AJAX requests
	 *
	 * @return void
	 *
	 * @since 2.5
	 */
	public function ajax()
	{
		// Note: we don't do a token check as we're fetching information
		// asynchronously. This means that between requests the token might
		// change, making it impossible for AJAX to work.

		$eid  = Request::getInt('eid', 0);
		$skip = Request::getVar('skip', array(), 'default', 'array');

		$cache_timeout = Request::getInt('cache_timeout', 0);
		if ($cache_timeout == 0)
		{
			$cache_timeout = Component::params('com_installer')->get('cachetimeout', 6, 'int');
			$cache_timeout = 3600 * $cache_timeout;
		}

		$model  = $this->getModel('update');
		$result = $model->findUpdates($eid, $cache_timeout);

		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);
		if ($eid != 0)
		{
			$model->setState('filter.extension_id', $eid);
		}
		$updates = $model->getItems();

		if (!empty($skip))
		{
			$unfiltered_updates = $updates;
			$updates = array();

			foreach ($unfiltered_updates as $update)
			{
				if (!in_array($update->extension_id, $skip)) $updates[] = $update;
			}
		}

		echo json_encode($updates);

		App::close();
	}
}
