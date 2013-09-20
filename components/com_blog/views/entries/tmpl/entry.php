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

ximport('Hubzero_User_Profile');

$juser =& JFactory::getUser();

$first = $this->model->entries('first');

$entry_year  = substr($this->row->get('publish_up'), 0, 4);
$entry_month = substr($this->row->get('publish_up'), 5, 2);
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<p>
		<a class="icon-archive archive btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=archive'); ?>">
			<?php echo JText::_('COM_BLOG_ARCHIVE'); ?>
		</a>
	</p>
</div>

<div class="main section">
	<div class="aside">
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
			$now = date("Y");
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
		<?php if ($popular = $this->model->entries('recent', $this->filters)) { ?>
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

		<div class="container blog-recent-entries">
			<h4><?php echo JText::_('COM_BLOG_RECENT_ENTRIES'); ?></h4>
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
			<p><?php echo JText::_('COM_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-recent-entries -->
	</div><!-- / .aside -->

	<div class="subject">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

<?php if ($this->row) { ?>
		<div class="entry" id="e<?php echo $this->row->get('id'); ?>">

			<h2 class="entry-title">
				<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>
			</h2>

			<dl class="entry-meta">
				<dt>
					<span>
						<?php echo JText::sprintf('Entry #%s', $this->row->get('id')); ?>
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
					<?php echo JText::_('COM_BLOG_STATE_' . strtoupper($row->state('text'))); ?>
				</dd>
				<dd class="entry-options">
					<a class="edit" href="<?php echo JRoute::_($this->row->link('edit')); ?>" title="<?php echo JText::_('COM_BLOG_EDIT'); ?>">
						<span><?php echo JText::_('COM_BLOG_EDIT'); ?></span>
					</a>
					<a class="delete" href="<?php echo JRoute::_($this->row->link('delete')); ?>" title="<?php echo JText::_('COM_BLOG_DELETE'); ?>">
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
				$author = Hubzero_User_Profile::getInstance($this->row->get('created_by'));
				if (is_object($author) && $author->get('name')) 
				{
			?>
				<div class="entry-author">
					<h3><?php echo JText::_('COM_BLOG_AUTHOR_ABOUT'); ?></h3>
					<p class="entry-author-photo"><img src="<?php echo $author->getPicture(); ?>" alt="" /></p>
					<div class="entry-author-content">
						<h4>
							<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->get('created_by')); ?>">
								<?php echo $this->escape(stripslashes($author->get('name'))); ?>
							</a>
						</h4>
						<p class="entry-author-bio">
						<?php if ($author->get('bio')) { ?>
							<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($author->get('bio')), 300, 0); ?>
						<?php } else { ?>
							<em><?php echo JText::_('COM_BLOG_AUTHOR_NO_BIO'); ?></em>
						<?php } ?>
						</p>
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
	<div class="clear"></div>
</div><!-- / .main section -->

<?php if ($this->row->get('allow_comments')) { ?>
<div class="below section">
	<h3>
		<!-- <a name="comments"></a> -->
		<?php echo JText::_('COM_BLOG_COMMENTS_HEADER'); ?>
	</h3>
	<div class="aside">
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
						$jconfig =& JFactory::getConfig();
						$live_site = rtrim(JURI::base(), '/');
						
						$feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
					}
					$feed = str_replace('https:://', 'http://', $feed);
				?>
				<a class="icon-feed feed btn" href="<?php echo $feed; ?>"><?php echo JText::_('COM_BLOG_FEED'); ?></a>
			</p>
		</div>
	</div><!-- / .aside -->
	<div class="subject">
	<?php if ($this->row->comments('count') > 0) { ?>
		<?php 
			$view = new JView(
				array(
					'name'    => 'entries',
					'layout'  => '_list'
				)
			);
			$view->parent     = 0;
			$view->cls        = 'odd';
			$view->depth      = 0;
			$view->option     = $this->option;
			$view->comments   = $this->row->comments('list');
			$view->config     = $this->config;
			$view->base       = $this->row->link();
			$view->parser     = Hubzero_Wiki_Parser::getInstance();
			$view->wikiconfig = array(
				'option'   => $this->option,
				'scope'    => 'blog',
				'pagename' => $this->row->get('alias'),
				'pageid'   => 0,
				'filepath' => $this->config->get('uploadpath'),
				'domain'   => ''
			);
			$view->display();
		?>
	<?php } else { ?>
		<p class="no-comments">
			<?php echo JText::_('COM_BLOG_NO_COMMENTS'); ?>
		</p>
	<?php } ?>
	</div><!-- / .subject -->
	<div class="clear"></div>

	<h3>
		<!-- <a name="post-comment"></a> -->
		<?php echo JText::_('Post a comment'); ?>
	</h3>

	<div class="subject">
		<form method="post" action="<?php echo JRoute::_($this->row->link()); ?>" id="commentform">
			<p class="comment-member-photo">
				<?php
				$jxuser = new Hubzero_User_Profile; //::getInstance($juser->get('id'));
				if (!$juser->get('guest')) {
					$jxuser = Hubzero_User_Profile::getInstance($juser->get('id'));
					$anonymous = 0;
				} else {
					$anonymous = 1;
				}
				?>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, $anonymous); ?>" alt="" />
			</p>
			<fieldset>
			<?php
			$replyto = $this->row->comment(JRequest::getInt('reply', 0));
			if (!$juser->get('guest')) 
			{
				if ($replyto->exists()) 
				{
					ximport('Hubzero_View_Helper_Html');
					$name = JText::_('COM_BLOG_ANONYMOUS');
					if (!$replyto->get('anonymous')) 
					{
						$xuser = Hubzero_User_Profile::getInstance($replyto->get('created_by'));
						if (is_object($xuser) && $xuser->get('name')) 
						{
							$name = '<a href="'.JRoute::_('index.php?option=com_members&id=' . $replyto->get('created_by')) . '">' . stripslashes($xuser->get('name')) . '</a>';
						}
					}
				?>
				<blockquote cite="c<?php echo $replyto->get('id'); ?>">
					<p>
						<strong><?php echo $name; ?></strong> 
						<span class="comment-date-at">@</span> 
						<span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span> 
						<span class="comment-date-on"><?php echo JText::_('COM_BLOG_ON'); ?></span> 
						<span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
					</p>
					<p>
						<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($replyto->get('content')), 300, 0); ?>
					</p>
				</blockquote>
				<?php
				}
			}
			?>
				<?php if (!$juser->get('guest')) { ?>
				<label for="commentcontent">
					Your <?php echo ($replyto->exists()) ? 'reply' : 'comments'; ?>: <span class="required"><?php echo JText::_('COM_BLOG_REQUIRED'); ?></span>
					<?php
						//ximport('Hubzero_Wiki_Editor');
						echo Hubzero_Wiki_Editor::getInstance()->display('comment[content]', 'commentcontent', '', 'minimal', '40', '15');
					?>
				</label>
				<?php } else { ?>
				<input type="hidden" name="comment[content]" id="commentcontent" value="" />

				<p class="warning">
					<?php echo JText::sprintf('COM_BLOG_MUST_LOG_IN', '<a href="' . JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_($this->row->link() . '#post-comment', false, true))) . '">' . JText::_('COM_BLOG_LOG_IN') . '</a>'); ?>
				</p>
				<?php } ?>

			<?php if (!$juser->get('guest')) { ?>
				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
					<?php echo JText::_('COM_BLOG_POST_ANONYMOUS'); ?>
				</label>

				<p class="submit">
					<input type="submit" name="submit" value="Submit" />
				</p>
			<?php } ?>
				<input type="hidden" name="comment[id]" value="0" />
				<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
				<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
				<input type="hidden" name="comment[created]" value="" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="savecomment" />

				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('COM_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
					</p>
					<p>
						<?php echo JText::_('COM_BLOG_COMMENT_HELP'); ?> <a href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>" class="popup">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->
<?php } ?>
