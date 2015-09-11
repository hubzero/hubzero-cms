<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\BreadCrumbs;

use Hubzero\Module\Module;
use Route;
use Lang;
use Html;
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

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Get the list of crumbs
	 *
	 * @return  array
	 */
	public function getList()
	{
		$items = \Pathway::items();

		$count = count($items);

		// Don't use $items here as it references JPathway properties directly
		$crumbs = array();
		for ($i = 0; $i < $count; $i ++)
		{
			$crumbs[$i] = new stdClass();
			$crumbs[$i]->name = stripslashes(htmlspecialchars($items[$i]->name, ENT_COMPAT, 'UTF-8'));
			$crumbs[$i]->link = Route::url($items[$i]->link);
		}

		if ($this->params->get('showHome', 1))
		{
			$item = new stdClass();
			$item->name = htmlspecialchars($this->params->get('homeText', Lang::txt('MOD_BREADCRUMBS_HOME')));
			$item->link = Route::url('index.php?Itemid=' . \App::get('menu')->getDefault()->id);

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
			if (Lang::isRTL())
			{
				$_separator = Html::asset('image', 'system/arrow_rtl.png', NULL, NULL, true);
			}
			else
			{
				$_separator = Html::asset('image', 'system/arrow.png', NULL, NULL, true);
			}
		}
		else
		{
			$_separator = htmlspecialchars($custom);
		}

		return $_separator;
	}
}
