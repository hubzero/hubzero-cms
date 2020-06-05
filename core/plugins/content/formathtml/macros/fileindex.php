<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * Wiki macro class for listing files
 */
class FileIndex extends Macro
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
	 * @return     string
	 */
	public function render()
	{
		$et = $this->args;
		$live_site = rtrim(\Request::base(), '/');

		// What pages are we getting?
		if ($et)
		{
			$et = strip_tags($et);
			// Get pages with a prefix
			$sql  = "SELECT * FROM `#__wiki_attachments` WHERE LOWER(filename) LIKE " . $this->_db->quote(strtolower($et) . '%') . " AND page_id=" . $this->_db->quote($this->pageid) . " ORDER BY created ASC";
		}
		else
		{
			// Get all pages
			$sql  = "SELECT * FROM `#__wiki_attachments` WHERE page_id=" . $this->_db->quote($this->pageid) . " ORDER BY created ASC";
		}

		// Perform query
		$this->_db->setQuery($sql);
		$rows = $this->_db->loadObjectList();

		// Did we get a result from the database?
		if ($rows)
		{
			$config = Component::params('com_wiki');
			if ($this->filepath != '')
			{
				$config->set('filepath', $this->filepath);
			}

			// Build and return the link
			$html = '<ul>';
			foreach ($rows as $row)
			{
				$link = $live_site . DS . trim($config->get('filepath', '/site/wiki'), DS) . DS . $this->page_id . DS . $row->filename;
				$fpath = PATH_APP . DS . trim($config->get('filepath', '/site/wiki'), DS) . DS . $this->page_id . DS . $row->filename;

				$html .= '<li><a href="' . \Route::url($link) . '">' . $row->filename . '</a> (' . (file_exists($fpath) ? \Hubzero\Utility\Number::formatBytes(filesize($fpath)) : '-- file not found --') . ') ';
				$huser = \User::getInstance($row->created_by);
				if ($huser->get('id'))
				{
					$html .= '- added by <a href="' . \Route::url('index.php?option=com_members&id=' . $huser->get('id')) . '">' . stripslashes($huser->get('name')) . '</a> ';
				}
				if ($row->created && $row->created != '0000-00-00 00:00:00')
				{
					$html .= \Date::of($row->created)->relative() . '. ';
				}
				$html .= ($row->description) ? '<span>"' . stripslashes($row->description) . '"</span>' : '';
				$html .= '</li>' . "\n";
			}
			$html .= '</ul>';

			return $html;
		}
		else
		{
			// Return error message
			//return '(TitleIndex('.$et.') failed)';
			return '(No ' . $et . ' files to display)';
		}
	}
}
