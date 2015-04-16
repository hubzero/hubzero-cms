<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Languages Overrides Controller
 *
 * @package			Joomla.Administrator
 * @subpackage	com_languages
 * @since				2.5
 */
class LanguagesControllerOverrides extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages
	 *
	 * @var		string
	 * @since	2.5
	 */
	protected $text_prefix = 'COM_LANGUAGES_VIEW_OVERRIDES';

	/**
	 * Method for deleting one or more overrides
	 *
	 * @return	void
	 *
	 * @since		2.5
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(Lang::txt('JINVALID_TOKEN'));

		// Get items to dlete from the request
		$cid	= Request::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			$this->setMessage(Lang::txt($this->text_prefix.'_NO_ITEM_SELECTED'), 'warning');
		}
		else
		{
			// Get the model
			$model = $this->getModel('overrides');

			// Remove the items
			if ($model->delete($cid))
			{
				$this->setMessage(Lang::txts($this->text_prefix.'_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}

		$this->setRedirect(Route::url('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}
}
