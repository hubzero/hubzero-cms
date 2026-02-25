<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Toptags;

use Hubzero\Module\Module;
use Components\Tags\Models\Tag;

/**
 * Module class for displaying a tag cloud of most used tags
 */
class Toptags extends Module
{
    protected $tags;

    /**
     * Get module contents
     *
     * @return  void
     */
    public function run()
    {

        $this->tags = Tag::all()
            ->whereEquals('admin', 0)
            ->limit((int)$this->params->get('numtags', 25))
            ->order('objects', 'desc')
            ->rows();

        require $this->getLayoutPath();
    }

    /**
     * Display module
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
