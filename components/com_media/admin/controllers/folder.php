<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Folder Media Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since 1.5
 */
class MediaControllerFolder extends JControllerLegacy
{

	/**
	 * Deletes paths from the current path
	 *
	 * @since 1.5
	 */
	public function delete()
	{
		Session::checkToken('request') or jexit(Lang::txt('JINVALID_TOKEN'));

		// Get some data from the request
		$tmpl   = Request::getCmd('tmpl');
		$paths  = Request::getVar('rm', array(), '', 'array');
		$folder = Request::getVar('folder', '', '', 'path');

		$redirect = 'index.php?option=com_media&folder=' . $folder;
		if ($tmpl == 'component')
		{
			// We are inside the iframe
			$redirect .= '&view=mediaList&tmpl=component';
		}
		$this->setRedirect($redirect);

		// Just return if there's nothing to do
		if (empty($paths))
		{
			return true;
		}

		if (!User::authorise('core.delete', 'com_media'))
		{
			// User is not authorised to delete
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$ret = true;

		if (count($paths))
		{
			foreach ($paths as $path)
			{
				if ($path !== Filesystem::clean($path))
				{
					$dirname = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
					Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_WARNDIRNAME', substr($dirname, strlen(COM_MEDIA_BASE))));
					continue;
				}

				$fullPath = \Hubzero\Filesystem\Util::normalizePath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
				$object_file = new \Hubzero\Base\Object(array('filepath' => $fullPath));
				if (is_file($fullPath))
				{
					// Trigger the onContentBeforeDelete event.
					$result = Event::trigger('content.onContentBeforeDelete', array('com_media.file', &$object_file));
					if (in_array(false, $result, true))
					{
						// There are some errors in the plugins
						Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
						continue;
					}

					$ret &= Filesystem::delete($fullPath);

					// Trigger the onContentAfterDelete event.
					Event::trigger('content.onContentAfterDelete', array('com_media.file', &$object_file));
					$this->setMessage(Lang::txt('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE))));
				}
				elseif (is_dir($fullPath))
				{
					$contents = Filesystem::files($fullPath, '.', true, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html'));
					if (empty($contents))
					{
						// Trigger the onContentBeforeDelete event.
						$result = Event::trigger('content.onContentBeforeDelete', array('com_media.folder', &$object_file));
						if (in_array(false, $result, true))
						{
							// There are some errors in the plugins
							Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_DELETE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
							continue;
						}

						$ret &= !Filesystem::deleteDirectory($fullPath);

						// Trigger the onContentAfterDelete event.
						Event::trigger('content.onContentAfterDelete', array('com_media.folder', &$object_file));
						$this->setMessage(Lang::txt('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE))));
					}
					else
					{
						//This makes no sense...
						Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', substr($fullPath, strlen(COM_MEDIA_BASE))));
					}
				}
			}
			return $ret;
		}
	}

	/**
	 * Create a folder
	 *
	 * @param string $path Path of the folder to create
	 * @since 1.5
	 */
	public function create()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Lang::txt('JINVALID_TOKEN'));

		$folder      = Request::getCmd('foldername', '');
		$folderCheck = Request::getVar('foldername', null, '', 'string', JREQUEST_ALLOWRAW);
		$parent      = Request::getVar('folderbase', '', '', 'path');

		$this->setRedirect('index.php?option=com_media&folder='.$parent.'&tmpl='.Request::getCmd('tmpl', 'index'));

		if (strlen($folder) > 0)
		{
			if (!User::authorise('core.create', 'com_media'))
			{
				// User is not authorised to delete
				Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_CREATE_NOT_PERMITTED'));
				return false;
			}

			// Set FTP credentials, if given
			JClientHelper::setCredentialsFromRequest('ftp');

			Request::setVar('folder', $parent);

			if (($folderCheck !== null) && ($folder !== $folderCheck))
			{
				$this->setMessage(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_CREATE_FOLDER_WARNDIRNAME'));
				return false;
			}

			$path = \Hubzero\Filesystem\Util::normalizePath(COM_MEDIA_BASE . '/' . $parent . '/' . $folder);
			if (!is_dir($path) && !is_file($path))
			{
				// Trigger the onContentBeforeSave event.
				$object_file = new \Hubzero\Base\Object(array('filepath' => $path));

				$result = Event::trigger('content.onContentBeforeSave', array('com_media.folder', &$object_file, true));
				if (in_array(false, $result, true))
				{
					// There are some errors in the plugins
					Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
					return false;
				}

				Filesystem::makeDirectory($path);
				$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
				Filesystem::write($path . "/index.html", $data);

				// Trigger the onContentAfterSave event.
				Event::trigger('content.onContentAfterSave', array('com_media.folder', &$object_file, true));
				$this->setMessage(Lang::txt('COM_MEDIA_CREATE_COMPLETE', substr($path, strlen(COM_MEDIA_BASE))));
			}
			Request::setVar('folder', ($parent) ? $parent.'/'.$folder : $folder);
		}
	}
}
