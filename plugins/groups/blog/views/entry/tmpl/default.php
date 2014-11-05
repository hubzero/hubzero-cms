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

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=blog';

$this->css()
     ->js();
?>
<?php if ($this->canpost || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
	<ul id="page_options">
	<?php if ($this->canpost) { ?>
		<li>
			<a class="icon-add add btn" href="<?php echo JRoute::_($base . '&action=new'); ?>">
				<?php echo JText::_('PLG_GROUPS_BLOG_NEW_ENTRY'); ?>
			</a>
		</li>
	<?php } ?>
	<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
		<li>
			<a class="icon-config config btn" href="<?php echo JRoute::_($base . '&action=settings'); ?>">
				<?php echo JText::_('PLG_GROUPS_BLOG_SETTINGS'); ?>
			</a>
		</li>
	<?php } ?>
	</ul>
<?php } ?>

<section class="main section entry-container">
	<div class="subject">
		<?php
			$cls = '';

			if (!$this->row->isAvailable())
			{
				$cls = ' pending';
			}
			if ($this->row->ended())
			{
				$cls = ' expired';
			}
			if ($this->row->get('state') == 0)
			{
				$cls = ' private';
			}
		?>
		<div class="entry<?php echo $cls; ?>" id="e<?php echo $this->row->get('id'); ?>">
			<h2 class="entry-title">
				<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>
			</h2>

			<dl class="entry-meta">
				<dt>
					<span>
						<?php echo JText::sprintf('PLG_GROUPS_BLOG_ENTRY_NUMBER', $this->row->get('id')); ?>
					</span>
				</dt>
				<dd class="date">
					<time datetime="<?php echo $this->row->published(); ?>">
						<?php echo $this->row->published('date'); ?>
					</time>
				</dd>
				<dd class="time">
					<time datetime="<?php echo $this->row->published(); ?>">
						<?php echo $this->row->published('time'); ?>
					</time>
				</dd>
			<?php if ($this->row->get('allow_comments')) { ?>
				<dd class="comments">
					<a href="<?php echo JRoute::_($this->row->link('comments')); ?>">
						<?php echo JText::sprintf('PLG_GROUPS_BLOG_NUM_COMMENTS', $this->row->comments('count')); ?>
					</a>
				</dd>
			<?php } else { ?>
				<dd class="comments">
					<span>
						<?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_OFF'); ?>
					</span>
				</dd>
			<?php } ?>
			<?php if ($this->juser->get('id') == $this->row->get('created_by') || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
				<dd class="state">
					<?php echo JText::_('PLG_GROUPS_BLOG_STATE_' . strtoupper($this->row->state('text'))); ?>
				</dd>
				<dd class="entry-options">
					<a class="icon-edit edit" href="<?php echo JRoute::_($this->row->link('edit')); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_EDIT'); ?>">
						<span><?php echo JText::_('PLG_GROUPS_BLOG_EDIT'); ?></span>
					</a>
					<a class="icon-delete delete" data-confirm="<?php echo JText::_('PLG_GROUPS_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo JRoute::_($this->row->link('delete')); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_DELETE'); ?>">
						<span><?php echo JText::_('PLG_GROUPS_BLOG_DELETE'); ?></span>
					</a>
				</dd>
			<?php } ?>
			</dl>

			<div class="entry-content">
				<?php echo $this->row->content('parsed'); ?>
				<?php echo $this->row->tags('cloud'); ?>
			</div>

			<?php
			if ($name = $this->row->creator()->get('name'))
			{
				$name = $this->escape(stripslashes($name));
			?>
			<div class="entry-author">
				<h3><?php echo JText::_('PLG_GROUPS_BLOG_ABOUT_AUTHOR'); ?></h3>
				<p class="entry-author-photo">
					<img src="<?php echo $this->row->creator('picture'); ?>" alt="" />
				</p>
				<div class="entry-author-content">
					<h4>
						<?php if ($this->row->creator()->get('public')) { ?>
							<a href="<?php echo JRoute::_($this->row->creator()->getLink()); ?>">
								<?php echo $name; ?>
							</a>
						<?php } else { ?>
							<?php echo $name; ?>
						<?php } ?>
					</h4>
					<p class="entry-author-bio">
						<?php if ($this->row->creator('bio')) { ?>
							<?php echo $this->row->creator()->getBio('parsed', 300); ?>
						<?php } else { ?>
							<em><?php echo JText::_('PLG_GROUPS_BLOG_AUTHOR_BIO_BLANK'); ?></em>
						<?php } ?>
					</p>
				</div>
			</div>
			<?php
			}
			?>
		</div>
	</div><!-- /.subject -->
	<aside class="aside">
	<?php
	$limit = $this->filters['limit'];
	$this->filters['limit'] = 5;
	?>
		<div class="container blog-popular-entries">
			<h4><?php echo JText::_('PLG_GROUPS_BLOG_POPULAR_ENTRIES'); ?></h4>
		<?php if ($popular = $this->model->entries('popular', $this->filters)) { ?>
			<ol>
			<?php foreach ($popular as $row) { ?>
				<?php
					if (!$row->isAvailable() && $row->get('created_by') != JFactory::getUser()->get('id'))
					{
						continue;
					}
				?>
				<li>
					<a href="<?php echo JRoute::_($row->link()); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				</li>
			<?php } ?>
			</ol>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-popular-entries -->

		<div class="container blog-recent-entries">
			<h4><?php echo JText::_('PLG_GROUPS_BLOG_RECENT_ENTRIES'); ?></h4>
		<?php if ($recent = $this->model->entries('recent', $this->filters)) { ?>
			<ol>
			<?php foreach ($recent as $row) { ?>
				<?php
					if (!$row->isAvailable() && $row->get('created_by') != JFactory::getUser()->get('id'))
					{
						continue;
					}
				?>
				<li>
					<a href="<?php echo JRoute::_($row->link()); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				</li>
			<?php } ?>
			</ol>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-recent-entries -->
	<?php
	$this->filters['limit'] = $limit;
	?>
	</aside><!-- /.aside -->
</section>

<?php if ($this->row->get('allow_comments')) { ?>
	<section class="section below">
		<div class="subject">
			<h3 class="below_heading">
				<?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_HEADER'); ?>
			</h3>
			<?php if ($this->row->comments('count') > 0) { ?>
				<?php
					$this->view('_list', 'comments')
					     ->set('group', $this->group)
					     ->set('parent', 0)
					     ->set('cls', 'odd')
					     ->set('depth', 0)
					     ->set('option', $this->option)
					     ->set('comments', $this->row->comments('list'))
					     ->set('config', $this->config)
					     ->set('base', $this->row->link())
					     ->display();
				?>
			<?php } else { ?>
				<p class="no-comments">
					<?php echo JText::_('PLG_GROUPS_BLOG_NO_COMMENTS'); ?>
				</p>
			<?php } ?>

			<h3 class="below_heading">
				<?php echo JText::_('PLG_GROUPS_BLOG_POST_COMMENT'); ?>
			</h3>

			<form method="post" action="<?php echo JRoute::_($this->row->link()); ?>" id="commentform">
				<p class="comment-member-photo">
					<?php
						$jxuser = \Hubzero\User\Profile::getInstance($juser->get('id'));
						$anon = 1;
						if (!$juser->get('guest'))
						{
							$anon = 0;
						}
					?>
					<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($jxuser, $anon); ?>" alt="" />
				</p>
				<fieldset>
					<?php
						$replyto = $this->row->comment(JRequest::getInt('reply', 0));
						if ($replyto->exists())
						{
							$name = JText::_('PLG_GROUPS_BLOG_ANONYMOUS');
							if (!$replyto->get('anonymous'))
							{
								$name = $this->escape(stripslashes($replyto->creator('name', $name)));
								if ($replyto->creator('public'))
								{
									$name = '<a href="' . JRoute::_($replyto->creator()->getLink()) . '">' . $name . '</a>';
								}
							}
					?>
					<blockquote cite="c<?php echo $replyto->get('id'); ?>">
						<p>
							<strong><?php echo $name; ?></strong>
							<span class="comment-date-at"><?php echo JText::_('PLG_GROUPS_BLOG_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span>
							<span class="comment-date-on"><?php echo JText::_('PLG_GROUPS_BLOG_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
						</p>
						<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($replyto->get('content')), 300); ?></p>
					</blockquote>
					<?php } ?>

					<?php if (!$this->juser->get('guest')) { ?>
						<label for="comment_content">
							Your <?php echo ($replyto->exists()) ? 'reply' : 'comments'; ?>: <span class="required"><?php echo JText::_('PLG_GROUPS_BLOG_REQUIRED'); ?></span>
							<?php echo $this->editor('comment[content]', '', 40, 15, 'comment_content', array('class' => 'minimal no-footer')); ?>
						</label>

						<label id="comment-anonymous-label">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo JText::_('PLG_GROUPS_BLOG_POST_ANONYMOUS'); ?>
						</label>

						<p class="submit">
							<input type="submit" name="submit" value="<?php echo JText::_('PLG_GROUPS_BLOG_SUBMIT'); ?>" />
						</p>
					<?php } else { ?>
						<p class="warning">
							<?php echo JText::sprintf('PLG_GROUPS_BLOG_MUST_LOG_IN', '<a href="'. JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->row->link() . '#post-comment', false, true))) . '">' . JText::_('PLG_GROUPS_BLOG_LOG_IN') . '</a>'); ?>
						</p>
					<?php } ?>

					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->juser->get('id'); ?>" />
					<input type="hidden" name="comment[state]" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="active" value="blog" />
					<input type="hidden" name="action" value="savecomment" />

					<div class="sidenote">
						<p>
							<strong><?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
						</p>
						<p>
							<?php echo JText::_('PLG_GROUPS_BLOG_COMMENT_HELP'); ?>
						</p>
					</div>
				</fieldset>
			</form>
		</div><!-- / .subject -->
		<aside class="aside">
			<p>
				<a class="icon-add btn" href="#post-comment">
					<?php echo JText::_('PLG_GROUPS_BLOG_ADD_A_COMMENT'); ?>
				</a>
			</p>
		</aside><!-- / .aside -->
	</section>
<?php } //end if allow comments ?>
