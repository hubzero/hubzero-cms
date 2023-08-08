<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for displaying a list of pages
 */
class TitleIndexMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all wiki pages into the output. Accepts a prefix string as parameter: if provided, only pages with names that start with the prefix are included in the resulting list. If this parameter is omitted, all pages are listed.</p><p>The list may have a sorting applied by adding the sort=[title,created(oldest to newest),modified(newest to oldest)] argument. For example, <code>[[TitleIndex(sort=modified)]]</code> will list all pages by their last modified date (most recent to oldest). If you have a page prefix, simply add a comma and the sort parameter <em>after</em>. For example: <code>[[TitleIndex(Help, sort=modified)]]</code></p>';
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

		$sort = '';
		if ($et)
		{
			$et = strip_tags($et);

			if (strstr($et, ','))
			{
				$attribs = explode(',', $et);
				$et = trim($attribs[0]);
				$sort = strtolower(trim($attribs[1]));
			}

			if (strtolower($et) == 'sort=modified'
			 || strtolower($et) == 'sort=created'
			 || strtolower($et) == 'sort=title')
			{
				$sort = $et;
				$et = '';
			}
		}

		$pages = \Components\Wiki\Models\Page::all()
			->whereEquals('state', 1);

		if ($et)
		{
			$pages->whereLike('pagename', strtolower($et));
		}

		if ($this->domain && $et && substr(strtolower($et), 0, 4) != 'help')
		{
			$pages->whereEquals('scope', $this->domain);
			$pages->whereEquals('scope_id', $this->domain_id);
		}
		else
		{
			$pages->whereEquals('scope', 'site');
			$pages->whereEquals('scope_id', 0);
		}

		switch ($sort)
		{
			case 'sort=created':
				$pages->order('created', 'asc');
			break;
			case 'sort=modified':
				$pages->order('modified', 'asc');
			break;
			case 'sort=title':
			default:
				$pages->order('title', 'asc');
			break;
		}

		$rows = $pages->rows();

		// Did we get a result from the database?
		if ($rows)
		{
			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				if ($row->get('pagename') == $this->pagename)
				{
					continue;
				}

				if ($row->get('namespace') == 'Help')
				{
					$row->set('path', ($row->get('path') ? rtrim($this->scope, '/') . '/' . ltrim($row->get('path'), '/') : $this->scope));
					$row->set('scope', $this->domain);
					$row->set('scope_id', $this->domain_id);
				}

				$html .= '<li><a href="' . Route::url($row->link()) . '">';
				$html .= stripslashes($row->get('title', $row->get('pagename')));
				$html .= '</a></li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		}

		// Return error message
		return '(No ' . $et . ' pages to display)';
	}
}
