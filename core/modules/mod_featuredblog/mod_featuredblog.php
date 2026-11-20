<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Featuredblog;

use Hubzero\Module\Module;
use Components\Blog\Models\Entry;
use Hubzero\Facades\App;

/**
 * Module class for displaying a random, featured blog entry
 */
class Featuredblog extends Module
{
    /** @var string */
    protected $cls;
    /**
     * @var  \Components\Blog\Models\Entry|null
     */
    protected $row = null;
    /** @var string */
    protected $txt_length;

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

    /**
     * Build module contents
     *
     * @return  void
     */
    public function run()
    {

        $this->row = null;

        $filters = [
            'state'      => 1,
            'access'     => 1,
            'sort'       => App::get('db')->sqlRand(),
            'sort_Dir'   => '',
            'search'     => '',
            'scope'      => 'member',
            'scope_id'   => 0,
            'authorized' => false,
        ];

        $row = Entry::all()
            ->whereEquals('scope', $filters['scope'])
            ->whereEquals('state', $filters['state'])
            ->whereEquals('access', $filters['access'])
            ->row();

        // Did we have a result to display?
        if ($row->get('id')) {
            $this->row = $row;
            $this->cls = trim($this->params->get('moduleclass_sfx', ''));
            $this->txt_length = trim($this->params->get('txt_length', ''));

            require $this->getLayoutPath();
        }
    }
}
