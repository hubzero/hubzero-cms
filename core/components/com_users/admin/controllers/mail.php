<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 */

defined('_HZEXEC_') or die();

/**
 * Users mail controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 */
class UsersControllerMail extends JControllerLegacy
{
	public function send()
	{
		// Check for request forgeries.
		Session::checkToken('request') or exit(Lang::txt('JINVALID_TOKEN'));

		$model = $this->getModel('Mail');
		if ($model->send())
		{
			$type = 'message';
		}
		else
		{
			$type = 'error';
		}

		$msg = $model->getError();
		$this->setredirect('index.php?option=com_users&view=mail', $msg, $type);
	}

	public function cancel()
	{
		// Check for request forgeries.
		Session::checkToken('request') or exit(Lang::txt('JINVALID_TOKEN'));
		$this->setRedirect('index.php');
	}
}
