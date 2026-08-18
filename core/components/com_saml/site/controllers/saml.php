<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Site\Controllers;

use App;

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
