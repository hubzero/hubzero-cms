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
JPlugin::loadLanguage( 'plg_members_usage' );

//-----------

class plgMembersUsage extends JPlugin
{
	function plgMembersUsage(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'usage' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onMembersAreas( $authorized ) 
	{
		$areas = array(
			'usage' => JText::_('USAGE')
		);
		return $areas;
	}

	//-----------

	function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		
		$arr = array(
				'html'=>'',
				'metadata'=>''
			);
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		$database =& JFactory::getDBO();
		$tables = $database->getTableList();
		$table = $database->_table_prefix.'author_stats';

		if (!in_array($table,$tables)) {
			$arr['html'] = MembersHtml::error( JText::_('USAGE_ERROR_MISSING_TABLE') );
			$arr['metadata'] = '<p class="usage"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=usage').'">'.JText::_('DETAILED_USAGE').'</a></p>'.n;
			return $arr;
		}

		$html = '';
		if ($returnhtml) {
			ximport('xdocument');
			XDocument::addComponentStylesheet('com_usage');
			
			$dthis = JRequest::getVar('dthis',date('Y').'-'.date('m'));
			$period = '14';
			$cls = 'even';

			$contribution = $this->first_last_contribution($database, $member->get('uidNumber'));
			$rank = $this->get_rank($database, $member->get('uidNumber'));

			//$sbjt  = '<div id="statistics">'.n;
			$sbjt  = t.'<table class="data" summary="'.JText::_('TBL_SUMMARY_OVERVIEW').'">'.n;
			$sbjt .= t.t.'<caption>'.JText::_('TBL_CAPTION_OVERVIEW').'</caption>'.n;
			$sbjt .= t.t.'<thead>'.n;
			$sbjt .= t.t.t.'<tr>'.n;
			$sbjt .= t.t.t.t.'<th scope="col" class="textual-data">'.JText::_('TBL_TH_ITEM').'</th>'.n;
			$sbjt .= t.t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_VALUE').'</th>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$sbjt .= t.t.'</thead>'.n;
			$sbjt .= t.t.'<tbody>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('TBL_TH_CONTRIBUTIONS').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.$contribution['contribs'].'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$total = $this->get_total_stats($database, $member->get('uidNumber'), 'tool_users',14);
			if ($total) {
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$sbjt .= t.t.t.t.t.t.'<tr class="'.$cls.'">'.n;
				$sbjt .= t.t.t.t.t.t.t.'<th scope="row">'.JText::_('TBL_TH_USERS_SERVED_TOOLS').':</th>'.n;
				$sbjt .= t.t.t.t.t.t.t.'<td>'.number_format($total).'</td>'.n;
				$sbjt .= t.t.t.t.t.t.'</tr>'.n;
			}
			$total = $this->get_total_stats($database, $member->get('uidNumber'), 'andmore_users',14);
			if ($total) {
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$sbjt .= t.t.t.t.t.t.'<tr class="'.$cls.'">'.n;
				$sbjt .= t.t.t.t.t.t.t.'<th scope="row">'.JText::_('TBL_TH_USERS_SERVED_ANDMORE').':</th>'.n;
				$sbjt .= t.t.t.t.t.t.t.'<td>'.number_format($total).'</td>'.n;
				$sbjt .= t.t.t.t.t.t.'</tr>'.n;
			}
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('TBL_TH_CONTRIBUTIONS_RANK').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.$rank.'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('TBL_TH_CONTRIBUTIONS_FIRST').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.$contribution['first'].'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('TBL_TH_CONTRIBUTIONS_LAST').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.$contribution['last'].'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">Citations on Contributions:</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.plgMembersUsage::get_citationcount($database, null, $member->get('uidNumber')).'</td>'.n;

			$sbjt .= t.t.t.'</tr>'.n;
			$sbjt .= t.t.'</tbody>'.n;
			$sbjt .= t.'</table>'.n;

			$sbjt .= $this->tool_stats($database, $member->get('uidNumber'), $dthis, $period);	
			$sbjt .= $this->andmore_stats($database, $member->get('uidNumber'), $dthis, $period);

			//$sbjt .= '</div>'.n;

			$html  = MembersHtml::hed(3,'<a name="usage"></a>'.JText::_('USAGE')).n;
			$html .= MembersHtml::aside('<p class="info">'.JText::_('USAGE_EXPLANATION').'</p>');
			$html .= MembersHtml::subject($sbjt, 'statistics');
		}

