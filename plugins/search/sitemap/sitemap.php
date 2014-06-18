<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'plgSearchSiteMap'
 *
 * Long description (if any) ...
 */
class plgSearchSiteMap extends SearchPlugin
{

	/**
	 * Short description for 'getName'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	public static function getName()
	{
		return 'Site Map';
	}

	/**
	 * Short description for 'onYSearch'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $request Parameter description (if any) ...
	 * @param      object &$results Parameter description (if any) ...
	 * @return     void
	 */
	public static function onSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(s.title, s.description) against (\'' . join(' ', $terms['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
		{
			$addtl_where[] = "(s.title LIKE '%$mand%' OR s.description LIKE '%$mand%')";
		}
		foreach ($terms['forbidden'] as $forb)
		{
			$addtl_where[] = "(s.title NOT LIKE '%$forb%' AND s.description NOT LIKE '%$forb%')";
		}

		$results->add(new SearchResultSQL(
			"SELECT
				title, description, link, $weight as weight
			FROM
				#__ysearch_site_map s
			WHERE $weight > 0" . ($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
		));
	}

	/**
	 * Short description for 'onYSearchAdministrate'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $context Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function onSearchAdministrate($context)
	{
		$dbh = JFactory::getDBO();
		$dbh->setQuery('SELECT id, title, link, description FROM #__ysearch_site_map ORDER BY title');
		$map = $dbh->loadAssocList();
		$edit = NULL;
		if (array_key_exists('sitemap', $context)
		 && array_key_exists('edit_id', $context['sitemap'])
		 && (!array_key_exists('save_id', $context['sitemap']) || $context['sitemap']['save_id'] != $context['sitemap']['edit_id']))
		{
			$edit = $context['sitemap']['edit_id'];
		}

		$html = array();
		//$html[] = '<p>The site search is aimed at accessing content, not structure. So, queries look for certain parts of the site may not work as well as one might hope, instead turning up tangentially related pieces of content. By encoding the site structure as content here the search has a better chance of doing the right thing.</p>';
		$html[] = '<form action="index.php?option=com_search" method="post">';
		$html[] = '<input type="hidden" name="search-task" value="SiteMap' . ($edit ? 'SaveEdit' : 'Edit') . '" />';
		$html[] = '<table class="adminlist">';
		$html[] = '<thead>';
		$html[] = '<tr><th>' . JText::_('COM_SEARCH_COL_TITLE') . '</th><th>' . JText::_('COM_SEARCH_COL_LINK') . '</th><th>' . JText::_('COM_SEARCH_COL_DESCRIPTION') . '</th><th></th></tr>';
		$html[] = '</thead>';
		$html[] = '<tbody>';
		foreach ($map as $item)
		{
			$html[] = '<tr>';
			if ($edit == $item['id'])
			{
				$html[] = '<td><input type="text" name="sm-title" value="' . htmlentities(array_key_exists('sm-title', $_POST) ? $_POST['sm-title'] : $item['title']) . '" /></td>';
				$html[] = '<td><input type="text" name="sm-link" value="' . htmlentities(array_key_exists('sm-link', $_POST) ? $_POST['sm-link'] : $item['link']) . '" /></td>';
				$html[] = '<td><textarea cols="60" rows="3" name="sm-description">' . htmlentities(array_key_exists('sm-description', $_POST) ? $_POST['sm-description'] : $item['description']) . '</textarea></td>';
				$html[] = '<td><input type="hidden" name="sm-id" value="' . $item['id'] . '" /><input type="submit" name="save" value="' . JText::_('COM_SEARCH_SAVE') . '" /><input type="submit" name="cancel" value="' . JText::_('COM_SEARCH_CANCEL') . '" /></td>';
			}
			else
			{
				$html[] = '<td>' . htmlentities($item['title']) . '</td>';
				$html[] = '<td>' . htmlentities($item['link']) . '</td>';
				$html[] = '<td>' . htmlentities($item['description']) . '</td>';
				if ($edit)
				{
					$html[] = '<td></td>';
				}
				else
				{
					$html[] = '<td><input type="hidden" name="ysearch-task" value="SiteMapEdit" /><input type="submit" name="edit-' . $item['id'] . '" value="' . JText::_('COM_SEARCH_EDIT') . '" /><input type="submit" name="delete-' . $item['id'] . '" value="' . JText::_('COM_SEARCH_DELETE') . '" /></td>';
				}
			}
			$html[] = '</tr>';
		}
		if (!$edit)
		{
			$html[] = '<tr>';
			$html[] = '<td><input type="text" name="new-sm-title" value="' . htmlentities(array_key_exists('new-sm-title', $_POST) ? $_POST['new-sm-title'] : '') . '" /></td>';
			$html[] = '<td><input type="text" name="new-sm-link" value="' . htmlentities(array_key_exists('new-sm-link', $_POST) ? $_POST['new-sm-link'] : '') . '" /></td>';
			$html[] = '<td><textarea cols="60" rows="3" name="new-sm-description">' . htmlentities(array_key_exists('new-sm-description', $_POST) ? $_POST['new-sm-description'] : '') . '</textarea></td>';
			$html[] = '<td><input type="submit" name="add" value="' . JText::_('COM_SEARCH_ADD') . '" /></td>';
			$html[] = '</tr>';
		}
		$html[] = '</tbody>';
		$html[] = '</table>';
		$html[] = '</form>';
		return array('Site Map', join("\n", $html));
	}

	/**
	 * Short description for 'save_entry_from_post'
	 *
	 * Long description (if any) ...
	 *
	 * @param      boolean $update Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private static function save_entry_from_post($update = false)
	{
		$dbh = JFactory::getDBO();
		$fields = array('sm-title', 'sm-link', 'sm-description');
		if ($update)
		{
			$fields[] = 'sm-id';
		}

		foreach ($fields as $key)
		{
			if (!$update)
			{
				$key = 'new-' . $key;
			}
			if (!array_key_exists($key, $_POST) || empty($_POST[$key]))
			{
				return array('sitemap', '<p class="error">' . JText::_('COM_SEARCH_ERROR_REQUIRED_FIELDS') . '</p>', array());
			}
		}

		$id = NULL;
		if ($update)
		{
			$dbh->execute('UPDATE #__ysearch_site_map SET title = ' . $dbh->quote($_POST['sm-title']) . ', description = ' . $dbh->quote($_POST['sm-description']) . ', link = ' . $dbh->quote($_POST['sm-link']) . ' WHERE id = ' . (int)$_POST['sm-id']);
			$id = (int)$_POST['sm-id'];
		}
		else
		{
			$dbh->execute('INSERT INTO #__ysearch_site_map(title, description, link) VALUES (' . $dbh->quote($_POST['new-sm-title']) . ', ' . $dbh->quote($_POST['new-sm-description']) . ', ' . $dbh->quote($_POST['new-sm-link']) . ')');
			unset($_POST['new-sm-title']);
			unset($_POST['new-sm-description']);
			unset($_POST['new-sm-link']);
			$id = $dbh->insertid();
		}
		return array('sitemap', '<p class="success">' . JText::_('COM_SEARCH_ENTRY_SAVED') . '</p>', array('save_id' => $id));
	}

	/**
	 * Short description for 'onYSearchTaskSiteMapEdit'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public static function onSearchTaskSiteMapEdit()
	{
		if (array_key_exists('add', $_POST))
		{
			return self::save_entry_from_post();
		}
		foreach ($_POST as $k => $v)
		{
			if (preg_match('/(delete|edit)-(\d+)/', $k, $id))
			{
				if ($id[1] == 'edit')
				{
					return array('sitemap', '', array('edit_id' => (int)$id[2]));
				}
				else
				{
					$dbh = JFactory::getDBO();
					$dbh->execute('DELETE FROM #__ysearch_site_map WHERE id = ' . (int)$id[2]);
					return array('sitemap', '<p class="success">' . JText::_('COM_SEARCH_ENTRY_DELETED') . '</p>', array());
				}
			}
		}
		return array('sitemap', '', array());
	}

	/**
	 * Short description for 'onYSearchTaskSiteMapSaveEdit'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	public static function onSearchTaskSiteMapSaveEdit()
	{
		if (array_key_exists('cancel', $_POST))
		{
			return array('sitemap', '', array());
		}

		return self::save_entry_from_post(true);
	}
}

