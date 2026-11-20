<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Title;

use Hubzero\Module\Module;
use Hubzero\Facades\App;

/**
 * Module class for displaying component title
 */
class Title extends Module
{
    /**
     * @var  mixed
     */
    protected $title;

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        // Get the component title div
        if (App::has('ComponentTitle')) {
            $this->title = App::get('ComponentTitle');
        }

        // Get the view
        parent::display();
    }
}
