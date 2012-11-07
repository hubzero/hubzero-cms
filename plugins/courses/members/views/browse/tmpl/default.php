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

$offering = $this->course->offering();
?>
<div class="course_members">
	<a name="members"></a>
	<h3 class="heading"><?php echo JText::_('COURSES_MEMBERS'); ?></h3>
		
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->course->get('alias').'&offering=' . $offering->get('alias') . '&active=members&filter='.$this->filter); ?>" method="post">
		<div class="subject">
			<div class="container">
				<ul class="entries-menu filter-options">
				<?php 
					$filter = null;
					foreach ($offering->roles() as $role) 
					{ 
						$active = '';
						if ($this->filter == $role->alias) 
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
						<input type="text" name="q" value="<?php echo $this->escape($this->q); ?>" />
						<input type="submit" name="search_members" value="" />
					</fieldset>
				</div>
				<div class="clearfix"></div>

				<table class="courses entries" summary="Members of this course">
					<caption>
						<?php 
							if ($this->q) 
							{
								echo 'Search: "' . $this->escape($this->q) . '" in ';
							} 
							echo $this->escape(stripslashes($filter));
						?>
						<span>(<?php echo $offering->members(array('count' => true)); ?>)</span>
						
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
						if ($offering->members(array('role' => $this->filter))) 
						{
							ximport('Hubzero_User_Profile_Helper');
							foreach ($offering->members() as $member)
							{
								$cls = '';
								$u = Hubzero_User_Profile::getInstance($member->get('user_id'));
							// Path to users' thumbnails
							/*$config =& JComponentHelper::getParams( 'com_members' );
							$thumb = $config->get('webpath');
							if (substr($thumb, 0, 1) != DS) {
								$thumb = DS.$thumb;
							}
							if (substr($thumb, -1, 1) == DS) {
								$thumb = substr($thumb, 0, (strlen($thumb) - 1));
							}

							// Default thumbnail
							$dfthumb = $config->get('defaultpic');
							if (substr($dfthumb, 0, 1) != DS) {
								$dfthumb = DS.$dfthumb;
							}
							$dfthumb = plgCoursesMembers::thumbit($dfthumb);
							$emailthumb = '/components/com_courses/assets/img/emailthumb.png';

							// Some needed libraries
							ximport('Hubzero_User_Profile');
							$juser =& JFactory::getUser();
							// Loop through the results
							$html = '';
							if ($this->limit == 0) {
								$this->limit = 500;
							}*/
							/*for ($i=0, $n=$this->limit; $i < $n; $i++)
							{
								$inviteemail = false;

								if (($i+$this->start) >= count($this->courseusers)) {
									break;
								}
								$guser = $this->courseusers[($i+$this->start)];

								$u = new Hubzero_User_Profile();
								$u->load( $guser );
								if (!is_object($u)) {
									continue;
								} elseif(preg_match("/^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $guser)) {
									$inviteemail = true;
								}

								$cls = '';
								//$cls = (($cls == 'even') ? 'odd' : 'even');

								// User photo
								$uthumb = '';
								if ($u->get('picture')) {
									$uthumb = $thumb.DS.plgCoursesMembers::niceidformat($u->get('uidNumber')).DS.$u->get('picture');
									$uthumb = plgCoursesMembers::thumbit($uthumb);
								}

								if ($uthumb && is_file(JPATH_ROOT.$uthumb)) {
									$p = $uthumb;
								} elseif($inviteemail === true) {
									 $p = $emailthumb;
								} else {
									$p = $dfthumb;
								}

								switch ($this->filter)
								{
									case 'invitees':
										$status = JText::_('PLG_COURSES_MEMBERS_STATUS_INVITEE');
									break;
									case 'pending':
										$status = JText::_('PLG_COURSES_MEMBERS_STATUS_PENDING');
									break;
									case 'managers':
										$status = JText::_('PLG_COURSES_MEMBERS_STATUS_MANAGER');
										$cls .= ' manager';
									break;
									case 'members':
									default:
										$status = 'Member';
										if (in_array($guser,$this->managers)) {
											$status = JText::_('PLG_COURSES_MEMBERS_STATUS_MANAGER');
											$cls .= ' manager';
										}
									break;
								}

								if ($juser->get('id') == $u->get('uidNumber')) {
									$cls .= ' me';
								}*/
?>
						<tr class="<?php echo $cls; ?>">
							<td class="photo">
								<img width="50" height="50" src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($u); ?>" alt="Photo for <?php echo $this->escape(stripslashes($u->get('name'))); ?>" /></td>
							<td>
							<td>
								<?php echo $this->escape(stripslashes($u->get('name'))); ?>
							</td>
							<td>
								<?php echo $this->escape(stripslashes($member->get('role'))); ?>
							</td>
<?php
								/*if($inviteemail) {
									$html .= '<span class="name"><a href="mailto:'.$guser.'">'.$guser.'</a></span>';
									$html .= '<span class="status">Invite Sent to Email</span><br />';
								} else {
									$html .= '<span class="name"><a href="'.JRoute::_('index.php?option=com_members&id='.$u->get('uidNumber')).'">'.$u->get('name').'</a></span> <span class="status">'.$status.'</span><br />';

									if ($u->get('organization')) {
										$html .= '<span class="organization">'.$u->get('organization').'</span><br />';
									}
								}

								if($this->filter == 'members' || $this->filter == 'managers') {
									$html .= '<span class="roles">';
									$all_roles = '';
									$roles = $u->getCourseMemberRoles($u->get('uidNumber'),$this->course->gidNumber);

									if($roles) {
										$html .= '<strong>Member Roles:</strong> ';
										foreach($roles as $role) {
											$all_roles .= ', <span><a href="'.JRoute::_('index.php?option=com_courses&gid='.$this->course->cn.'&active=members&filter='.$this->filter.'&role_filter='.$role['id']).'">'.$role['role'].'</a>';

											if($this->authorized == 'manager' || $this->authorized == 'admin') {
												if($this->membership_control == 1) {
													$all_roles .= '<span class="delete-role"><a href="'.JRoute::_('index.php?option=com_courses&gid='.$this->course->cn.'&active=members&task=deleterole&uid='.$u->get('uidNumber').'&role='.$role['id']).'">x</a></span></span>';
												}
											} else {
												$all_roles .= '</span>';
											}
										}

										$html .= '<span class="roles-list" id="roles-list-'.$u->get('uidNumber').'">'.substr($all_roles,2).'</span>';

										if ($this->authorized == 'manager' || $this->authorized == 'admin') {
											if($this->membership_control == 1) {
												$html .= ', <a class="assign-role" href="'.JRoute::_('index.php?option=com_courses&gid='.$this->course->cn.'&active=members&task=assignrole&uid='.$u->get('uidNumber')).'">Assign Role &rsaquo;</a>';
											}
										}

									}

									if($this->membership_control == 1) {
										if(($this->authorized == 'manager' || $this->authorized == 'admin') && !$roles) {
											$html .= '<strong>Member Roles:</strong> ';
											$html .= '<span class="roles-list" id="roles-list-'.$u->get('uidNumber').'"></span>';
											$html .= ' <a class="assign-role" href="'.JRoute::_('index.php?option=com_courses&gid='.$this->course->cn.'&active=members&task=assignrole&uid='.$u->get('uidNumber')).'">Assign Role &rsaquo;</a>';
										}
									}
									$html .= '</span>';

								}

								if ($this->filter == 'pending') {
									$database =& JFactory::getDBO();
									$row = new CoursesReason( $database );
									$row->loadReason($u->get('uidNumber'), $this->course->gidNumber);

									if ($row) {
										$html .= '<span class="reason">'.stripslashes($row->reason).'</span>';
									}
								} else {
									//$html .= '<span class="activity">Activity: </span>';
								}

								$html .= '</td>'."\n";
								if ($this->authorized == 'manager' || $this->authorized == 'admin') {
									switch ($this->filter)
									{
										case 'invitees':
											if($this->membership_control == 1) {
												if(!$inviteemail) {
													$html .= "\t\t\t\t".'<td class="remove-member"><a class="cancel tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=members&task=cancel&users[]='.$guser.'&filter='.$this->filter).'" title="'.JText::sprintf('PLG_COURSES_MEMBERS_CANCEL_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_COURSES_MEMBERS_CANCEL').'</a></td>'."\n";
												} else {
													$html .= "\t\t\t\t".'<td class="remove-member"><a class="cancel tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=members&task=cancel&users[]='.$guser.'&filter='.$this->filter).'" title="'.JText::sprintf('PLG_COURSES_MEMBERS_CANCEL_MEMBER',htmlentities($guser,ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_COURSES_MEMBERS_CANCEL').'</a></td>'."\n";
												}
											}
											$html .= "\t\t\t\t".'<td class="approve-member"> </td>'."\n";
										break;
										case 'pending':
											if($this->membership_control == 1) {
												$html .= "\t\t\t\t".'<td class="decline-member"><a class="decline tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=members&task=deny&users[]='.$guser.'&filter='.$this->filter).'" title="'.JText::sprintf('PLG_COURSES_MEMBERS_DECLINE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_COURSES_MEMBERS_DENY').'</a></td>'."\n";
												$html .= "\t\t\t\t".'<td class="approve-member"><a class="approve tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=members&task=approve&users[]='.$guser.'&filter='.$this->filter).'" title="'.JText::sprintf('PLG_COURSES_MEMBERS_APPROVE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_COURSES_MEMBERS_APPROVE').'</a></td>'."\n";
											}
										break;
										case 'managers':
										case 'members':
										default:
											if($this->membership_control == 1) {
												if (!in_array($guser,$this->managers) || (in_array($guser,$this->managers) && count($this->managers) > 1)) {
													$html .= "\t\t\t\t".'<td class="remove-member"><a class="remove tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=members&task=remove&users[]='.$guser.'&filter='.$this->filter).'" title="'.JText::sprintf('PLG_COURSES_MEMBERS_REMOVE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_COURSES_MEMBERS_REMOVE').'</a></td>'."\n";
												} else {
													$html .= "\t\t\t\t".'<td class="remove-member"> </td>'."\n";
												}

												if (in_array($guser,$this->managers)) {
													//force admins to use backend to demote manager if only 1
													//if ($this->authorized == 'admin' || count($this->managers) > 1) {
													if (count($this->managers) > 1) {
														$html .= "\t\t\t\t".'<td class="demote-member"><a class="demote tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=members&task=demote&users[]='.$guser.'&filter='.$this->filter.'&limit='.$this->limit.'&limitstart='.$this->start).'" title="'.JText::sprintf('PLG_COURSES_MEMBERS_DEMOTE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_COURSES_MEMBERS_DEMOTE').'</a></td>'."\n";
													} else {
														$html .= "\t\t\t\t".'<td class="demote-member"> </td>'."\n";
													}
												} else {
													$html .= "\t\t\t\t".'<td class="promote-member"><a class="promote tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=members&task=promote&users[]='.$guser.'&filter='.$this->filter.'&limit='.$this->limit.'&limitstart='.$this->start).'" title="'.JText::sprintf('PLG_COURSES_MEMBERS_PROMOTE_MEMBER',htmlentities($u->get('name'),ENT_COMPAT,'UTF-8')).'">'.JText::_('PLG_COURSES_MEMBERS_PROMOTE').'</a></td>'."\n";
												}
											}
										break;
									}
								} else {
									$html .= "\t\t\t\t".'<td class="remove-member"> </td>'."\n";
									$html .= "\t\t\t\t".'<td class="demote-member"> </td>'."\n";
								}
								if ($juser->get('id') == $u->get('uidNumber') || $this->filter == 'invitees' || $this->filter == 'pending') {
									$html .= "\t\t\t\t".'<td class="message-member"> </td>'."\n";
								} else {
									if(!$inviteemail && ($this->authorized == 'manager' || $this->authorized == 'admin') && $this->messages_acl != 'nobody') {
										$html .= "\t\t\t\t".'<td class="message-member"><a class="message tooltips" href="'.JRoute::_('index.php?option='.$option.'&gid='.$this->course->cn.'&active=messages&task=new&users[]='.$guser).'" title="Message :: Send a message to '.htmlentities($u->get('name'),ENT_COMPAT,'UTF-8').'">'.JText::_('PLG_COURSES_MEMBERS_MESSAGE').'</a></td>'."\n";
									} else {
										$html .= "\t\t\t\t".'<td class="message-member"></td>'."\n";
									}
								}*/?>
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
				count($offering->get('members')), 
				$this->start, 
				$this->limit
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
	
		
		<input type="hidden" name="gid" value="<?php echo $this->course->cn; ?>" />
		<input type="hidden" name="active" value="members" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="filter" value="<?php echo $this->filter; ?>" />
	</form>
</div><!--/ #course_members -->
