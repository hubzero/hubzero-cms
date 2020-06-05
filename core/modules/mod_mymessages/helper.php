<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyMessages;

use Hubzero\Module\Module;
use Hubzero\Message\Recipient;
use Plugin;
use User;
use Lang;

/**
 * Module class for displaying the latest messages
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!Plugin::isEnabled('members', 'messages'))
		{
			$this->setError(Lang::txt('MOD_MYMESSAGES_REQUIRED_PLUGIN_DISABLED'));
		}
		else
		{
			$this->moduleclass = $this->params->get('moduleclass');
			$this->limit = intval($this->params->get('limit', 10));

			// Find the user's most recent support tickets
			$recipient = Recipient::blank();
			$this->rows  = $recipient->getUnreadMessages(User::get('id'), $this->limit);
			$this->total = $recipient->getUnreadMessagesCount(User::get('id'));

			if ($recipient->getError())
			{
				$this->setError($recipient->getError());
			}
		}

		require $this->getLayoutPath();
	}
}
