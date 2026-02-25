<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Myactivity;

use Hubzero\Module\Module;
use Hubzero\Activity\Recipient;
use Hubzero\Facades\User;

/**
 * Module class for displaying a list of user activity
 */
class Myactivity extends Module
{
    protected $limit;
    protected $moduleclass;
    protected $rows;

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        // Get the module parameters
        $this->moduleclass = $this->params->get('moduleclass');
        $this->limit = intval($this->params->get('limit', 10));
        $this->limit = $this->limit ? $this->limit : 10;

        $this->rows = Recipient::all()
            ->including(['log', function ($log) {
                    $log->select('*');
            }])
            ->whereEquals('scope', 'user')
            ->whereEquals('scope_id', User::get('id'))
            ->whereEquals('state', 1)
            ->ordered()
            ->limit($this->limit)
            ->paginated();

        require $this->getLayoutPath();
    }
}