		$metadata  = '<p class="usage"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=usage').'">'.JText::_('DETAILED_USAGE').'</a></p>'.n;
		if (is_file(JPATH_ROOT.DS.'site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'_th.gif')) {
			$metadata .= '<p><a rel="lightbox" href="/site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'.gif"><img src="/site/stats/contributor_impact/impact_'.$this->uid($member->get('uidNumber')).'_th.gif" alt="'.JText::_('Impact plot').'" /></a></p>'.n;
		}

		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
	
	//-----------
	
	public function uid($uid) 
	{
		if ($uid < 0) {
			return 'n' . -$uid;
		} else {
			return $uid;
		}
	}

	//-----------
	
	public function first_last_contribution(&$database, $authorid) 
	{
		$sql = "SELECT COUNT(DISTINCT aa.subid) as contribs, DATE_FORMAT(MIN(res.publish_up), '%d %b %Y') AS first_contrib, DATE_FORMAT(MAX(res.publish_up), '%d %b %Y') AS last_contrib FROM #__resources res, #__author_assoc aa, #__resource_types restypes WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = '".$authorid."' AND res.published = 1 AND res.access != 1 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1";
		
		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		
		$contribution = array();
		$contribution['contribs'] = '';
		$contribution['first'] = '';
		$contribution['last'] = '';
		
		if ($results) {
			foreach ($results as $row) 
			{
				$contribution['contribs'] = $row->contribs;
				$contribution['first'] = $row->first_contrib;
				$contribution['last'] = $row->last_contrib;
	        }
		}
		
		return $contribution;
	}
	
	//-----------
	
	public function tool_stats(&$database, $authorid, $dthis, $period) 
	{
		$cls = 'even';
		$html = '';
		$count = 1;
		$sum_simcount_12 = 0;
		$sum_simcount_14 = 0;
		$sql = "SELECT res.id, res.title, DATE_FORMAT(res.publish_up, '%d %b %Y') AS publish_up, restypes.type 
				FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
				WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = '".$authorid."' AND res.published = 1 AND res.access != 1 AND res.type = 7 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 ORDER BY res.publish_up DESC";

		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		
		$html .= '<table class="data" summary="'.JText::_('TBL_SUMMARY_TOOLS').'">'.n;
		$html .= t.'<caption>'.JText::_('TBL_CAPTION_TOOLS').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col">'.JText::_('TBL_TH_NUMBER').'</th>'.n;
		$html .= t.t.t.'<th scope="col">'.JText::_('TBL_TH_TOOL_TITLE').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_USERS_YEAR').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_SIM_RUNS_YEAR').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_USERS_TOTAL').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_SIM_RUNS_TOTAL').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_CITATIONS').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_PUBLISHED').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		if ($results) {	
			foreach ($results as $row) 
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				
				$sim_count_12 = $this->get_simcount($database, $row->id, 12);
				$sim_count_14 = $this->get_simcount($database, $row->id, 14);
				$sum_simcount_12 += $sim_count_12;
				$sum_simcount_14 += $sim_count_14;
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<td>'.$count.'</td>'.n;
				$html .= t.t.t.'<td class="textual-data"><a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$row->id).'">'.$row->title.'</a></td>'.n;
				$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option=com_usage'.a.'task=tools'.a.'id='.$row->id.a.'period=12').'">'.number_format($this->get_usercount($database, $row->id, 12, 7)).'</a></td>'.n;
				$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option=com_usage'.a.'task=tools'.a.'id='.$row->id.a.'period=12').'">'.number_format($sim_count_12).'</a></td>'.n;
				$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option=com_usage'.a.'task=tools'.a.'id='.$row->id.a.'period=14').'">'.number_format($this->get_usercount($database, $row->id, 14, 7)).'</a></td>'.n;
				$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option=com_usage'.a.'task=tools'.a.'id='.$row->id.a.'period=14').'">'.number_format($sim_count_14).'</a></td>'.n;
				$html .= t.t.t.'<td>'.$this->get_citationcount($database, $row->id, 0).'</td>'.n;
				$html .= t.t.t.'<td>'.$row->publish_up.'</td>';
				$html .= t.t.'</tr>'.n;
				$count++;
        	}
		} else {
			$cls = ($cls == 'even') ? 'odd' : 'even';
			$html .= t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.'<td colspan="8" class="textual-data">'.JText::_('NO_RESULTS').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}

		$total_12 = $this->get_total_stats($database, $authorid, 'tool_users',12);
		$total_14 = $this->get_total_stats($database, $authorid, 'tool_users',14);
		if ($total_14 && $total_12) {
			$html .= t.t.'<tr class="summary">'.n;
			$html .= t.t.t.'<td></td>'.n;
			$html .= t.t.t.'<td class="textual-data">'.JText::_('TOTAL').'</td>'.n;
			$html .= t.t.t.'<td>'.number_format($total_12).'</td>'.n;
			$html .= t.t.t.'<td>'.number_format($sum_simcount_12).'</td>'.n;
			$html .= t.t.t.'<td>'.number_format($total_14).'</td>'.n;
			$html .= t.t.t.'<td>'.number_format($sum_simcount_14).'</td>'.n;
			$html .= t.t.t.'<td></td>'.n;
			$html .= t.t.t.'<td></td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		return $html;
	}
	
	//-----------
	
	public function andmore_stats(&$database, $authorid, $dthis, $period) 
	{
		$cls = 'even';
		$html = '';
		$count = 1;
		$sql = "SELECT res.id, res.title, DATE_FORMAT(res.publish_up, '%d %b %Y') AS publish_up, restypes.type 
				FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
				WHERE res.id = aa.subid AND res.type = restypes.id AND aa.authorid = '".$authorid."' AND res.published = 1 AND res.access != 1 AND res.type <> 7 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 ORDER BY res.publish_up DESC";
		
		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		
		$html .= '<table class="data" summary="'.JText::_('TBL_SUMMARY_RESOURCES').'">'.n;
		$html .= t.'<caption>'.JText::_('TBL_CAPTION_RESOURCES').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col">'.JText::_('TBL_TH_NUMBER').'</th>'.n;
		$html .= t.t.t.'<th scope="col">'.JText::_('TBL_TH_RESOURCE_TITLE').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_USERS_YEAR').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_USERS_TOTAL').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_CITATIONS').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TBL_TH_PUBLISHED').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		if ($results) {
			foreach ($results as $row) 
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<td>'.$count.'</td>'.n;
				$html .= t.t.t.'<td class="textual-data"><a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$row->id).'">'.$row->title.'</a> <span class="small">'.$row->type.'</span></td>'.n;
				$html .= t.t.t.'<td>'.number_format($this->get_usercount($database, $row->id,12)).'</td>'.n;
				$html .= t.t.t.'<td>'.number_format($this->get_usercount($database, $row->id,14)).'</td>'.n;
				$html .= t.t.t.'<td>'.$this->get_citationcount($database, $row->id, 0).'</td>'.n;
				$html .= t.t.t.'<td>'.$row->publish_up.'</td>'.n;
				$html .= t.t.'</tr>'.n;
				$count++;
        	}
		} else {
			$cls = ($cls == 'even') ? 'odd' : 'even';
			$html .= t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.'<td colspan="6" class="textual-data">'.JText::_('NO_RESULTS').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$total_12 = $this->get_total_stats($database, $authorid, 'andmore_users',12);
		$total_14 = $this->get_total_stats($database, $authorid, 'andmore_users',14);
		if ($total_14 && $total_12) {
			$html .= t.t.'<tr class="summary">'.n;
			$html .= t.t.t.'<td></td>'.n;
			$html .= t.t.t.'<td>'.JText::_('TOTAL').'</td>'.n;
			$html .= t.t.t.'<td>'.number_format($total_12).'</td>'.n;
			$html .= t.t.t.'<td>'.number_format($total_14).'</td>'.n;
			$html .= t.t.t.'<td></td>'.n;
			$html .= t.t.t.'<td></td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		return $html;
	}
	
	//-----------

	public function get_simcount(&$database, $resid, $period)
	{
		$data = 0;
    		$sql = 'SELECT jobs FROM #__resource_stats_tools WHERE resid="'.$resid.'" AND period="'.$period.'" ORDER BY datetime DESC LIMIT 1';

		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		if ($results) {
			foreach ($results as $row) 
			{
         			$data = $row->jobs;
			}
          }

    		return $data;
	}

	//-----------
	
	public function get_usercount(&$database, $resid, $period, $restype='0') 
	{
		if ($restype == '7') {
			$table = "#__resource_stats_tools";
		} else {
			$table = "#__resource_stats";
		}

		$data = '-';
		$sql = "SELECT MAX(datetime), users FROM ".$table." WHERE resid = '".$resid."' AND period = '".$period."' GROUP BY datetime ORDER BY datetime DESC LIMIT 1";

		$database->setQuery( $sql );
		$results = $database->loadObjectList();
		if ($results) {
			foreach ($results as $row) 
			{
				$data = $row->users;
			}
		}
		
		return $data;
	}
	
	//-----------
	
	public function get_citationcount(&$database, $resid, $authorid=0) 
	{
		if ($authorid) {
			$sql = 'SELECT COUNT(DISTINCT (c.id) ) FROM #__citations c, #__citations_assoc ca, #__author_assoc aa, #__resources r WHERE c.id = ca.cid AND r.id = ca.oid AND r.id = aa.subid AND  aa.subtable = "resources" AND ca.table = "resource" AND r.published = "1" AND r.standalone = "1" AND aa.authorid = "'.$authorid.'"';
		} else {
			$sql = 'SELECT COUNT( DISTINCT (c.id) ) AS citations FROM #__resources r, #__citations c, #__citations_assoc ca WHERE r.id = ca.oid AND ca.cid = c.id AND ca.table = "resource" AND standalone = "1" AND r.id = "'.$resid.'"';
		}
		
		$database->setQuery( $sql );
		$result = $database->loadResult();
		if ($result) {
			return $result;
		} else {
			return '-';
		}
	}
	
	//-----------
	
	public function get_rank(&$database, $authorid) 
	{
		$rank = 1;
		$sql = "SELECT aa.authorid, COUNT(DISTINCT aa.subid) as contribs 
				FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
				WHERE res.id = aa.subid AND res.type = restypes.id AND res.published = 1 AND res.access != 1 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 
				GROUP BY aa.authorid having contribs > (
					SELECT COUNT(DISTINCT aa.subid) as contribs 
					FROM #__resources res, #__author_assoc aa, #__resource_types restypes 
					WHERE res.id = aa.subid AND res.type = restypes.id AND res.published = 1 AND res.access != 1 AND res.access != 4 AND aa.subtable = 'resources' AND standalone = 1 AND aa.authorid = '".$authorid."'
				)"; 
		
		$database->setQuery( $sql );
		$results = $database->loadObjectList();
				
		if ($results) {
			foreach ($results as $row) 
			{
				$rank++;
	    	}
		}
		
		$sql = "SELECT COUNT(DISTINCT a.uidNumber) as authors 
				FROM #__xprofiles a, #__author_assoc aa, #__resources res 
				WHERE a.uidNumber=aa.authorid AND aa.subid=res.id AND aa.subtable='resources' AND res.published=1 AND res.access !=1 AND res.access!=4 AND res.standalone=1";
		
		$database->setQuery( $sql );
		$total_authors = $database->loadResult();
		
		$rank = $rank.' / '.$total_authors;
		return $rank;
	}
	
	//-----------
	
	public function get_total_stats(&$database, $authorid, $user_type, $period) 
	{
		$sql = "SELECT ".$user_type." FROM #__author_stats WHERE authorid = '".$authorid."' AND period = '".$period."' ORDER BY datetime DESC LIMIT 1";
		
		$database->setQuery( $sql ); 
		return $database->loadResult();
	}
}
