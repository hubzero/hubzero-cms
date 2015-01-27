<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Footer;

use Hubzero\Module\Module;
use JFactory;
use JString;
use JText;

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

		$app  = JFactory::getApplication();
		$date = JFactory::getDate();

		$cur_year   = $date->format('Y');
		$csite_name = $app->getCfg('sitename');

		if (is_int(JString::strpos(JText::_('MOD_FOOTER_LINE1'), '%date%')))
		{
			$line1 = str_replace('%date%', $cur_year, JText::_('MOD_FOOTER_LINE1'));
		}
		else
		{
			$line1 = JText::_('MOD_FOOTER_LINE1');
		}

		if (is_int(JString::strpos($line1, '%sitename%')))
		{
			$lineone = str_replace('%sitename%', $csite_name, $line1);
		}
		else
		{
			$lineone = $line1;
		}

		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

		require $this->getLayoutPath($params->get('layout', 'default'));
	}
}
