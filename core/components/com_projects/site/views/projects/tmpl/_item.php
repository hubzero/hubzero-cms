<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$privacyTxt = $this->row->isPublic()
	? Lang::txt('COM_PROJECTS_PUBLIC')
	: Lang::txt('COM_PROJECTS_PRIVATE');
?>
<tr class="mline" id="tr_<?php echo $this->row->get('id'); ?>">
	<td class="th_image">
		<a href="<?php echo Route::url($this->row->link()); ?>">
			<img src="<?php echo Route::url($this->row->link('thumb')); ?>" alt="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" title="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>" />
		</a>
	</td>
	<td class="th_privacy"><span class="privacy-icon<?php echo !$this->row->isPublic() ? ' private' : ''; ?>" title="<?php echo ucfirst($privacyTxt) . ' ' . Lang::txt('COM_PROJECTS_PROJECT'); ?>"></span></td>
	<td class="th_title">
		<a href="<?php echo Route::url($this->row->link()); ?>">
		<?php echo $this->escape(stripslashes($this->row->get('title')));  ?>
		</a>
	</td>
	<td class="mini faded">
		<?php
		echo ($this->row->groupOwner()) ? '<span class="i_group"><a href="' . Route::url('index.php?option=com_groups&cn=' . $this->row->groupOwner('cn')) . '">' . $this->row->groupOwner('description') . '</a></span>' : '<span class="i_user"><a href="' . Route::url('index.php?option=com_members&id=' . $this->row->owner('id')) . '">' . $this->row->owner('name') . '</a></span>';

		// Reviewers
		if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive')) && $this->row->owner())
		{
			echo '<span class="block">' . $this->row->owner('email') . '</span>';
			if ($this->row->owner('phone'))
			{
				echo '<span class="block"> Tel.' . $this->row->owner('phone') . '</span>';
			}
		}
		?>
	</td>
	<?php
	// Reviewers extra info
	if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive')))
	{
		// Get project params
		$params = new \Hubzero\Config\Registry($this->row->get('params'));

		if ($this->filters['reviewer'] == 'sensitive') { ?>
			<td class="mini">
				<?php
				$info = '';
				if ($params->get('hipaa_data') == 'yes')
				{
					$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_TERMS_HIPAA') . '</span>';
				}
				if ($params->get('ferpa_data') == 'yes')
				{
					$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_TERMS_FERPA') . '</span>';
				}
				if ($params->get('export_data') == 'yes')
				{
					$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_EXPORT_CONTROLLED') . '</span>';
				}
				if ($params->get('irb_data') == 'yes')
				{
					$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_IRB') . '</span>';
				}
				if ($params->get('restricted_data') == 'maybe' && $params->get('followup') == 'yes')
				{
					$info .= '<span class="block">' . Lang::txt('COM_PROJECTS_SETUP_FOLLOW_UP_NECESSARY') . '</span>';
				}
				echo $info;
				?>
			</td>
			<td class="mini">
				<?php
				if ($this->row->isActive())
				{
					echo '<span class="active green">' . Lang::txt('COM_PROJECTS_ACTIVE') . '</span>';
				}
				else if ($this->row->inSetup())
				{
					echo '<span class="setup">' . Lang::txt('COM_PROJECTS_STATUS_SETUP') . '</span> ' . Lang::txt('COM_PROJECTS_IN_PROGRESS');
				}
				else if ($this->row->isInactive())
				{
					echo '<span class="faded italic">' . Lang::txt('COM_PROJECTS_STATUS_INACTIVE') . '</span> ';
				}
				else if ($this->row->isArchived())
				{
					echo '<span class="archived">' . Lang::txt('COM_PROJECTS_STATUS_ARCHIVED') . '</span> ';
				}
				else if ($this->row->isPending())
				{
					echo '<span class="italic pending">' . Lang::txt('COM_PROJECTS_STATUS_PENDING') . '</span>';
				}
				$commentCount = 0;
				if ($this->row->get('admin_notes'))
				{
					$commentCount = \Components\Projects\Helpers\Html::getAdminNoteCount($this->row->get('admin_notes'), 'sensitive');
					echo \Components\Projects\Helpers\Html::getLastAdminNote($this->row->get('admin_notes'), 'sensitive');
				}
				echo '<span class="block mini"><a href="' . Route::url('index.php?option=' . $this->option .  '&task=process&id=' . $this->row->get('id') . '&reviewer=' . $this->filters['reviewer']) . '" class="showinbox">' . $commentCount . ' ' . Lang::txt('COM_PROJECTS_COMMENTS') . '</a></span>';
				?>
			</td>
			<td>
				<?php
				if ($this->row->isPending())
				{
					echo '<span class="manage mini"><a href="' . Route::url('index.php?option=' . $this->option . '&task=process&id=' . $this->row->get('id') . '&reviewer=' . $this->filters['reviewer'] ) . '" class="showinbox">' . Lang::txt('COM_PROJECTS_APPROVE') . '</a></span>';
				}
				?>
			</td>
		<?php } ?>
		<?php if ($this->filters['reviewer'] == 'sponsored') { ?>
			<td class="mini">
				<?php
				$info = '';
				if ($params->get('grant_title'))
				{
					$info .= '<span class="block"><span class="faded">' . Lang::txt('COM_PROJECTS_GRANT_TITLE') . ':</span> ' . $params->get('grant_title') . '</span>';
				}
				if ($params->get('grant_PI'))
				{
					$info .= '<span class="block"><span class="faded">' . Lang::txt('COM_PROJECTS_GRANT_PI') . ':</span> ' . $params->get('grant_PI') . '</span>';
				}
				if ($params->get('grant_agency'))
				{
					$info .= '<span class="block"><span class="faded">' . Lang::txt('COM_PROJECTS_GRANT_AGENCY') . ':</span> ' . $params->get('grant_agency') . '</span>';
				}
				if ($params->get('grant_budget'))
				{
					$info .= '<span class="block"><span class="faded">' . Lang::txt('COM_PROJECTS_GRANT_BUDGET') . ':</span> ' . $params->get('grant_budget') . '</span>';
				}
				echo $info;
				?>
			</td>
			<td class="mini">
				<?php
				if (!$params->get('grant_approval') && $params->get('grant_status', 0) == 0)
				{
					echo '<span class="italic pending">' . Lang::txt('COM_PROJECTS_STATUS_PENDING_SPS') . '</span>';
				}
				else if ($params->get('grant_approval') || $params->get('grant_status') == 1 )
				{
					echo '<span class="active green">' . Lang::txt('COM_PROJECTS_APPROVAL_CODE') . ': ' . $params->get('grant_approval', '(N/A)') . '</span>';
				}
				else if ($params->get('grant_status') == '2')
				{
					echo '<span class="italic dark">' . Lang::txt('COM_PROJECTS_STATUS_SPS_REJECTED') . '</span>';
				}
				if ($this->row->get('admin_notes'))
				{
					echo \Components\Projects\Helpers\Html::getLastAdminNote($this->row->get('admin_notes'), 'sponsored');
				}
				?>
			</td>
			<td class="faded actions">
				<?php echo '<span class="manage mini"><a href="' . Route::url('index.php?option=' . $this->option . '&task=process&id=' . $this->row->get('id') ) . '?reviewer=' . $this->filters['reviewer'] . '&filterby=' . $this->filters['filterby'] . '" class="showinbox">' . Lang::txt('COM_PROJECTS_MANAGE') . '</a></span>'; ?>
			</td>
		<?php } ?>
	<?php } ?>
</tr>