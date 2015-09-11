<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('usage.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section" id="statistics">

	<table class="activeusers">
		<caption><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_TABLE1'); ?></caption>
		<thead>
			<tr>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_NAME'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_LOGIN'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_ORG_TYPE'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_ORGANIZATION'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_RESIDENT'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_IP'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_IDLE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="summary">
				<th colspan="6" class="numerical-data"><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_TABLE1_TOTAL'); ?></th>
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

					$html .= "\t\t".'<tr class="'.$cls.'">'."\n";

					$html .= "\t\t\t".'<td class="textual-data">'. $this->escape(stripslashes($users[$userkey]['name'])) .'</td>'."\n";
					$html .= "\t\t\t".'<td class="textual-data"><a href="'.Route::url('index.php?option='.$this->option.'&id='.$users[$userkey]['uidNumber']).'">'.$this->escape($userkey).'</td>'."\n";
					$html .= "\t\t\t".'<td class="textual-data">';
					switch ($users[$userkey]['orgtype'])
					{
						case 'universitystudent':
							$html .= Lang::txt('UNIVERSITY_STUDENT');
							break;
						case 'university':
						case 'universityfaculty':
							$html .= Lang::txt('UNIVERSITY_FACULTY');
							break;
						case 'universitystaff':
							$html .= Lang::txt('UNIVERSITY_STAFF');
							break;
						case 'precollege':
						case 'precollegefacultystaff': $html .= Lang::txt('PRECOLLEGE_STAFF'); break;
						case 'precollegestudent':  $html .= Lang::txt('PRECOLLEGE_STUDENT'); break;
						case 'educational': $html .= Lang::txt('EDUCATIONAL');          break;
						case 'nationallab': $html .= Lang::txt('NATIONALLAB');  break;
						case 'industry':    $html .= Lang::txt('INDUSTRY');   break;
						case 'government':  $html .= Lang::txt('GOVERNMENT');    break;
						case 'military':    $html .= Lang::txt('MILITARY');             break;
						case 'personal':    $html .= Lang::txt('PERSONAL');             break;
						case 'unemployed':  $html .= Lang::txt('UNEMPLOYED'); break;
						default: $html .=  $users[$userkey]['orgtype']; break;
					}
					$html .= '</td>'."\n";
					$html .= "\t\t\t".'<td class="textual-data">'. $this->escape(stripslashes($users[$userkey]['org'])) .'</td>'."\n";
					$html .= "\t\t\t".'<td class="textual-data">'. $this->escape($users[$userkey]['countryresident']) .'</td>'."\n";

					$html .= "\t\t\t".'<td class="textual-data">'. $this->escape($users[$userkey][0]['ip']) .'</td>'."\n";
					$html .= "\t\t\t".'<td class="textual-data">'. \Components\Members\Helpers\Html::valformat($users[$userkey][0]['idle'], 3) .'</td>'."\n";
					$html .= "\t\t".'</tr>'."\n";
				}
			} else {
				$html .= "\t\t".'<tr class="odd">'."\n";
				$html .= "\t\t\t".'<td colspan="8">'.Lang::txt('COM_MEMBERS_ACTIVITY_NO_RESULTS').'</td>'."\n";
				$html .= "\t\t".'</tr>'."\n";
			}
			echo $html;
			?>
		</tbody>
	</table>
	<br /><br />
	<table>
		<caption><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_TABLE2'); ?></caption>
		<thead>
			<tr>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_NAME'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_IP'); ?></th>
				<th><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_COL_IDLE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr class="summary">
				<th colspan="2" class="numerical-data"><?php echo Lang::txt('COM_MEMBERS_ACTIVITY_TABLE2_TOTAL'); ?></th>
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

				$guest['ip'] = ($guest['ip']) ? $guest['ip'] : Lang::txt('COM_MEMBERS_ACTIVITY_UNKNOWN');

				$html .= "\t\t".'<tr class="'.$cls.'">'."\n";
				$html .= "\t\t\t".'<td class="textual-data">'.Lang::txt('COM_MEMBERS_ACTIVITY_GUEST').'</td>'."\n";
				$html .= "\t\t\t".'<td class="textual-data">'.$this->escape($guest['ip']).'</td>'."\n";
				$html .= "\t\t\t".'<td class="textual-data">'.\Components\Members\Helpers\Html::valformat($guest['idle'], 3).'</td>'."\n";
				$html .= "\t\t".'</tr>'."\n";
			}
		} else {
			$html .= "\t\t".'<tr class="odd">'."\n";
			$html .= "\t\t\t".'<td colspan="5">'.Lang::txt('COM_MEMBERS_ACTIVITY_NO_RESULTS').'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
		}
		echo $html;
		?>
		</tbody>
	</table>

</section><!-- / .section -->