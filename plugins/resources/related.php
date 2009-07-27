<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_related' );
	
//-----------

class plgResourcesRelated extends JPlugin
{
	function plgResourcesRelated(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'related' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onResourcesSubAreas( $resource )
	{
		$areas = array(
			'related' => JText::_('RELATED')
		);
		return $areas;
	}

	//-----------

	function onResourcesSub( $resource, $option )
	{
		$database =& JFactory::getDBO();

		// Build the query that checks topic pages
		/*$sql1 = "SELECT f.id, d.pageid, MAX(d.version), d.title, d.pagename AS alias, f.pagetext AS introtext, NULL AS type, NULL AS published, NULL AS publish_up, d.scope, d.rating, d.times_rated, d.ranking, 'Topic' AS section
		FROM #__wiki_version AS f, 
			(
				SELECT v.pageid, w.title, w.pagename, w.scope, w.rating, w.times_rated, w.ranking, MAX(v.version) AS version
				FROM #__wiki_page AS w, #__wiki_version AS v
				WHERE w.id=v.pageid AND v.approved=1 
				GROUP BY pageid
			) AS d
		WHERE f.version=d.version 
		AND f.pageid=d.pageid AND (f.pagetext LIKE '%[[Resource(".$resource->id.")]]%' OR f.pagetext LIKE '%[[Resource(".$resource->id.",%' OR f.pagetext LIKE '%[/resources/".$resource->id." %'";
		$sql1 .= ($resource->alias) ? " OR f.pagetext LIKE '%[[Resource(".$resource->alias."%') " : ") ";
		$sql1 .= "GROUP BY pageid ORDER BY ranking DESC, title";*/
		$sql1 = "SELECT v.id, v.pageid, MAX(v.version) AS version, w.title, w.pagename AS alias, v.pagetext AS introtext, NULL AS type, NULL AS published, NULL AS publish_up, w.scope, w.rating, w.times_rated, w.ranking, 'Topic' AS section, w.`group`  
				FROM #__wiki_page AS w, #__wiki_version AS v
				WHERE w.id=v.pageid AND v.approved=1 AND (v.pagetext LIKE '%[[Resource(".$resource->id.")]]%' OR v.pagetext LIKE '%[[Resource(".$resource->id.",%' OR v.pagetext LIKE '%[/resources/".$resource->id." %'";
		$sql1 .= ($resource->alias) ? " OR v.pagetext LIKE '%[[Resource(".$resource->alias."%') " : ") ";
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			if ($juser->authorize('com_resources', 'manage') || $juser->authorize('com_groups', 'manage')) {
				$sql1 .= '';
			} else {
				ximport('xuserhelper');

				$ugs = XUserHelper::getGroups( $juser->get('id'), 'members' );
				$groups = array();
				if ($ugs && count($ugs) > 0) {
					foreach ($ugs as $ug) 
					{
						$groups[] = $ug->cn;
					}
				}
				$g = "'".implode("','",$groups)."'";

				$sql1 .= "AND (w.access!=1 OR (w.access=1 AND (w.group IN ($g) OR w.created_by='".$juser->get('id')."'))) ";
			}
		} else {
			$sql1 .= "AND w.access!=1 ";
		}
		$sql1 .= "GROUP BY pageid ORDER BY ranking DESC, title LIMIT 10";

		// Build the query that checks resource parents
		$sql2 = "SELECT DISTINCT r.id, NULL AS pageid, NULL AS version, r.title, r.alias, r.introtext, r.type, r.published, r.publish_up, NULL AS scope, r.rating, r.times_rated, r.ranking, rt.type AS section, NULL AS `group` "
			 . "\n FROM #__resource_types AS rt, #__resources AS r"
			 . "\n JOIN #__resource_assoc AS a ON r.id=a.parent_id"
			 . "\n LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . "\n WHERE r.published=1 AND a.child_id=".$resource->id." AND r.type=rt.id AND r.type!=8"
			 . "\n ORDER BY r.ranking LIMIT 10";

		// Build the final query
		$query = "SELECT k.* FROM (($sql1) UNION ($sql2)) AS k ORDER BY ranking DESC LIMIT 10";

		// Execute the query
		$database->setQuery( $sql1 );
		$topics = $database->loadObjectList();
		
		$database->setQuery( $sql2 );
		$resources = $database->loadObjectList();
		
		$rel = array();
		if ($topics) {
			foreach ($topics as $t) 
			{
				$rel[$t->ranking] = $t;
			}
		}
		if ($resources) {
			foreach ($resources as $r) 
			{
				$rel[$r->ranking] = $r;
			}
		}
		
		krsort($rel);
		$i = 0;
		$related = array();
		foreach ($rel as $k=>$r) 
		{
			$i++;
			if ($i == 11) {
				break;
			}
			$related[] = $r;
		}
		
		// Did we find any results?
		if ($related) {
			$juser =& JFactory::getUser();

			$sbjt  = '<table class="related-resources">'.n;
			$sbjt .= t.'<tbody>'.n; 
			foreach ($related as $line)
			{
				if ($line->section != 'Topic') {
					$class = ResourcesHtml::getRatingClass( $line->rating );

					$resourceEx = new ResourceExtended( $line->id, $database );
					$resourceEx->getContributors();

					// If the user is logged in, get their rating for this resource
					if (!$juser->get('guest')) {
						$mr = new ResourcesReview( $database );
						$myrating = $mr->loadUserRating( $line->id, $juser->get('id') );
					} else {
						$myrating = 0;
					}
					$myclass = ResourcesHtml::getRatingClass( $myrating );

					// Get the SEF for the resource
					if ($line->alias) {
						$sef = JRoute::_('index.php?option=com_resources'.a.'alias='. $line->alias);
					} else {
						$sef = JRoute::_('index.php?option=com_resources'.a.'id='. $line->id);
					}
				} else {
					if ($line->group != '' && $line->scope != '') {
						$sef = JRoute::_('index.php?option=com_groups&scope='.$line->scope.'&pagename='.$line->alias);
					} else {
						$sef = JRoute::_('index.php?option=com_topics&scope='.$line->scope.'&pagename='.$line->alias);
					}
					//$sef = JRoute::_('index.php?option=com_topics'.a.'scope='.$line->scope.a.'pagename='. $line->alias);
				}

				// Encode some potentially troublesome characters
				$line->title = ResourcesHtml::encode_html( $line->title );

				// Make sure we have an SEF, otherwise it's a querystring
				if (strstr($sef,'option=')) {
					$d = a;
				} else {
					$d = '?';
				}

				// Format the ranking
				$line->ranking = round($line->ranking, 1);
				$r = (10*$line->ranking);
				if (intval($r) < 10) {
					$r = '0'.$r;
				}

				$sbjt .= t.t.'<tr>'.n; 
				$sbjt .= t.t.t.'<td class="ranking">'.number_format($line->ranking,1).' <span class="rank-'.$r.'">'.JText::_('RELATED_RANKING').'</span></td>'.n;
				$sbjt .= t.t.t.'<td>';
				//if ($line->section != 'Topic' && $show_edit == 1) {
				//	$html .= ResourcesHtml::adminIcon( $line->id, $line->published, $show_edit, 0, 'edit', $line->type);
				//}
				if ($line->section != 'Topic') {
					$sbjt .= JText::_('PART_OF').' ';
					$sbjt .= '<a href="'.$sef.'" class="fixedResourceTip" title="DOM:rsrce'.$line->id.'">'. $line->title . '</a>'.n;
					$sbjt .= t.t.'<div style="display:none;" id="rsrce'.$line->id.'">'.n;
					$sbjt .= t.t.t.ResourcesHtml::hed(4,$line->title).n;
					$sbjt .= t.t.t.'<div>'.n;
					$sbjt .= t.t.t.t.'<table>'.n;
					$sbjt .= t.t.t.t.t.'<tbody>'.n;
					$sbjt .= ResourcesHtml::tableRow(JText::_('RELATED_TYPE'),$line->section);
					if ($resourceEx->contributors) {
						$sbjt .= ResourcesHtml::tableRow(JText::_('RELATED_CONTRIBUTORS'),$resourceEx->contributors);
					}
					$sbjt .= ResourcesHtml::tableRow(JText::_('RELATED_DATE'),JHTML::_('date',$line->publish_up, '%d %b, %Y'));
					$sbjt .= ResourcesHtml::tableRow(JText::_('RELATED_AVG_RATING'),'<span class="avgrating'.$class.'"><span>'.JText::sprintf('OUT_OF_5_STARS',$line->rating).'</span>&nbsp;</span> ('.$line->times_rated.')');
					$starz  = t.t.t.t.t.'<ul class="starsz'.$myclass.'">'.n;
					$starz .= t.t.t.t.t.' <li class="str1"><a href="'.$sef.'/reviews'.$d.'action=addreview'.a.'myrating=1#reviewform" title="'.JText::_('RATING_POOR').'">'.JText::_('RATING_1_STAR').'</a></li>'.n;
					$starz .= t.t.t.t.t.' <li class="str2"><a href="'.$sef.'/reviews'.$d.'action=addreview'.a.'myrating=2#reviewform" title="'.JText::_('RATING_FAIR').'">'.JText::_('RATING_2_STARS').'</a></li>'.n;
					$starz .= t.t.t.t.t.' <li class="str3"><a href="'.$sef.'/reviews'.$d.'action=addreview'.a.'myrating=3#reviewform" title="'.JText::_('RATING_GOOD').'">'.JText::_('RATING_3_STARS').'</a></li>'.n;
					$starz .= t.t.t.t.t.' <li class="str4"><a href="'.$sef.'/reviews'.$d.'action=addreview'.a.'myrating=4#reviewform" title="'.JText::_('RATING_VERY_GOOD').'">'.JText::_('RATING_4_STARS').'</a></li>'.n;
					$starz .= t.t.t.t.t.' <li class="str5"><a href="'.$sef.'/reviews'.$d.'action=addreview'.a.'myrating=5#reviewform" title="'.JText::_('RATING_EXCELLENT').'">'.JText::_('RATING_5_STARS').'</a></li>'.n;
					$starz .= t.t.t.t.t.'</ul>'.n;
					$sbjt .= ResourcesHtml::tableRow(JText::_('RELATED_RATE_THIS'),$starz);
					$sbjt .= t.t.t.t.t.'</tbody>'.n;
					$sbjt .= t.t.t.t.'</table>'.n;
					$sbjt .= t.t.t.'</div>'.n;
					$sbjt .= t.t.t.ResourcesHtml::shortenText( $line->introtext ).n;
					$sbjt .= t.t.'</div>'.n;
				} else {
					$sbjt .= '<a href="'.$sef.'" title="'.$line->title.'">'. $line->title . '</a>'.n;
				}
				$sbjt .= '</td>'.n;
				$sbjt .= t.t.t.'<td class="type">'.$line->section.'</td>'.n;
				$sbjt .= t.t.'</tr>'.n;
			}
			$sbjt .= t.'</tbody>'.n;
			$sbjt .= '</table>'.n;
		} else {
			$sbjt  = '<p>'.JText::_('NO_RELATED_RESULTS_FOUND').'</p>'.n;
		}
		
		// Build the final HTML
		$html  = ResourcesHtml::hed(3,'<a name="related"></a>'.JText::_('RELATED_HEADER')).n;
		$html .= ResourcesHtml::aside('<p>'.JText::_('RELATED_EXPLANATION').'</p>');
		$html .= ResourcesHtml::div( $sbjt, 'subject' );

		// Return the an array of content
		$arr = array(
				'html'=>$html,
				'metadata'=>''
			);

		return $arr;
	}
}
