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

$juser = JFactory::getUser();
$offering = $this->course->offering();
$section = $offering->section();
?>
<div class="course_members">
	<a name="members"></a>
	<h3 class="heading"><?php echo JText::_('COURSES_MEMBERS'); ?></h3>
		
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&offering=' . $offering->get('alias') . '&active=members&filter='.$this->filters); ?>" method="post">
		<div class="subject">
			<div class="container">
				<ul class="entries-menu filter-options">
				<?php 
					$filter = null;
					foreach ($offering->roles() as $role) 
					{ 
						$active = '';
						if ($this->filters['role'] == $role->alias) 
						{
							$filter = $role->title;
							$active = ' active';
						} 

						if (!$offering->access('manage')) 
						{
							continue;
						}
				?>
					<li>
						<a class="<?php echo $role->alias . $active; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&offering=' . $offering->get('alias') . '&active=members&filter='.$role->alias); ?>">
							<?php echo $this->escape(stripslashes($role->title)); ?> (<?php echo $role->total; ?>)
						</a>
					</li>
				<?php
					}
				?>
				</ul>
				<div class="entries-search">
					<fieldset>
						<input type="text" name="q" value="<?php echo $this->escape($this->filters['search']); ?>" />
						<input type="submit" name="search_members" value="" />
					</fieldset>
				</div>
				<div class="clearfix"></div>

				<table class="courses entries" summary="Members of this course">
					<caption>
						<?php 
							if ($this->filters['search']) 
							{
								echo 'Search: "' . $this->escape($this->filters['search']) . '" in ';
							} 
							echo $this->escape(stripslashes($filter));
							
							$total = $section->members(array('count' => true, 'role' => $this->filters['role']));
						?>
						<span>(<?php echo $total; ?>)</span>
						
						<?php /*if (($this->authorized == 'manager' || $this->authorized == 'admin') && count($this->courseusers) > 0) { ?>
							<span class="message-all">
								<?php if($this->messages_acl != 'nobody') { ?>
								<?php
									if($role_id) {
										$append = '&users[]=role&role_id='.$role_id;
										$title = 'Send message to all '.$role_name.'.';
									} else {
										switch($this->filter)
										{
											case 'pending':
												$append = '&users[]=applicants';
												$title = 'Send message to all course applicants.';
												break;
											case 'invitees':
												$append = '&users[]=invitees';
												$title = 'Send message to all course invitees.';
												break;
											case 'managers':
												$append = '&users[]=managers';
												$title = 'Send message to all course managers.';
												break;
											case 'members':
											default:
												$append = '&users[]=all';
												$title = 'Send message to all course members.';
												break;
										}
									}
								?>
								<a class="message tooltips" href="<?php echo JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=messages&task=new'.$append); ?>" title="Message :: <?php echo $title; ?>">Message All</a>
								<?php } ?>
							</span><!-- / .message-all -->
						<?php }*/ ?>
					</caption>
					<tbody>
						<?php
						if (($members = $section->members(array('role' => $this->filters['role'])))) 
						{
							ximport('Hubzero_User_Profile_Helper');
							foreach ($members as $member)
							{
								$cls = '';
								$u = Hubzero_User_Profile::getInstance($member->get('user_id'));

								if ($juser->get('id') == $u->get('uidNumber')) 
								{
									$cls .= ' me';
								}
?>
						<tr class="<?php echo $cls; ?>">
							<td class="photo">
								<img width="50" height="50" src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($u); ?>" alt="Photo for <?php echo $this->escape(stripslashes($u->get('name'))); ?>" /></td>
							<td>
							<td>
								<span class="member-name">
									<?php echo $this->escape(stripslashes($u->get('name'))); ?>
								</span>
								<?php if ($u->get('organization')) { ?>
									<br /><span class="member-organization">
										<?php echo $this->escape(stripslashes($u->get('organization'))); ?>
									</span>
								<?php } ?>
							</td>
							<td>
								<span class="member-role">
									<?php echo $this->escape(stripslashes($member->get('role'))); ?>
								</span>
							</td>
							<td>
								<time datetime="<?php echo $member->get('enrolled'); ?>"><?php echo $member->get('enrolled'); ?></time>
							</td>
							<!-- <td>
								manage course: <?php echo ($member->access('manage', 'course')) ? 'yes' : 'no'; ?><br />
								<br />
								view offering: <?php echo (intval($member->access('view'))) ? 'yes' : 'no'; ?><br />
								admin offering: <?php echo (intval($member->access('admin'))) ? 'yes' : 'no'; ?><br />
								manage offering: <?php echo (intval($member->access('manage'))) ? 'yes' : 'no'; ?><br />
								delete offering: <?php echo (intval($member->access('delete'))) ? 'yes' : 'no'; ?><br />
								edit offering: <?php echo ($member->access('edit')) ? 'yes' : 'no'; ?><br />
								edit state offering: <?php echo ($member->access('edit-state')) ? 'yes' : 'no'; ?><br />
								<br />
								manage student: <?php echo ($member->access('manage', 'student')) ? 'yes' : 'no'; ?><br />
								delete student: <?php echo ($member->access('delete', 'student')) ? 'yes' : 'no'; ?><br />
								edit student: <?php echo ($member->access('edit', 'student')) ? 'yes' : 'no'; ?><br />
								create student: <?php echo ($member->access('create', 'student')) ? 'yes' : 'no'; ?><br />
							</td> -->
						</tr>
						<?php } ?>
					<?php } else { ?>
						<tr class="odd">
							<td><?php echo JText::_('PLG_COURSES_MEMBERS_NO_RESULTS'); ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			
			<?php 
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(
				$total, 
				$this->filters['start'], 
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
			$pageNav->setAdditionalUrlParam('offering', $offering->get('alias'));
			$pageNav->setAdditionalUrlParam('active', 'members');
			echo $pageNav->getListFooter();
			?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<div class="clear"></div>
	
		
		<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
		<input type="hidden" name="active" value="members" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="filter" value="<?php echo $this->filters; ?>" />
	</form>
</div><!--/ #course_members -->
