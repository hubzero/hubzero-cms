<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Helpers\View;

class Menu extends Renderer
{
    /**
     * Render menu to group template
     *
     * @return  string
     */
    public function render()
    {
        return View::displaySections($this->groupOrFail(), 'class="cf"');
    }
}
