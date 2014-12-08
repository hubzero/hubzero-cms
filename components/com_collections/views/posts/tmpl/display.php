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

$item = $this->post->item();

$juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
$no_html = JRequest::getInt('no_html', 0);

if (!$no_html) {
	$this->css();
?>
<header id="content-header">
	<h2><?php echo JText::_('COM_COLLECTIONS'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-info btn popup" href="<?php echo JRoute::_('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">
				<span><?php echo JText::_('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
			</a>
		</p>
	</div>
</header>
<?php } ?>

<section class="section">
	<div class="grid">
		<div class="col span8">

			<div class="post full <?php echo $item->type(); ?>" id="p<?php echo $this->post->get('id'); ?>" data-id="<?php echo $this->post->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&post=' . $this->post->get('id') . '&task=comment'); ?>" data-width="600" data-height="350">
				<div class="content">
					<div class="creator attribution cf">
						<?php if ($item->get('type') == 'file' || $item->get('type') == 'collection') { ?>
							<?php
							$name = $this->escape(stripslashes($item->creator('name')));

							if ($item->creator('public')) { ?>
								<a href="<?php echo JRoute::_($item->creator()->getLink()); ?>" title="<?php echo $name; ?>" class="img-link">
									<img src="<?php echo $item->creator()->getPicture(); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</a>
							<?php } else { ?>
								<span class="img-link">
									<img src="<?php echo $item->creator()->getPicture(); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</span>
							<?php } ?>
							<p>
								<?php echo JText::sprintf('COM_COLLECTIONS_USER_CREATED_POST', ($item->creator('public') ? '<a href="' . JRoute::_($item->creator()->getLink()) . '">' : '') . $this->escape(stripslashes($item->creator()->get('name'))) . ($item->creator('public') ? '</a>' : '')); ?>
								<br />
								<span class="entry-date">
									<span class="entry-date-at"><?php echo JText::_('COM_COLLECTIONS_AT'); ?></span>
									<span class="time"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('time'); ?></time></span>
									<span class="entry-date-on"><?php echo JText::_('COM_COLLECTIONS_ON'); ?></span>
									<span class="date"><time datetime="<?php echo $item->created(); ?>"><?php echo $item->created('date'); ?></time></span>
								</span>
							</p>
						<?php } else { ?>
							<p class="typeof <?php echo $item->get('type'); ?>">
								<?php echo $this->escape($item->type('title')); ?>
							</p>
						<?php } ?>
					</div><!-- / .attribution -->
					<?php
					$this->view('display_' . $item->type(), 'posts')
					     ->set('actual', true)
					     ->set('option', $this->option)
					     ->set('params', $this->config)
					     ->set('row', $this->post)
					     ->display();
					?>
					<?php if (count($item->tags()) > 0) { ?>
						<div class="tags-wrap">
							<p><?php echo $item->tags('render'); ?></p>
						</div><!-- / .tags-wrap -->
					<?php } ?>
					<div class="meta" data-metadata-url="<?php echo JRoute::_($base . '&task=metadata&post=' . $this->post->get('id')); ?>">
						<p class="stats">
							<span class="likes">
								<?php echo JText::sprintf('COM_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
							</span>
							<span class="comments">
								<?php echo JText::sprintf('COM_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)); ?>
							</span>
							<span class="reposts">
								<?php echo JText::sprintf('COM_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)); ?>
							</span>
						</p>
					</div><!-- / .meta -->
					<div class="convo attribution">
						<?php
						$name = $this->escape(stripslashes($this->post->creator('name')));

						if ($this->post->creator('public')) { ?>
							<a href="<?php echo JRoute::_($this->post->creator()->getLink()); ?>" title="<?php echo $name; ?>" class="img-link">
								<img src="<?php echo $this->post->creator()->getPicture(); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</a>
						<?php } else { ?>
							<span class="img-link">
								<img src="<?php echo $this->post->creator()->getPicture(); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</span>
						<?php } ?>
						<p>
							<?php if ($this->post->creator('public')) { ?>
								<a href="<?php echo JRoute::_($this->post->creator()->getLink() . '&active=collections'); ?>">
									<?php echo $name; ?>
								</a>
							<?php } else { ?>
								<?php echo $name; ?>
							<?php } ?>
							<?php echo JText::_('COM_COLLECTIONS_ONTO'); ?>
							<a href="<?php echo JRoute::_($base . '&task=' . $this->collection->get('alias')); ?>">
								<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>
							</a>
							<br />
							<span class="entry-date">
								<span class="entry-date-at"><?php echo JText::_('COM_COLLECTIONS_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $this->post->created(); ?>"><?php echo $this->post->created('time'); ?></time></span>
								<span class="entry-date-on"><?php echo JText::_('COM_COLLECTIONS_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $this->post->created(); ?>"><?php echo $this->post->created('date'); ?></time></span>
							</span>
						</p>
					</div><!-- / .attribution -->
				</div><!-- / .content -->
			</div><!-- / .post -->

			<div class="post-comments">
				<?php if ($item->get('comments')) { ?>
					<ol class="comments">
					<?php
					foreach ($item->comments() as $comment)
					{
						$cuser = \Hubzero\User\Profile::getInstance($comment->created_by);
						$cname = $this->escape(stripslashes($cuser->get('name')));
					?>
						<li class="comment" id="c<?php echo $comment->id; ?>">
							<p class="comment-member-photo">
								<img src="<?php echo $cuser->getPicture($comment->anonymous); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $cname); ?>" />
							</p>
							<div class="comment-content">
								<p class="comment-title">
									<strong>
										<?php if ($cuser->get('public')) { ?>
											<a href="<?php echo JRoute::_($cuser->getLink()); ?>">
												<?php echo $cname; ?>
											</a>
										<?php } else { ?>
											<?php echo $cname; ?>
										<?php } ?>
									</strong>
									<a class="permalink" href="#c">
										<span class="entry-date">
											<span class="entry-date-at"><?php echo JText::_('COM_COLLECTIONS_AT'); ?></span>
											<span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, JText::_('TIME_FORMAT_HZ1')); ?></time></span>
											<span class="entry-date-on"><?php echo JText::_('COM_COLLECTIONS_ON'); ?></span>
											<span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
										</span>
									</a>
								</p>
								<div class="comment-body">
									<p><?php echo stripslashes($comment->content); ?></p>
								</div>
							</div>
						</li>
					<?php } ?>
					</ol>
				<?php } ?>
				<?php if (!$juser->get('guest')) { ?>
					<form action="<?php echo JRoute::_($base . '&post=' . $this->post->get('id') . '&task=savecomment' . ($this->no_html ? '&no_html=' . $this->no_html  : '')); ?>" method="post" id="commentform" enctype="multipart/form-data">
						<p class="comment-member-photo">
							<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($this->juser, 0); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $this->escape(stripslashes($this->juser->get('name')))); ?>" />
						</p>

						<fieldset>
							<p class="comment-title">
								<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id')); ?>">
									<?php echo $this->escape(stripslashes($this->juser->get('name'))); ?>
								</a>
								<span class="permalink">
									<?php
									$now = JFactory::getDate()->toSql();
									?>
									<span class="entry-date-at"><?php echo JText::_('COM_COLLECTIONS_AT'); ?></span>
									<span class="time"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('TIME_FORMAT_HZ1')); ?></time></span>
									<span class="entry-date-on"><?php echo JText::_('COM_COLLECTIONS_ON'); ?></span>
									<span class="date"><time datetime="<?php echo $now; ?>"><?php echo JHTML::_('date', $now, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
								</span>
							</p>

							<label for="comment-content">
								<span class="label-text"><?php echo JText::_('COM_COLLECTIONS_FIELD_COMMENTS'); ?></span>
								<?php echo $this->editor('comment[content]', '', 35, 5, 'comment-content', array('class' => 'minimal no-footer')); ?>
							</label>

							<input type="hidden" name="comment[id]" value="0" />
							<input type="hidden" name="comment[item_id]" value="<?php echo $item->get('id'); ?>" />
							<input type="hidden" name="comment[item_type]" value="collection" />
							<input type="hidden" name="comment[state]" value="1" />

							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="post" value="<?php echo $this->post->get('id'); ?>" />
							<input type="hidden" name="task" value="savecomment" />
							<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

							<?php echo JHTML::_('form.token'); ?>

							<label for="comment-anonymous" id="comment-anonymous-label">
								<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
								<?php echo JText::_('COM_COLLECTIONS_FIELD_ANONYMOUS'); ?>
							</label>

							<p class="submit">
								<input type="submit" value="<?php echo JText::_('COM_COLLECTIONS_SAVE'); ?>" />
							</p>
						</fieldset>
					</form>
				<?php } ?>
			</div>

		</div>
		<div class="col span4 omega">
			<div class="post full collection" id="b<?php echo $this->collection->get('id'); ?>" data-id="<?php echo $this->collection->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=posts&collection=' . $this->collection->get('id')); ?>">
				<div class="content">
					<?php
						$this->view('display_collection', 'posts')
						     ->set('option', $this->option)
						     ->set('params', $this->config)
						     ->set('row', $this->collection)
						     ->display();
					?>
					<div class="meta">
						<p class="stats">
							<span class="likes">
								<?php echo JText::sprintf('COM_COLLECTIONS_NUM_LIKES', $this->collection->get('positive', 0)); ?>
							</span>
							<?php /*<span class="reposts">
								<?php echo JText::sprintf('COM_COLLECTIONS_NUM_REPOSTS', $this->collection->get('reposts', 0)); ?>
							</span> */ ?>
							<span class="posts">
								<?php echo JText::sprintf('COM_COLLECTIONS_NUM_POSTS', $this->collection->count('post')); ?>
							</span>
						</p>
						<div class="actions">
							<?php if (!$this->juser->get('guest')) { ?>
								<?php if ($this->collection->get('object_type') == 'member' && $this->collection->get('object_id') == $this->juser->get('id')) { ?>
										<a class="edit" data-id="<?php echo $this->collection->get('id'); ?>" href="<?php echo JRoute::_($this->collection->link() . '/edit'); ?>">
											<span><?php echo JText::_('COM_COLLECTIONS_EDIT'); ?></span>
										</a>
										<a class="delete" data-id="<?php echo $this->collection->get('id'); ?>" href="<?php echo JRoute::_($this->collection->link() . '/delete'); ?>">
											<span><?php echo JText::_('COM_COLLECTIONS_DELETE'); ?></span>
										</a>
								<?php } else { ?>
										<a class="repost" data-id="<?php echo $this->collection->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&board=' . $this->collection->get('id') . '&task=collect'); ?>">
											<span><?php echo JText::_('COM_COLLECTIONS_COLLECT'); ?></span>
										</a>
									<?php if ($this->collection->isFollowing()) { ?>
										<a class="unfollow" data-id="<?php echo $this->collection->get('id'); ?>" data-text-follow="<?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($this->collection->link() . '/unfollow'); ?>">
											<span><?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?></span>
										</a>
									<?php } else { ?>
										<a class="follow" data-id="<?php echo $this->collection->get('id'); ?>" data-text-follow="<?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($this->collection->link() . '/follow'); ?>">
											<span><?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?></span>
										</a>
									<?php } ?>
								<?php } ?>
							<?php } else { ?>
								<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&controller=posts&board=' . $this->collection->get('id') . '&task=collect', false, true)), false); ?>" title="<?php echo JText::_('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
									<span><?php echo JText::_('COM_COLLECTIONS_COLLECT'); ?></span>
								</a>
								<a class="follow tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->collection->link() . '/follow')), false); ?>" title="<?php echo JText::_('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
									<span><?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?></span>
								</a>
							<?php } ?>
						</div>
					</div>
					<div class="convo attribution">
						<?php
						$name = $this->escape(stripslashes($this->collection->creator('name')));

						if ($this->collection->creator('public')) { ?>
							<a href="<?php echo JRoute::_($this->collection->creator()->getLink()); ?>" title="<?php echo $name; ?>" class="img-link">
								<img src="<?php echo $this->collection->creator()->getPicture(); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</a>
						<?php } else { ?>
							<span class="img-link">
								<img src="<?php echo $this->collection->creator()->getPicture(); ?>" alt="<?php echo JText::_('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
							</span>
						<?php } ?>
						<p>
							<?php if ($this->collection->creator('public')) { ?>
								<a href="<?php echo JRoute::_($this->collection->creator()->getLink()); ?>">
									<?php echo $name; ?>
								</a>
							<?php } else { ?>
								<?php echo $name; ?>
							<?php } ?>
							<br />
							<span class="entry-date">
								<span class="entry-date-at"><?php echo JText::_('COM_COLLECTIONS_AT'); ?></span>
								<span class="time"><time datetime="<?php echo $this->collection->created(); ?>"><?php echo $this->collection->created('time'); ?></time></span>
								<span class="entry-date-on"><?php echo JText::_('COM_COLLECTIONS_ON'); ?></span>
								<span class="date"><time datetime="<?php echo $this->collection->created(); ?>"><?php echo $this->collection->created('date'); ?></time></span>
							</span>
						</p>
					</div><!-- / .attribution -->
				</div><!-- / .content -->
			</div><!-- / .post -->
		</div>
	</div>
</section>

<?php if ($item->collections('list', array('collection_id' => $this->collection->get('id')))->total()) { ?>
	<section class="section post-collections">
		<h3><?php echo JText::_('COM_COLLECTIONS_ALSO_IN_THESE_COLLECTIONS'); ?></h3>
		<div id="posts">
			<?php foreach ($item->collections() as $collection) { ?>
				<div class="post collection" id="b<?php echo $collection->get('id'); ?>" data-id="<?php echo $collection->get('id'); ?>" data-closeup-url="<?php echo JRoute::_($base . '&controller=collection&id=' . $collection->get('id')); ?>" data-width="600" data-height="350">
					<div class="content">
						<?php
						$this->view('display_collection', 'posts')
						     ->set('option', $this->option)
						     ->set('params', $this->config)
						     ->set('row', $collection)
						     ->display();
						?>
						<div class="meta">
							<p class="stats">
								<span class="likes">
									<?php echo JText::sprintf('COM_COLLECTIONS_NUM_LIKES', $collection->get('positive', 0)); ?>
								</span>
								<?php /*<span class="reposts">
									<?php echo JText::sprintf('COM_COLLECTIONS_NUM_REPOSTS', $collection->count('reposts')); ?>
								</span>*/ ?>
								<span class="posts">
									<?php echo JText::sprintf('COM_COLLECTIONS_NUM_POSTS', $collection->count('posts')); ?>
								</span>
							</p>
							<div class="actions">
								<?php if (!$this->juser->get('guest')) { ?>
									<?php if ($collection->get('object_type') == 'member' && $collection->get('object_id') == $this->juser->get('id')) { ?>
											<a class="edit" data-id="<?php echo $collection->get('id'); ?>" href="<?php echo JRoute::_($collection->link() . '/edit'); ?>">
												<span><?php echo JText::_('COM_COLLECTIONS_EDIT'); ?></span>
											</a>
											<a class="delete" data-id="<?php echo $collection->get('id'); ?>" href="<?php echo JRoute::_($collection->link() . '/delete'); ?>">
												<span><?php echo JText::_('COM_COLLECTIONS_DELETE'); ?></span>
											</a>
									<?php } else { ?>
											<a class="repost" data-id="<?php echo $collection->get('id'); ?>" href="<?php echo JRoute::_($base . '&controller=posts&board=' . $collection->get('id') . '&task=collect'); ?>">
												<span><?php echo JText::_('COM_COLLECTIONS_COLLECT'); ?></span>
											</a>
										<?php if ($collection->isFollowing()) { ?>
											<a class="unfollow" data-id="<?php echo $collection->get('id'); ?>" data-text-follow="<?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($collection->link() . '/unfollow'); ?>">
												<span><?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?></span>
											</a>
										<?php } else { ?>
											<a class="follow" data-id="<?php echo $collection->get('id'); ?>" data-text-follow="<?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?>" data-text-unfollow="<?php echo JText::_('COM_COLLECTIONS_UNFOLLOW'); ?>" href="<?php echo JRoute::_($collection->link() . '/follow'); ?>">
												<span><?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?></span>
											</a>
										<?php } ?>
									<?php } ?>
								<?php } else { ?>
									<a class="repost tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($base . '&controller=posts&board=' . $collection->get('id') . '&task=collect', false, true)), false); ?>" title="<?php echo JText::_('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'); ?>">
										<span><?php echo JText::_('COM_COLLECTIONS_COLLECT'); ?></span>
									</a>
									<a class="follow tooltips" href="<?php echo JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($collection->link() . '/follow')), false); ?>" title="<?php echo JText::_('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'); ?>">
										<span><?php echo JText::_('COM_COLLECTIONS_FOLLOW'); ?></span>
									</a>
								<?php } ?>
							</div><!-- / .actions -->
						</div><!-- / .meta -->
						<div class="convo attribution">
							<?php
							$name = $this->escape(stripslashes($collection->creator('name')));

							if ($collection->creator('public')) { ?>
								<a href="<?php echo JRoute::_($collection->creator()->getLink() . '&active=collections'); ?>" title="<?php echo $name; ?>" class="img-link">
									<img src="<?php echo $collection->creator()->getPicture(); ?>" alt="<?php echo JText::sprintf('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</a>
							<?php } else { ?>
								<span class="img-link">
									<img src="<?php echo $collection->creator()->getPicture(); ?>" alt="<?php echo JText::sprintf('COM_COLLECTIONS_PROFILE_PICTURE', $name); ?>" />
								</span>
							<?php } ?>
							<p>
								<?php if ($collection->creator('public')) { ?>
									<a href="<?php echo JRoute::_($collection->creator()->getLink() . '&active=collections'); ?>">
										<?php echo $name; ?>
									</a>
								<?php } else { ?>
									<?php echo $name; ?>
								<?php } ?>
								<br />
								<span class="entry-date">
									<span class="entry-date-at"><?php echo JText::_('COM_COLLECTIONS_AT'); ?></span>
									<span class="time"><?php echo JHTML::_('date', $collection->get('created'), JText::_('TIME_FORMAT_HZ1')); ?></span>
									<span class="entry-date-on"><?php echo JText::_('COM_COLLECTIONS_ON'); ?></span>
									<span class="date"><?php echo JHTML::_('date', $collection->get('created'), JText::_('DATE_FORMAT_HZ1')); ?></span>
								</span>
							</p>
						</div><!-- / .attribution -->
					</div>
				</div>
			<?php } ?>
		</div>
	</section>
<?php } ?>