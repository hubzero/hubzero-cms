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

defined('_JEXEC') or die('Restricted access');

$base = $this->offering->link() . '&active=forum';
?>
<div class="filters">
	<div class="filters-inner">
		<p>
			<a class="comments btn" href="<?php echo JRoute::_($base . '&unit=' . $this->category->alias); ?>">
				<?php echo JText::_('All discussions'); ?>
			</a>
		</p>
		<h3 class="thread-title">
			<?php echo $this->escape(stripslashes($this->post->title)); ?>
		</h3>
	</div>
</div>

<section class="main section">
	<div class="subject">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<form action="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $this->post->id); ?>" method="get">
			<?php
			if ($this->rows)
			{
				$last = '0000-00-00 00:00:00';
				foreach ($this->rows as $row)
				{
					if ($row->created > $last)
					{
						$last = $row->created;
					}
				}
				echo '<input type="hidden" name="lastchange" id="lastchange" value="' . $last . '" />';
				$this->view('list')
				     ->set('option', $this->option)
				     ->set('comments', $this->rows)
				     ->set('post', $this->post)
				     ->set('unit', $this->category->alias)
				     ->set('lecture', $this->post->id)
				     ->set('config', $this->config)
				     ->set('depth', 0)
				     ->set('cls', 'odd')
				     ->set('base', $base)
				     ->set('attach', $this->attach)
				     ->set('course', $this->course)
				     ->display();
			}
			else
			{
				?>
				<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_NO_REPLIES_FOUND'); ?></p>
				<?php
			}
			$this->pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
			$this->pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
			$this->pageNav->setAdditionalUrlParam('active', 'forum');
			$this->pageNav->setAdditionalUrlParam('unit', $this->category->alias);
			$this->pageNav->setAdditionalUrlParam('b', $this->post->id);

			echo $this->pageNav->getListFooter();
			?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ALL_TAGS'); ?></h4>
			<?php if ($this->tags) { ?>
				<?php echo $this->tags; ?>
			<?php } else { ?>
				<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_NONE'); ?></p>
			<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_DISCUSSIONS_PARTICIPANTS'); ?></h4>
			<?php if ($this->participants) { ?>
				<ul>
				<?php
					$anon = false;
					foreach ($this->participants as $participant)
					{
						if (!$participant->anonymous) {
						?>
						<li><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $participant->created_by); ?>"><?php echo $this->escape(stripslashes($participant->name)); ?></a></li>
						<?php
						} else if (!$anon) {
							$anon = true;
						?>
						<li><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ANONYMOUS'); ?></li>
						<?php
						}
					}
				?>
				</ul>
			<?php } ?>
		</div><!-- / .container -->
		<div class="container">
			<h4><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ATTACHMENTS'); ?></h4>
			<?php if ($this->attachments) { ?>
				<ul class="attachments">
				<?php
				foreach ($this->attachments as $attachment)
				{
					$title = ($attachment->description) ? $attachment->description : $attachment->filename;
					?>
					<li><a href="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $attachment->parent . '&c=' . $attachment->post_id . '/' . $attachment->filename); ?>"><?php echo $this->escape($title); ?></a></li>
				<?php } ?>
				</ul>
			<?php } else { ?>
				<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_NONE'); ?></p>
			<?php } ?>
		</div><!-- / .container -->
	</aside><!-- / .aside  -->
</section><!-- / .main section -->

<section class="below section">
	<div class="subject">
		<h3 class="post-comment-title">
			<?php echo JText::_('PLG_COURSES_DISCUSSIONS_ADD_COMMENT'); ?>
		</h3>
		<form action="<?php echo JRoute::_($base . '&unit=' . $this->category->alias . '&b=' . $this->post->id); ?>" method="post" id="commentform" enctype="multipart/form-data">
			<p class="comment-member-photo">
				<?php
				$juser = JFactory::getUser();
				$anon = 1;
				$jxuser = \Hubzero\User\Profile::getInstance($juser->get('id'));
				if (!$juser->get('guest'))
				{
					$anon = 0;
				}
				$now = JFactory::getDate();
				?>
				<img src="<?php echo $jxuser->getPicture($anon); ?>" alt="" />
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
						<span class="comment-date-at"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_AT'); ?><</span>
						<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('TIME_FORMAt_HZ1')); ?></time></span>
						<span class="comment-date-on"><?php echo JText::_('PLG_COURSES_DISCUSSIONS_ON'); ?></span>
						<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('DATE_FORMAt_HZ1')); ?></time></span>
					</span>
				</p>

				<label for="field_comment">
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?>
					<?php
					echo \JFactory::getEditor()->display('fields[comment]', '', '', '', 35, 15, false, 'field_comment', null, null, array('class' => 'minimal no-footer'));
					?>
				</label>

				<fieldset>
					<legend><?php echo JText::_('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?></legend>
					<div class="grouping">
						<label>
							<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_FILE'); ?>:
							<input type="file" name="upload" id="upload" />
						</label>

						<label>
							<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_DESCRIPTION'); ?>:
							<input type="text" name="description" value="" />
						</label>
					</div>
				</fieldset>

				<label for="field-anonymous" id="comment-anonymous-label">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
					<?php echo JText::_('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" value="<?php echo JText::_('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
				</p>
			<?php } ?>

			</fieldset>
			<input type="hidden" name="fields[category_id]" value="<?php echo $this->post->category_id; ?>" />
			<input type="hidden" name="fields[parent]" value="<?php echo $this->post->id; ?>" />
			<input type="hidden" name="fields[state]" value="1" />
			<input type="hidden" name="fields[scope]" value="course" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->offering->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="" />

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
			<input type="hidden" name="active" value="forum" />
			<input type="hidden" name="action" value="savethread" />
			<input type="hidden" name="section" value="<?php echo $this->filters['section']; ?>" />

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><?php echo JText::_('PLG_COURSES_DISCUSSIONS_EDIT_HINT'); ?></p>
	</aside><!-- /.aside -->
</section><!-- / .below section -->