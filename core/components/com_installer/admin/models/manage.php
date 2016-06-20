<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Installer\Admin\Models;

use Request;
use Notify;
use Lang;
use App;

// Import library dependencies
require_once __DIR__ . DS . 'extension.php';

/**
 * Installer Manage Model
 */
class Manage extends Extension
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @return  void
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name',
				'client_id',
				'status',
				'type',
				'folder',
				'extension_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$filters = Request::getVar('filters');
		if (empty($filters))
		{
			$data = User::getState($this->context . '.data');
			$filters = $data['filters'];
		}
		else
		{
			User::setState($this->context . '.data', array('filters' => $filters));
		}

		$this->setState('message', User::getState('com_installer.message'));
		$this->setState('extension_message', User::getState('com_installer.extension_message'));

		User::setState('com_installer.message', '');
		User::setState('com_installer.extension_message', '');

		$this->setState('filter.search', isset($filters['search']) ? $filters['search'] : '');
		$this->setState('filter.status', isset($filters['status']) ? $filters['status'] : '');
		$this->setState('filter.type', isset($filters['type']) ? $filters['type'] : '');
		$this->setState('filter.group', isset($filters['group']) ? $filters['group'] : '');
		$this->setState('filter.client_id', isset($filters['client_id']) ? $filters['client_id'] : '');

		parent::populateState('name', 'asc');
	}

	/**
	 * Enable/Disable an extension.
	 *
	 * @return	boolean True on success
	 * @since	1.5
	 */
	public function publish(&$eid = array(), $value = 1)
	{
		// Initialise variables.
		if (User::authorise('core.edit.state', 'com_installer'))
		{
			$result = true;

			// Ensure eid is an array of extension ids
			// TODO: If it isn't an array do we want to set an error and fail?
			if (!is_array($eid))
			{
				$eid = array($eid);
			}

			// Get a database connector
			$db = \App::get('db');

			// Get a table object for the extension type
			$table = \JTable::getInstance('Extension');
			\JTable::addIncludePath(PATH_CORE . '/components/com_templates/admin/tables');
			// Enable the extension in the table and store it in the database
			foreach ($eid as $i=>$id)
			{
				$table->load($id);
				if ($table->type == 'template')
				{
					$style = \JTable::getInstance('Style', 'TemplatesTable');
					if ($style->load(array('template' => $table->element, 'client_id' => $table->client_id, 'home'=>1)))
					{
						App::abort(403, Lang::txt('COM_INSTALLER_ERROR_DISABLE_DEFAULT_TEMPLATE_NOT_PERMITTED'));
						unset($eid[$i]);
						continue;
					}
				}
				/*if ($table->protected == 1)
				{
					$result = false;
					App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				}
				else
				{*/
					$table->enabled = $value;
				//}
				if (!$table->store())
				{
					$this->setError($table->getError());
					$result = false;
				}
			}
		}
		else
		{
			$result = false;
			App::abort(403, Lang::txt('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}
		return $result;
	}

	/**
	 * Refreshes the cached manifest information for an extension.
	 *
	 * @param	int		extension identifier (key in #__extensions)
	 * @return	boolean	result of refresh
	 * @since	1.6
	 */
	public function refresh($eid)
	{
		if (!is_array($eid))
		{
			$eid = array($eid => 0);
		}

		// Get a database connector
		$db = \App::get('db');

		// Get an installer object for the extension type
		$installer = \JInstaller::getInstance();
		$row = \JTable::getInstance('extension');
		$result = 0;

		// Uninstall the chosen extensions
		foreach ($eid as $id)
		{
			$result |= $installer->refreshManifestCache($id);
		}
		return $result;
	}

	/**
	 * Remove (uninstall) an extension
	 *
	 * @param	array	An array of identifiers
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function remove($eid = array())
	{
		// Initialise variables.
		if (User::authorise('core.delete', 'com_installer'))
		{
			// Initialise variables.
			$failed = array();

			// Ensure eid is an array of extension ids in the form id => client_id
			// TODO: If it isn't an array do we want to set an error and fail?
			if (!is_array($eid))
			{
				$eid = array($eid => 0);
			}

			// Get a database connector
			$db = \App::get('db');

			// Get an installer object for the extension type
			$installer = \JInstaller::getInstance();
			$row = \JTable::getInstance('extension');

			// Uninstall the chosen extensions
			foreach ($eid as $id)
			{
				$id = trim($id);
				$row->load($id);
				if ($row->type)
				{
					$result = $installer->uninstall($row->type, $id);

					// Build an array of extensions that failed to uninstall
					if ($result === false)
					{
						$failed[] = $id;
					}
				}
				else
				{
					$failed[] = $id;
				}
			}

			$langstring = 'COM_INSTALLER_TYPE_TYPE_' . strtoupper($row->type);
			$rowtype = Lang::txt($langstring);
			if (strpos($rowtype, $langstring) !== false)
			{
				$rowtype = $row->type;
			}

			if (count($failed))
			{
				// There was an error in uninstalling the package
				Notify::error(Lang::txt('COM_INSTALLER_UNINSTALL_ERROR', $rowtype));
				$result = false;
			}
			else
			{
				// Package uninstalled sucessfully
				Notify::success(Lang::txt('COM_INSTALLER_UNINSTALL_SUCCESS', $rowtype));
				$result = true;
			}

			$this->setState('action', 'remove');
			$this->setState('name', $installer->get('name'));

			User::setState('com_installer.message', $installer->message);
			User::setState('com_installer.extension_message', $installer->get('extension_message'));

			return $result;
		}
		else
		{
			$result = false;
			App::abort(403, Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
		}
	}

	/**
	 * Method to get the database query
	 *
	 * @return	JDatabaseQuery	The database query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$status = $this->getState('filter.status');
		$type   = $this->getState('filter.type');
		$client = $this->getState('filter.client_id');
		$group  = $this->getState('filter.group');

		$query = \App::get('db')->getQuery(true);
		$query->select('*');
		$query->select('2*protected+(1-protected)*enabled as status');
		$query->from('#__extensions');
		$query->where('state=0');
		if ($status != '')
		{
			if ($status == '2')
			{
				$query->where('protected = 1');
			}
			else
			{
				//$query->where('protected = 0');
				$query->where('enabled=' . intval($status));
			}
		}
		if ($type)
		{
			$query->where('type=' . $this->_db->quote($type));
		}
		if ($client != '')
		{
			$query->where('client_id=' . intval($client));
		}
		if ($group != '' && in_array($type, array('plugin', 'library', '')))
		{
			$query->where('folder=' . $this->_db->quote($group == '*' ? '' : $group));
		}

		// Filter by search in id
		$search = $this->getState('filter.search');
		if (!empty($search) && stripos($search, 'id:') === 0)
		{
			$query->where('extension_id = ' . (int) substr($search, 3));
		}

		return $query;
	}

	/**
	 * Method to get the row form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		\JForm::addFormPath(__DIR__ . '/forms');
		\JForm::addFieldPath(__DIR__ . '/fields');
		$form = \JForm::getInstance('com_installer.manage', 'manage', array('load_data' => $loadData));

		// Check for an error.
		if ($form == false)
		{
			$this->setError($form->getMessage());
			return false;
		}
		// Check the session for previously entered form data.
		$data = $this->loadFormData();

		// Bind the form data if present.
		if (!empty($data))
		{
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = User::getState($this->context . '.data', array());

		return $data;
	}
}
