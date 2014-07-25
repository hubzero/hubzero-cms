<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js();

$juser = JFactory::getUser();

$this->category->set('section_alias', $this->filters['section']);

$this->thread->set('section', $this->filters['section']);
$this->thread->set('category', $this->category->get('alias'));
?>
<header id="content-header">
	<h2><?php echo JText::_('COM_FORUM'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-comments comments btn" href="<?php echo JRoute::_($this->category->link()); ?>">
				<?php echo JText::_('COM_FORUM_ALL_DISCUSSIONS'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<?php
				foreach ($this->notifications as $notification)
				{
					echo '<p class="' . $notification['type'] . '">' . $notification['message'] . '</p>';
				}
			?>

			<h3 class="thread-title<?php echo ($this->thread->get('closed')) ? ' closed' : ''; ?>">
				<?php echo $this->escape(stripslashes($this->thread->get('title'))); ?>
			</h3>

			<?php
			if ($this->thread->posts($this->config->get('threading', 'list'), $this->filters)->total() > 0)
			{
				$this->view('_list')
				     ->set('option', $this->option)
				     ->set('controller', $this->controller)
				     ->set('comments', $this->thread->posts($this->config->get('threading', 'list')))
				     ->set('thread', $this->thread)
				     ->set('parent', 0)
				     ->set('config', $this->config)
				     ->set('depth', 0)
				     ->set('cls', 'odd')
				     ->set('filters', $this->filters)
				     ->set('category', $this->category)
				     ->display();
			}
			else
			{
				?>
				<ol class="comments">
					<li>
						<p><?php echo JText::_('COM_FORUM_NO_REPLIES_FOUND'); ?></p>
					</li>
				</ol>
				<?php
			}
			?>
			<form action="<?php echo JRoute::_($this->thread->link()); ?>" method="get">
				<?php
				jimport('joomla.html.pagination');
				$pageNav = new JPagination(
					$this->thread->posts('count', $this->filters),
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('section', $this->filters['section']);
				$pageNav->setAdditionalUrlParam('category', $this->category->get('alias'));
				$pageNav->setAdditionalUrlParam('thread', $this->thread->get('id'));

				echo $pageNav->getListFooter();
				?>
			</form>

			<?php if (!$this->thread->get('closed')) { ?>
				<h3 class="post-comment-title">
					<?php echo JText::_('COM_FORUM_ADD_COMMENT'); ?>
				</h3>
				<form action="<?php echo JRoute::_($this->thread->link()); ?>" method="post" id="commentform" enctype="multipart/form-data">
					<p class="comment-member-photo">
						<?php
						$anon = (!$juser->get('guest') ? 0 : 1);
						$now  = JFactory::getDate();
						?>
						<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($juser, $anon); ?>" alt="<?php echo JText::_('COM_FORUM_USER_PHOTO'); ?>" />
					</p>

					<fieldset>
						<?php if ($juser->get('guest')) { ?>
							<p class="warning"><?php echo JText::_('COM_FORUM_LOGIN_COMMENT_NOTICE'); ?></p>
						<?php } else if ($this->config->get('access-create-post')) { ?>
							<p class="comment-title">
								<strong>
									<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id')); ?>"><?php echo $this->escape(stripslashes($juser->get('name'))); ?></a>
								</strong>
								<span class="permalink">
									<span class="comment-date-at"><?php echo JText::_('COM_FORUM_AT'); ?></span>
									<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('TIME_FORMAT_HZ1')); ?></time></span>
									<span class="comment-date-on"><?php echo JText::_('COM_FORUM_ON'); ?></span>
									<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
								</span>
							</p>

							<label for="fieldcomment">
								<?php echo JText::_('COM_FORUM_FIELD_COMMENTS'); ?>
								<?php
								echo \JFactory::getEditor()->display('fields[comment]', '', '', '', 35, 15, false, 'fieldcomment', null, null, array('class' => 'minimal no-footer'));
								?>
							</label>

							<label>
								<?php echo JText::_('COM_FORUM_FIELD_YOUR_TAGS'); ?>
								<?php
									$tags = $this->thread->tags('string');

									JPluginHelper::importPlugin('hubzero');
									$dispatcher = JDispatcher::getInstance();
									$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $tags)) );
									if (count($tf) > 0) {
										echo $tf[0];
									} else {
										echo '<input type="text" name="tags" value="' . $tags . '" />';
									}
								?>
							</label>

							<fieldset>
								<legend><?php echo JText::_('COM_FORUM_LEGEND_ATTACHMENTS'); ?></legend>
								<div class="grid">
									<div class="col span6">
										<label for="upload">
											<?php echo JText::_('COM_FORUM_FIELD_FILE'); ?>:
											<input type="file" name="upload" id="upload" />
										</label>
									</div>
									<div class="col span6 omega">
										<label for="field-description">
											<?php echo JText::_('COM_FORUM_FIELD_DESCRIPTION'); ?>:
											<input type="text" name="description" id="field-description" value="" />
										</label>
									</div>
								</div>
							</fieldset>

							<label for="field-anonymous" id="comment-anonymous-label">
								<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
								<?php echo JText::_('COM_FORUM_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo JText::_('COM_FORUM_SUBMIT'); ?>" />
							</p>
						<?php } else { ?>
							<p class="warning"><?php echo JText::_('COM_FORUM_PERMISSION_DENIED'); ?></p>
						<?php } ?>

						<div class="sidenote">
							<p>
								<strong><?php echo JText::_('COM_FORUM_KEEP_POLITE'); ?></strong>
							</p>
						</div>
					</fieldset>
					<input type="hidden" name="fields[category_id]" value="<?php echo $this->thread->get('category_id'); ?>" />
					<input type="hidden" name="fields[parent]" value="<?php echo $this->thread->get('id'); ?>" />
					<input type="hidden" name="fields[state]" value="1" />
					<input type="hidden" name="fields[id]" value="" />
					<input type="hidden" name="fields[scope]" value="site" />
					<input type="hidden" name="fields[scope_id]" value="0" />
					<input type="hidden" name="fields[thread]" value="<?php echo $this->thread->get('id'); ?>" />
					<input type="hidden" name="fields[scope_sub_id]" value="<?php echo $this->thread->get('scope_sub_id'); ?>" />
					<input type="hidden" name="fields[object_id]" value="<?php echo $this->thread->get('object_id'); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="threads" />
					<input type="hidden" name="task" value="save" />

					<?php echo JHTML::_('form.token'); ?>
				</form>
			<?php } ?>
		</div><!-- / .subject -->

		<aside class="aside">
			<div class="container">
				<h3><?php echo JText::_('COM_FORUM_ALL_TAGS'); ?></h3>
				<?php if ($this->thread->tags('cloud')) { ?>
					<?php echo $this->thread->tags('cloud'); ?>
				<?php } else { ?>
					<p><?php echo JText::_('COM_FORUM_NONE'); ?></p>
				<?php } ?>
			</div><!-- / .container -->

			<?php if ($this->thread->participants()->total() > 0) { ?>
				<div class="container">
					<h3><?php echo JText::_('COM_FORUM_PARTICIPANTS'); ?></h3>
					<ul>
					<?php
						$anon = false;
						foreach ($this->thread->participants() as $participant)
						{
							if (!$participant->anonymous) {
					?>
						<li>
							<a class="member" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $participant->created_by); ?>">
								<?php echo $this->escape(stripslashes($participant->name)); ?>
							</a>
						</li>
					<?php
							} else if (!$anon) {
								$anon = true;
					?>
						<li>
							<span class="member">
								<?php echo JText::_('COM_FORUM_ANONYMOUS'); ?>
							</span>
						</li>
					<?php
							}
						}
					?>
					</ul>
				</div><!-- / .container -->
			<?php } ?>

			<?php if ($this->thread->attachments()->total() > 0) { ?>
				<div class="container">
					<h3><?php echo JText::_('COM_FORUM_ATTACHMENTS'); ?></h3>
					<ul class="attachments">
						<?php
						foreach ($this->thread->attachments() as $attachment)
						{
							$cls = 'file';
							$title = trim($attachment->get('description', $attachment->get('filename')));
							$title = ($title ? $title : $attachment->get('filename'));
							if (preg_match("/bmp|gif|jpg|jpe|jpeg|png/i", $attachment->get('filename')))
							{
								$cls = 'img';
							}
							?>
							<li>
								<a class="<?php echo $cls; ?> attachment" href="<?php echo JRoute::_($this->thread->link() . '&post=' . $attachment->get('post_id') . '&file=' . $attachment->get('filename')); ?>">
									<?php echo $this->escape(stripslashes($title)); ?>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
				</div><!-- / .container -->
			<?php } ?>
		</aside><!-- / .aside -->
	</div>
</section><!-- / .below section -->