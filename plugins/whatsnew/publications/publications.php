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
 * What's New Plugin class for com_publications entries
 */
class plgWhatsnewPublications extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Resource types and "all" category
	 *
	 * @var array
	 */
	private $_areas = null;

	/**
	 * Resource types
	 *
	 * @var array
	 */
	private $_cats  = null;

	/**
	 * Results total
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

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'category.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'author.php');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function onWhatsnewAreas()
	{
		return array(
			'publications' => JText::_('PLG_WHATSNEW_PUBLICATIONS')
		);
	}

	/**
	 * Pull a list of records that were created within the time frame ($period)
	 *
	 * @param      object  $period     Time period to pull results for
	 * @param      mixed   $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      array   $areas      Active area(s)
	 * @param      array   $tagids     Array of tag IDs
	 * @return     array
	 */
	public function onWhatsnew($period, $limit=0, $limitstart=0, $areas=null, $tagids=array())
	{
		if (is_array($areas) && $limit)
		{
			if (!isset($areas[$this->_name])
			 && !in_array($this->_name, $areas))
			{
				return array();
			}
		}

		// Do we have a time period?
		if (!is_object($period))
		{
			return array();
		}

		$database = JFactory::getDBO();

		// Instantiate some needed objects
		$rr = new Publication($database);

		// Build query
		$filters = array();
		$filters['startdate'] = $period->cStartDate;
		$filters['enddate']   = $period->cEndDate;
		$filters['sortby']    = 'date';
		if (count($tagids) > 0)
		{
			$filters['tag'] = $tagids;
		}

		$juser = JFactory::getUser();

		if ($limit)
		{
			if ($this->_total != null)
			{
				$total = 0;
				$t = $this->_total;
				foreach ($t as $l)
				{
					$total += $l;
				}
				if ($total == 0)
				{
					return array();
				}
			}

			$filters['limit'] = $limit;
			$filters['start'] = $limitstart;

			// Get results
			$rows = $rr->getRecords($filters);

			// Did we get any results?
			if ($rows)
			{
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					$rows[$key]->text = NULL;
					if ($row->alias)
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_publications&alias=' . $row->alias);
					}
					else
					{
						$rows[$key]->href = JRoute::_('index.php?option=com_publications&id=' . $row->id);
					}
					if ($row->abstract)
					{
						$rows[$key]->text = $rows[$key]->abstract;
					}
					$rows[$key]->section = NULL;
					$rows[$key]->area = $row->cat_name;
					$rows[$key]->publish_up = $row->published_up;
				}
			}

			return $rows;
		}
		else
		{
			// Get a count
			$counts = array();

			// Execute count query
			$results = $rr->getCount( $filters );

			return ($results && is_array($results)) ? count($results) : 0;
		}
	}

	/**
	 * Push styles and scripts to the document
	 *
	 * @return     void
	 */
	public static function documents()
	{
		\Hubzero\Document\Assets::addComponentStylesheet('com_publications');

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'helper.php');
	}

	/**
	 * Special formatting for results
	 *
	 * @param      object $row    Database row
	 * @param      string $period Time period
	 * @return     string
	 */
	public static function out($row, $period)
	{
		$database = JFactory::getDBO();
		$juser = JFactory::getUser();
		$config = JComponentHelper::getParams( 'com_publications' );

		// Instantiate a helper object
		$helper = new PublicationHelper($database);

		$juri = JURI::getInstance();

		// Get version authors
		$pa = new PublicationAuthor( $database );
		$authors = $pa->getAuthors($row->version_id);

		// Start building HTML
		$html  = "\t" . '<li class="publication">' . "\n";
		$html .= "\t\t" . '<p><span class="pub-thumb"><img src="' . JRoute::_('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb' . '" alt="" /></span>';
		$html .= '<span class="pub-details"><a href="' . $row->href . '">' . stripslashes($row->title) . '</a>' . "\n";
		$html .= "\t\t" . '<span class="block details">' . JHTML::_('date', $row->published_up, 'd M Y') . ' <span>|</span> ' . $row->cat_name;
		if ($authors)
		{
			$html .= ' <span>|</span> ' . JText::_('PLG_WHATSNEW_PUBLICATIONS_CONTRIBUTORS') . ' ' . $helper->showContributors( $authors, false, true );
		}
		$html .= '</span></span></p>' . "\n";
		if ($row->text)
		{
			$html .= "\t\t" . '<p>' . \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->text)), 200) . '</p>' . "\n";
		}
		$html .= "\t\t" . '<p class="href">' . $juri->base() . trim($row->href, DS) . '</p>' . "\n";
		$html .= "\t" . '</li>' . "\n";

		// Return output
		return $html;
	}
}