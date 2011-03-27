<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$juser =& JFactory::getUser();

?>
<div class="main section">
	<div class="aside">
		<div class="container">
			<h3>Start Your Own</h3>
			<p class="starter"><span class="starter-point"></span></p>
			<p class="starter">Create your own discussion where you and other users can discuss related topics.</p>
			<?php if(in_array($this->juser->get('id'),$this->members)) { ?>
				<p class="add">
					<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=newtopic'); ?>">Add Topic</a>
				</p>
			<?php } else { ?>
				<?php
					if ($this->juser->get('guest')) {
						$rtrn = JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum');
						echo "<p class=\"warning\">".JText::sprintf('PLG_GROUPS_FORUM_MUST_LOGIN_AND_MEMBER', base64_encode($rtrn), 'post a topic')."</p>";
					} else {
						echo "<p class=\"warning\">".JText::sprintf('PLG_GROUPS_FORUM_MUST_MEMBER', 'post a topic')."</p>";
					}
				?>
			<?php } ?>
		</div>
	</div>
	
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum'); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<legend>Search for articles</legend>				
					<label for="entry-search-field">Enter keyword or phrase</label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo htmlentities($this->search, ENT_QUOTES); ?>" />
				</fieldset>
			</div><!-- / .container -->
			<div class="container">
				<table class="entries">
					<caption><?php echo JText::_('Discussion Topics'); ?></caption>
					<tbody>
						<?php if($this->rows) { ?>
							<?php 
								$counter = 0; 
								if($this->limit == 0) {
									$this->limit = count($this->rows);
								}
							?>
							<?php foreach($this->rows as $row) { ?>
								<?php if($counter < $this->limit) { ?>
									<?php
										$creator =& JUser::getInstance($row->created_by);
										if(is_object($creator)) {
											$creator_link = "<a href=\"/members/{$creator->get('id')}\">{$creator->get('name')}</a>";
										}

										$lastpost = $this->forum->getLastPost( $row->id );
										if(count($lastpost) > 0) {
											$lastpost = $lastpost[0];
											$lastposter =& JUser::getInstance($lastpost->created_by);
											if(is_object($lastposter)) {
												$lastposter_link = "<a href=\"/members/{$lastposter->get('id')}\">{$lastposter->get('name')}</a>";
											}
										}
									?>
									<tr>
										<td>
											<?php if($row->sticky) { ?>
												<p class="topic sticky tooltips" title="Sticky Topic :: This is a topic that is pushed to the top of the discussions list.">
													<a class="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=topic&topic='.$row->id); ?>"><?php echo stripslashes($row->topic); ?></a>
												</p>
											<?php } else { ?>
												<p class="topic">
													<a class="" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=topic&topic='.$row->id); ?>"><?php echo stripslashes($row->topic); ?></a>
												</p>
											<?php } ?>
											<p class="details">
												<span class="created">
													<span class="created_on"><?php echo date("d M, Y", strtotime($row->created)); ?></span>
													<?php 
														echo "<span class=\"created_by\">Created by: ";
														echo ($row->anonymous) ? JText::_('Anonymous') : $creator_link; 
														echo "</span>";
													?>
												</span>
												<?php if($row->replies) { ?>
													|<span class="replies"><?php echo $row->replies; ?> <span><?php echo ($row->replies > 1 || $row->replies == 0) ? 'replies' : 'reply'; ?></span></span>|
												<?php } ?>
												<?php if($lastpost) { ?>
													<span class="lastpost">Last Post by:
														<?php 
															echo "<span class=\"lastpost_by\">";
															echo ($lastpost->anonymous) ? JText::_('Anonymous') : $lastposter_link; 
															echo "</span>";
														?>
														~ <span class="lastpost_on"><?php echo date("d M, Y", strtotime($lastpost->created)); ?></span>
														

													</span>
												<?php } ?>
											</p>
											<?php if($this->authorized) { ?>
												<span class="options">
													<?php if($row->created_by == $juser->get('id') || $this->authorized == 'admin') { ?>
														<a class="edit" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=edittopic&topic='.$row->id); ?>">Edit</a>
													<?php } ?>
													<?php if($this->authorized == 'admin') { ?>
														<a class="delete" href="<?php echo JRoute::_('index.php?option='.$this->option.'&gid='.$this->group->get('cn').'&active=forum&task=deletetopic&topic='.$row->id); ?>">Delete</a>
													<?php } ?>
												</span>
											<?php } ?>
										</td>
									</tr>
								<?php } ?>
								<?php $counter++; ?>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td>Currently there are no discussions.</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php 
				if ($this->pageNav) {
					// @FIXME: Nick's Fix Based on Resources View
					$pf = $this->pageNav->getListFooter();
					$nm = str_replace('com_','',$this->option);
					$pf = str_replace($nm.'/?',$nm.'/'.$this->group->get('cn').'/'.$this->_element.'/?',$pf);
					echo $pf;
					//echo $this->pageNav->getListFooter();
					// @FIXME: End Nick's Fix
				}
				?>
				<br class="clear" />
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		</form>
	</div>
</div>
