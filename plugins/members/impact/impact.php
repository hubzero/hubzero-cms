<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Members Plugin class for author's impact
 */
class plgMembersImpact extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Publication areas
	 *
	 * @var array
	 */
	private $_areas = null;

	/**
	 * Publication stats
	 *
	 * @var    boolean
	 */
	protected $_stats = null;

	/**
	 * Publication categories
	 *
	 * @var array
	 */
	private $_cats  = null;

	/**
	 * Record count
	 *
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->_database = JFactory::getDBO();

		require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'. DS .'com_publications' . DS . 'tables' . DS . 'logs.php');
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS
			. 'helpers' . DS . 'helper.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' .
			DS .'com_publications' . DS . 'tables' . DS . 'publication.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' .
			DS .'com_publications' . DS . 'tables' . DS . 'author.php');
		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' .
			DS .'com_publications' . DS . 'tables' . DS . 'category.php');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function &onMembersAreas($user, $member)
	{
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if (($user->get('id') == $member->get('uidNumber') && $this->params->get('show_impact', 0) == 1) || $this->params->get('show_impact', 0) == 2)
		{
			// Check if user has any publications
			$pubLog = new PublicationLog($this->_database);
			$this->_stats = $pubLog->getAuthorStats($user->get('id'), 0, false );

			if ($this->_stats)
			{
				$areas['impact'] = JText::_('PLG_MEMBERS_IMPACT');
				$areas['icon'] = '';
			}
		}

		return $areas;
	}

	/**
	 * Perform actions when viewing a member profile
	 *
	 * @param      object $user   Current user
	 * @param      object $member Current member page
	 * @param      string $option Start of records to pull
	 * @param      array  $areas  Active area(s)
	 * @return     array
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		if ($returnhtml)
		{
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'helper.php');
			require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS .'com_publications' . DS . 'tables' . DS . 'version.php');

			$this->_option = $option;

			// Which view
			$task = JRequest::getVar('action', '');

			switch ($task)
			{
				case 'view':
				default:        $arr['html'] = $this->_view($member->get('uidNumber'));   break;
			}
		}

		//get meta
		$arr['metadata'] = array();
		$arr['metadata']['count'] = $this->_stats ? count($this->_stats) : 0;

		return $arr;
	}

	/**
	 * View entries
	 *
	 * @param      int $uid
	 * @return     string
	 */
	protected function _view($uid = 0)
	{
		// Build the final HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => $this->_type,
				'element' => $this->_name,
				'name'    => 'stats'
			)
		);

		// Get pub stats for each publication
		$pubLog = new PublicationLog($this->_database);
		$view->pubstats = $pubLog->getAuthorStats($uid, 0, false);

		// Get date of first log
		$view->firstlog = $pubLog->getFirstLogDate();

		// Test
		$view->totals = $pubLog->getTotals($uid);

		// Output HTML
		$view->option    = $this->_option;
		$view->database  = $this->_database;
		$view->uid       = $uid;
		$view->pubconfig = JComponentHelper::getParams('com_publications');
		$view->helper    = new PublicationHelper($this->_database);

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Return a list of categories
	 *
	 * @return     array
	 */
	public function &onMembersContributionsAreas()
	{
		$areas = array();

		// Load contributions plugin parameters
		$this->_cPlugin = JPluginHelper::getPlugin('members', 'contributions');
		$this->_cParams = new JParameter($this->_cPlugin->params);

		if ($this->_cParams->get('include_publications', 0) == 1)
		{
			$areas = array(
				'impact' => JText::_('PLG_MEMBERS_IMPACT_PUBLICATIONS')
			);
		}
		$this->_areas = $areas;
		return $areas;
	}

	/**
	 * Build SQL for returning the count of the number of contributions
	 *
	 * @param      string $user_id  Field to join on user ID
	 * @param      string $username Field to join on username
	 * @return     string
	 */
	public function onMembersContributionsCount($user_id='m.uidNumber', $username='m.username')
	{
		$query = "SELECT COUNT(DISTINCT P.id) FROM #__publications AS P,
							#__publication_versions as V,
							#__publication_authors as A
							WHERE V.publication_id=P.id AND A.publication_version_id = V.id
							AND A.user_id=" . $user_id . " AND
							V.state=1 AND A.status=1 AND A.role!='submitter'";
		return $query;
	}

	/**
	 * Return either a count or an array of the member's contributions
	 *
	 * @param      object  $member     Current member
	 * @param      string  $option     Component name
	 * @param      string  $authorized Authorization level
	 * @param      integer $limit      Number of record to return
	 * @param      integer $limitstart Record return start
	 * @param      string  $sort       Field to sort records on
	 * @param      array   $areas      Areas to return data for
	 * @return     array
	 */
	public function onMembersContributions($member, $option, $limit=0, $limitstart=0, $sort, $areas=null)
	{
		$database = JFactory::getDBO();

		if (is_array($areas) && $limit)
		{
			if (!isset($areas[$this->_name])
			  && !in_array($this->_name, $areas)
			  && !array_intersect($areas, array_keys($this->onMembersContributionsAreas())))
			{
				return array();
			}
		}

		// Do we have a member ID?
		if ($member instanceof \Hubzero\User\Profile)
		{
			if (!$member->get('uidNumber'))
			{
				return array();
			}
			else
			{
				$uidNumber = $member->get('uidNumber');
				$username = $member->get('username');
			}
		}
		else
		{
			if (!$member->uidNumber)
			{
				return array();
			}
			else
			{
				$uidNumber = $member->uidNumber;
				$username = $member->username;
			}
		}

		// Instantiate some needed objects
		$objP = new Publication($database);

		// Build query
		$filters = array(
			'sortby' 		=> $sort,
			'limit'  		=> $limit,
			'start'  		=> $limitstart,
			'author' 		=> $uidNumber
		);

		if (!$limit)
		{
			$results = $objP->getCount($filters);
			return count($results);
		}
		else
		{
			$rows = $objP->getRecords($filters);
			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					if ($row->alias)
					{
						$sef = JRoute::_('index.php?option=com_publications&alias='
							. $row->alias . '&v=' . $row->version_number);
					}
					else
					{
						$sef = JRoute::_('index.php?option=com_publications&id='
							. $row->id . '&v=' . $row->version_number);
					}
					$rows[$key]->href = $sef;
					$rows[$key]->text = $rows[$key]->abstract;
					$rows[$key]->section = 'impact';
				}
			}

			return $rows;
		}
	}

	/**
	 * Static method for formatting results
	 *
	 * @param      object $row Database row
	 * @return     string HTML
	 */
	public static function out($row)
	{
		$database 	= JFactory::getDBO();
		$thedate 	= JHTML::_('date', $row->published_up, 'd M Y');

		$helper 	= new PublicationHelper($database);

		// Get version authors
		$pa = new PublicationAuthor( $database );
		$authors = $pa->getAuthors($row->version_id);

		$html  = "\t" . '<li class="resource">' . "\n";
		$html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a></p>' . "\n";
		$html .= "\t\t".'<p class="details">' . $thedate . ' <span>|</span> '
			  . stripslashes($row->cat_name);
		if ($authors)
		{
			$html .= ' <span>|</span>' . JText::_('PLG_MEMBERS_IMPACT_CONTRIBUTORS').': '. $helper->showContributors( $authors, false, true ) . "\n";
		}
		if ($row->doi)
		{
			$html .= ' <span>|</span> doi:' . $row->doi . "\n";
		}

		$html .= ' <span>|</span> Project: <a href="' . JRoute::_('index.php?option=com_projects&alias='. $row->project_alias . '&active=publications&pid=' . $row->id) . '">' . $row->project_title . '</a>' . "\n";
		$html .= '</p>' . "\n";
		if ($row->text)
		{
			$html .= "\t\t<p>" . \Hubzero\Utility\String::truncate(strip_tags(stripslashes($row->text)), 300) . "</p>\n";
		}
		$html .= "\t" . '</li>' . "\n";
		return $html;
	}
}