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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
JPluginHelper::loadLanguage( 'plg_whatsnew_resources' );

/**
 * Short description for 'plgWhatsnewResources'
 * 
 * Long description (if any) ...
 */
class plgWhatsnewResources extends JPlugin
{

	/**
	 * Description for '_areas'
	 * 
	 * @var unknown
	 */
	private $_areas = null;

	/**
	 * Description for '_cats'
	 * 
	 * @var unknown
	 */
	private $_cats  = null;

	/**
	 * Description for '_total'
	 * 
	 * @var unknown
	 */
	private $_total = null;

	/**
	 * Short description for 'plgWhatsnewResources'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgWhatsnewResources(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'whatsnew', 'resources' );
		$this->_params = new JParameter( $this->_plugin->params );

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'type.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php' );
	}

	/**
	 * Short description for 'onWhatsnewAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function onWhatsnewAreas()
	{
		$areas = $this->_areas;
		if (is_array($areas)) {
			return $areas;
		}

		$categories = $this->_cats;
		if (!is_array($categories)) {
			// Get categories
			$database =& JFactory::getDBO();
			$rt = new ResourcesType( $database );
			$categories = $rt->getMajorTypes();
			$this->_cats = $categories;
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$normalized_valid_chars = 'a-zA-Z0-9';
		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $categories[$i]->type);
			$normalized = strtolower($normalized);

			$cats[$normalized] = $categories[$i]->type;
		}

		$areas = array(
			'resources' => $cats
		);
		$this->_areas = $areas;
		return $areas;
	}

	/**
	 * Short description for 'onWhatsnew'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $period Parameter description (if any) ...
	 * @param      integer $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      array $areas Parameter description (if any) ...
	 * @param      array $tagids Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onWhatsnew( $period, $limit=0, $limitstart=0, $areas=null, $tagids=array() )
	{
		if (is_array( $areas ) && $limit) {
			$ars = $this->onWhatsnewAreas();
			if (!array_intersect( $areas, $ars )
			&& !array_intersect( $areas, array_keys( $ars ) )
			&& !array_intersect( $areas, array_keys( $ars['resources'] ) )) {
				return array();
			}
		}

		// Do we have a time period?
		if (!is_object($period)) {
			return array();
		}

		$database =& JFactory::getDBO();

		// Instantiate some needed objects
		$rr = new ResourcesResource( $database );

		// Build query
		$filters = array();
		$filters['startdate'] = $period->cStartDate;
		$filters['enddate'] = $period->cEndDate;
		$filters['sortby'] = 'date';
		if (count($tagids) > 0) {
			$filters['tags'] = $tagids;
		}

		//ximport('Hubzero_User_Helper');
		ximport('Hubzero_User_Profile');
		$juser =& JFactory::getUser();
		$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
		//$filters['usergroups'] = Hubzero_User_Helper::getGroups($juser->get('id'), 'all');
		$filters['usergroups'] = $profile->getGroups('all');

		// Get categories
		$categories = $this->_cats;
		if (!is_array($categories)) {
			$rt = new ResourcesType( $database );
			$categories = $rt->getMajorTypes();
		}

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		$normalized_valid_chars = 'a-zA-Z0-9';
		for ($i = 0; $i < count($categories); $i++)
		{
			$normalized = preg_replace("/[^$normalized_valid_chars]/", "", $categories[$i]->type);
			$normalized = strtolower($normalized);

			$cats[$normalized] = array();
			$cats[$normalized]['id'] = $categories[$i]->id;
		}

		$filters['authorized'] = false;

		if ($limit) {
			if ($this->_total != null) {
				$total = 0;
				$t = $this->_total;
				foreach ($t as $l)
				{
					$total += $l;
				}
				if ($total == 0) {
					return array();
				}
			}

			$filters['select'] = 'records';
			$filters['limit'] = $limit;
			$filters['limitstart'] = $limitstart;

			// Check the area of return. If we are returning results for a specific area/category
			// we'll need to modify the query a bit
			if (count($areas) == 1 && $areas[0] != 'resources') {
				$filters['type'] = $cats[$areas[0]]['id'];
			}

			// Get results
			$database->setQuery( $rr->buildPluginQuery( $filters ) );
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows) {
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					if ($row->alias) {
						$rows[$key]->href = JRoute::_('index.php?option=com_resources&alias='.$row->alias);
					} else {
						$rows[$key]->href = JRoute::_('index.php?option=com_resources&id='.$row->id);
					}
					if ($row->itext) {
						$rows[$key]->text = $rows[$key]->itext;
					} else if ($row->ftext) {
						$rows[$key]->text = $rows[$key]->ftext;
					}
				}
			}

			return $rows;
		} else {
			$filters['select'] = 'count';

			// Get a count
			$counts = array();
			$ares = $this->onWhatsnewAreas();
			foreach ($ares as $area=>$val)
			{
				if (is_array($val)) {
					foreach ($val as $a=>$t)
					{
						$filters['type'] = $cats[$a]['id'];

						$database->setQuery( $rr->buildPluginQuery( $filters ) );
						$counts[] = $database->loadResult();
					}
				}
			}
			// Return the counts
			$this->_total = $counts;
			return $counts;
		}
	}

	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	/**
	 * Short description for 'documents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function documents()
	{
		// Push some CSS and JS to the tmeplate that may be needed
	 	$document =& JFactory::getDocument();
		$document->addScript('components'.DS.'com_resources'.DS.'resources.js');

		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_resources');

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'helper.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'usage.php' );
	}

	/*public function before()
	{
		// ...
	}*/

