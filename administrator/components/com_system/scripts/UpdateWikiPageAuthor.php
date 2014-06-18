<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2008-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'UpdateWikiPageAuthor'
 *
 * Long description (if any) ...
 */
class UpdateWikiPageAuthor extends SystemHelperScript
{

	/**
	 * Description for '_description'
	 *
	 * @var string
	 */
	protected $_description = 'Transitions wiki page "authors" string to table of user IDs.';

	/**
	 * Short description for 'run'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function run()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'author.php');

		$this->_db->setQuery("SHOW TABLES");
		$tables = $this->_db->loadResultArray();
		if (!in_array('jos_wiki_page_author', $tables)) {
			$this->_db->setQuery("CREATE TABLE `#__wiki_page_author` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `user_id` int(11) default '0',
			  `page_id` int(11) default '0',
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
			}
		}
		$this->_db->setQuery("SELECT id, authors FROM #__wiki_page WHERE authors!='' AND authors IS NOT NULL");
		$pages = $this->_db->loadObjectList();
		if ($pages) {
			foreach ($pages as $page)
			{
				$authors = explode(',', $page->authors);
				$authors = array_map('trim', $authors);
				foreach ($authors as $author)
				{
					$targetuser = JUser::getInstance($author);

					// Ensure we found an account
					if (is_object($targetuser)) {
						$wpa = new WikiTableAuthor($this->_db);
						$wpa->page_id = $page->id;
						$wpa->user_id = $targetuser->get('id');
						if ($wpa->check()) {
							$wpa->store();
						} else {
							$this->setError("Error adding page author: (page_id: $wpa->page_id, user_id: $wpa->user_id).");
						}
					}
				}
			}
		}
		/*if (!$this->getError()) {
			$this->_db->setQuery("ALTER TABLE $this->_tbl DROP COLUMN `authors`");
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
			}
		}*/
		if (!$this->getError()) {
			echo '<p class="passed">Success!</p>';
		}
		echo '<p class="error">' . $this->getError() . '</p>';
	}
}
