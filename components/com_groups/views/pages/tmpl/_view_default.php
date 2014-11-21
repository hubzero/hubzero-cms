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

if (!function_exists('isSystemUser'))
{
	function isSystemUser( $userid )
	{
		return ($userid < 1000) ? null : $userid;
	}
}

// get group params
$params = JComponentHelper::getParams("com_groups");
$displaySystemUsers = $params->get('display_system_users', 'no');

//get this groups params
$gparams = new JParameter($this->group->get('params'));
$displaySystemUsers = $gparams->get('display_system_users', $displaySystemUsers);

//get the group members
$members = $this->group->get('members');
shuffle($members);

//if we dont want to display system users
//filter values through callback above and then reset array keys
if ($displaySystemUsers == 'no')
{
	$members = array_map("isSystemUser", $members);
	$members = array_values(array_filter($members));
}

//are we a group member
$isMember = (in_array($this->juser->get('id'), $this->group->get('members'))) ? true : false;

//get the members plugin access for this group
$memberAccess = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'members');
?>

<div class="group-content-header">
	<h3><?php echo JText::_('COM_GROUPS_OVERVIEW_ABOUT_HEADING'); ?></h3>
	<?php if ($isMember && $this->privateDesc != '') : ?>
		<div class="group-content-header-extra">
			<a id="toggle_description" class="hide" href="#"><?php echo JText::_('COM_GROUPS_SHOW_PUBLIC_DESCRIPTION'); ?></a>
		</div>
	<?php endif; ?>
</div>
<div id="description">
	<?php if ($isMember && $this->privateDesc != '') : ?>
		<div id="private">
			<?php echo $this->privateDesc; ?>
		</div>
		<div id="public" class="hide">
			<?php echo $this->publicDesc; ?>
		</div>
	<?php else : ?>
		<div id="public">
			<?php echo $this->publicDesc; ?>
		</div>
	<?php endif; ?>
</div>

<?php if ($memberAccess == 'anyone' || ($memberAccess == 'registered' && !$this->juser->get('guest')) || ($memberAccess == 'members' && $isMember)) : ?>
	<div class="group-content-header">
		<h3><?php echo JText::_('COM_GROUPS_OVERVIEW_MEMBERS_HEADING'); ?></h3>
		<div class="group-content-header-extra">
			<a href="<?php echo JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=members'); ?>">
				<?php echo JText::_('COM_GROUPS_OVERVIEW_MEMBERS_BTN_TEXT') . ' &rarr;'; ?>
			</a>
		</div>
	</div>

	<div id="member_browser" class="member_browser">
		<?php
			$counter = 1;
			foreach ($members as $k => $member) : ?>
			<?php
				$profile = \Hubzero\User\Profile::getInstance($member);
				if ($counter <= 12 && is_object($profile)) :
			?>
				<a href="<?php echo JRoute::_($profile->getLink()); ?>" class="member" title="<?php echo JText::sprintf('COM_GROUPS_MEMBER_PROFILE', stripslashes($profile->get('name'))); ?>">
					<img src="<?php echo $profile->getPicture(0, true); ?>" alt="<?php echo $this->escape(stripslashes($profile->get('name'))); ?>" class="member-border" width="50px" height="50px" />
					<span class="name"><?php echo $this->escape(stripslashes($profile->get('name'))); ?></span>
					<span class="org"><?php echo $this->escape(stripslashes($profile->get('organization'))); ?></span>
				</a>
			<?php $counter++; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>