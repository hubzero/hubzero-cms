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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$levels   = array();
$labels   = array();
$selected = array();
$txtlabel = '';

if ($this->audience && count($this->audience) > 0) { ?>
	<div class="usagescale">
		<div class="showscale">
			<ul class="audiencelevel">
				<?php
				$this->audience = $this->audience[0];

				for ($i = 0, $n = $this->numlevels; $i <= $n; $i++)
				{
					$lb = 'label' . $i;
					$lv = 'level' . $i;
					$ds = 'desc' . $i;
					$levels[$lv] = $this->audience->$lv;
					$labels[$lv]['title'] = $this->audience->$lb;
					$labels[$lv]['desc']  = $this->audience->$ds;
					if ($this->audience->$lv)
					{
						$selected[] = $lv;
					}
				}

				// colored circles
				foreach ($levels as $key => $value)
				{
					$class = (!$value) ? ' isoff' : '';
					$class = (!$value && $key == 'level0') ? '_isoff' : $class;
					?>
					<li class="<?php echo $key . $class; ?>"><span>&nbsp;</span></li>
					<?php
				}

				// figure out text label
				if (count($selected) == 1)
				{
					$txtlabel = $labels[$selected[0]]['title'];
				}
				else if (count($selected) > 1)
				{
					$first 	    = array_shift($selected);
					$first		= $labels[$first]['title'];
					$firstbits  = explode("-", $first);
					$first 	    = array_shift($firstbits);

					$last     = end($selected);
					$last     = $labels[$last]['title'];
					$lastbits = explode("-", $last);
					$last     = end($lastbits);

					$txtlabel = $first . '-' . $last;
				}
				else
				{
					$txtlabel = JText::_('Tool Audience Unrated');
				}
				?>
				<li class="txtlabel"><?php echo $txtlabel; ?></li>
			</ul>
		</div>

		<?php if ($this->showtips) { ?>
			<div class="explainscale">
				<table class="skillset">
					<thead>
						<tr>
							<td colspan="2" class="combtd"><?php echo JText::_('Difficulty Level'); ?></td>
							<td><?php echo JText::_('Target Audience'); ?></td>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($labels as $key => $label) { ?>
						<tr>
							<th>
								<ul class="audiencelevel">
									<?php foreach ($labels as $ky => $val) { ?>
										<li class="<?php
											$class = ($ky != $key) ? ' isoff' : '';
											$class = ($ky != $key && $ky == 'level0') ? '_isoff' : $class;
											echo $ky . $class;
											?>"><span>&nbsp;</span></li>
									<?php } ?>
								</ul>
							</th>
							<td><?php echo $label['title']; ?></td>
							<td class="secondcol"><?php echo $label['desc']; ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<p class="learnmore"><a href="<?php echo $this->audiencelink; ?>"><?php echo JText::_('Learn more'); ?> &rsaquo;</a></p>
			</div>
		<?php } ?>
	</div>
<?php } ?>