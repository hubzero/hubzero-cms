<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Custom;

use Hubzero\Module\Module;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\Html;

/**
 * Module class for displaying custom HTML
 */
class Custom extends Module
{
    /**
     * Display module
     *
     * @return  void
     */
    public function display()
    {
        // Legacy compatibility for older view overrides
        $params = $this->params;
        $module = $this->module;

        if ($params->def('prepare_content', 1)) {
            Plugin::import('content');
            $module->content = Html::content('prepare', $module->content, '', 'mod_custom.content');
        }

        $moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx', ''));

        require $this->getLayoutPath($params->get('layout', 'default'));
    }
}
