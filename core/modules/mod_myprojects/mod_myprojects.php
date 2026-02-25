<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Myprojects;

use Hubzero\Module\Module;
use Components\Projects\Tables\Project;
use Hubzero\Facades\Component;
use Hubzero\Facades\User;

/**
 * Module class for displaying a user's projects
 */
class Myprojects extends Module
{
    protected $filters;
    protected $limit;
    protected $moduleclass;
    protected $pconfig;
    protected $rows;
    protected $total;

    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        $db = \Hubzero\Facades\App::get('db');

        // Get the module parameters
        $params = $this->params;
        $this->moduleclass = $params->get('moduleclass', '');
        $limit = intval($params->get('limit', 5));

        // Load component configs
        $config = Component::params('com_projects');

        // Load classes

        // Set filters
        $filters = array(
            'mine'     => 1,
            'limit'    => $limit,
            'start'    => 0,
            'updates'  => 1,
            'sortby'   => 'myprojects',
            'getowner' => 1
        );

        if (!$this->params->get('include_archived', 1)) {
            $filters['filterby'] = 'active';
        }

        $setup_complete = $config->get('confirm_step', 0) ? 3 : 2;
        $this->filters  = $filters;
        $this->pconfig  = $config;

        // Get a record count
        $obj = new Project($db);
        $this->total = $obj->getCount($filters, false, User::get('id'), 0, $setup_complete);

        // Get records
        $this->rows = $obj->getRecords($filters, false, User::get('id'), 0, $setup_complete);

        // pass limit to view
        $this->limit = $limit;

        require $this->getLayoutPath();
    }
}
