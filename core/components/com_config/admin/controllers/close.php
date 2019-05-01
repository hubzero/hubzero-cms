<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
