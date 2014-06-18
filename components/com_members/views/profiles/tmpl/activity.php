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

$this->css()
     ->css('usage.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section" id="statistics">

	<table class="activeusers">
		<caption><?php echo JText::_('COM_MEMBERS_ACTIVITY_TABLE1'); ?></caption>
		<thead>
			<tr>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_NAME'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_LOGIN'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_ORG_TYPE'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_ORGANIZATION'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_RESIDENT'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_IP'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_IDLE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="summary">
				<th colspan="6" class="numerical-data"><?php echo JText::_('COM_MEMBERS_ACTIVITY_TABLE1_TOTAL'); ?></th>
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
						$html .= "\t\t".'<tr class="'.$cls.'">'."\n";
						if ($i) {
							$html .= "\t\t\t".'<td colspan="5">&nbsp;</td>'."\n";
						} else {
							$html .= "\t\t\t".'<td class="textual-data">'. $this->escape(stripslashes($users[$userkey]['name'])) .'</td>'."\n";
							$html .= "\t\t\t".'<td class="textual-data"><a href="'.JRoute::_('index.php?option='.$this->option.'&task=view&username='.$userkey).'">'.$this->escape($userkey).'</td>'."\n";
							$html .= "\t\t\t".'<td class="textual-data">';
							switch ($users[$userkey]['orgtype'])
							{
								case 'universitystudent':
									$html .= JText::_('UNIVERSITY_STUDENT');
									break;
								case 'university':
								case 'universityfaculty':
									$html .= JText::_('UNIVERSITY_FACULTY');
									break;
								case 'universitystaff':
									$html .= JText::_('UNIVERSITY_STAFF');
									break;
								case 'precollege':
								case 'precollegefacultystaff': $html .= JText::_('PRECOLLEGE_STAFF'); break;
								case 'precollegestudent':  $html .= JText::_('PRECOLLEGE_STUDENT'); break;
								case 'educational': $html .= JText::_('EDUCATIONAL');          break;
								case 'nationallab': $html .= JText::_('NATIONALLAB');  break;
								case 'industry':    $html .= JText::_('INDUSTRY');   break;
								case 'government':  $html .= JText::_('GOVERNMENT');    break;
								case 'military':    $html .= JText::_('MILITARY');             break;
								case 'personal':    $html .= JText::_('PERSONAL');             break;
								case 'unemployed':  $html .= JText::_('UNEMPLOYED'); break;
								default: $html .=  $users[$userkey]['orgtype']; break;
							}
							$html .= '</td>'."\n";
							$html .= "\t\t\t".'<td class="textual-data">'. $this->escape(stripslashes($users[$userkey]['org'])) .'</td>'."\n";
							$html .= "\t\t\t".'<td class="textual-data">'. $this->escape($users[$userkey]['countryresident']) .'</td>'."\n";
						}
						$html .= "\t\t\t".'<td class="textual-data">'. $this->escape($users[$userkey][$i]['ip']) .'</td>'."\n";
						$html .= "\t\t\t".'<td class="textual-data">'. MembersHtml::valformat($users[$userkey][$i]['idle'], 3) .'</td>'."\n";
						$html .= "\t\t".'</tr>'."\n";
					}
				}
			} else {
				$html .= "\t\t".'<tr class="odd">'."\n";
				$html .= "\t\t\t".'<td colspan="8">'.JText::_('COM_MEMBERS_ACTIVITY_NO_RESULTS').'</td>'."\n";
				$html .= "\t\t".'</tr>'."\n";
			}
			echo $html;
			?>
		</tbody>
	</table>

	<table>
		<caption><?php echo JText::_('COM_MEMBERS_ACTIVITY_TABLE2'); ?></caption>
		<thead>
			<tr>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_NAME'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_IP'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_ACTIVITY_COL_IDLE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="summary">
				<th colspan="2" class="numerical-data"><?php echo JText::_('COM_MEMBERS_ACTIVITY_TABLE2_TOTAL'); ?></th>
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

				$guest['ip'] = ($guest['ip']) ? $guest['ip'] : JText::_('COM_MEMBERS_ACTIVITY_UNKNOWN');

				$html .= "\t\t".'<tr class="'.$cls.'">'."\n";
				$html .= "\t\t\t".'<td class="textual-data">'.JText::_('COM_MEMBERS_ACTIVITY_GUEST').'</td>'."\n";
				$html .= "\t\t\t".'<td class="textual-data">'.$this->escape($guest['ip']).'</td>'."\n";
				$html .= "\t\t\t".'<td class="textual-data">'.MembersHtml::valformat($guest['idle'], 3).'</td>'."\n";
				$html .= "\t\t".'</tr>'."\n";
			}
		} else {
			$html .= "\t\t".'<tr class="odd">'."\n";
			$html .= "\t\t\t".'<td colspan="5">'.JText::_('COM_MEMBERS_ACTIVITY_NO_RESULTS').'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
		}
		echo $html;
		?>
		</tbody>
	</table>

</section><!-- / .section -->