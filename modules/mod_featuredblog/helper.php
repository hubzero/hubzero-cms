<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a random, featured blog entry
 */
class modFeaturedblog extends JObject
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $this->params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_features' . DS . 'tables' . DS . 'history.php');

		if (!class_exists('FeaturesHistory')) 
		{
			$this->setError(JText::_('FeaturesHistory class missing'));
			return false;
		}

		ximport('Hubzero_User_Profile');

		$database =& JFactory::getDBO();

		$pass = 1;
		$filters = array();
		$filters['limit'] = 1;
		$filters['show']  = trim($this->params->get('show'));

		$this->cls = trim($this->params->get('moduleclass_sfx'));
		$this->txt_length = trim($this->params->get('txt_length'));

		$catid = intval($this->params->get('catid'));
		$start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 00:00:00';
		$end   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 23:59:59';

		$this->row = null;

		$fh = new FeaturesHistory($database);

		// Is a specific content category set?
		if ($catid) 
		{
			// Yes - so we need to check if there's an active article to display
			$juser =& JFactory::getUser();
			$aid = $juser->get('aid', 0);

			$contentConfig =& JComponentHelper::getParams('com_content');
			$noauth = !$contentConfig->get('shownoauth');

			$date =& JFactory::getDate();
			$now = $date->toMySQL();

			$nullDate = $database->getNullDate();

			// Load an article
			$query = 'SELECT a.*,' .
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
				' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
				' FROM #__content AS a' .
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
				' WHERE a.state = 1 ' .
				($noauth ? ' AND a.access <= ' . (int) $aid . ' AND cc.access <= ' . (int) $aid . ' AND s.access <= ' . (int) $aid : '').
				' AND (a.publish_up = ' . $database->Quote($nullDate) . ' OR a.publish_up <= ' . $database->Quote($now) . ') ' .
				' AND (a.publish_down = ' . $database->Quote($nullDate) . ' OR a.publish_down >= ' . $database->Quote($now) . ')' .
				' AND cc.id = '. (int) $catid .
				' AND cc.section = s.id' .
				' AND cc.published = 1' .
				' AND s.published = 1' .
				' ORDER BY a.ordering LIMIT 1';
			$database->setQuery($query, 0, $filters['limit']);
			$rows = $database->loadObjectList();
			if (count($rows) > 0) 
			{
				$this->row = $rows[0];
			}
		}
		$this->profile = null;

		// Do we have an article to display?
		if (!$this->row) 
		{
			// No - so we need to display a member
			// Load some needed libraries
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'blog.comment.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'blog.entry.php');

			// Check the feature history for today's feature
			$fh->loadActive($start, 'blogs');

			// Did we find a feature for today?
			if ($fh->id && $fh->tbl == 'blogs') 
			{
				// Yes - load the member profile
				$this->row = new BlogEntry($database);
				$this->row->load($fh->objectid);
			} 
			else 
			{
				// No - so we need to randomly choose one
				// Get the pass number
				$database->setQuery("SELECT MAX(note) FROM #__feature_history WHERE tbl='blogs'");
				$pass = $database->loadResult();

				// Get all the items featured for the pass 
				$database->setQuery("SELECT DISTINCT(objectid) FROM #__feature_history WHERE tbl='blogs' AND note='$pass'");
				$ids = $database->loadResultArray();

				// Some IDs found. So add SQL that tells the query not to select them (everyone should be featured at least once)
				if ($ids && count($ids) > 0) 
				{
					$idst = implode("','", $ids);

					$filters['sql'] = "m.id NOT IN('$idst')";
				}

				$filters['start']      = 0;
				$filters['state']      = 'public';
				$filters['order']      = "RAND()";
				$filters['search']     = '';
				$filters['scope']      = 'member';
				$filters['group_id']   = 0;
				$filters['authorized'] = false;

				$mp = new BlogEntry($database);
				$rows = $mp->getRecords($filters);
				if ($rows && count($rows) > 0) 
				{
					$this->row = $rows[0];
				} 
				else 
				{
					// No records found. Start a new pass.
					$filters['sql'] = '';
					$pass++;
					$rows = $mp->getRecords($filters);
					if (count($rows) > 0) 
					{
						$this->row = $rows[0];
					}
				}
			}
		}

		// Did we have a result to display?
		if ($this->row) 
		{
			ximport('Hubzero_View_Helper_Html');

			$config =& JComponentHelper::getParams('com_members');

			// Is this a content article or a member profile?
			if (isset($this->row->catid)) 
			{
				// Content article
				$this->title = $this->row->title;
				$this->id    = $this->row->created_by_alias;
				$this->txt   = $this->row->introtext;

				$this->profile = Hubzero_User_Profile::getInstance($this->id);

				// Check if the article has been saved in the feature history
				$fh->loadObject($this->row->id, 'content');
				if (!$fh->id) 
				{
					$fh->featured = $start;
					$fh->objectid = $this->row->id;
					$fh->tbl      = 'content';
					$fh->note     = $pass;
					$fh->store();
				}
			} 
			else 
			{
				if (!isset($this->profile) && !is_object($this->profile)) 
				{
					$this->profile = Hubzero_User_Profile::getInstance($this->row->created_by);
				}
				$this->txt   = $this->row->content;
				$this->title = $this->row->title;
				$this->id    = $this->row->id;

				// Check if this has been saved in the feature history
				if (!$fh->id) 
				{
					$fh->featured = $start;
					$fh->objectid = $this->row->id;
					$fh->tbl      = 'blogs';
					$fh->note     = $pass;
					$fh->store();
				}
			}

			// Do we have a picture?
			$thumb = '';
			if (isset($this->profile->picture) && $this->profile->picture != '') 
			{
				// Yes - so build the path to it
				$thumb  = DS . trim($config->get('webpath', '/site/members'), DS);
				$thumb .= DS . Hubzero_View_Helper_Html::niceidformat($this->profile->uidNumber) . DS . $this->profile->picture;

				// No - use default picture
				if (is_file(JPATH_ROOT . $thumb)) 
				{
					// Build a thumbnail filename based off the picture name
					$thumb = Hubzero_View_Helper_Html::thumbit($thumb);

					if (!is_file(JPATH_ROOT . $thumb)) 
					{
						// Create a thumbnail image
						include_once(JPATH_ROOT . DS . 'components' . DS . 'com_members' . DS . 'helpers' . DS . 'imghandler.php');

						$ih = new MembersImgHandler();
						$ih->set('image', $this->profile->picture);
						$ih->set('path', JPATH_ROOT . DS . trim($config->get('webpath', '/site/members'), DS) . DS . Hubzero_View_Helper_Html::niceidformat($this->profile->uidNumber) . DS);
						$ih->set('maxWidth', 50);
						$ih->set('maxHeight', 50);
						$ih->set('cropratio', '1:1');
						$ih->set('outputName', $ih->createThumbName());
						if (!$ih->process()) 
						{
							$this->setError($ih->getError());
						}
					}
				}
			}

			// No - use default picture
			if (!is_file(JPATH_ROOT . $thumb)) 
			{
				$thumb = DS . ltrim($config->get('defaultpic', '/components/com_members/images/profile.gif'), DS);
				// Build a thumbnail filename based off the picture name
				$thumb = Hubzero_View_Helper_Html::thumbit($thumb);
			}

			$this->thumb   = $thumb;
			$this->filters = $filters;

			require(JModuleHelper::getLayoutPath($this->module->module));
		}
	}
}

