<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Admin\Controllers;

use Hubzero\Component\AdminController;

class SAML extends AdminController
{
        public function displayTask()
        {
                $this->view->setLayout('saml')->display();
        }
}
