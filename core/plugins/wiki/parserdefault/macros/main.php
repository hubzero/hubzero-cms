<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
