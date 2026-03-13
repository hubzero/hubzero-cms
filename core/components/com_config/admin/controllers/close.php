<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Facades\Document;

/**
 * Controller class for closing the config
 */
class Close extends AdminController
{
    /**
     * Close the configuration and redirect
     *
     * @return  void
     */
    public function displayTask()
    {
        if (Document::getViewEngine() === 'blade') {
            Document::setBodyAttribute('data-auto-close', 'close-refresh');
        } else {
            Document::addScriptDeclaration('
			window.parent.location.href=window.parent.location.href;
			window.parent.$.fancybox.close();
		');
        }
    }
}
