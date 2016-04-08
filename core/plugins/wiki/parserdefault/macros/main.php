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

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying a message with links to articles
 */
class MainMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Displays a message containing links to articles with further details on a topic. Accepts a list of comma-separated page names.';
		$txt['html'] = '<p>Displays a message containing links to articles with further details on a topic. Accepts a list of comma-separated page names.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et)
		{
			return '';
		}

		$pages = explode(',', $et);

		$html = '<div class="rellink relarticle mainarticle">Main articles: ';

		foreach ($pages as $page)
		{
			$page = trim($page);

			// Is it numeric?
			$scope = '';
			if (is_numeric($page))
			{
				// Yes
				$page = intval($page);
			}
			else
			{
				$page = trim($page, '/');
				if (strstr($page, '/') && !strstr($page, ' '))
				{
					$bits = explode('/', $page);
					$page = array_pop($bits);
					$scope = implode('/', $bits);
				}
			}

			if ($this->domain != '' && $scope == '')
			{
				$scope = $this->scope;
			}
			// No, get resource by alias
			if (strstr($page, ' '))
			{
				$g = \Components\Wiki\Models\Page::oneByTitle($page, $this->domain, $this->domain_id);
			}
			else
			{
				$g = \Components\Wiki\Models\Page::oneByPath(($scope ? $scope . '/' : '') . $page, $this->domain, $this->domain_id);
			}
			if (!$g->get('id'))
			{
				$g->set('pagename', $page);
				$g->set('scope', $this->domain);
				$g->set('scope_id', $this->domain_id);
			}

			// Build and return the link
			if (!$g->get('id'))
			{
				$l[] = '<a href="' . Route::url($g->link()) . '">' . stripslashes($g->title) . '</a>';
			}
			else
			{
				$l[] = '<a class="int-link" href="' . Route::url($g->link()) . '">' . stripslashes($g->title) . '</a>';
			}
		}

		if (count($l) > 1)
		{
			$last = array_pop($l);

			$html .= implode(', ', $l);
			$html .= ' and ' . $last;
		}
		else
		{
			$html .= $l[0];
		}

		return $html . '</div>';
	}
}
