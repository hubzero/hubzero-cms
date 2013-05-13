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

$juser = JFactory::getUser();
$database = JFactory::getDBO();

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:i a';
	$tz = true;
}

//ximport('Hubzero_View_Helper_Html');
//ximport('Hubzero_User_Profile');
ximport('Hubzero_User_Profile_Helper');

$base = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . ($this->course->offering()->section()->get('alias') != '__default' ? ':' . $this->course->offering()->section()->get('alias') : '');
?>
<div id="comments-container" data-action="<?php echo JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>">

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>">
		<?php echo $this->escape($notification['message']); ?>
	</p>
<?php } ?>

	<div class="comments-wrap">

		<div class="comments-views">
			<div class="comments-feed">
				<div class="comments-toolbar cf">
					<p class="comment-sort-options">
						<?php echo JText::sprintf('%s Discussions', count($this->threads)); ?>
					</p>
					<p class="comments-controls">
						<a class="add<?php if (!$this->thread) { echo ' active'; } ?>" href="<?php echo JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" title="<?php echo JText::_('Start a new discussion'); ?>"><?php echo JText::_('New'); ?></a>
					</p>
				</div><!-- / .comments-toolbar -->

				<div class="comments-options-bar">
					<form class="comments-search" action="<?php echo JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" method="get">
						<fieldset>
							<input type="text" name="search" class="search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('search ...'); ?>" />
							<input type="submit" class="submit" value="<?php echo JText::_('Go'); ?>" />
							<input type="hidden" name="action" value="search" />
						</fieldset>
					</form>
				</div><!-- / .comments-options-bar -->

				<div class="comment-threads">
					<div class="category search-results hide">
						<div class="category-header">
							<span class="category-title"><?php echo JText::_('Search'); ?></span>
						</div>
						<div class="category-content">
						</div>
					</div>
					<div class="category category-results">
						<!-- <div class="category-header">
							<span class="category-title"><?php echo JText::_('Discussions'); ?></span>
							<span class="category-discussions count"><?php echo count($this->threads); ?></span>
						</div> -->
						<div class="category-content">
					<?php 
					$threads_lastchange = '0000-00-00 00:00:00';
					if ($this->threads)
					{
						$threads_lastchange = $this->threads[0]->created;
						$category = $this->threads[0]->category_id;
					}

					$instructors = array();
					$inst = $this->course->instructors();
					if (count($inst) > 0) 
					{
						foreach ($inst as $i)
						{
							$instructors[] = $i->get('user_id');
						}
					}

					$cview = new Hubzero_Plugin_View(
						array(
							'folder'  => 'courses',
							'element' => 'discussions',
							'name'    => 'threads',
							'layout'  => '_threads'
						)
					);
					$cview->category   = 'category' . $this->post->category_id;
					$cview->option     = $this->option;
					$cview->threads    = $this->threads;
					$cview->unit       = $this->unit->get('alias');
					$cview->lecture    = $this->lecture->get('alias');
					$cview->config     = $this->config;
					$cview->cls        = 'odd';
					$cview->base       = 'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&active=outline';
					$cview->course     = $this->course;
					$cview->search     = $this->filters['search'];
					$cview->active     = $this->thread;
					$cview->display();
					?>
					<input type="hidden" name="threads_lastchange" id="threads_lastchange" value="<?php echo $threads_lastchange; ?>" />
						</div>
					</div>
				</div><!-- / .comment-threads -->
			</div><!-- / .comments-feed -->

			<div class="comments-panel">
				<div class="comments-toolbar">
					<p><span class="comments" data-comments="%s comments" data-add="Start a discussion">Start a discussion</span><!--  <span class="instructor-comments">0 instructor comments</span> --></p>
				</div>
				<div class="comments-frame">
<?php if (!$this->data) { ?>
					<form action="<?php echo JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias')); ?>" method="post" id="commentform" enctype="multipart/form-data">
						<p class="comment-member-photo">
							<a class="comment-anchor" name="commentform"></a>
							<?php
							$anone = 1;
							if (!$juser->get('guest')) 
							{
								$anon = 0;
							}
							$now = date('Y-m-d H:i:s', time());
							?>
							<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($juser, $anon); ?>" alt="<?php echo JText::_('User photo'); ?>" />
						</p>

						<fieldset>
						<?php if ($juser->get('guest')) { ?>
							<p class="warning"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE'); ?></p>
						<?php } else { ?>
							<p class="comment-title">
								<strong>
									<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape($juser->get('name')); ?></a>
								</strong> 
								<span class="permalink">
									<span class="comment-date-at">@</span>
									<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $timeFormat, $tz); ?></time></span> <span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span> 
									<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, $dateFormat, $tz); ?></time></span>
								</span>
							</p>

							<label for="field_comment">
								<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?></span>
								<?php
								ximport('Hubzero_Wiki_Editor');
								$editor = Hubzero_Wiki_Editor::getInstance();
								echo $editor->display('fields[comment]', 'field_comment', '', 'minimal no-footer', '35', '5');
								?>
							</label>

							<label for="field-upload" id="comment-upload">
								<span class="label-text"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?>:</span>
								<input type="file" name="upload" id="field-upload" />
							</label>

							<label for="field-anonymous" id="comment-anonymous-label">
								<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" /> 
								<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
							</p>
						<?php } ?>
						</fieldset>
						<input type="hidden" name="fields[category_id]" id="field-category_id" value="<?php echo $this->post->get('category_id'); ?>" />
						<input type="hidden" name="fields[parent]" id="field-parent" value="0" />
						<input type="hidden" name="fields[state]" id="field-state" value="1" />
						<input type="hidden" name="fields[scope]" id="field-scope" value="<?php echo $this->post->get('scope'); ?>" />
						<input type="hidden" name="fields[scope_id]" id="field-scope_id" value="<?php echo $this->post->get('scope_id'); ?>" />
						<input type="hidden" name="fields[id]" id="field-id" value="" />
						<input type="hidden" name="fields[object_id]" id="field-object_id" value="<?php echo $this->post->get('object_id'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
						<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
						<input type="hidden" name="active" value="discussions" />
						<input type="hidden" name="action" value="savethread" />
						<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />
						<input type="hidden" name="return" value="<?php echo base64_encode(JRoute::_($base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->lecture->get('alias'))); ?>" />
						<p class="instructions">
							Click on a comment on the left to view a discussion or start your own above.
						</p>
					</form>
<?php } ?>
					<div class="comment-thread"><?php if ($this->data) { echo $this->data->html; } ?></div>
					<!-- 
					<input type="hidden" name="lastchange" id="lastchange" value="" />
					<input type="hidden" name="lastid" id="lastid" value="" />
					<input type="hidden" name="parent-thread" id="parent-thread" value="" />
					-->

				</div><!-- / .comments-frame -->
			</div><!-- / .comments-panel -->
		</div><!-- / .comments-views -->

 	</div><!-- / .comments-wrap -->
</div><!-- / #comments-container -->