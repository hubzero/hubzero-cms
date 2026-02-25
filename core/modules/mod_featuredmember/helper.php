<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredmember;

use Hubzero\Module\Module;
use Hubzero\Config\Registry;
use Hubzero\Database\Expression;
use Hubzero\Facades\Component;
use Hubzero\Facades\User;

/**
 * Module class for displaying featured members
 */
class Helper extends Module
{
    protected $cls;
    protected $row;
    protected $txt_length;

    /**
     * Generate module contents
     *
     * @return  void
     */
    public function run()
    {
        include_once Component::path('com_members') . DS . 'models' . DS . 'member.php';

        $database = \Hubzero\Facades\App::get('db');
        $this->row = null;

        // Randomly choose one
        $filters = array(
            'limit'      => 1,
            'show'       => trim($this->params->get('show', '')),
            'start'      => 0,
            'sortby'     => $database->sqlRand(),
            'search'     => '',
            'authorized' => false
        );
        if ($min = $this->params->get('min_contributions')) {
            $filters['contributions'] = $min;
        }

        $userId = (int) $database->getQuery()
            ->select('id')
            ->from('#__users')
            ->whereEquals('block', 0)
            ->order(Expression::randomOrder(), 'asc')
            ->limit(1)
            ->value('id');

        // Load their bio
        $this->row = User::oneOrNew($userId);

        if (trim(strip_tags($this->row->get('bio'))) == '') {
            return '';
        }

        // Did we have a result to display?
        if ($this->row) {
            $this->cls = trim($this->params->get('moduleclass_sfx', ''));
            $this->txt_length = trim($this->params->get('txt_length', ''));

            $config = Component::params('com_members');

            $rparams = new Registry($this->row->get('params'));
            $this->params = $config;
            $this->params->merge($rparams);

            require $this->getLayoutPath();
        }
    }

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if ($content = $this->getCacheContent()) {
            echo $content;
            return;
        }

        $this->run();
    }
}
