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
 * Module class for displaying featured members
 */
class modFeaturedmember extends JObject
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
	 * Generate module contents
	 * 
	 * @return     void
	 */
	public function run()
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_features' . DS . 'tables' . DS . 'history.php');

		if (!class_exists('FeaturesHistory')) 
		{
			$this->setError(JText::_('FeaturesHistory class missing'));
			return false;
		}

		ximport('Hubzero_User_Profile');

		$database =& JFactory::getDBO();

		$filters = array();
		$filters['limit'] = 1;
		$filters['show']  = trim($this->params->get('show'));

		$this->cls = trim($this->params->get('moduleclass_sfx'));
		$this->txt_length = trim($this->params->get('txt_length'));

		$catid = trim($this->params->get('catid'));
		$min   = trim($this->params->get('min_contributions'));
		$show  = trim($this->params->get('show'));

		$start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 00:00:00';
		$end   = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 23:59:59';

		$this->row = null;

		$fh = new FeaturesHistory($database);

		$juser =& JFactory::getUser();

		// Is a specific content category set?
		if ($catid) 
		{
			// Yes - so we need to check if there's an active article to display
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
				($noauth ? ' AND a.access <= ' . (int) $aid . ' AND cc.access <= ' . (int) $aid . ' AND s.access <= ' . (int) $aid : '') .
				' AND (a.publish_up = ' . $database->Quote($nullDate) . ' OR a.publish_up <= ' . $database->Quote($now) . ') ' .
				' AND (a.publish_down = ' . $database->Quote($nullDate) . ' OR a.publish_down >= ' . $database->Quote($now) . ')' .
				' AND cc.id = '. (int) $catid .
				' AND cc.section = s.id' .
				' AND cc.published = 1' .
				' AND s.published = 1' .
				' ORDER BY a.ordering';
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
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');

			// Check the feature history for today's feature
			$fh->loadActive($start, 'profiles');

			// Did we find a feature for today?
			if ($fh->id && $fh->tbl == 'profiles') 
			{
				// Yes - load the member profile
				$this->row = new MembersProfile($database);
				$this->row->load($fh->objectid);
			} 
			else 
			{
				// No - so we need to randomly choose one
				$filters['start']      = 0;
				$filters['sortby']     = "RAND()";
				$filters['search']     = '';
				$filters['authorized'] = false;
				$filters['show']       = $show;
				if ($min) 
				{
					$filters['contributions'] = $min;
				}

				$mp = new MembersProfile($database);

				$rows = $mp->getRecords($filters, false);
				if (count($rows) > 0) 
				{
					$this->row = $rows[0];
				}

				// Load their bio
				$this->profile = Hubzero_User_Profile::getInstance($this->row->uidNumber);

				if (trim(strip_tags($this->profile->get('bio'))) == '') 
				{
					return '';
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

				$this->profile = Hubzero_User_Profile::getInstance($id);
				$this->row->picture = $this->profile->get('picture');

				// Check if the article has been saved in the feature history
				$fh->loadObject($this->row->id, 'content');
				if (!$fh->id) 
				{
					$fh->featured = $start;
					$fh->objectid = $this->row->id;
					$fh->tbl      = 'content';
					$fh->store();
				}
			} 
			else 
			{
				if (!isset($this->profile) && !is_object($this->profile)) 
				{
					$this->profile = Hubzero_User_Profile::getInstance($this->row->uidNumber);
				}

				$paramsClass = 'JParameter';
				if (version_compare(JVERSION, '1.6', 'ge'))
				{
					$paramsClass = 'JRegistry';
				}

				$rparams = new $paramsClass($this->profile->get('params'));
				$this->params = $config;
				$this->params->merge($rparams);

				if ($this->params->get('access_bio') == 0
				 || ($this->params->get('access_bio') == 1 && !$juser->get('guest'))
				) 
				{
					$this->txt = $this->profile->get('bio');
				} 
				else 
				{
					$this->txt = '';
				}

				// Member profile
				$this->title = $this->row->name;
				if (!trim($this->title)) 
				{
					$this->title = $this->row->givenName . ' ' . $this->row->surname;
				}
				$this->id = $this->row->uidNumber;

				// Check if this has been saved in the feature history
				if (!$fh->id) 
				{
					$fh->featured = $start;
					$fh->objectid = $this->row->uidNumber;
					$fh->tbl      = 'profiles';
					$fh->store();
				}
			}

			// Do we have a picture?
			$thumb = '';
			if (isset($this->row->picture) && $this->row->picture != '') 
			{
				// Yes - so build the path to it
				$thumb  = DS . trim($config->get('webpath', '/site/members'), DS);
				$thumb .= DS . Hubzero_View_Helper_Html::niceidformat($this->row->uidNumber) . DS . $this->row->picture;

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
						$ih->set('image', $this->row->picture);
						$ih->set('path', JPATH_ROOT . DS . trim($config->get('webpath', '/site/members'), DS) . DS . Hubzero_View_Helper_Html::niceidformat($this->row->uidNumber) . DS);
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

	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		$juser =& JFactory::getUser();

		if (!$juser->get('guest') && intval($this->params->get('cache', 0)))
		{
			$cache =& JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 15)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . date('Y-m-d H:i:s', time()) . ' -->';
			return;
		}

		$this->run();
	}
}

