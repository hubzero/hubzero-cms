<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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

namespace Modules\BreadCrumbs;

use Hubzero\Module\Module;
use JModuleHelper;
use JFactory;
use JHtml;
use JRoute;
use JText;
use stdClass;

/**
 * Module class for displaying breadcrumbs
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// Legacy support in case old view overrides reference
		// $params instead of $this->params
		$params = $this->params;

		// Get the breadcrumbs
		$list   = $this->getList();
		$count  = count($list);

		// Set the default separator
		$separator = $this->setSeparator($this->params->get('separator'));
		$moduleclass_sfx = htmlspecialchars($this->params->get('moduleclass_sfx'));

		require JModuleHelper::getLayoutPath($this->module->module, $this->params->get('layout', 'default'));
	}

	/**
	 * Get the list of crumbs
	 *
	 * @return  array
	 */
	public function getList()
	{
		// Get the PathWay object from the application
		$app     = JFactory::getApplication();
		$pathway = $app->getPathway();
		$items   = $pathway->getPathWay();

		$count = count($items);

		// Don't use $items here as it references JPathway properties directly
		$crumbs = array();
		for ($i = 0; $i < $count; $i ++)
		{
			$crumbs[$i] = new stdClass();
			$crumbs[$i]->name = stripslashes(htmlspecialchars($items[$i]->name, ENT_COMPAT, 'UTF-8'));
			$crumbs[$i]->link = JRoute::_($items[$i]->link);
		}

		if ($this->params->get('showHome', 1))
		{
			$item = new stdClass();
			$item->name = htmlspecialchars($this->params->get('homeText', JText::_('MOD_BREADCRUMBS_HOME')));
			$item->link = JRoute::_('index.php?Itemid=' . $app->getMenu()->getDefault()->id);

			array_unshift($crumbs, $item);
		}

		return $crumbs;
	}

	/**
	 * Set the breadcrumbs separator for the breadcrumbs display.
	 *
	 * @param   string  $custom  Custom xhtml complient string to separate the items of the breadcrumbs
	 * @return  string  Separator string
	 */
	public function setSeparator($custom = null)
	{
		// If a custom separator has not been provided we try to load a template
		// specific one first, and if that is not present we load the default separator
		if ($custom == null)
		{
			if (JFactory::getLanguage()->isRTL())
			{
				$_separator = JHtml::_('image', 'system/arrow_rtl.png', NULL, NULL, true);
			}
			else
			{
				$_separator = JHtml::_('image', 'system/arrow.png', NULL, NULL, true);
			}
		}
		else
		{
			$_separator = htmlspecialchars($custom);
		}

		return $_separator;
	}
}
