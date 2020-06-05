<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Admin\Controllers;

use Hubzero\Component\AdminController;
use Document;

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
