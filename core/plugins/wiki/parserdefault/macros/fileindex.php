<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for listing files
 */
class FileIndexMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.';
		$txt['html'] = '<p>Inserts an alphabetic list of all files and images attached to this page into the output. Accepts a prefix string as parameter: if provided, only files with names that start with the prefix are included in the resulting list. If this parameter is omitted, all files are listed.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		$et = $this->args;
		$live_site = rtrim(Request::base(), '/');

		// Get resource by ID
		$attach = \Components\Wiki\Models\Attachment::all()
			->whereEquals('page_id', $this->pageid);

		if ($et)
		{
			$et = strip_tags($et);

			$attach->whereLike('filename', strtolower($et) . '%');
		}

		$rows = $attach->rows();

		// Did we get a result from the database?
		if ($rows)
		{
			$config = Component::params('com_wiki');
			if ($this->filepath != '')
			{
				$config->set('filepath', $this->filepath);
			}

			$page = \Components\Wiki\Models\Page::oneOrFail($this->pageid);

			if ($page->get('namespace') == 'help')
			{
				$page->set('path', ($page->get('path') ? rtrim($this->scope, '/') . '/' . ltrim($page->get('path'), '/') : $this->scope));
			}

			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				$page->set('pagename', $page->get('pagename') . '/' . 'File:' . $row->get('filename'));

				$link  = $page->link();
				$fpath = $row->filespace() . DS . $this->pageid . DS . $row->get('filename');

				$html .= '<li><a href="' . Route::url($link) . '">' . $row->get('filename') . '</a> (' . (file_exists($fpath) ? \Hubzero\Utility\Number::formatBytes(filesize($fpath)) : '-- file not found --') . ') ';
				$huser = $row->creator();
				if ($huser->get('id'))
				{
					$html .= '- added by <a href="' . Route::url('index.php?option=com_members&id=' . $huser->get('id')) . '">' . stripslashes($huser->get('name')) . '</a> ';
				}
				if ($row->get('created') && $row->get('created') != '0000-00-00 00:00:00')
				{
					$html .= Date::of($row->get('created'))->relative() . '. ';
				}
				$html .= $row->get('description') ? '<span>"' . stripslashes($row->get('description')) . '"</span>' : '';
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		}

		// Return error message
		return '(No ' . $et . ' files to display)';
	}
}
