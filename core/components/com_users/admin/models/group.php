<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.modeladmin');

/**
 * User group model.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class UsersModelGroup extends JModelAdmin
{
	/**
	 * @var		string	The event to trigger after saving the data.
	 * @since	1.6
	 */
	protected $event_after_save = 'onUserAfterSaveGroup';

	/**
	 * @var		string	The event to trigger after before the data.
	 * @since	1.6
	 */
	protected $event_before_save = 'onUserBeforeSaveGroup';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Usergroup', $prefix = 'JTable', $config = array())
	{
		$return = JTable::getInstance($type, $prefix, $config);
		return $return;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_users.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
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
		$data = User::getState('com_users.edit.group.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $groups = '')
	{
		$obj = is_array($data) ? \Hubzero\Utility\Arr::toObject($data, '\\Hubzero\\Base\\Obj') : $data;
		if (isset($obj->parent_id) && $obj->parent_id == 0 && $obj->id > 0)
		{
			$form->setFieldAttribute('parent_id', 'type', 'hidden');
			$form->setFieldAttribute('parent_id', 'hidden', 'true');
		}
		parent::preprocessForm($form, $data, 'user');
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
	public function save($data)
	{
		Plugin::import('user');

		// Check the super admin permissions for group
		// We get the parent group permissions and then check the group permissions manually
		// We have to calculate the group permissions manually because we haven't saved the group yet
		$parentSuperAdmin = JAccess::checkGroup($data['parent_id'], 'core.admin');
		// Get core.admin rules from the root asset
		$rules = JAccess::getAssetRules('root.1')->getData('core.admin');
		// Get the value for the current group (will be true (allowed), false (denied), or null (inherit)
		$groupSuperAdmin = $rules['core.admin']->allow($data['id']);

		// We only need to change the $groupSuperAdmin if the parent is true or false. Otherwise, the value set in the rule takes effect.
		if ($parentSuperAdmin === false)
		{
			// If parent is false (Denied), effective value will always be false
			$groupSuperAdmin = false;
		}
		elseif ($parentSuperAdmin === true)
		{
			// If parent is true (allowed), group is true unless explicitly set to false
			$groupSuperAdmin = ($groupSuperAdmin === false) ? false : true;
		}

		// Check for non-super admin trying to save with super admin group
		$iAmSuperAdmin = User::authorise('core.admin');
		if ((!$iAmSuperAdmin) && ($groupSuperAdmin))
		{
			try
			{
				throw new Exception(Lang::txt('JLIB_USER_ERROR_NOT_SUPERADMIN'));
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
		}

		// Check for super-admin changing self to be non-super-admin
		// First, are we a super admin>
		if ($iAmSuperAdmin)
		{
			// Next, are we a member of the current group?
			$myGroups = JAccess::getGroupsByUser(User::get('id'), false);
			if (in_array($data['id'], $myGroups))
			{
				// Now, would we have super admin permissions without the current group?
				$otherGroups = array_diff($myGroups, array($data['id']));
				$otherSuperAdmin = false;
				foreach ($otherGroups as $otherGroup)
				{
					$otherSuperAdmin = ($otherSuperAdmin) ? $otherSuperAdmin : JAccess::checkGroup($otherGroup, 'core.admin');
				}
				// If we would not otherwise have super admin permissions
				// and the current group does not have super admin permissions, throw an exception
				if ((!$otherSuperAdmin) && (!$groupSuperAdmin))
				{
					try
					{
						throw new Exception(Lang::txt('JLIB_USER_ERROR_CANNOT_DEMOTE_SELF'));
					}
					catch (Exception $e)
					{
						$this->setError($e->getMessage());
						return false;
					}
				}
			}
		}

		// Proceed with the save
		return parent::save($data);
	}

	/**
	 * Method to delete rows.
	 *
	 * @param	array	An array of item ids.
	 * @return	boolean	Returns true on success, false on failure.
	 * @since	1.6
	 */
	public function delete(&$pks)
	{
		// Typecast variable.
		$pks = (array) $pks;
		$groups = JAccess::getGroupsByUser(User::get('id'));

		// Get a row instance.
		$table = $this->getTable();

		// Check if I am a Super Admin
		$iAmSuperAdmin = User::authorise('core.admin');

		// do not allow to delete groups to which the current user belongs
		foreach ($pks as $i => $pk)
		{
			if (in_array($pk, $groups))
			{
				Notify::warning(Lang::txt('COM_USERS_DELETE_ERROR_INVALID_GROUP'));
				return false;
			}
		}
		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				// Access checks.
				$allow = User::authorise('core.edit.state', 'com_users');
				// Don't allow non-super-admin to delete a super admin
				$allow = (!$iAmSuperAdmin && JAccess::checkGroup($pk, 'core.admin')) ? false : $allow;

				if ($allow)
				{
					// Fire the onUserBeforeDeleteGroup event.
					Event::trigger('user.onUserBeforeDeleteGroup', array($table->getProperties()));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
					else
					{
						// Trigger the onUserAfterDeleteGroup event.
						Event::trigger('user.onUserAfterDeleteGroup', array($table->getProperties(), true, $this->getError()));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					Notify::warning(Lang::txt('JERROR_CORE_DELETE_NOT_PERMITTED'));
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}
}
