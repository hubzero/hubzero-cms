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

$base = 'index.php?option=com_members&id=' . $this->member->get('uidNumber') . '&active=groups';

$this->css();
?>
<h3 class="section-header">
	<?php echo JText::_('PLG_MEMBERS_GROUPS'); ?>
</h3>

<ul id="page_options">
	<li>
		<a class="icon-add btn add" href="<?php echo JRoute::_('index.php?option=com_groups&task=new'); ?>">
			<?php echo JText::_('PLG_MEMBERS_GROUPS_CREATE'); ?>
		</a>
	</li>
</ul>

<?php if ($this->total) { ?>
	<div class="container">
		<ul class="entries-menu filter-options">
			<li>
				<a<?php echo ($this->filter == '') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base); ?>">
					<?php echo JText::sprintf('PLG_MEMBERS_GROUPS_STATUS_ALL', $this->total); ?>
				</a>
			</li>
			<li>
				<a<?php echo ($this->filter == 'managers') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&filter=managers'); ?>">
					<?php echo JText::_('PLG_MEMBERS_GROUPS_STATUS_MANAGER'); ?>
				</a>
			</li>
			<li>
				<a<?php echo ($this->filter == 'members') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&filter=members'); ?>">
					<?php echo JText::_('PLG_MEMBERS_GROUPS_STATUS_MEMBER'); ?>
				</a>
			</li>
			<li>
				<a<?php echo ($this->filter == 'applicants') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&filter=applicants'); ?>">
					<?php echo JText::_('PLG_MEMBERS_GROUPS_STATUS_APPLICANT'); ?>
				</a>
			</li>
			<li>
				<a<?php echo ($this->filter == 'invitees') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&filter=invitees'); ?>">
					<?php echo JText::_('PLG_MEMBERS_GROUPS_STATUS_INVITEES'); ?>
				</a>
			</li>
		</ul>

		<table class="groups entries">
			<caption>
				<?php echo JText::_('PLG_MEMBERS_GROUPS_YOURS'); ?>
				<span>(<?php echo count($this->groups); ?>)</span>
			</caption>
			<tbody>
			<?php
			if ($this->groups)
			{
				foreach ($this->groups as $group)
				{
					$status = '';
					$options = '';
					$approved = false;

					if ($group->manager)
					{
						$status = 'manager';

						$options  = '<a class="manage tooltips" href="' . JRoute::_('index.php?option=' . $this->option . '&cn=' . $group->cn . '&active=members') .'" title="' . JText::_('PLG_MEMBERS_GROUPS_ACTION_MANAGE_TITLE') . '">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_MANAGE').'</a>';
						$options .= ' <a class="customize tooltips" href="' . JRoute::_('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=edit') .'" title="' . JText::_('PLG_MEMBERS_GROUPS_ACTION_EDIT_TITLE') . '">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_EDIT').'</a>';
						$options .= ' <a class="delete tooltips" href="' . JRoute::_('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=delete') .'" title="' . JText::_('PLG_MEMBERS_GROUPS_ACTION_DELETE_TITLE') . '">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_DELETE').'</a>';
					}
					else if ($group->registered && $group->regconfirmed)
					{
						$status = 'member';

						$options = '<a class="cancel tooltips" href="' . JRoute::_('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=cancel') .'" title="' . JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL_TITLE') . '">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
					}
					else if ($group->registered && !$group->regconfirmed)
					{
						$status = 'pending';

						$options = '<a class="cancel tooltips" href="' . JRoute::_('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=cancel') .'" title="' . JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL_TITLE') . '">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
					}
					else if (!$group->registered && $group->regconfirmed)
					{
						$status = 'invitee';

						$options  = '<a class="accept tooltips" href="' . JRoute::_('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=accept') .'" title="' . JText::_('PLG_MEMBERS_GROUPS_ACTION_ACCEPT_TITLE') . '">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_ACCEPT').'</a>';
						$options .= ' <a class="cancel tooltips" href="' . JRoute::_('index.php?option=' . $this->option . '&cn=' . $group->cn . '&task=cancel') .'" title="' . JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL_TITLE') . '">'.JText::_('PLG_MEMBERS_GROUPS_ACTION_CANCEL').'</a>';
					}

					//do we have a new unpublished group
					$approved = (!$group->approved) ? true : false;

					//are we published
					$published = ($group->published) ? true : false;
			?>
				<tr class=" <?php echo (!$published) ? 'notpublished' : '' ?>">
					<th>
						<span class="entry-id"><?php echo $group->gidNumber; ?></span>
					</th>
					<td>
						<?php if($published) : ?>
							<a class="entry-title" rel="<?php echo $group->gidNumber; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn='. $group->cn); ?>">
								<?php echo $this->escape(stripslashes($group->description)); ?>
							</a><br />
						<?php else : ?>
							<span class="entry-title">
								<?php echo $this->escape(stripslashes($group->description)); ?>
							</span><br />
						<?php endif; ?>
						<span class="entry-details">
							<span class="entry-alias"><?php echo $this->escape($group->cn); ?></span>
						</span>
					</td>
					<td>
						<?php
							if ($published) :
								switch ($group->join_policy)
								{
									case 3: echo '<span class="closed join-policy">' . JText::_('PLG_MEMBERS_GROUPS_STATE_CLOSED') . '</span>'."\n"; break;
									case 2: echo '<span class="inviteonly join-policy">' . JText::_('PLG_MEMBERS_GROUPS_STATE_INVITE') . '</span>'."\n"; break;
									case 1: echo '<span class="restricted join-policy">' . JText::_('PLG_MEMBERS_GROUPS_STATE_RESTRICTED') . '</span>'."\n";  break;
									case 0:
									default: echo '<span class="open join-policy">' . JText::_('PLG_MEMBERS_GROUPS_STATE_OPEN') . '</span>'."\n"; break;
								}
							endif;
						?>
					</td>
					<td>
						<?php if ($published) : ?>
							<span class="<?php echo $status; ?> status">
								<?php
									switch ($status)
									{
										case 'manager': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_MANAGER'); break;
										case 'member':  echo JText::_('PLG_MEMBERS_GROUPS_STATUS_MEMBER');  break;
										case 'pending': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_PENDING'); break;
										case 'invitee': echo JText::_('PLG_MEMBERS_GROUPS_STATUS_INVITED'); break;
										default: break;
									}
								?>
							</span>
						<?php endif; ?>
					</td>
					<td>
						<?php if (!$published) : ?>
							<span class="not-published status"><?php echo JText::_('PLG_MEMBERS_GROUPS_STATUS_NOT_PUBLISHED_GROUP'); ?></span>
						<?php elseif ($approved) : ?>
							<span class="pending-approval status"><?php echo JText::_('PLG_MEMBERS_GROUPS_STATUS_NEW_GROUP'); ?></span>
						<?php endif; ?>
					</td>
					<td class="user-options">
						<?php if ($published) : ?>
							<?php echo $options; ?>
						<?php endif; ?>
					</td>
				</tr>
			<?php
				}
			}
			else
			{
			?>
				<tr>
					<td colspan="6">
						<?php echo JText::_('PLG_MEMBERS_GROUPS_NONE_FOUND'); ?>
					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>
	</div><!-- / .container -->
<?php } else { ?>
	<div class="grid">
		<div class="col span-half">
			<h4><?php echo JText::_('PLG_MEMBERS_GROUPS_YOURS'); ?></h4>
			<p><?php echo JText::_('PLG_MEMBERS_GROUPS_YOURS_EXPLANATION'); ?></p>
		</div><!-- / .col -->
		<div class="col span-half omega">
			<h4><?php echo JText::_('PLG_MEMBERS_GROUPS_WHAT_ARE_GROUPS'); ?></h4>
			<p><?php echo JText::_('PLG_MEMBERS_GROUPS_EXPLANATION'); ?></p>
			<p><?php echo JText::sprintf('PLG_MEMBERS_GROUPS_GO_TO_GROUPS', JRoute::_('index.php?option=com_groups')); ?></p>
		</div><!-- / .col -->
	</div>
<?php } ?>
