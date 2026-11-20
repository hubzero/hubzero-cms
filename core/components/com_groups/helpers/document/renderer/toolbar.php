<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Helpers\View;

class Toolbar extends Renderer
{
    /**
     * Render toolbar to group template
     *
     * @return  string
     */
    public function render()
    {
        return View::displayToolbar($this->groupOrFail(), 'id="group_options"', true);
    }
}
