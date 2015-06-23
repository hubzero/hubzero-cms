<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Languages Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @since		1.5
 */
class LanguagesControllerInstalled extends JControllerLegacy
{
	/**
	 * task to set the default language
	 */
	function setDefault()
	{
		// Check for request forgeries
		Session::checkToken() or exit(Lang::txt('JInvalid_Token'));

		$cid = Request::getCmd('cid', '');

		$model = $this->getModel('installed');
		if ($model->publish($cid))
		{
			$msg = Lang::txt('COM_LANGUAGES_MSG_DEFAULT_LANGUAGE_SAVED');
			$type = 'message';
		}
		else
		{
			$msg = $this->getError();
			$type = 'error';
		}
		$client = $model->getClient();
		$clientId = $model->getState('filter.client_id');

		$this->setredirect('index.php?option=com_languages&view=installed&client='.$clientId, $msg, $type);
	}
}
