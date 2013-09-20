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
 * @author	Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!$this->juser->get('guest')) {
	$category = ($this->level==0) ? 'wish': 'wishcomment';
	
	$class = ' hide';
	if (is_object($this->addcomment)) {
		$class = ($this->addcomment->referenceid == $this->refid && $this->addcomment->category==$category) ? '' : ' hide';
	}
	if ($this->level == 0) { ?>
		<div class="below section<?php echo $class; ?>">
			<h3>
				<!-- <a name="commentform"></a> -->
				<?php echo JText::_('COM_WISHLIST_ACTION_ADD_COMMENT'); ?>
			</h3>
			<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="commentform" enctype="multipart/form-data">
				<div class="aside">
					<table class="wiki-reference">
						<caption>Wiki Syntax Reference</caption>
						<tbody>
							<tr>
								<td>'''bold'''</td>
								<td><b>bold</b></td>
							</tr>
							<tr>
								<td>''italic''</td>
								<td><i>italic</i></td>
							</tr>
							<tr>
								<td>__underline__</td>
								<td><span style="text-decoration:underline;">underline</span></td>
							</tr>
							<tr>
								<td>{{{monospace}}}</td>
								<td><code>monospace</code></td>
							</tr>
							<tr>
								<td>~~strike-through~~</td>
								<td><del>strike-through</del></td>
							</tr>
							<tr>
								<td>^superscript^</td>
								<td><sup>superscript</sup></td>
							</tr>
							<tr>
								<td>,,subscript,,</td>
								<td><sub>subscript</sub></td>
							</tr>
						</tbody>
					</table>
				</div><!-- / .aside -->
				<div class="subject">
					<p class="comment-member-photo">
						<span class="comment-anchor"><a name="answerform"></a></span>
					<?php
						if (!$this->juser->get('guest')) {
							$jxuser = new Hubzero_User_Profile();
							$jxuser->load($this->juser->get('id'));
							$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
						} else {
							$config =& JComponentHelper::getParams('com_members');
							$thumb = $config->get('defaultpic');
							if (substr($thumb, 0, 1) != DS) {
								$thumb = DS.$dfthumb;
							}
							$thumb = Hubzero_User_Profile_Helper::thumbit($thumb);
						}
					?>
						<img src="<?php echo $thumb; ?>" alt="" />
					</p>
					<fieldset>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="listid" value="<?php echo $this->listid; ?>" />
						<input type="hidden" name="wishid" value="<?php echo $this->wishid; ?>" />
						<input type="hidden" name="active" value="answers" />
						<input type="hidden" name="task" value="savereply" />
						<input type="hidden" name="referenceid" value="<?php echo $this->refid; ?>" />
						<input type="hidden" name="cat" value="<?php echo $category; ?>" />

						<label>
							<?php echo JText::_('COM_WISHLIST_ENTER_COMMENTS'); ?>
							<?php
							ximport('Hubzero_Wiki_Editor');
							$editor = Hubzero_Wiki_Editor::getInstance();
							echo $editor->display('comment', 'field_comment', '', 'minimal no-footer', '50', '10');
							?>
						</label>
						
						<fieldset>
							<div class="grouping">
								<label>
									 <?php echo JText::_('COM_WISHLIST_ACTION_ATTACH_FILE'); ?>
									<input type="file" name="upload" />
								</label>
								<label>
									 <?php echo JText::_('COM_WISHLIST_ACTION_ATTACH_FILE_DESC'); ?>
									<input type="text" name="description" value="" />
								</label>
							</div>
						</fieldset>

						<label id="comment-anonymous-label">
							<input class="option" type="checkbox" name="anonymous" value="1" id="comment-anonymous" /> 
							<?php echo JText::_('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
						</label>

						<p class="submit">
							<input type="submit" value="<?php echo JText::_('COM_WISHLIST_POST_COMMENT'); ?>" />
						</p>

						<div class="sidenote">
							<p>
								<strong><?php echo JText::_('COM_WISHLIST_COMMENT_KEEP_POLITE'); ?></strong>
							</p>
							<p>
								<?php echo JText::_('COM_WISHLIST_PLAN_FORMATTING_HELP'); ?> <a href="/wiki/Help:WikiFormatting" class="popup 400x500">Wiki syntax</a> is supported.
							</p>
						</div>
					</fieldset>
				</div><!-- / .subject -->
				<div class="clear"></div>
			</form>
		</div><!-- / .below section -->
<?php } else { ?>
					<div class="addcomment<?php echo $class; ?>" id="comm_<?php echo $this->refid; ?>">
						<form action="index.php" method="post" id="commentform_<?php echo $this->refid; ?>" enctype="multipart/form-data">
							<fieldset>
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="listid" value="<?php echo $this->listid; ?>" />
								<input type="hidden" name="wishid" value="<?php echo $this->wishid; ?>" />
								<input type="hidden" name="active" value="answers" />
								<input type="hidden" name="task" value="savereply" />
								<input type="hidden" name="referenceid" value="<?php echo $this->refid; ?>" />
								<input type="hidden" name="cat" value="<?php echo $category; ?>" />
								
								<label for="comment<?php echo $this->refid; ?>">
									<?php echo JText::_('COM_WISHLIST_ENTER_COMMENTS'); ?>
									<textarea name="comment" id="comment<?php echo $this->refid; ?>" rows="4" cols="50" class="commentarea"><?php echo JText::_('COM_WISHLIST_ENTER_COMMENTS'); ?></textarea>
								</label>
								
								<fieldset>
									<div class="grouping">
										<label for="upload<?php echo $this->refid; ?>">
											<?php echo JText::_('COM_WISHLIST_ACTION_ATTACH_FILE'); ?>
											<input type="file" name="upload" id="upload<?php echo $this->refid; ?>" />
										</label>
										<label for="description<?php echo $this->refid; ?>">
											<?php echo JText::_('COM_WISHLIST_ACTION_ATTACH_FILE_DESC'); ?>
											<input type="text" name="description" value="" id="description<?php echo $this->refid; ?>" />
										</label>
									</div>
								</fieldset>
								
								<label for="anonymous<?php echo $this->refid; ?>" class="reply-anonymous-label">
									<input class="option" type="checkbox" name="anonymous" id="anonymous<?php echo $this->refid; ?>" value="1" /> 
									<?php echo JText::_('COM_WISHLIST_POST_COMMENT_ANONYMOUSLY'); ?>
								</label>
							
								<p class="submit">
									<input type="submit" value="<?php echo JText::_('COM_WISHLIST_POST_COMMENT'); ?>" /> 
									<a class="closeform cancelreply" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&listid='.$this->listid.'&wishid='.$this->wishid.'#c'.$this->refid); ?>" id="close_<?php echo $this->refid; ?>"><?php echo JText::_('COM_WISHLIST_CANCEL'); ?></a>
								</p>
							</fieldset>
						</form>
					</div><!-- / .addcomment -->
<?php } ?>
<?php
}
?>
