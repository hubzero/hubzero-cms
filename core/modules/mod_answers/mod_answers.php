<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Answers;

use Hubzero\Module\Module;
use Hubzero\Facades\User;
use Hubzero\Facades\App;

/**
 * Module class for com_answers data
 */
class Answers extends Module
{
    /**
     * @var  mixed
     */
    protected $closed;
    /**
     * @var  mixed
     */
    protected $myclosed;
    /**
     * @var  mixed
     */
    protected $myopen;
    /**
     * @var  mixed
     */
    protected $open;
    /**
     * @var  mixed
     */
    protected $pastDay;
    /**
     * @var  mixed
     */
    protected $username;

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if (!App::isAdmin()) {
            return;
        }

        $database = App::get('db');

        $queries = [
            'closed'  => "state=1",
            'open'    => "state=0",
            'pastDay' => "created >= " . $database->quote(gmdate('Y-m-d', (time() - 24 * 3600)) . ' 00:00:00'),
        ];

        if ($this->params->get('showMine', 0)) {
            $this->username = User::get('username');

            // My Open
            $queries['myclosed'] = "state=1 AND created_by=" . User::get('id');

            // My Closed
            $queries['myopen']   = "state=0 AND created_by=" . User::get('id');
        }

        foreach ($queries as $key => $where) {
            $database->setQuery("SELECT count(*) FROM `#__answers_questions` WHERE $where");
            $this->$key = $database->loadResult();
        }

        // Get the view
        parent::display();
    }
}
