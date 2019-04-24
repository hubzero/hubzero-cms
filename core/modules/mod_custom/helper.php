<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Custom;

use Hubzero\Module\Module;
use Plugin;
use Html;

/**
 * Module class for displaying custom HTML
 */
class Helper extends Module
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

		if ($params->def('prepare_content', 1))
		{
			Plugin::import('content');
			$module->content = Html::content('prepare', $module->content, '', 'mod_custom.content');
		}

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}
}
