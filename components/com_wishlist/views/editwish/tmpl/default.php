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

/* Add/Edit Wish */

	$wishlist = $this->wishlist;
	$wish = $this->wish;
	$task = $this->task;
	$admin = $this->admin;
	$infolink = $this->infolink;
	$juser = $this->juser;
	$funds = $this->funds;

	$html = '';

	if ($wishlist) 
	{
		// what is submitter name?
		if ($task=='editwish') 
		{
			$login = JText::_('COM_WISHLIST_UNKNOWN');
			$ruser =& JUser::getInstance($wish->proposed_by);
			if (is_object($ruser)) 
			{
				$login = $ruser->get('username');
			}
		}

		$wish->subject = str_replace('&quote;','&quot;', stripslashes($wish->subject));
		$wish->subject = $this->escape($wish->subject);

		$wish->about = trim(stripslashes($wish->about));
		$wish->about = preg_replace('/<br\\s*?\/??>/i', '', $wish->about);
		$wish->about = WishlistHtml::txt_unpee($wish->about);
?>
	<div id="content-header">
		<h2><?php echo $this->escape($this->title); ?></h2>
	</div>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-wish nav_wishlist btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=wishlist&category='. $wishlist->category . '&rid=' . $wishlist->referenceid); ?>">
					<?php echo JText::_('COM_WISHLIST_WISHES_ALL'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->

	<div class="main section">
		<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
			<?php if ($this->getError()) { ?>
				<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
			<?php } ?>
			<div class="explaination">
				<p><?php echo JText::_('COM_WISHLIST_TEXT_ADD_WISH'); ?></p>
				<?php if ($this->banking && $task!='editwish') { ?>
					<p class="help" style="margin-top: 2em;">
						<strong><?php echo JText::_('COM_WISHLIST_WHAT_IS_REWARD'); ?></strong><br />
						<?php echo JText::_('COM_WISHLIST_WHY_ADDBONUS'); ?> <a href="<?php echo $infolink; ?>"><?php echo JText::_('COM_WISHLIST_LEARN_MORE'); ?></a> <?php echo JText::_('COM_WISHLIST_ABOUT_POINTS'); ?>.
					</p>
				<?php } ?>
			</div><!-- / .aside -->
				<fieldset>
			<?php if ($task == 'editwish') { ?>
					<label>
						<?php echo JText::_('COM_WISHLIST_WISH_PROPOSED_BY'); ?>: <span class="required"><?php echo JText::_('COM_WISHLIST_REQUIRED'); ?></span>
						<input name="by" maxlength="50" id="by" type="text" value="<?php echo $login; ?>" />
					</label>
			<?php } ?>
					<input type="hidden" id="proposed_by" name="proposed_by" value="<?php echo $wish->proposed_by; ?>" />
					<label>
						<input class="option" type="checkbox" name="anonymous" value="1" <?php echo ($wish->anonymous) ? 'checked="checked"' : ''; ?>/> 
						<?php echo JText::_('COM_WISHLIST_WISH_POST_ANONYMOUSLY'); ?>
					</label>
			<?php if ($admin == 2 && $wishlist->public) { // list owner ?>
 					<label>
						<input class="option" type="checkbox" name="private" value="1" <?php echo ($wish->private) ? 'checked="checked"' : ''; ?>/>
						<?php echo JText::_('COM_WISHLIST_WISH_MAKE_PRIVATE'); ?>
					</label>
			<?php } ?>
					<input type="hidden"  name="task" value="savewish" />
					<input type="hidden" id="wishlist" name="wishlist" value="<?php echo $wishlist->id; ?>" />
					<input type="hidden" id="status" name="status" value="<?php echo $wish->status; ?>" />
					<input type="hidden" id="id" name="id" value="<?php echo $wish->id; ?>" />
					
					<label for="subject">
						<?php echo JText::_('COM_WISHLIST_SUMMARY_OF_WISH'); ?> <span class="required"><?php echo JText::_('COM_WISHLIST_REQUIRED'); ?></span>
						<input name="subject" maxlength="120" id="subject" type="text" value="<?php echo $wish->subject; ?>" />
					</label>
					<label for="field_about">
						<?php echo JText::_('COM_WISHLIST_WISH_EXPLAIN_IN_DETAIL'); ?>: 
						<?php
							ximport('Hubzero_Wiki_Editor');
							$editor = Hubzero_Wiki_Editor::getInstance();
							echo $editor->display('about', 'field_about', $wish->about, 'minimal', '50', '10');
						?>
					</label>
					<label>
						<?php echo JText::_('COM_WISHLIST_WISH_ADD_TAGS'); ?>: <br />
			<?php 
			// Tag editor plug-in
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher =& JDispatcher::getInstance();
			$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags','', $wish->tags)) );
			if (count($tf) > 0) {
				echo $tf[0];
			} else { ?>
						<textarea name="tags" id="tags-men" rows="6" cols="35"><?php echo $wish->tags; ?></textarea>
			<?php } ?>
					</label>
			<?php if ($this->banking && $task != 'editwish') { ?>
					<label>
						<?php echo JText::_('COM_WISHLIST_ASSIGN_REWARD'); ?>:<br />
						<input type="text" name="reward" value="" size="5"<?php if ($funds <= 0 ) { echo ' disabled="disabled" style="background:#e2e2e2;"'; } ?> /> 
						<span class="subtext"><?php echo JText::_('COM_WISHLIST_YOU_HAVE'); ?> <strong><?php echo $funds; ?></strong> <?php echo JText::_('COM_WISHLIST_POINTS_TO_SPEND'); ?>.</span>
					</label>
					<input type="hidden"  name="funds" value="<?php echo $funds; ?>" />
			<?php } ?>
					
				</fieldset>
				<div class="clear"></div>
				
				<p class="submit"><input type="submit" id="send-wish" value="<?php echo JText::_('COM_WISHLIST_FORM_SUBMIT'); ?>" /></p>
			</form>

		
	</div><!-- / .main section -->
	<?php } else { ?>
	<p class="error"><?php echo JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'); ?></p>
	<?php } ?>