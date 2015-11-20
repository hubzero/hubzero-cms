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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juri    = JURI::getInstance();
$jconfig = JFactory::getConfig();

// build urls
$base      = rtrim(str_replace('administrator', '', $juri->base()), DS);
$groupLink = $base . DS . 'groups' . DS . $this->group->get('cn');
?>

<?php echo $jconfig->getValue('config.sitename'); ?> - <?php echo $jconfig->getValue('config.MetaDesc'); ?>
-- Group Settings Saved @ <?php echo JHTML::_('date', 'now', JText::_('TIME_FORMAT_HZ1')); ?> on <?php echo JHTML::_('date', 'now', JText::_('DATE_FORMAT_HZ1')); ?>
<?php echo JText::_('Name:'); ?> <?php echo $this->group->get('cn') . '\n'; ?>
<?php echo $this->juser->get('name') . ' ('.$this->juser->get('email').') \n';?>
<?php echo JText::_('Interests (Tags):'); ?>
<?php
	$gt = new GroupsModelTags($this->group->get('gidNumber'));
	$tags = $gt->render('string'); ?>
	<?php if ($tags) : ?>
		<?php echo ($tags); ?>
	<?php else : ?>
			&lt;Empty&gt;
	<?php endif; ?>
	<?php echo JText::_('Public Description:'); ?>
		<?php if ($this->group->get('public_desc')) : ?>
			<?php echo $this->group->get('public_desc') . '\n\n'; ?>
		<?php else : ?>
			Empty\n
		<?php endif; ?>
	<?php echo JText::_('Private Description:'); ?>
		<?php if ($this->group->get('private_desc')) : ?>
			<?php echo $this->group->get('private_desc') . '\n\n'; ?>
		<?php else : ?>
			Empty
		<?php endif; ?>
		<?php echo JText::_('Logo:'); ?>
		<?php if ($this->group->get('logo')) : ?>
			<img src="<?php echo $base . DS . ltrim($this->group->getLogo(), DS); ?>" width="50px" />
		<?php else : ?>
			Not Set\n\n
		<?php endif; ?>
		<?php echo JText::_('Membership Settings/Join Policy:'); ?>
		<?php
		// Determine the join policy
		switch ($this->group->get('join_policy'))
		{
			case 3: $policy = JText::_('Closed');      break;
			case 2: $policy = JText::_('Invite Only'); break;
			case 1: $policy = JText::_('Restricted');  break;
			case 0:
			default: $policy = JText::_('Open'); break;
		}
		echo $policy;

		if ($this->group->get('join_policy') == 1)
		{
			echo  $this->group->get('restrict_msg') . '\n';
		}
		?>

		<?php echo JText::_('Discoverability:'); ?>
		<?php
		// Determine the discoverability
		switch ($this->group->get('discoverability'))
		{
			case 1:  $discoverability = JText::_('Hidden'); break;
			case 0:
			default: $discoverability = JText::_('Visible'); break;
		}
		echo $discoverability . '\n';
		?>
		<?php echo JText::_('Access Permissions:'); ?>
		<?php
		//access levels
		$levels = array(
			//'anyone' => 'Enabled/On',
			'anyone' => 'Any HUB Visitor',
			'registered' => 'Only Registered User of the HUB',
			'members' => 'Only Group Members',
			'nobody' => 'Disabled/Off'
		);

		// Get plugins
		JPluginHelper::importPlugin('groups');
		$dispatcher = JDispatcher::getInstance();
		$group_plugins = $dispatcher->trigger('onGroupAreas', array());
		array_unshift($group_plugins, array(
			'name'             => 'overview',
			'title'            => 'Overview',
			'default_access'   => 'anyone',
			'display_menu_tab' => true
		));

		$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group);

		foreach ($group_plugins as $plugin)
		{
			if ($plugin['display_menu_tab'] == 1)
			{
				$title  = $plugin['title'];
				$perm = $access[$plugin['name']];
				echo $title . ' => ' . $levels[$perm] . '\n\n\n';
			}
		}
		?>
<?php $params = JComponentHelper::getParams('com_groups'); ?>

<?php if ($params->get('email_comment_processing')) :?>
			<?php echo JText::_('Discussion Group Emails Autosubscribe:'); ?>
			<?php
			if ($this->group->get('discussion_email_autosubscribe'))
			{
				echo JText::_('On') . '\n\n';
			}
			else
			{
				echo JText::_('Off') . '\n\n';
			}
			?>
<?php endif; ?>

		<?php echo JText::_('Page Comments:'); ?>
		<?php
			$gparams = new JParameter($this->group->get('params'));
			if ($gparams->get('page_comments') == 2)
			{
				echo JText::_('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK') . '\n\n';
			}
			elseif ($gparams->get('page_comments') == 1)
			{
				echo JText::_('COM_GROUPS_PAGES_PAGE_COMMENTS_YES') . '\n\n';
			}
			else
			{
				echo JText::_('COM_GROUPS_PAGES_PAGE_COMMENTS_NO') . '\n\n';
			}
		?>
		<?php echo JText::_('Page Author Details:'); ?>
		<?php
			$gparams = new JParameter($this->group->get('params'));
			if ($gparams->get('page_author') == 1)
			{
				echo JText::_('COM_GROUPS_PAGES_SETTING_AUTHOR_YES') . '\n\n';
			}
			else
			{
				echo JText::_('COM_GROUPS_PAGES_SETTING_AUTHOR_NO') . '\n\n';
			}
		?>

<?php echo $jconfig->getValue('config.sitename'); ?> sent this email because you are a group manager for this group. Visit our <a href="<?php echo rtrim($base, DS); ?>/legal/privacy">Privacy Policy</a> and <a href="<?php echo rtrim($base, DS); ?>/support">Support Center</a> if you have any questions.</span>
