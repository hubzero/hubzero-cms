<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyTodos;

use Hubzero\Module\Module;
use Hubzero\Facades\User;
use Hubzero\Facades\App;

/**
 * Module class for displaying a user's to do items
 */
class Helper extends Module
{
    protected $rows;

    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        $this->rows = array();

        // Find the user's most recent to do items
        if (!User::isGuest()) {
            $database = App::get('db');
            $limit = intval($this->params->get('limit', 10));
            $this->rows = $database->getQuery()
                ->select('a.id')
                ->select('a.content')
                ->select('b.title')
                ->select('b.alias')
                ->from('#__project_todo', 'a')
                ->joinRaw('#__projects AS b', 'b.id = a.projectid', 'inner')
                ->whereEquals('a.assigned_to', (int) User::get('id'))
                ->whereEquals('a.state', 0)
                ->limit($limit)
                ->fetch();
        }

        // Push the module CSS to the template
        $this->css();

        require $this->getLayoutPath();
    }
}
