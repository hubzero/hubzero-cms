<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
JPlugin::loadLanguage( 'plg_members_points' );

//-----------

class plgMembersPoints extends JPlugin
{
	function plgMembersPoints(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'points' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onMembersAreas( $authorized )
	{
		if (!$authorized) {
			$areas = array();
		} else {
			$areas = array(
				'points' => JText::_('POINTS')
			);
		}

		return $areas;
	}

	//-----------

	function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		if (!$authorized) {
			$returnhtml = false;
			$returnmeta = false;
		}
		
		$database =& JFactory::getDBO();
		$tables = $database->getTableList();
		$table = $database->_table_prefix.'users_points';
		
		if (!in_array($table,$tables)) {
			$arr['html'] = MembersHtml::error( JText::_('Required database table not found.') );
			return $arr;
		}

		ximport('bankaccount');

		$BTL  = new BankTeller( $database, $member->get('uidNumber') );
		$sum  = $BTL->summary();
		$hist = $BTL->history(0);
		
		
		$credit 	= $BTL->credit_summary();
		$funds 		= $sum - $credit;			
		$funds 		= ($funds > 0) ? $funds : '0';

		// Build the final HTML
		$out = '';
		if ($returnhtml) {
			$out  = MembersHtml::hed(3,'<a name="points"></a>'.JText::_('POINTS')).n;
			$out .= MembersHtml::aside(
					'<p id="point-balance"><span>'.JText::_('YOU_HAVE').' </span> '.$sum.'<small> '.strtolower(JText::_('POINTS')).'</small>'.
					'<br /><small style="font-size:70%; font-weight:normal">( '.$funds.' '.strtolower(JText::_('available to spend')).' )</small></p>'.
					'<p class="help"><strong>'.JText::_('HOW_ARE_POINTS_AWARDED').'</strong><br />'.JText::_('POINTS_AWARDED_EXPLANATION').'</p>'
				);
			$out .= MembersHtml::subject( $this->history($hist) );
		}
		
		// Build the HTML meant for the "about" tab's metadata overview
		$metadata = '';
		if ($returnmeta) {
			$metadata  = '<p class="points"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=points').'">'.JText::sprintf('NUMBER_POINTS',$sum).'</a></p>'.n;
		}
		
		$arr = array(
				'html'=>$out,
				'metadata'=>$metadata
			);

		return $arr;
	}
	
	//-----------

	function history($hist)
	{
		$cls = 'even';
		
		$html  = '<table class="transactions" summary="'.JText::_('TRANSACTIONS_TBL_SUMMARY').'">'.n;
		$html .= t.'<caption>'.JText::_('TRANSACTIONS_TBL_CAPTION').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col">'.JText::_('TRANSACTIONS_TBL_TH_DATE').'</th>'.n;
		$html .= t.t.t.'<th scope="col">'.JText::_('TRANSACTIONS_TBL_TH_DESCRIPTION').'</th>'.n;
		$html .= t.t.t.'<th scope="col">'.JText::_('TRANSACTIONS_TBL_TH_TYPE').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TRANSACTIONS_TBL_TH_AMOUNT').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('TRANSACTIONS_TBL_TH_BALANCE').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		if ($hist) {
			foreach ($hist as $item)
			{
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<td>'.JHTML::_('date',$item->created, '%d %b, %Y').'</td>'.n;
				$html .= t.t.t.'<td>'.$item->description.'</td>'.n;
				$html .= t.t.t.'<td>'.$item->type.'</td>'.n;
				if ($item->type == 'withdraw') {
					$html .= t.t.t.'<td class="numerical-data"><span class="withdraw">-'.$item->amount.'</span></td>'.n;
				} elseif ($item->type == 'hold') {
					$html .= t.t.t.'<td class="numerical-data"><span class="hold">('.$item->amount.')</span></td>'.n;
				} else {
					$html .= t.t.t.'<td class="numerical-data"><span class="deposit">+'.$item->amount.'</span></td>'.n;
				}
				$html .= t.t.t.'<td class="numerical-data">'.$item->balance.'</td>'.n;
				$html .= t.t.'</tr>'.n;
			}
		} else {
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$html .= t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.'<td colspan="5">'.JText::_('NO_TRANSACTIONS').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		return $html;
	}
}