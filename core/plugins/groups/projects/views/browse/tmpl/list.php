<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

switch ($this->which)
{
	case 'group': $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_GROUP'); break;
	case 'owned': $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OWNED'); break;
	case 'other': $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OTHER'); break;
	default:
	case 'all':   $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_ALL');   break;
}
?>

	<table class="entries">
		<caption><?php echo $title . ' (' . count($this->rows) . ')'; ?></caption>
<?php if (count($this->rows) > 0) { ?>
		<thead>
			<tr>
				<th class="th_image" colspan="2"></th>
				<th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_TITLE'); ?></th>
				<th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_STATUS'); ?></th>
				<th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_MY_ROLE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($this->rows as $row)
			{
				$role = $row->access('manager')
					? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_MANAGER')
					: Lang::txt('PLG_GROUPS_PROJECTS_STATUS_COLLABORATOR');
				$role = $row->access('readonly')
					? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_REVIEWER')
					: $role;

				$setup = $row->inSetup() ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SETUP') : '';

				$i++;
				?>
				<tr class="mline">
					<td class="th_image">
						<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>">
							<img src="<?php echo Route::url($row->link('thumb')); ?>" alt="<?php echo $this->escape($row->get('title')); ?>"  class="project-image" />
						</a> <?php if ($row->get('newactivity') && $row->isActive() && !$setup) { ?><span class="s-new"><?php echo $row->get('newactivity'); ?></span><?php } ?>
					</td>
					<td class="th_privacy">
						<?php if (!$row->isPublic()) { echo '<span class="privacy-icon">&nbsp;</span>' ; } ?>
					</td>
					<td class="th_title">
						<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>"><?php echo $this->escape($row->get('title')); ?></a>
						<?php if ($this->which != 'owned') { ?>
							<span class="block"><?php echo $row->groupOwner() ? $row->groupOwner('description') : $row->owner('name'); ?></span>
						<?php } ?>
					</td>
					<td class="th_status">
						<?php
						$html = '';
						if ($row->access('owner')) {
							if ($row->isActive()) {
								$html .= '<span class="active"><a href="' . Route::url($row->link()) . '" title="' . Lang::txt('PLG_GROUPS_PROJECTS_GO_TO_PROJECT') . '">&raquo; ' . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_ACTIVE') . '</a></span>';
							}
							else if ($row->inSetup())
							{
									$html .= '<span class="setup"><a href="' . Route::url($row->link('setup')) . '" title="' . Lang::txt('PLG_GROUPS_PROJECTS_CONTINUE_SETUP') . '">&raquo; ' . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SETUP') . '</a></span> ';
							}
							else if ($row->isInactive())
							{
								$html .= '<span class="suspended">' . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SUSPENDED') . '</span> ';
							}
							else if ($row->isPending())
							{
								$html .= '<span class="pending">' . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_PENDING') . '</span> ';
							}
						}
						echo $html;
						?>
					</td>
					<td class="th_role">
						<?php echo $role; ?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
<?php } else { ?>
		<tbody>
			<tr>
				<td>
					<p class="noresults"><?php echo Lang::txt('PLG_GROUPS_PROJECTS_NO_PROJECTS'); ?></p>
				</td>
			</tr>
		</tbody>
<?php } ?>
	</table>