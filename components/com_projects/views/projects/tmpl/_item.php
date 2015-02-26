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

$privacyTxt = $this->row->private
	? JText::_('COM_PROJECTS_PRIVATE')
	: JText::_('COM_PROJECTS_PUBLIC');

?>
<tr class="mline" id="tr_<?php echo $this->row->id; ?>">
	<td class="th_image">
		<a href="<?php echo JRoute::_('index.php?option=' . $this->option .  '&task=view&alias=' . $this->row->alias); ?>">
			<img src="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->row->alias . '&task=media'); ?>" alt="<?php echo $this->escape(stripslashes($this->row->title)); ?>" title="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
		</a>
	</td>
	<td class="th_privacy"><span class="privacy-icon<?php echo $this->row->private ? ' private' : ''; ?>" title="<?php echo ucfirst($privacyTxt) . ' ' . JText::_('COM_PROJECTS_PROJECT'); ?>"></span></td>
	<td class="th_title">
		<a href="<?php echo JRoute::_('index.php?option=' . $this->option .  '&task=view&alias=' . $this->row->alias); ?>">
		<?php echo $this->escape(stripslashes($this->row->title));  ?>
		</a>
	</td>
	<td class="mini faded">
		<?php
		echo ($this->row->owned_by_group) ? '<span class="i_group"><a href="' . JRoute::_('index.php?option=com_groups&cn=' . $this->row->groupcn) . '">' . $this->row->groupname . '</a></span>' : '<span class="i_user"><a href="' . JRoute::_('index.php?option=com_members&id=' . $this->row->owned_by_user) . '">' . $this->row->authorname . '</a></span>';

		// Reviewers
		if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive')))
		{
			$profile = \Hubzero\User\Profile::getInstance($this->row->created_by_user);
			if ($profile)
			{
				echo '<span class="block">'. $profile->get('email').'</span>';
				if ($profile->get('phone'))
				{
					echo '<span class="block"> Tel.'. $profile->get('phone').'</span>';
				}
			}
		}
		?>
	</td>
<?php
// Reviewers extra info
if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive')))
{
	// Get project params
	$params = new JParameter( $this->row->params );

	if ($this->filters['reviewer'] == 'sensitive')
	{
		$info = '';
		if ($params->get('hipaa_data') == 'yes')
		{
			$info .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_TERMS_HIPAA') . '</span>';
		}
		if ($params->get('ferpa_data') == 'yes')
		{
			$info .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_TERMS_FERPA') . '</span>';
		}
		if ($params->get('export_data') == 'yes')
		{
			$info .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_EXPORT_CONTROLLED') . '</span>';
		}
		if ($params->get('irb_data') == 'yes')
		{
			$info .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_IRB') . '</span>';
		}
		if ($params->get('restricted_data') == 'maybe' && $params->get('followup') == 'yes')
		{
			$info .= '<span class="block">' . JText::_('COM_PROJECTS_SETUP_FOLLOW_UP_NECESSARY') . '</span>';
		}
	?>
	<td class="mini"><?php echo $info; ?></td>
	<td class="mini">
	<?php if ($this->row->state == 1 && $this->row->setup_stage >= 3) {
		echo '<span class="active green">'.JText::_('COM_PROJECTS_ACTIVE').'</span>';
	}
	else if ($this->row->setup_stage < 3) {
		echo '<span class="setup">'.JText::_('COM_PROJECTS_STATUS_SETUP').'</span> '.JText::_('COM_PROJECTS_IN_PROGRESS');
	}
	else if ($this->row->state == 0) {
		echo '<span class="faded italic">'.JText::_('COM_PROJECTS_STATUS_INACTIVE').'</span> ';
	}
	else if ($this->row->state == 5) {
		echo '<span class="italic pending">'.JText::_('COM_PROJECTS_STATUS_PENDING').'</span>';
	}
	$commentCount = 0;
	if (isset($this->row->admin_notes) && $this->row->admin_notes) {
		$commentCount = ProjectsHtml::getAdminNoteCount($this->row->admin_notes, 'sensitive');
		echo ProjectsHtml::getLastAdminNote($this->row->admin_notes, 'sensitive');
	}
	echo '<span class="block mini"><a href="' . JRoute::_('index.php?option=' . $this->option .  '&task=process&id=' . $this->row->id . '&reviewer=' . $this->filters['reviewer']) . '" class="showinbox">' . $commentCount . ' ' . JText::_('COM_PROJECTS_COMMENTS') . '</a></span>';

	?></td>
	<td><?php if ($this->row->state == 5) {
		echo '<span class="manage mini"><a href="' . JRoute::_('index.php?option=' . $this->option . '&task=process&id=' . $this->row->id . '&reviewer=' . $this->filters['reviewer'] ) . '" class="showinbox">' . JText::_('COM_PROJECTS_APPROVE') . '</a></span>';
	} ?>
	</td>
<?php } ?>
<?php if ($this->filters['reviewer'] == 'sponsored') {
		$info = '';
		if ($params->get('grant_title'))
		{
			$info .= '<span class="block"><span class="faded">' . JText::_('COM_PROJECTS_GRANT_TITLE')
			. ':</span> ' . $params->get('grant_title') . '</span>';
		}
		if ($params->get('grant_PI'))
		{
			$info .= '<span class="block"><span class="faded">' . JText::_('COM_PROJECTS_GRANT_PI')
			. ':</span> ' . $params->get('grant_PI') . '</span>';
		}
		if ($params->get('grant_agency'))
		{
			$info .= '<span class="block"><span class="faded">' . JText::_('COM_PROJECTS_GRANT_AGENCY')
			. ':</span> ' . $params->get('grant_agency') . '</span>';
		}
		if ($params->get('grant_budget'))
		{
			$info .= '<span class="block"><span class="faded">' . JText::_('COM_PROJECTS_GRANT_BUDGET')
			. ':</span> ' . $params->get('grant_budget') . '</span>';
		}
	?>
	<td class="mini"><?php echo $info; ?></td>
	<td class="mini"><?php
	if (!$params->get('grant_approval') && $params->get('grant_status', 0) == 0)
	{
		echo '<span class="italic pending">'
		. JText::_('COM_PROJECTS_STATUS_PENDING_SPS') . '</span>';
	}
	else if ($params->get('grant_approval') || $params->get('grant_status') == 1 )
	{
		echo '<span class="active green">'
		. JText::_('COM_PROJECTS_APPROVAL_CODE') . ': ' . $params->get('grant_approval', '(N/A)') . '</span>';
	}
	else if ($params->get('grant_status') == '2')
	{
		echo '<span class="italic dark">'
		. JText::_('COM_PROJECTS_STATUS_SPS_REJECTED') . '</span>';
	}
	if (isset($this->row->admin_notes) && $this->row->admin_notes)
	{
		echo ProjectsHtml::getLastAdminNote($this->row->admin_notes, 'sponsored');
	}
	?></td>
	<td class="faded actions"><?php echo '<span class="manage mini"><a href="' . JRoute::_('index.php?option=' . $this->option . '&task=process&id=' . $this->row->id ) . '?reviewer=' . $this->filters['reviewer'] . '&filterby=' . $this->filters['filterby'] . '" class="showinbox">' . JText::_('COM_PROJECTS_MANAGE') . '</a></span>'; ?></td>
<?php }
} ?>
</tr>