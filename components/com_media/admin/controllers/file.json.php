<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * File Media Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.6
 */
class MediaControllerFile extends JControllerLegacy
{
	/**
	 * Upload a file
	 *
	 * @since 1.5
	 */
	function upload()
	{
		$params = Component::params('com_media');
		// Check for request forgeries
		if (!Session::checkToken('request'))
		{
			$response = array(
				'status' => '0',
				'error' => Lang::txt('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$log = JLog::getInstance('upload.error.php');

		// Get some data from the request
		$file   = Request::getVar('Filedata', '', 'files', 'array');
		$folder = Request::getVar('folder', '', '', 'path');
		$return = Request::getVar('return-url', null, 'post', 'base64');

		if (
			$_SERVER['CONTENT_LENGTH']>($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('upload_max_filesize'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('post_max_size'))* 1024 * 1024 ||
			$_SERVER['CONTENT_LENGTH']>(int)(ini_get('memory_limit'))* 1024 * 1024
		)
		{
			$response = array(
				'status' => '0',
				'error' => Lang::txt('COM_MEDIA_ERROR_WARNFILETOOLARGE')
			);
			echo json_encode($response);
			return;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			$filepath = \Hubzero\Filesystem\Util::normalizePath(COM_MEDIA_BASE . '/' . $folder . '/' . strtolower($file['name']));

			if (!MediaHelper::canUpload($file, $err))
			{
				$log->addEntry(array('comment' => 'Invalid: '.$filepath.': '.$err));
				$response = array(
					'status' => '0',
					'error' => Lang::txt($err)
				);
				echo json_encode($response);
				return;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);
			$object_file->filepath = $filepath;
			$result = Event::trigger('content.onContentBeforeSave', array('com_media.file', &$object_file, true));
			if (in_array(false, $result, true))
			{
				// There are some errors in the plugins
				$log->addEntry(array('comment' => 'Errors before save: '.$filepath.' : '.implode(', ', $object_file->getErrors())));
				$response = array(
					'status' => '0',
					'error' => Lang::txts('COM_MEDIA_ERROR_BEFORE_SAVE', count($errors = $object_file->getErrors()), implode('<br />', $errors))
				);
				echo json_encode($response);
				return;
			}

			if (Filesystem::exists($filepath))
			{
				// File exists
				$log->addEntry(array('comment' => 'File exists: '.$filepath.' by user_id '.User::get('id')));
				$response = array(
					'status' => '0',
					'error' => Lang::txt('COM_MEDIA_ERROR_FILE_EXISTS')
				);
				echo json_encode($response);
				return;
			}
			elseif (!User::authorise('core.create', 'com_media'))
			{
				// File does not exist and user is not authorised to create
				$log->addEntry(array('comment' => 'Create not permitted: '.$filepath.' by user_id '.User::get('id')));
				$response = array(
					'status' => '0',
					'error' => Lang::txt('COM_MEDIA_ERROR_CREATE_NOT_PERMITTED')
				);
				echo json_encode($response);
				return;
			}

			$file = (array) $object_file;
			if (!Filesystem::upload($file['tmp_name'], $file['filepath']))
			{
				// Error in upload
				$log->addEntry(array('comment' => 'Error on upload: '.$filepath));
				$response = array(
					'status' => '0',
					'error' => Lang::txt('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);
				echo json_encode($response);
				return;
			}
			else
			{
				// Trigger the onContentAfterSave event.
				Event::trigger('content.onContentAfterSave', array('com_media.file', &$object_file, true));
				$log->addEntry(array('comment' => $folder));
				$response = array(
					'status' => '1',
					'error' => Lang::txt('COM_MEDIA_UPLOAD_COMPLETE', substr($file['filepath'], strlen(COM_MEDIA_BASE)))
				);
				echo json_encode($response);
				return;
			}
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => Lang::txt('COM_MEDIA_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
	}
}
