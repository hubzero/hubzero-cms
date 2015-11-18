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

$this->css()
     ->js();

$juser = JFactory::getUser();

$filters = array(
	'scope' => $this->config->get('show_from', 'site'),
	'state' => 'public',
	'group_id' => 0,
	'authorized' => false
);
if ($filters['scope'] == 'both')
{
	$filters['scope'] = '';
}
if (!$juser->get('guest'))
{
	$filters['state'] = 'registered';

	if ($this->config->get('access-manage-component'))
	{
		$filters['state'] = 'all';
		$filters['authorized'] = true;
	}
}

$first = $this->model->entries('first', $filters);

$entry_year  = substr($this->row->get('publish_up'), 0, 4);
$entry_month = substr($this->row->get('publish_up'), 5, 2);
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-archive archive btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=archive'); ?>">
				<?php echo JText::_('COM_BLOG_ARCHIVE'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

	<?php if ($this->row) { ?>
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
							<?php echo JText::sprintf('COM_BLOG_ENTRY_NUMBER', $this->row->get('id')); ?>
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
							<?php echo JText::sprintf('COM_BLOG_NUM_COMMENTS', $this->row->comments('count')); ?>
						</a>
					</dd>
				<?php } else { ?>
					<dd class="comments">
						<span>
							<?php echo JText::_('COM_BLOG_COMMENTS_OFF'); ?>
						</span>
					</dd>
				<?php } ?>
				<?php if ($juser->get('id') == $this->row->get('created_by')) { ?>
					<dd class="state">
						<?php echo JText::_('COM_BLOG_STATE_' . strtoupper($this->row->state('text'))); ?>
					</dd>
					<dd class="entry-options">
						<a class="edit" href="<?php echo JRoute::_($this->row->link('edit')); ?>" title="<?php echo JText::_('COM_BLOG_EDIT'); ?>">
							<span><?php echo JText::_('COM_BLOG_EDIT'); ?></span>
						</a>
						<a class="delete" data-confirm="<?php echo JText::_('COM_BLOG_CONFIRM_DELETE'); ?>" href="<?php echo JRoute::_($this->row->link('delete')); ?>" title="<?php echo JText::_('COM_BLOG_DELETE'); ?>">
							<span><?php echo JText::_('COM_BLOG_DELETE'); ?></span>
						</a>
					</dd>
				<?php } ?>
				</dl>

				<div class="entry-content">
					<?php echo $this->row->content('parsed'); ?>
					<?php echo $this->row->tags('cloud'); ?>
				</div>

				<?php
				if ($this->config->get('show_authors'))
				{
					if ($name = $this->row->creator()->get('name'))
					{
						$name = $this->escape(stripslashes($name));
				?>
					<div class="entry-author">
						<h3><?php echo JText::_('COM_BLOG_AUTHOR_ABOUT'); ?></h3>
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
							<div class="entry-author-bio">
							<?php if ($this->row->creator('bio')) { ?>
								<?php echo $this->row->creator()->getBio('parsed', 300); ?>
							<?php } else { ?>
								<em><?php echo JText::_('COM_BLOG_AUTHOR_NO_BIO'); ?></em>
							<?php } ?>
							</div>
							<div class="clearfix"></div>
						</div><!-- / .entry-author-content -->
					</div><!-- / .entry-author -->
				<?php
					}
				}
				?>
			</div><!-- / .entry -->
	<?php } ?>
		</div><!-- / .subject -->

		<aside class="aside hide6">
		<?php if ($this->config->get('access-create-entry')) { ?>
			<p>
				<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">
					<?php echo JText::_('COM_BLOG_NEW_ENTRY'); ?>
				</a>
			</p>
		<?php } ?>

			<div class="container blog-entries-years">
				<h4><?php echo JText::_('COM_BLOG_ENTRIES_BY_YEAR'); ?></h4>
				<ol>
			<?php
			if ($first->exists()) {
				$start = intval(substr($first->get('publish_up'), 0, 4));
				$now = JFactory::getDate()->format("Y");
				//$mon = date("m");
				for ($i=$now, $n=$start; $i >= $n; $i--)
				{
			?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&year=' . $i); ?>">
						<?php echo $i; ?>
					</a>
				<?php if ($i == $entry_year) { ?>
					<ol>
					<?php
						$months = array(
							'01' => JText::_('COM_BLOG_JANUARY'),
							'02' => JText::_('COM_BLOG_FEBRUARY'),
							'03' => JText::_('COM_BLOG_MARCH'),
							'04' => JText::_('COM_BLOG_APRIL'),
							'05' => JText::_('COM_BLOG_MAY'),
							'06' => JText::_('COM_BLOG_JUNE'),
							'07' => JText::_('COM_BLOG_JULY'),
							'08' => JText::_('COM_BLOG_AUGUST'),
							'09' => JText::_('COM_BLOG_SEPTEMBER'),
							'10' => JText::_('COM_BLOG_OCTOBER'),
							'11' => JText::_('COM_BLOG_NOVEMBER'),
							'12' => JText::_('COM_BLOG_DECEMBER')
						);
						foreach ($months as $key => $month)
						{
							if (intval($key) <= $entry_month)
							{
							?>
						<li>
							<a <?php if ($entry_month == $key) { echo 'class="active" '; } ?>href="<?php echo JRoute::_('index.php?option=' . $this->option . '&year=' . $i . '&month=' . $key); ?>">
								<?php echo $month; ?>
							</a>
						</li>
							<?php
							}
						}
					?>
					</ol>
			<?php } ?>
				</li>
			<?php
				}
			}
			?>
				</ol>
			</div><!-- / .blog-entries-years -->

			<div class="container blog-popular-entries">
				<h4><?php echo JText::_('COM_BLOG_POPULAR_ENTRIES'); ?></h4>
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
				<p><?php echo JText::_('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
			<?php } ?>
			</div><!-- / .blog-popular-entries -->
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .main section -->