	/**
	 * Short description for 'out'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $row Parameter description (if any) ...
	 * @param      unknown $period Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function out( $row, $period )
	{
		$database =& JFactory::getDBO();

		// Instantiate a helper object
		$helper = new ResourcesHelper($row->id, $database);
		$helper->getContributors();

		// Get the component params and merge with resource params
		$config =& JComponentHelper::getParams( 'com_resources' );
		$rparams = new JParameter( $row->params );
		$params = $config;
		$params->merge( $rparams );

		// Set the display date
		switch ($params->get('show_date'))
		{
			case 0: $thedate = ''; break;
			case 1: $thedate = JHTML::_('date', $row->created, '%d %b %Y');    break;
			case 2: $thedate = JHTML::_('date', $row->modified, '%d %b %Y');   break;
			case 3: $thedate = JHTML::_('date', $row->publish_up, '%d %b %Y'); break;
		}

		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}

		// Start building HTML
		$html  = "\t".'<li class="';
		/*switch ($row->access)
		{
			case 1: $html .= 'registered '; break;
			case 2: $html .= 'special ';    break;
			case 3: $html .= 'protected ';  break;
			case 4: $html .= 'private ';    break;
			case 0:
			default: $html .= 'public '; break;
		}*/
		$html .= 'resource">'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($params->get('show_ranking')) {
			$helper->getCitationsCount();
			$helper->getLastCitationDate();

			if ($row->area == 'Tools') {
				$stats = new ToolStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			} else {
				$stats = new AndmoreStats($database, $row->id, $row->category, $row->rating, $helper->citationsCount, $helper->lastCitationDate);
			}
			$statshtml = $stats->display();

			$row->ranking = round($row->ranking, 1);

			$html .= "\t\t".'<div class="metadata">'."\n";

			$r = (10*$row->ranking);
			if (intval($r) < 10) {
				$r = '0'.$r;
			}

			$html .= "\t\t\t".'<dl class="rankinfo">'."\n";
			$html .= "\t\t\t\t".'<dt class="ranking"><span class="rank-'.$r.'">'.JText::_('PLG_WHATSNEW_RESOURCES_THIS_HAS').'</span> '.number_format($row->ranking,1).' '.JText::_('PLG_WHATSNEW_RESOURCES_RANKING').'</dt>'."\n";
			$html .= "\t\t\t\t".'<dd>'."\n";
			$html .= "\t\t\t\t\t".'<p>'.JText::_('PLG_WHATSNEW_RESOURCES_RANKING_EXPLANATION').'</p>'."\n";
			$html .= "\t\t\t\t\t".'<div>'."\n";
			$html .= $statshtml;
			$html .= "\t\t\t\t\t".'</div>'."\n";
			$html .= "\t\t\t\t".'</dd>'."\n";
			$html .= "\t\t\t".'</dl>'."\n";
			$html .= "\t\t".'</div>'."\n";
		} elseif ($params->get('show_rating')) {
			switch ($row->rating)
			{
				case 0.5: $class = ' half-stars';      break;
				case 1:   $class = ' one-stars';       break;
				case 1.5: $class = ' onehalf-stars';   break;
				case 2:   $class = ' two-stars';       break;
				case 2.5: $class = ' twohalf-stars';   break;
				case 3:   $class = ' three-stars';     break;
				case 3.5: $class = ' threehalf-stars'; break;
				case 4:   $class = ' four-stars';      break;
				case 4.5: $class = ' fourhalf-stars';  break;
				case 5:   $class = ' five-stars';      break;
				case 0:
				default:  $class = ' no-stars';      break;
			}

			$html .= "\t\t".'<div class="metadata">'."\n";
			$html .= "\t\t\t".'<p class="rating"><span class="avgrating'.$class.'"><span>'.JText::sprintf('PLG_WHATSNEW_RESOURCES_OUT_OF_5_STARS',$row->rating).'</span>&nbsp;</span></p>'."\n";
			$html .= "\t\t".'</div>'."\n";
		}
		$html .= "\t\t".'<p class="details">'.$thedate.' <span>|</span> '.$row->area;
		if ($helper->contributors) {
			$html .= ' <span>|</span> '.JText::_('PLG_WHATSNEW_RESOURCES_CONTRIBUTORS').' '.$helper->contributors;
		}
		$html .= '</p>'."\n";
		if ($row->itext) {
			$html .= "\t\t".'<p>'.Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->itext)),200,0).'</p>'."\n";
		} else if ($row->ftext) {
			$html .= "\t\t".'<p>'.Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)),200,0).'</p>'."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";

		// Return output
		return $html;
	}

	/*public function after()
	{
		// ...
	}*/
}

