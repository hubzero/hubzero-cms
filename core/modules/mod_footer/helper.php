<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Footer;

use Hubzero\Module\Module;
use Config;
use Lang;
use Date;

/**
 * Module class for diplaying site footer
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
		// [!] Legacy compatibility
		$params = $this->params;

		$cur_year   = Date::format('Y');
		$csite_name = Config::get('sitename');

		if (is_int(strpos(Lang::txt('MOD_FOOTER_LINE1'), '%date%')))
		{
			$line1 = str_replace('%date%', $cur_year, Lang::txt('MOD_FOOTER_LINE1'));
		}
		else
		{
			$line1 = Lang::txt('MOD_FOOTER_LINE1');
		}

		if (is_int(strpos($line1, '%sitename%')))
		{
			$lineone = str_replace('%sitename%', $csite_name, $line1);
		}
		else
		{
			$lineone = $line1;
		}

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx',''));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}
}
