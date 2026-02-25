<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Poll;

use Hubzero\Module\Module;
use Components\Poll\Models\Poll as PollModel;
use Hubzero\Facades\Component;
use Hubzero\Facades\App;

/**
 * Module class for displaying a poll
 */
class Poll extends Module
{
    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {

        $menu   = App::get('menu');
        $items  = $menu->getItems('link', 'index.php?option=com_poll&view=poll');
        $itemid = isset($items[0]) ? '&Itemid=' . $items[0]->id : '';

        if ($id = $this->params->get('id', 0)) {
            $poll = PollModel::oneOrNew($id);
        } else {
            $poll = PollModel::current();
        }

        if ($poll && $poll->id) {
            require $this->getLayoutPath();
        }
    }
}
