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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dateTimeFormat = '%b. %d, %Y @%I:%M %p';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateTimeFormat = 'M. d, Y @h:i A';
	$tz = null;
}

?>
<table class="activity" summary="<?php echo JText::_('PLG_COURSES_MEMBERS_ACTIVITY_TABLE_SUMMARY'); ?>">
	<tbody>
<?php 
if ($this->logs) {
	foreach ($this->logs as $log)
	{
		$name = JText::_('UNKNOWN');
		//$username = JText::_('UNKNOWN');

		$juser =& JUser::getInstance( $log->actorid );
		if (is_object($juser) && $juser->get('name')) {
			$name = $juser->get('name');
			//$username = $juser->get('username');
		}

		$info = '';

		if ($log->uid && $log->uid != $log->actorid) {
			$target_name = JText::_('UNKNOWN');
			//$target_username = JText::_('UNKNOWN');

			if(is_numeric($log->uid)) {
				$target_user =& JUser::getInstance( $log->uid );
				if (is_object($target_user) && $target_user->get('name')) {
					$target_name = $target_user->get('name');
					//$target_username = $target_user->get('username');
				}
				$info .= ' <a href="'.JRoute::_('index.php?option=com_members&id='.$log->uid).'">'.$target_name.'</a>';
			} else {
				$info .= $log->uid;
			}

		}

		switch ($log->action)
		{
			case 'membership_cancelled':
			case 'membership_invites_sent':
			case 'membership_email_sent':
			case 'membership_invite_accepted':
			case 'membership_invite_cancelled':
			case 'membership_requested':
			case 'membership_denied':
			case 'membership_approved':
			case 'membership_promoted':
			case 'membership_demoted':
				$area = '<span class="membership-action">'.JText::_('PLG_COURSES_MEMBERS_MEMBER').'</span>';
			break;

			case 'course_created':
			case 'course_edited':
			case 'course_approved':
			case 'course_deleted':
				$area = '<span class="course-action">'.JText::_('PLG_COURSES_MEMBERS_COURSE').'</span>';
			break;

			case 'course_members_message':
				$area = '<span class="course-action">'.JText::_('PLG_COURSES_MEMBERS_COURSE').'</span>';
				$info .= ' '.JText::_('PLG_COURSES_MEMBERS_ALL_MEMBERS');
			break;
			case 'course_managers_message':
				$area = '<span class="course-action">'.JText::_('PLG_COURSES_MEMBERS_COURSE').'</span>';
				$info .= ' '.JText::_('PLG_COURSES_MEMBERS_ALL_MANAGERS');
			break;
			case 'course_pending_message':
				$area = '<span class="course-action">'.JText::_('PLG_COURSES_MEMBERS_COURSE').'</span>';
				$info .= ' '.JText::_('PLG_COURSES_MEMBERS_ALL_PENDING_MEMBERS');
			break;
			case 'course_invitees_message':
				$area = '<span class="course-action">'.JText::_('PLG_COURSES_MEMBERS_COURSE').'</span>';
				$info .= ' '.JText::_('PLG_COURSES_MEMBERS_ALL_INVITEES');
			break;
		}
?>
		<tr>
			<th scope="row"><?php echo $area; ?></th>
			<td class="author"><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$log->actorid); ?>"><?php echo stripslashes($name); ?></a></td>
			<td class="action"><?php echo JText::_('PLG_COURSES_'.strtoupper($log->action)).$info; ?></td>
			<td class="date"><?php echo JHTML::_('date', $log->timestamp, $dateTimeFormat, $tz); ?></td>
		</tr>
<?php
	}
} else {
	// Do nothing if there are no events to display
?>
		<tr>
			<td><?php echo JText::_('PLG_COURSES_MEMBERS_NO_ACTIVITY_FOUND'); ?></td>
		</tr>
<?php 
}
?>
	</tbody>
</table>
