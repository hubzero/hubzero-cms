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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for showing content spotlight
 */
class modSpotlight extends JObject
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
	 * @param      object $params JParameter
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
	 * Check if a property is set
	 * 
	 * @param      string $property Property to check
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
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
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . date('Y-m-d H:i:s', time()) . ' -->';
			return;
		}

		$this->run();
	}

	/**
	 * Get module contents
	 * 
	 * @return     void
	 */
	public function run()
	{
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'question.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'response.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_features' . DS . 'tables' . DS . 'history.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'blog.entry.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'tables' . DS . 'blog.comment.php');

		ximport('Hubzero_User_Profile');
		ximport('Hubzero_View_Helper_Html');

		if (!class_exists('FeaturesHistory'))
		{
			$this->error = true;
			return false;
		}

		$this->database = JFactory::getDBO();

		// Get the admin configured settings
		$filters = array();
		$filters['limit'] = 5;
		$filters['start'] = 0;

		// featured items
		$tbls = array('resources', 'profiles');

		$spots    = array();
		$spots[0] = trim($this->params->get('spotone'));
		$spots[1] = trim($this->params->get('spottwo'));
		$spots[2] = trim($this->params->get('spotthree'));
		$spots[3] = trim($this->params->get('spotfour'));
		$spots[4] = trim($this->params->get('spotfive'));
		$spots[5] = trim($this->params->get('spotsix'));
		$spots[6] = trim($this->params->get('spotseven'));

		$numspots = $this->params->get('numspots', 3);

		// some collectors
		$activespots = array();
		$rows = array();

		// styling
		$cls = trim($this->params->get('moduleclass_sfx'));
		$txtLength = trim($this->params->get('txt_length'));

		$start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 00:00:00';
		$end = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 23:59:59';

		$this->html = '';
		$k = 1;
		$out = '';

		for ($i = 0, $n = $numspots; $i < $numspots; $i++)
		{
			$spot = $spots[$i];
			if ($spot == '')
			{
				continue;
			}

			$row = null;
			$out = '';
			$tbl = '';
			$fh = new FeaturesHistory($this->database);

			$tbl = ($spot == 'tools' || $spot == 'nontools') ? 'resources' : '';
			$tbl = $spot == ('members') ? 'profiles' : $tbl;
			$tbl = $spot == ('topics')  ? 'topics'   : $tbl;
			$tbl = $spot == ('itunes')  ? 'itunes'   : $tbl;
			$tbl = $spot == ('answers') ? 'answers'  : $tbl;
			$tbl = $spot == ('blog')    ? 'blog'     : $tbl;
			$tbl = (!$tbl) ? array_rand($tbls, 1) : $tbl;

			// Check the feature history for today's feature
			$fh->loadActive($start, $tbl, $spot . $k);

			// Did we find a feature for today?

			if ($fh->id && $fh->objectid)
			{
				switch ($fh->tbl)
				{
					case 'resources':
					case 'itunes':
						// Load the resource
						$row = new ResourcesResource($this->database);
						$row->load($fh->objectid);
						if ($row)
						{
							$row->typetitle = $row->getTypetitle();
						}
					break;

					case 'profiles':
						// Load the member profile
						$row = new MembersProfile($this->database);
						$row->load($fh->objectid);
					break;

					case 'topics':
						include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php');
						// Yes - load the topic page
						$row = new WikiPage($this->database);
						$row->load($fh->objectid);
					break;

					case 'answers':
						// Yes - load the question
						$row = new AnswersQuestion($this->database);
						$row->load($fh->objectid);

						$ar = new AnswersResponse($this->database);
						$row->rcount = count($ar->getIds($row->id));
					break;

					case 'blog':
						// Yes - load the blog
						$row = new BlogEntry($this->database);
						$row->load($fh->objectid);
					break;

					default:
						// Nothing
					break;
				}
			}
			else
			{
				// No - so we need to randomly choose one
				switch ($tbl)
				{
					case 'resources':
						// Initiate a resource object
						$rr = new ResourcesResource($this->database);
						$filters['start'] = 0;
						$filters['type'] = $spot;
						$filters['sortby'] = 'random';
						$filters['minranking'] = trim($this->params->get('minranking'));
						$filters['tag'] = ($spot == 'tools') ? trim($this->params->get('tag')) : ''; // tag is set for tools only

						// Get records
						$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $rr->getRecords($filters, false);
					break;

					case 'profiles':
						// No - so we need to randomly choose one
						$filters['start'] = 0;
						$filters['sortby'] = "RAND()";
						$filters['search'] = '';
						$filters['state'] = 'public';
						$filters['authorized'] = false;
						$filters['tag'] = '';
						$filters['contributions'] = trim($this->params->get('min_contributions'));
						$filters['show'] = trim($this->params->get('show'));

						$mp = new MembersProfile($this->database);

						// Get records
						$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $mp->getRecords($filters, false);
					break;

					case 'topics':
						// No - so we need to randomly choose one
						$topics_tag = trim($this->params->get('topics_tag'));
						$query  = "SELECT DISTINCT w.id, w.pagename, w.title ";
						$query .= " FROM #__wiki_page AS w ";
						if ($topics_tag)
						{
							$query .= " JOIN #__tags_object AS RTA ON RTA.objectid=w.id AND RTA.tbl='wiki' ";
							$query .= " INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
						}
						else
						{
							$query .= ", #__wiki_version AS v ";
						}
						$query .= " WHERE w.access!=1 AND w.scope = ''  ";
						if ($topics_tag)
						{
							$query .= " AND (TA.tag='" . $topics_tag . "' OR TA.raw_tag='" . $topics_tag . "') ";
						}
						else
						{
							$query .= " AND v.pageid=w.id AND v.approved = 1 AND v.pagetext != '' ";
						}
						$query .= " ORDER BY RAND() ";
						$this->database->setQuery($query);

						$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $this->database->loadObjectList();
					break;

					case 'itunes':
						// Initiate a resource object
						$rr = new ResourcesResource($this->database);
						$filters['start'] = 0;
						$filters['sortby'] = 'random';
						$filters['tag'] = trim($this->params->get('itunes_tag'));

						// Get records
						$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $rr->getRecords($filters, false);
					break;

					case 'answers':
						$query  = "SELECT C.id, C.subject, C.question, C.created, C.created_by, C.anonymous  ";
						$query .= ", (SELECT COUNT(*) FROM #__answers_responses AS a WHERE a.state!=2 AND a.qid=C.id) AS rcount ";
						$query .= " FROM #__answers_questions AS C ";
						$query .= " WHERE C.state=0 ";
						$query .= " AND (C.reward > 0 OR C.helpful > 0) ";
						$query .= " ORDER BY RAND() ";
						$this->database->setQuery($query);

						$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $this->database->loadObjectList();
					break;

					case 'blog':
						$filters = array();
						$filters['limit'] = 1;
						$filters['start'] = 0;
						$filters['state'] = 'public';
						$filters['order'] = "RAND()";
						$filters['search'] = '';
						$filters['scope'] = 'member';
						$filters['group_id'] = 0;
						$filters['authorized'] = false;
						$filters['sql'] = '';
						$mp = new BlogEntry($this->database);
						$entry = $mp->getRecords($filters);

						$rows[$spot] = (isset($rows[$spot])) ? $rows[$spot] : $entry;
					break;
				}

				if ($rows && count($rows[$spot]) > 0)
				{
					$row = $rows[$spot][0];
				}

				// make sure we aren't pulling the same item
				if ($k != 1 && in_array($spot, $activespots) && $rows && count($rows[$spot]) > 1)
				{
					$row = (count($rows[$spot]) < $k) ? $rows[$spot][$k-1] : $rows[$spot][1]; // get the next one
				}
			}

			// pull info
			if ($row)
			{
				$out = $this->_composeEntry($row, $tbl, $txtLength);
				$itemid = $this->_composeEntry($row, $tbl, 0, 1);
				$activespots[] = $spot;
			}

			// Did we get any results?
			if ($out)
			{
				$this->html .= '<li class="spot_' . $k . '">' . $out . '</li>' . "\n";

				// Check if this has been saved in the feature history
				if (!$fh->id || !$fh->objectid)
				{
					$fh->featured = $start;
					$fh->objectid = $itemid;
					$fh->tbl = $tbl;
					$fh->note = $spot . $k;
					$fh->store();
				}

				$k++;
			}
		}

		// Output HTML
		require(JModuleHelper::getLayoutPath($this->module->module));
	}

	/**
	 * Format an entry
	 * 
	 * @param      object  $row       Database row
	 * @param      string  $tbl       Format type
	 * @param      number  $txtLength Max text length to display
	 * @param      integer $getid     Just return the ID or not
	 * @return     string HTML
	 */
	private function _composeEntry($row, $tbl, $txtLength=100, $getid=0)
	{
		$yearFormat = '%Y';
		$monthFormat = '%m';
		$tz = 0;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$yearFormat = 'Y';
			$monthFormat = 'm';
			$tz = null;
		}

		$out = '';

		// Do we have a picture?
		$thumb = '';

		switch ($tbl)
		{
			case 'profiles':
				if ($getid)
				{
					return $row->uidNumber;
				}

				// Load their bio
				$profile = Hubzero_User_Profile::getInstance($row->uidNumber);

				$mconfig =& JComponentHelper::getParams('com_members');

				if (isset($row->picture) && $row->picture != '')
				{
					// Yes - so build the path to it
					$thumb  = DS . trim($mconfig->get('webpath'), DS);
					$thumb .= DS . Hubzero_View_Helper_Html::niceidformat($row->uidNumber) . DS . $row->picture;

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
							$ih->set('image', $row->picture);
							$ih->set('path', JPATH_ROOT . $config->get('webpath') . DS . Hubzero_View_Helper_Html::niceidformat($row->uidNumber) . DS);
							$ih->set('maxWidth', 50);
							$ih->set('maxHeight', 50);
							$ih->set('cropratio', '1:1');
							$ih->set('outputName', $ih->createThumbName());
						}
					}
				}
				// No - use default picture
				if (!$thumb || !is_file(JPATH_ROOT . $thumb))
				{
					$thumb = DS . trim($mconfig->get('defaultpic'), DS);
				}

				$title = $row->name;
				if (!trim($title))
				{
					$title = $row->givenName . ' ' . $row->surname;
				}
				$out .= '<span class="spotlight-img"><a href="' . JRoute::_('index.php?option=com_members&id=' . $row->uidNumber) . '"><img width="30" height="30" src="' . $thumb . '" alt="' . htmlentities($title) . '" /></a></span>' . "\n";
				$out .= '<span class="spotlight-item"><a href="' . JRoute::_('index.php?option=com_members&id=' . $row->uidNumber) . '">' . $title . '</a></span>, ' . $row->organization . "\n";
				$out .= ' - ' . JText::_('Contributions') . ': ' . $this->_countContributions($row->uidNumber) . "\n";
				$out .= '<div class="clear"></div>'."\n";
			break;

			case 'blog':
				$thumb = trim($this->params->get('default_blogpic', '/modules/mod_spotlight/default.gif'));

				$profile = Hubzero_User_Profile::getInstance($row->created_by);

				if ($getid)
				{
					return $row->id;
				}
				if (!$row->title)
				{
					$out = '';
				}
				else
				{
					$out .= '<span class="spotlight-img"><a href="' . JRoute::_('index.php?option=com_members&id=' . $row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, $yearFormat, $tz) . '/'.JHTML::_('date',$row->publish_up, $monthFormat, $tz) . '/' . $row->alias) . '"><img width="30" height="30" src="' . $thumb.'" alt="'.htmlentities(stripslashes($row->title)) . '" /></a></span>'."\n";
					$out .= '<span class="spotlight-item"><a href="' . JRoute::_('index.php?option=com_members&id=' . $row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, $yearFormat, $tz) . '/'.JHTML::_('date',$row->publish_up, $monthFormat, $tz) . '/' . $row->alias) . '">' . $row->title.'</a></span> ';
					$out .=  ' by <a href="'. JRoute::_('index.php?option=com_members&id=' . $row->created_by) . '">' . $profile->get('name') . '</a> - '.JText::_('in Blogs')."\n";
					$out .= '<div class="clear"></div>'."\n";
				}
			break;

			case 'topics':
				if ($getid)
				{
					return $row->id;
				}
				$url = ($row->group_cn && $row->scope) ? 'groups' . DS . $row->scope . DS . $row->pagename : 'topics' . DS . $row->pagename;

				$thumb = trim($this->params->get('default_topicpic', '/modules/mod_spotlight/default.gif'));
				$out .= '<span class="spotlight-img"><a href="' . JRoute::_('index.php?option=com_topics&pagename=' . $row->pagename) . '"><img width="30" height="30" src="' . $thumb.'" alt="'.htmlentities(stripslashes($row->title)) . '" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="' . $url.'">'.stripslashes($row->title) . '</a></span> ';
				$out .=  ' - '.JText::_('in') . ' <a href="' . JRoute::_('index.php?option=com_topics') . '">'.JText::_('Topics') . '</a>'."\n";
				$out .= '<div class="clear"></div>'."\n";
			break;

			case 'answers':
				if ($getid)
				{
					return $row->id;
				}
				$thumb = trim($this->params->get('default_questionpic', '/modules/mod_spotlight/default.gif'));

				$name = JText::_('Anonymous');
				if ($row->anonymous == 0)
				{
					$juser =& JUser::getInstance($row->created_by);
					if (is_object($juser))
					{
						$name = $juser->get('name');
					}
				}
				$out .= '<span class="spotlight-img"><a href="' . JRoute::_('index.php?option=com_answers&task=question&id=' . $row->id) . '"><img width="30" height="30" src="' . $thumb.'" alt="'.htmlentities(stripslashes($row->subject)) . '" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="' . JRoute::_('index.php?option=com_answers&task=question&id=' . $row->id) . '">'.stripslashes($row->subject) . '</a></span> ';
				$out .=  ' - '.JText::_('asked by') . ' ' . $name.', '.JText::_('in') . ' <a href="' . JRoute::_('index.php?option=com_answers') . '">'.JText::_('Answers') . '</a>'."\n";
				$out .= '<div class="clear"></div>'."\n";
			break;

			default:
				if ($getid)
				{
					return $row->id;
				}

				if ($tbl == 'itunes')
				{
					$thumb = trim($this->params->get('default_itunespic', '/modules/mod_spotlight/default.gif'));
				}
				else
				{
					$rconfig =& JComponentHelper::getParams('com_resources');
					$path = $rconfig->get('uploadpath');
					if (substr($path, 0, 1) != DS)
					{
						$path = DS . $path;
					}
					if (substr($path, -1, 1) == DS)
					{
						$path = substr($path, 0, (strlen($path) - 1));
					}
					$path = $this->_buildPath($row->created, $row->id, $path);

					if ($row->type == 7)
					{
						include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');

						$tv = new ToolVersion($this->database);

						$versionid = $tv->getVersionIdFromResource($row->id, 'current');

						$picture = $this->_getToolImage($path, $versionid);
					}
					else
					{
						$picture = $this->_getImage($path);
					}

					$thumb = $path . DS . $picture;

					if (!is_file(JPATH_ROOT . $thumb) or !$picture)
					{
						$thumb = DS . trim($rconfig->get('defaultpic', '/modules/mod_spotlight/default.gif'), DS);
					}
				}

				$normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($row->typetitle));

				$row->typetitle = trim(stripslashes($row->typetitle));
				$row->title = stripslashes($row->title);

				$chars = strlen($row->title . $row->typetitle);
				$remaining = $txtLength - $chars;
				$remaining = ($remaining <= 0) ? 0 : $remaining;
				$titlecut  = ($remaining) ? 0 : $txtLength - strlen($row->typetitle);
				if ($titlecut)
				{
					$title = Hubzero_View_Helper_Html::shortenText(($row->title), $titlecut, 0);
				}
				else
				{
					$title = $row->title;
				}

				// resources
				$out .= '<span class="spotlight-img">';
				$out .= "\t" . '<a href="' . JRoute::_('index.php?option=com_resources&id=' . $row->id) . '">' . "\n";
				$out .= "\t\t" . '<img width="30" height="30" src="' . $thumb . '" alt="' . htmlentities($row->title) . '" />' . "\n";
				$out .= "\t" . '</a>' . "\n";
				$out .= '</span>' . "\n";
				$out .= '<span class="spotlight-item">' . "\n";
				$out .= "\t" . '<a href="' . JRoute::_('index.php?option=com_resources&id=' . $row->id) . '">' . $title . '</a>' . "\n";
				$out .= '</span>' . "\n";
				if ($row->type == 7 && $remaining > 30)
				{
					// Show bit of description for tools
					if ($row->introtext)
					{
						$out .= ': '.Hubzero_View_Helper_Html::shortenText($this->_encodeHtml(strip_tags($row->introtext)), $txtLength, 0);
					}
					else
					{
						$out .= ': '.Hubzero_View_Helper_Html::shortenText($this->_encodeHtml(strip_tags($row->fulltxt)), $txtLength, 0);
					}
				}
				if ($tbl == 'itunes')
				{
					$out .=  ' - ' . JText::_('featured on') .' <a href="/itunes">' . JText::_('iTunes') . ' U</a>' . "\n";
				}
				else
				{
					$out .=  ' - ' . JText::_('in') . ' <a href="' . JRoute::_('index.php?option=com_resources&type=' . $normalized) . '">' . $row->typetitle . '</a>' . "\n";
				}
				$out .= '<div class="clear"></div>' . "\n";
			break;
		}

		return $out;
	}

	/**
	 * Get a user's average ranking
	 * 
	 * @param      integer $uid User ID
	 * @return     integer
	 */
	private function _getAverageRanking($uid)
	{
		if ($uid === NULL)
		{
			 return 0;
		}

		// get average ranking of contributed resources
		$query  = "SELECT AVG (R.ranking) ";
		$query .= "FROM #__author_assoc AS AA,  #__resources AS R ";
		$query .= "WHERE AA.authorid = " . $uid . " ";
		$query .= "AND R.id = AA.subid ";
		$query .= "AND AA.subtable = 'resources' ";
		$query .= "AND R.published=1 AND R.standalone=1 AND R.access!=2 AND R.access!=4";

		$this->database->setQuery($query);
		return $this->database->loadResult();
	}

	/**
	 * Get a count of a user's contributions
	 * 
	 * @param      integer $uid User ID
	 * @return     integer
	 */
	private function _countContributions($uid)
	{
		if ($uid === NULL)
		{
			 return 0;
		}

		$this->database->setQuery('SELECT total_count FROM #__contributors_view WHERE uidNumber = ' . $uid);
		return $this->database->loadResult();
	}

	/**
	 * Get a resource image
	 * 
	 * @param      string $path Path to get resource image from
	 * @return     string
	 */
	private function _getImage($path)
	{
		$d = @dir(JPATH_ROOT . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				if (is_file(JPATH_ROOT . $path . DS . $entry)
				 && substr($entry,0,1) != '.'
				 && strtolower($entry) !== 'index.html')
				{
					if (preg_match("/^bmp|gif|jpg|jpe|jpeg|png$/i", $entry))
					{
						$images[] = $entry;
					}
				}
			}
			$d->close();
		}

		if ($images)
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img  = implode('.', $bits);

				if ($img == 'thumb')
				{
					return $ima;
				}
			}
		}
	}

	/**
	 * Get a screenshot of a tool
	 * 
	 * @param      string  $path      Path to look for screenshots in
	 * @param      integer $versionid Tool version
	 * @return     string
	 */
	private function _getToolImage($path, $versionid=0)
	{
		// Get contribtool parameters
		$tconfig =& JComponentHelper::getParams('com_tools');
		$allowversions = $tconfig->get('screenshot_edit');

		if ($versionid && $allowversions)
		{
			// Add version directory
			//$path .= DS.$versionid;
		}

		$d = @dir(JPATH_ROOT . $path);

		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				if (is_file(JPATH_ROOT . $path . DS . $entry)
				 && substr($entry,0,1) != '.'
				 && strtolower($entry) !== 'index.html')
				{
					if (preg_match("/^bmp|gif|jpg|jpe|jpeg|png$/i", $entry))
					{
						$images[] = $entry;
					}
				}
			}
			$d->close();
		}

		if ($images)
		{
			foreach ($images as $ima)
			{
				$bits = explode('.', $ima);
				$type = array_pop($bits);
				$img  = implode('.', $bits);

				if ($img == 'thumb')
				{
					return $ima;
				}
			}
		}
	}

	/**
	 * Generate a thumbnail name from a picture name
	 * 
	 * @param      string $pic Picture name
	 * @return     string
	 */
	private function _thumbnail($pic)
	{
		jimport('joomla.filesystem.file');
		$ext = JFile::getExt($pic);

		return JFile::stripExt($pic) . '-tn.gif';
	}

	/**
	 * Build a path to a resource's files
	 * 
	 * @param      string  $date Resource date
	 * @param      integer $id   Resource ID
	 * @param      string  $base Base path to prepend
	 * @return     string 
	 */
	private function _buildPath($date, $id, $base='')
	{
		if ($date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs))
		{
			$date = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}

		if ($date)
		{
			$dir_year  = date('Y', $date);
			$dir_month = date('m', $date);
		}
		else
		{
			$dir_year  = date('Y');
			$dir_month = date('m');
		}
		$dir_id = Hubzero_View_Helper_Html::niceidformat($id);

		return $base . DS . $dir_year . DS . $dir_month . DS . $dir_id;
	}

	/**
	 * Encode some HTML entities
	 * 
	 * @param      string  $str    String to encode
	 * @param      integer $quotes Encode quote marks?
	 * @return     string
	 */
	private function _encodeHtml($str, $quotes=1)
	{
		$str = $this->_ampersands($str);

		$a = array(
			//'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);

		if ($quotes)
		{
			$a = $a + array(
				"'" => '&#39;',
				'"' => '&#34;',
			);
		}

		return strtr($str, $a);
	}

	/**
	 * Convert ampersands
	 * 
	 * @param      string  $str    String to encode
	 * @return     string
	 */
	private function _ampersands($str)
	{
		$str = stripslashes($str);
		$str = str_replace('&#','*-*', $str);
		$str = str_replace('&amp;','&',$str);
		$str = str_replace('&','&amp;',$str);
		$str = str_replace('*-*','&#', $str);
		return $str;
	}
}