<?php if ($this->row->get('allow_comments')) { ?>
<section class="below section">
	<div class="section-inner">
		<div class="subject">
			<h3 id="comments">
				<?php echo JText::_('COM_BLOG_COMMENTS_HEADER'); ?>
			</h3>

		<?php if ($this->row->comments('count') > 0) { ?>
			<?php
				$this->view('_list')
					 ->set('parent', 0)
					 ->set('option', $this->option)
					 ->set('comments', $this->row->comments('list'))
					 ->set('config', $this->config)
					 ->set('depth', 0)
					 ->set('cls', 'odd')
					 ->set('base', $this->row->link())
					 ->display();
			?>
		<?php } else { ?>
			<p class="no-comments">
				<?php echo JText::_('COM_BLOG_NO_COMMENTS'); ?>
			</p>
		<?php } ?>

			<h3>
				<?php echo JText::_('COM_BLOG_POST_COMMENT'); ?>
			</h3>

			<form method="post" action="<?php echo JRoute::_($this->row->link()); ?>" id="commentform">
				<p class="comment-member-photo">
					<?php
					$jxuser = new \Hubzero\User\Profile; //::getInstance($juser->get('id'));
					if (!$juser->get('guest')) {
						$jxuser = \Hubzero\User\Profile::getInstance($juser->get('id'));
						$anonymous = 0;
					} else {
						$anonymous = 1;
					}
					?>
					<img src="<?php echo $jxuser->getPicture($anonymous); ?>" alt="" />
				</p>
				<fieldset>
				<?php
				$replyto = $this->row->comment(JRequest::getInt('reply', 0));
				if (!$juser->get('guest'))
				{
					if ($replyto->exists())
					{
						$name = JText::_('COM_BLOG_ANONYMOUS');
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
							<span class="comment-date-at"><?php echo JText::_('COM_BLOG_AT'); ?></span>
							<span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span>
							<span class="comment-date-on"><?php echo JText::_('COM_BLOG_ON'); ?></span>
							<span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
						</p>
						<p>
							<?php echo \Hubzero\Utility\String::truncate(stripslashes($replyto->get('content')), 300); ?>
						</p>
					</blockquote>
					<?php
					}
				}
				?>
					<?php if (!$juser->get('guest')) { ?>
					<label for="commentcontent">
						Your <?php echo ($replyto->exists()) ? 'reply' : 'comments'; ?>:
						<?php
							echo $this->editor('comment[content]', '', 40, 15, 'commentcontent', array('class' => 'minimal no-footer'));
						?>
					</label>
					<?php } else { ?>
					<input type="hidden" name="comment[content]" id="commentcontent" value="" />

					<p class="warning">
						<?php echo JText::sprintf('COM_BLOG_MUST_LOG_IN', '<a href="' . JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JRoute::_($this->row->link() . '#post-comment', false, true))) . '">' . JText::_('COM_BLOG_LOG_IN') . '</a>'); ?>
					</p>
					<?php } ?>

				<?php if (!$juser->get('guest')) { ?>
					<label id="comment-anonymous-label">
						<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
						<?php echo JText::_('COM_BLOG_POST_ANONYMOUS'); ?>
					</label>

					<p class="submit">
						<input type="submit" name="submit" value="<?php echo JText::_('COM_BLOG_SUBMIT'); ?>" />
					</p>
				<?php } ?>
					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />
					<input type="hidden" name="comment[state]" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="task" value="savecomment" />

					<?php echo JHTML::_('form.token'); ?>

					<div class="sidenote">
						<p>
							<strong><?php echo JText::_('COM_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
						</p>
					</div>
				</fieldset>
			</form>
		</div><!-- / .subject -->

		<aside class="aside">
			<div class="container blog-entries-years">
				<h4><?php echo JText::_('COM_BLOG_COMMENTS_FEED'); ?></h4>
				<p>
					<?php echo JText::_('COM_BLOG_COMMENTS_FEED_EXPLANATION'); ?>
				</p>
				<p>
					<?php
						$feed = JRoute::_($this->row->link() . '/comments.rss');
						if (substr($feed, 0, 4) != 'http')
						{
							$jconfig = JFactory::getConfig();
							$live_site = rtrim(JURI::base(), '/');

							$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
						}
						$feed = str_replace('https:://', 'http://', $feed);
					?>
					<a class="icon-feed feed btn" href="<?php echo $feed; ?>"><?php echo JText::_('COM_BLOG_FEED'); ?></a>
				</p>
			</div>
		</aside><!-- / .aside -->
	</div><!-- / .section-inner -->
</section><!-- / .below section -->
<?php } ?>
