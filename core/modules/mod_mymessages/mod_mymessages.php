<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Mymessages;

use Hubzero\Module\Module;
use Hubzero\Message\Recipient;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;

/**
 * Module class for displaying the latest messages
 */
class Mymessages extends Module
{
    protected $limit;
    protected $moduleclass;
    protected $rows;
    protected $total;

    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        if (!Plugin::isEnabled('members', 'messages')) {
            $this->setError(Lang::txt('MOD_MYMESSAGES_REQUIRED_PLUGIN_DISABLED'));
        } else {
            $this->moduleclass = $this->params->get('moduleclass');
            $this->limit = intval($this->params->get('limit', 10));

            // Find the user's most recent support tickets
            $recipient = Recipient::blank();
            $this->rows  = $recipient->getUnreadMessages(User::get('id'), $this->limit);
            $this->total = $recipient->getUnreadMessagesCount(User::get('id'));

            if ($recipient->getError()) {
                $this->setError($recipient->getError());
            }
        }

        require $this->getLayoutPath();
    }
}
