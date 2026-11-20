<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
        Document::addScriptDeclaration('
			window.parent.location.href=window.parent.location.href;
			window.parent.$.fancybox.close();
		');
    }
}
