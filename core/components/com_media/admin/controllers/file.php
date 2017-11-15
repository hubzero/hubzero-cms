<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Media File Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.5
 */
class MediaControllerFile extends JControllerLegacy
{
	/*
	 * The folder we are uploading into
	 */
	protected $folder = '';

	/**
	 * Upload one or more files
	 *
	 * @since 1.5
	 */
	public function upload()
	{
		// Check for request forgeries
		Session::checkToken(['get', 'post']);

		$params = Component::params('com_media');

		// Get some data from the request
		$files  = Request::getVar('Filedata', '', 'files', 'array');
		$return = Request::getVar('return-url', null, 'post', 'base64');
		$this->folder = Request::getVar('folder', '', '', 'path');

		// Set the redirect
		if ($return)
		{
			$this->setRedirect(base64_decode($return) . '&folder=' . $this->folder);
		}

		// Authorize the user
		if (!$this->authoriseUser('create'))
		{
			return false;
		}
		if ($_SERVER['CONTENT_LENGTH']>($params->get('upload_maxsize', 0) * 1024 * 1024)
		 || $_SERVER['CONTENT_LENGTH']>(int)(ini_get('upload_max_filesize'))* 1024 * 1024
		 || $_SERVER['CONTENT_LENGTH']>(int)(ini_get('post_max_size'))* 1024 * 1024
		 || (($_SERVER['CONTENT_LENGTH'] > (int) (ini_get('memory_limit')) * 1024 * 1024) && ((int) (ini_get('memory_limit')) != -1)))
		{
			Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
			return false;
		}
		// Input is in the form of an associative array containing numerically indexed arrays
		// We want a numerically indexed array containing associative arrays
		// Cast each item as array in case the Filedata parameter was not sent as such
		$files = array_map( array($this, 'reformatFilesArray'),
			(array) $files['name'], (array) $files['type'], (array) $files['tmp_name'], (array) $files['error'], (array) $files['size']
		);

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			if ($file['error']==1)
			{
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
				return false;
			}
			if ($file['size']>($params->get('upload_maxsize', 0) * 1024 * 1024))
			{
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE'));
				return false;
			}

			if (Filesystem::exists($file['filepath']))
			{
				// A file with this name already exists
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_FILE_EXISTS'));
				return false;
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by Filesystem::clean()
				$this->setRedirect('index.php', Lang::txt('COM_MEDIA_INVALID_REQUEST'), 'error');
				return false;
			}
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		foreach ($files as &$file)
		{
			// The request is valid
			$err = null;
			if (!MediaHelper::canUpload($file, $err))
			{
				// The file can't be upload
				Notify::warning(Lang::txt($err));
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new \Hubzero\Base\Obj($file);
			$result = Event::trigger('content.onContentBeforeSave', array('com_media.file', &$object_file, true));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				Notify::warning(Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors)));
				return false;
			}

			if (!Filesystem::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
				return false;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				Event::trigger('content.onContentAfterSave', array('com_media.file', &$object_file, true));
				$this->setMessage(Lang::txt('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE))));
			}
		}

		return true;
	}

	/**
	 * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
	 * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
	 *
	 * @param	string	- file name			($files['name'])
	 * @param	string	- file type			($files['type'])
	 * @param	string	- temporary name	($files['tmp_name'])
	 * @param	string	- error info		($files['error'])
	 * @param	string	- file size			($files['size'])
	 *
	 * @return	array
	 * @access	protected
	 */
	protected function reformatFilesArray($name, $type, $tmp_name, $error, $size)
	{
		$name = Filesystem::clean($name);
		return array(
			'name'     => $name,
			'type'     => $type,
			'tmp_name' => $tmp_name,
			'error'    => $error,
			'size'     => $size,
			'filepath' => Filesystem::cleanPath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $name)))
		);
	}

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string   $action - the action to be peformed (create or delete)
	 *
	 * @return  boolean
	 * @access  protected
	 */
	protected function authoriseUser($action)
	{
		if (!User::authorise('core.' . strtolower($action), 'com_media'))
		{
			// User is not authorised
			Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));
			return false;
		}

		return true;
	}

	/**
	 * Deletes paths from the current path
	 *
	 * @since 1.5
	 */
	public function delete()
	{
		Session::checkToken(['get', 'post']);

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

		// Nothing to delete
		if (empty($paths))
		{
			return true;
		}

		// Authorize the user
		if (!$this->authoriseUser('delete'))
		{
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$ret = true;
		foreach ($paths as $path)
		{
			if ($path !== Filesystem::clean($path))
			{
				// filename is not safe
				$filename = htmlspecialchars($path, ENT_COMPAT, 'UTF-8');
				Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FILE_WARNFILENAME', substr($filename, strlen(COM_MEDIA_BASE))));
				continue;
			}

			$fullPath = Filesystem::cleanPath(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $folder, $path)));
			$object_file = new \Hubzero\Base\Obj(array('filepath' => $fullPath));
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

					$ret &= Filesystem::deleteDirectory($fullPath);

					// Trigger the onContentAfterDelete event.
					Event::trigger('content.onContentAfterDelete', array('com_media.folder', &$object_file));
					$this->setMessage(Lang::txt('COM_MEDIA_DELETE_COMPLETE', substr($fullPath, strlen(COM_MEDIA_BASE))));
				}
				else
				{
					// This makes no sense...
					Notify::warning(Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_DELETE_FOLDER_NOT_EMPTY', substr($fullPath, strlen(COM_MEDIA_BASE))));
				}
			}
		}

		return $ret;
	}
}
