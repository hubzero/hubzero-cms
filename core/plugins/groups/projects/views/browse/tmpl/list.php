<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

switch ($this->which)
{
	case 'group':
		$title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_GROUP');
		break;
	case 'owned':
		$title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OWNED');
		break;
	case 'other':
		$title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OTHER');
		break;
	default:
	case 'all':
		$title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_ALL');
		break;
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
				<th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_MEMBERSHIP'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 0;
			foreach ($this->rows as $row)
			{
				$role = $row->access('member')
					? ($row->access('manager') ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_MANAGER') : Lang::txt('PLG_GROUPS_PROJECTS_STATUS_COLLABORATOR'))
					: Lang::txt('PLG_GROUPS_PROJECTS_STATUS_NOTMEMBER');

				$role = $row->access('readonly') && !$row->isArchived()
					? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_REVIEWER')
					: $role;

				$setup = $row->inSetup() ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SETUP') : '';

				$i++;
				?>
				<tr class="mline">
					<td class="th_image">
						<?php if ($row->access('member') || $row->access('readonly')) { ?>
							<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>">
								<img src="<?php echo Route::url($row->link('thumb')); ?>" alt="<?php echo $this->escape($row->get('title')); ?>" class="project-image" />
							</a>
						<?php } else { ?>
							<img src="<?php echo Route::url($row->link('thumb')); ?>" alt="<?php echo $this->escape($row->get('title')); ?>" class="project-image" />
						<?php } ?>
						<?php if ($row->get('newactivity') && $row->isActive() && !$setup) { ?>
							<span class="s-new"><?php echo $row->get('newactivity'); ?></span>
						<?php } ?>
					</td>
					<td class="th_privacy">
						<?php if (!$row->isPublic()) { echo '<span class="privacy-icon">&nbsp;</span>' ; } ?>
					</td>
					<td class="th_title">
						<?php if ($row->access('member') || $row->access('readonly')) { ?>
							<a href="<?php echo Route::url($row->link()); ?>" title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>">
								<?php echo $this->escape($row->get('title')); ?>
							</a>
						<?php } else { ?>
							<?php echo $this->escape($row->get('title')); ?>
						<?php } ?>
						<?php if ($this->which != 'owned') { ?>
							<span class="block"><?php echo $row->groupOwner() ? $row->groupOwner('description') : $row->owner('name'); ?></span>
						<?php } ?>
					</td>
					<td class="th_status">
						<?php
						$html = '';
						if ($row->access('owner'))
						{
							if ($row->isActive())
							{
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
							else if ($row->isArchived())
							{
								$html .= '<span class="archived">' . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_ARCHIVED') . '</span> ';
							}
						}
						echo $html;
						?>
					</td>
					<td class="th_role">
						<?php echo $role; ?>
					</td>
					<td class="th_membership">
						<?php echo ($row->get('sync_group') ? '<span class="synced">' . Lang::txt('PLG_GROUPS_PROJECTS_GROUP_SYNCED') . '</span>' : '<span class="selected">' . Lang::txt('PLG_GROUPS_PROJECTS_GROUP_SELECTED') . '</span>'); ?>
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
