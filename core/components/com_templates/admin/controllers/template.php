<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

JLoader::register('InstallerModelInstall', PATH_CORE . '/components/com_installer/models/install.php');

/**
 * Template style controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesControllerTemplate extends JControllerLegacy
{
	/**
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_templates&view=templates');
	}

	public function copy()
	{
		// Check for request forgeries
		Session::checkToken();

		Request::setVar('installtype', 'folder');
		$newName = Request::getCmd('new_name');
		$newNameRaw = Request::getVar('new_name', null, '', 'string');
		$templateID = Request::getInt('id', 0);

		$this->setRedirect('index.php?option=com_templates&view=template&id=' . $templateID);

		$model = $this->getModel('Template', 'TemplatesModel');
		$model->setState('new_name', $newName);
		$model->setState('tmp_prefix', uniqid('template_copy_'));
		$model->setState('to_path', Config::get('tmp_path') . '/' . $model->getState('tmp_prefix'));

		// Process only if we have a new name entered
		if (strlen($newName) > 0)
		{
			if (!User::authorise('core.create', 'com_templates'))
			{
				// User is not authorised to delete
				Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}

			// Set FTP credentials, if given
			JClientHelper::setCredentialsFromRequest('ftp');

			// Check that new name is valid
			if (($newNameRaw !== null) && ($newName !== $newNameRaw))
			{
				Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_INVALID_TEMPLATE_NAME'));
				return false;
			}

			// Check that new name doesn't already exist
			if (!$model->checkNewName())
			{
				Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_DUPLICATE_TEMPLATE_NAME'));
				return false;
			}

			// Check that from name does exist and get the folder name
			$fromName = $model->getFromName();
			if (!$fromName)
			{
				Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_INVALID_FROM_NAME'));
				return false;
			}

			// Call model's copy method
			if (!$model->copy())
			{
				Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_COULD_NOT_COPY'));
				return false;
			}

			// Call installation model
			Request::setVar('install_directory', Config::get('tmp_path') . '/' . $model->getState('tmp_prefix'));
			$installModel = $this->getModel('Install', 'InstallerModel');
			Lang::load('com_installer');
			if (!$installModel->install())
			{
				Notify::warning(Lang::txt('COM_TEMPLATES_ERROR_COULD_NOT_INSTALL'));
				return false;
			}

			$this->setMessage(Lang::txt('COM_TEMPLATES_COPY_SUCCESS', $newName));
			$model->cleanup();
			return true;

		}
	}
}
