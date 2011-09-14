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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div class="main section" id="statistics">
	
	<table class="activeusers" summary="Logged-in users">
		<caption><?php echo JText::_('Table 1: Logged-in Users'); ?></caption>
		<thead>
			<tr>
				<th><?php echo JText::_('Name'); ?></th>
				<th><?php echo JText::_('Login'); ?></th>
				<th><?php echo JText::_('Org Type'); ?></th>
				<th><?php echo JText::_('Organization'); ?></th>
				<th><?php echo JText::_('Resident'); ?></th>
				<th><?php echo JText::_('Host'); ?></th>
				<th><?php echo JText::_('IP'); ?></th>
				<th><?php echo JText::_('Idle'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="summary">
				<th colspan="7" class="numerical-data"><?php echo JText::_('Total Users'); ?></th>
				<td><?php echo count($this->users); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$html = '';
			if (count($this->users) > 0) {
				$cls = 'even';
				$users = $this->users;
				foreach (array_keys($users) as $userkey)
				{
					$cls = (($cls == 'even') ? 'odd' : 'even');

					for ($i = 0; $i < count($users[$userkey]) - 4; $i++)
					{
						if ($i) {
							$cls .=  ' sameuser';
						}
						$html .= t.t.'<tr class="'.$cls.'">'.n;
						if ($i) {
							$html .= t.t.t.'<td colspan="5">&nbsp;</td>'.n;
						} else {
							$html .= t.t.t.'<td class="textual-data">'. stripslashes($users[$userkey]['name']) .'</td>'.n;
							$html .= t.t.t.'<td class="textual-data"><a href="'.JRoute::_('index.php?option='.$this->option.a.'task=view'.a.'username='.$userkey).'">'.$userkey.'</td>'.n;
							$html .= t.t.t.'<td class="textual-data">';
							switch ($users[$userkey]['orgtype'])
							{
								case 'universitystudent':
									$html .= JText::_('University / College Student');
									break;
								case 'university':
								case 'universityfaculty':
									$html .= JText::_('University / College Faculty');
									break;
								case 'universitystaff':
									$html .= JText::_('University / College Staff');
									break;
								case 'precollege':
								case 'precollegefacultystaff': $html .= JText::_('K - 12 (Pre-College) Faculty or Staff'); break;
								case 'precollegestudent':  $html .= JText::_('K - 12 (Pre-College) Student'); break;
								case 'educational': $html .= JText::_('Educational');          break;
								case 'nationallab': $html .= JText::_('National Laboratory');  break;
								case 'industry':    $html .= JText::_('Industry / Private');   break;
								case 'government':  $html .= JText::_('Government Agency');    break;
								case 'military':    $html .= JText::_('Military');             break;
								case 'personal':    $html .= JText::_('Personal');             break;
								case 'unemployed':  $html .= JText::_('Retired / Unemployed'); break;
								default: $html .=  $users[$userkey]['orgtype']; break;
							}
							$html .= '</td>'.n;
							$html .= t.t.t.'<td class="textual-data">'. stripslashes($users[$userkey]['org']) .'</td>'.n;
							$html .= t.t.t.'<td class="textual-data">'. $users[$userkey]['countryresident'] .'</td>'.n;
						}
						$html .= t.t.t.'<td class="textual-data">'. $users[$userkey][$i]['host'] .'</td>'.n;
						$html .= t.t.t.'<td class="textual-data">'. $users[$userkey][$i]['ip'] .'</td>'.n;
						$html .= t.t.t.'<td class="textual-data">'. MembersHtml::valformat($users[$userkey][$i]['idle'], 3) .'</td>'.n;
						$html .= t.t.'</tr>'.n;
					}
				}
			} else {
				$html .= t.t.'<tr class="odd">'.n;
				$html .= t.t.t.'<td colspan="8">'.JText::_('No results found.').'</td>'.n;
				$html .= t.t.'</tr>'.n;
			}
			echo $html;
			?>
		</tbody>
	</table>
	
	<table summary="<?php echo JText::_('Guest users'); ?>">
		<caption><?php echo JText::_('Table 2: Guests'); ?></caption>
		<thead>
			<tr>
				<th><?php echo JText::_('Name'); ?></th>
				<th><?php echo JText::_('Host'); ?></th>
				<th><?php echo JText::_('IP'); ?></th>
				<th><?php echo JText::_('Idle'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="summary">
				<th colspan="3" class="numerical-data"><?php echo JText::_('Total Guests'); ?></th>
				<td><?php echo count($this->guests); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$html = '';
		if (count($this->guests) > 0) {
			$guests = $this->guests;
			$cls = 'even';
			foreach ($guests as $guest)
			{
				$cls = (($cls == 'even') ? 'odd' : 'even');

				$guest['host'] = ($guest['host']) ? $guest['host'] : JText::_('Unknown');
				$guest['ip'] = ($guest['ip']) ? $guest['ip'] : JText::_('Unknown');

				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<td class="textual-data">'.JText::_('(guest)').'</td>'.n;
				$html .= t.t.t.'<td class="textual-data">'.$guest['host'].'</td>'.n;
				$html .= t.t.t.'<td class="textual-data">'.$guest['ip'].'</td>'.n;
				$html .= t.t.t.'<td class="textual-data">'.MembersHtml::valformat($guest['idle'], 3).'</td>'.n;
				$html .= t.t.'</tr>'.n;
			}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="5">'.JText::_('No results found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		echo $html;
		?>
		</tbody>
	</table>
	
</div><!-- / .section -->

