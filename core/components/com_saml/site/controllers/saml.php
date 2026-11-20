<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Site\Controllers;

use Hubzero\Facades\App;

/**
 * There is nothing to serve at the bare /saml route — every endpoint lives
 * under /saml/idp/*.
 */
class Saml extends \Hubzero\Component\SiteController
{
    public function execute()
    {
        App::abort(404);
    }
}
