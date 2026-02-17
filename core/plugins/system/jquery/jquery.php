<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Jquery;

use Hubzero\Plugin\Plugin;

// no direct access

/**
 * System plugin for adding jQuery to the document
 */
class Jquery extends Plugin
{
    /**
     * Hook for after routing application
     *
     * @return  void
     */
    public function onAfterRoute()
    {
        if (!App::isSite()) {
            return;
        }

        // Check if active for this client
        if (!$this->params->get('activateSite') || Request::getString('format') == 'pdf') {
            return;
        }

        Html::behavior('framework');

        if ($this->params->get('jqueryui')) {
            Html::behavior('framework', true);
        }

        if ($this->params->get('jqueryfb')) {
            Html::behavior('modal');
        }

        if ($this->params->get('noconflictSite')) {
            Document::addScript(Request::root(true) . '/core/assets/js/jquery.noconflict.js');
        }
    }
}
