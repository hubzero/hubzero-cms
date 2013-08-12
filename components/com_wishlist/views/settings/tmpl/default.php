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
defined('_JEXEC') or die('Restricted access');

/* Edit Wish List Settings */

		$wishlist = $this->wishlist;
		$admin = $this->admin;
		$error = $this->getError();
		$juser = $this->juser;

		$html  ='';

		// Can't view wishes on a private list if not list admin
	if (!$wishlist->public && $admin!= 2) { ?>
		<div id="content-header">
			<h2><?php echo JText::_('COM_WISHLIST_PRIVATE_LIST'); ?></h2>
		</div><!-- / #content-header -->
		<div class="main section">
			<p class="error"><?php echo JText::_('COM_WISHLIST_ALERTNOTAUTH_PRIVATE_LIST'); ?></p>
		</div><!-- / .main section -->
	<?php } else { ?>
		<div id="content-header">
			<h2><?php echo $this->title; ?></h2>
		</div><!-- / #content-header -->

		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-wish nav_wishlist btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=wishlist&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid); ?>">
						<?php echo JText::_('COM_WISHLIST_WISHES_ALL'); ?>
					</a>
				</li>
			</ul>
		</div><!-- / #content-header-extra -->

		<div class="main section">
			<form id="hubForm" method="post"  action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=savesettings&listid=' . $wishlist->id); ?>">
				<div class="explaination">
					<p><?php echo JText::_('COM_WISHLIST_SETTINGS_INFO'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo JText::_('COM_WISHLIST_INFORMATION'); ?></legend>
					
					<label>
						<?php echo JText::_('COM_WISHLIST_TITLE'); ?>: 
		<?php if ($wishlist->category== 'resource') { ?>
						<span class="highighted"><?php echo $wishlist->title; ?></span>
						<input name="title" id="title" type="hidden" value="<?php echo $this->escape($wishlist->title); ?>" />
					</label>
					<p class="hint"><?php echo JText::_('COM_WISHLIST_TITLE_NOTE'); ?></p>
		<?php } else { ?>
						<input name="title" id="title" type="text" value="<?php echo $this->escape($wishlist->title); ?>" />
					</label>
		<?php } ?>
					<label>
						<?php echo JText::_('COM_WISHLIST_DESC'); ?> (<?php echo JText::_('COM_WISHLIST_OPTIONAL'); ?>): 
						<textarea name="description" rows="10" cols="50"><?php echo $wishlist->description; ?></textarea>
					</label>
					
					<label>
						<?php echo JText::_('COM_WISHLIST_THIS_LIST_IS'); ?>: 
						<input class="option" type="radio" name="public" value="1" <?php 
			if ($wishlist->public==1) {
				echo ' checked="checked"';
			}
			if ($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
				echo ' disabled="disabled"';
			} ?> /> <?php echo JText::_('COM_WISHLIST_PUBLIC'); ?>

						<input class="option" type="radio" name="public" value="0" <?php 
			if ($wishlist->public==0) {
				echo ' checked="checked"';
			}
			if ($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
				echo ' disabled="disabled"';
			} ?> /> <?php echo JText::_('COM_WISHLIST_PRIVATE'); ?>
					</label>
				</fieldset>
				<div class="clear"></div>
				
				<div class="explaination">
					<p><?php echo JText::_('COM_WISHLIST_SETTINGS_EDIT_GROUPS'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo JText::_('COM_WISHLIST_OWNER_GROUPS'); ?></legend>
					<div class="field-wrap">
						<table class="tktlist">
							<thead>
								<tr>
									<th style="width:20px;"></th>
									<th><?php echo JText::_('COM_WISHLIST_SETTINGS_GROUP_CN'); ?></th>
									<th><?php echo JText::_('COM_WISHLIST_GROUP_NUM_MEMBERS'); ?></th>
									<th style="width:80px;"><?php echo JText::_('COM_WISHLIST_GROUP_OPTIONS'); ?></th>
								</tr>
							</thead>
							<tbody>
			<?php
			$allmembers = array();
			if (count($wishlist->groups)>0) {
				$k=1;
				for ($i=0, $n=count($wishlist->groups); $i < $n; $i++) 
				{
					$instance = Hubzero_Group::getInstance($wishlist->groups[$i]);
					$cn = $instance->get('cn');
					$members = $instance->get('members');
					$managers = $instance->get('managers');
					$members = array_merge($members, $managers);
					$members = array_unique($members);

					$allmembers = array_merge($allmembers, $members);
			?>
								<tr>
									<td><?php echo $k; ?>.</td>
									<td><?php echo $cn; ?></td>
									<td><?php echo count($members); ?></td>
									<td>
										<?php echo ($n>1 && !in_array($wishlist->groups[$i], $wishlist->nativegroups)) ? '<a href="'.JRoute::_('index.php?option='.$this->option . '&task=savesettings&listid='.$wishlist->id . '&action=delete' . '&group='.$wishlist->groups[$i]).'" class="delete">'.JText::_('COM_WISHLIST_OPTION_REMOVE').'</a>' : '' ; ?>
									</td>
								</tr>
				
				<?php 
					$k++;
				} 
				?>
			<?php } else { ?>
								<tr>
									<td colspan="4"><?php echo JText::_('COM_WISHLIST_NO_OWNER_GROUPS_FOUND'); ?>.</td>
								</tr>
			<?php } ?>
							</tbody>
						</table>
					</div>
					
					<label>
						<?php echo JText::_('COM_WISHLIST_SETTINGS_ADD_GROUPS'); ?>: 
						<input name="newgroups"  type="text" value="" />
						<span><?php echo JText::_('COM_WISHLIST_GROUP_HINT'); ?></span>
					</label>
				</fieldset>
				<div class="clear"></div>
				
				<div class="explaination">
					<p><?php echo JText::_('COM_WISHLIST_INDIVIDUALS_HINT'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo JText::_('COM_WISHLIST_INDIVIDUALS'); ?></legend>
					<div class="field-wrap">
						<table class="tktlist">
							<thead>
								<tr>
									<th style="width:20px;"></th>
									<th><?php echo JText::_('COM_WISHLIST_IND_NAME'); ?></th>
									<th><?php echo JText::_('COM_WISHLIST_IND_LOGIN'); ?></th>
									<th style="width:80px;"><?php echo JText::_('COM_WISHLIST_GROUP_OPTIONS'); ?></th>
								</tr>
							</thead>
							<tbody>
					<?php 
			$allmembers = array_unique($allmembers);

			// if we have people outside of groups
			if (count($wishlist->owners) > count($allmembers)) {
				$k=1;
				for ($i=0, $n=count($wishlist->owners); $i < $n; $i++) {
					if (!in_array($wishlist->owners[$i], $allmembers)) { 
						$kuser =& Hubzero_User_Profile::getInstance($wishlist->owners[$i]);
					?>
								<tr>
									<td><?php echo $k; ?>.</td>
									<td><?php echo $kuser->get('name'); ?></td>
									<td><?php echo $kuser->get('username'); ?></td>
									<td>
										<?php echo ($n> 1 && !in_array($wishlist->owners[$i], $wishlist->nativeowners))  ? '<a href="'.JRoute::_('index.php?option='.$this->option . '&task=savesettings&listid='.$wishlist->id . '&action=delete' . '&user='.$wishlist->owners[$i]).'" class="delete">'.JText::_('COM_WISHLIST_OPTION_REMOVE').'</a>' : '' ; ?>
									</td>
								</tr>
					<?php
						$k++;
					}
				}
			} else { ?>
								<tr>
									<td colspan="4"><?php echo JText::_('COM_WISHLIST_NO_IND_FOUND'); ?></td>
								</tr>
			<?php } ?>
							</tbody>
						</table>
					</div>
					
					<label>
						<?php echo JText::_('COM_WISHLIST_ADD_IND'); ?>: 
						<input name="newowners" id="newowners" type="text" value="" />
						<span><?php echo JText::_('COM_WISHLIST_ENTER_LOGINS'); ?></span>
					</label>
				</fieldset>
				<div class="clear"></div>

		<?php if ($wishlist->allow_advisory) { ?>
				<div class="explaination">
					<p><?php echo JText::_('COM_WISHLIST_ADD_ADVISORY_INFO'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo JText::_('COM_WISHLIST_ADVISORY'); ?></legend>
					<div class="field-wrap">
						<table class="tktlist">
							<thead>
								<tr>
									<th style="width:20px;"></th>
									<th><?php echo JText::_('COM_WISHLIST_IND_NAME'); ?></th>
									<th><?php echo JText::_('COM_WISHLIST_IND_LOGIN'); ?></th>
									<th style="width:80px;"><?php echo JText::_('COM_WISHLIST_GROUP_OPTIONS'); ?></th>
								</tr>
							</thead>
							<tbody>
						<?php
				// if we have people outside of groups
				if (count($wishlist->advisory) > 0) 
				{
					$k=1;

					for ($i=0, $n=count($wishlist->advisory); $i < $n; $i++) 
					{
						if (!in_array($wishlist->advisory[$i], $allmembers)) 
						{
							$quser =& Hubzero_User_Profile::getInstance($wishlist->advisory[$i]);
						?>
								<tr>
									<td><?php echo $k; ?>.</td>
									<td><?php echo $quser->get('name'); ?></td>
									<td><?php echo $quser->get('username'); ?></td>
									<td>
										<a href="<?php echo JRoute::_('index.php?option='.$this->option . '&task=savesettings&listid='.$wishlist->id . '&action=delete' . '&user='.$wishlist->advisory[$i]); ?>" class="delete"><?php echo JText::_('COM_WISHLIST_OPTION_REMOVE'); ?></a>
									</td>
								</tr>
						<?php
							$k++;
						}
					}
				} else { ?>
								<tr>
									<td colspan="4"><?php echo JText::_('COM_WISHLIST_NO_ADVISORY_FOUND'); ?></td>
								</tr>
				<?php } ?>
							</tbody>
						</table>
					</div>
					
					<label>
						<?php echo JText::_('COM_WISHLIST_ADD_ADVISORY_MEMBERS'); ?>: 
						<input name="newadvisory" id="newadvisory" type="text" value="" />
						<span><?php echo JText::_('COM_WISHLIST_ENTER_LOGINS'); ?></span>
					</label>
				<?php if ($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) { ?>
					<input type="hidden" name="public" value="<?php echo $wishlist->public; ?>" />
				<?php } ?>
				</fieldset>
				<div class="clear"></div>
		<?php } // -- end if allow advisory ?>

				<p class="submit">
					<input type="submit" name="submit" value="<?php echo JText::_('COM_WISHLIST_SAVE'); ?>" />
					<span class="cancelaction">
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=wishlist&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid); ?>">
							<?php echo JText::_('COM_WISHLIST_CANCEL'); ?>
						</a>
					</span>
				</p>
			</form>
		</div>
	<?php } // end if authorized ?>