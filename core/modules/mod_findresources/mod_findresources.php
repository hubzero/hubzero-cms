<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Findresources;

use Hubzero\Module\Module;
use Components\Tags\Models\Tag;

/**
 * Module class for displaying ways to find resources
 */
class Findresources extends Module
{
    /**
     * @var  \Hubzero\Database\Rows
     */
    protected $categories;
    /**
     * @var  \Hubzero\Database\Rows
     */
    protected $tags;

    /**
     * Generate module contents
     *
     * @return  void
     */
    public function run()
    {

        $this->tags = Tag::all()
            ->whereEquals('admin', 0)
            ->limit((int) $this->params->get('limit', 25))
            ->order('objects', 'desc')
            ->rows();

        // Get major types
        $this->categories = \Components\Resources\Models\Type::getMajorTypes();

        require $this->getLayoutPath();
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
