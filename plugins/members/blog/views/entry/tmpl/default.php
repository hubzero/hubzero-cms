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
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

$entry_year  = substr($this->row->get('publish_up'), 0, 4);
$entry_month = substr($this->row->get('publish_up'), 5, 2);

$base = $this->member->getLink() . '&active=blog';

$this->css()
     ->js();
?>

<ul id="page_options">
<?php if ($juser->get('id') == $this->member->get('uidNumber')) : ?>
	<li>
		<a class="icon-add add btn" href="<?php echo JRoute::_($base . '&task=new'); ?>">
			<?php echo JText::_('PLG_MEMBERS_BLOG_NEW_ENTRY'); ?>
		</a>
	</li>
<?php endif; ?>
	<li>
		<a class="icon-archive archive btn" href="<?php echo JRoute::_($base); ?>">
			<?php echo JText::_('PLG_MEMBERS_BLOG_ARCHIVE'); ?>
		</a>
	</li>
</ul>

<section class="main section entry-container">
	<div class="subject">
		<?php if ($this->getError()) : ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php endif; ?>
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
		<div class="entry<?php echo $cls; ?>">
			<h2 class="entry-title">
				<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>
			</h2>

			<dl class="entry-meta">
				<dt>
					<span>
						<?php echo JText::sprintf('PLG_MEMBERS_BLOG_ENTRY_NUMBER', $this->row->get('id')); ?>
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
						<?php echo JText::sprintf('PLG_MEMBERS_BLOG_NUM_COMMENTS', $this->row->comments('count')); ?>
					</a>
				</dd>
			<?php } else { ?>
				<dd class="comments">
					<span>
						<?php echo JText::_('PLG_MEMBERS_BLOG_COMMENTS_OFF'); ?>
					</span>
				</dd>
			<?php } ?>
			<?php if ($juser->get('id') == $this->row->get('created_by')) { ?>
				<dd class="state">
					<?php echo JText::_('PLG_MEMBERS_BLOG_STATE_' . strtoupper($this->row->state('text'))); ?>
				</dd>
				<dd class="entry-options">
					<a class="edit" href="<?php echo JRoute::_($this->row->link('edit')); ?>" title="<?php echo JText::_('PLG_MEMBERS_BLOG_EDIT'); ?>">
						<span><?php echo JText::_('PLG_MEMBERS_BLOG_EDIT'); ?></span>
					</a>
					<a class="delete" data-confirm="<?php echo JText::_('PLG_MEMBERS_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo JRoute::_($this->row->link('delete')); ?>" title="<?php echo JText::_('PLG_MEMBERS_BLOG_DELETE'); ?>">
						<span><?php echo JText::_('PLG_MEMBERS_BLOG_DELETE'); ?></span>
					</a>
				</dd>
			<?php } ?>
			</dl>

			<div class="entry-content">
				<?php echo $this->row->content('parsed'); ?>
				<?php echo $this->row->tags('cloud'); ?>
			</div>
		</div>
	</div><!-- /.subject -->
	<aside class="aside">
		<?php
		$limit = $this->filters['limit'];
		$this->filters['limit'] = 5;
		?>
		<div class="container blog-popular-entries">
			<h4><?php echo JText::_('PLG_MEMBERS_BLOG_POPULAR_ENTRIES'); ?></h4>
		<?php if ($popular = $this->model->entries('popular', $this->filters)) { ?>
			<ol>
			<?php foreach ($popular as $row) { ?>
				<li>
					<a href="<?php echo JRoute::_($row->link()); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				</li>
			<?php } ?>
			</ol>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-popular-entries -->

		<div class="container blog-recent-entries">
			<h4><?php echo JText::_('PLG_MEMBERS_BLOG_RECENT_ENTRIES'); ?></h4>
		<?php if ($recent = $this->model->entries('recent', $this->filters)) { ?>
			<ol>
			<?php foreach ($recent as $row) { ?>
				<li>
					<a href="<?php echo JRoute::_($row->link()); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				</li>
			<?php } ?>
			</ol>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND'); ?></p>
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
			<h3 id="comments">
				<?php echo JText::_('PLG_MEMBERS_BLOG_COMMENTS_HEADER'); ?>
			</h3>
			<?php if ($this->row->comments('count') > 0) { ?>
				<?php
					$this->view('_list', 'comments')
					     ->set('parent', 0)
					     ->set('cls', 'odd')
					     ->set('depth', 0)
					     ->set('option', $this->option)
					     ->set('comments', $this->row->comments('list'))
					     ->set('config', $this->config)
					     ->set('base', $this->row->link())
					     ->set('member', $this->member)
					     ->display();
				?>
			<?php } else { ?>
				<p class="no-comments">
					<?php echo JText::_('PLG_MEMBERS_BLOG_NO_COMMENTS'); ?>
				</p>
			<?php } ?>

			<h3>
				<?php echo JText::_('Post a comment'); ?>
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
							$name = JText::_('PLG_MEMBERS_BLOG_ANONYMOUS');
							if (!$replyto->get('anonymous'))
							{
								$xuser = \Hubzero\User\Profile::getInstance($replyto->get('created_by'));
								if (is_object($xuser) && $xuser->get('name'))
								{
									$name = '<a href="' . JRoute::_($xuser->getLink()) . '">' . $this->escape(stripslashes($xuser->get('name'))) . '</a>';
								}
							}
					?>
					<blockquote cite="c<?php echo $replyto->get('id'); ?>">
						<p>
							<strong><?php echo $name; ?></strong>
							<span class="comment-date-at"><?php echo JText::_('PLG_MEMBERS_BLOG_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span>
							<span class="comment-date-on"><?php echo JText::_('PLG_MEMBERS_BLOG_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
						</p>
						<p><?php echo \Hubzero\Utility\String::truncate(stripslashes($replyto->get('content')), 300); ?></p>
					</blockquote>
					<?php
						}
					?>
					<label>
						<?php echo JText::_(($replyto->exists() ? 'Your reply' : 'Your comments')); ?>: <span class="required"><?php echo JText::_('PLG_MEMBERS_BLOG_REQUIRED'); ?></span>
						<?php
						if (!$juser->get('guest')) {
							echo JFactory::getEditor()->display('comment[content]', '', '', '', 40, 15, false, 'commentcontent', null, null, array('class' => 'minimal no-footer'));
						} else {
						?>
						<p class="warning">
							<?php echo JText::sprintf('PLG_MEMBERS_BLOG_MUST_LOG_IN', '<a href="' . JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->row->link() . '#post-comment', false, true))) . '">' . JText::_('PLG_MEMBERS_BLOG_LOG_IN') . '</a>'); ?>
						</p>
						<?php } ?>
					</label>

				<?php if (!$juser->get('guest')) { ?>
					<label id="comment-anonymous-label">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
						<?php echo JText::_('PLG_MEMBERS_BLOG_POST_ANONYMOUS'); ?>
					</label>

					<p class="submit">
						<input type="submit" name="submit" value="<?php echo JText::_('PLG_MEMBERS_BLOG_SUBMIT'); ?>" />
					</p>
				<?php } ?>
					<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />
					<input type="hidden" name="comment[state]" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="active" value="blog" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="action" value="savecomment" />

					<?php echo JHTML::_('form.token'); ?>

					<div class="sidenote">
						<p>
							<strong><?php echo JText::_('PLG_MEMBERS_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
						</p>
						<p>
							<?php echo JText::_('PLG_MEMBERS_BLOG_COMMENT_HELP'); ?>
						</p>
					</div>
				</fieldset>
			</form>
		</div><!-- /.subject -->
		<aside class="aside aside-below">
			<p>
				<a class="add btn" href="#post-comment">
					<?php echo JText::_('PLG_MEMBERS_BLOG_ADD_A_COMMENT'); ?>
				</a>
			</p>
		</aside><!-- / .aside -->
	</section>
<?php } ?>
