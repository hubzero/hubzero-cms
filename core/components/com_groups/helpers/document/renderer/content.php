<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document\Renderer;

use Components\Groups\Helpers\Document\Renderer;
use Components\Groups\Helpers\View;

class Content extends Renderer
{
    /**
     * Render content to group template
     *
     * @return  mixed
     */
    public function render()
    {
        // get the scope
        $scope = (isset($this->params->scope)) ? $this->params->scope : 'main';

        // based on which scope display content
        switch ($scope) {
            case 'before':
                View::displayBeforeSectionsContent($this->groupOrFail());

                return;
            default:
                return $this->content;
        }
    }
}
