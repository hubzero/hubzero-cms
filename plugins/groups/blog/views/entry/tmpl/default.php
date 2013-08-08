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
ximport('Hubzero_Wiki_Editor');

$juser =& JFactory::getUser();
$editor =& Hubzero_Wiki_Editor::getInstance();

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=blog'
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
			<a class="icon-config config btn" href="<?php echo JRoute::_($base . '&action=settings'); ?>" title="<?php echo JText::_('Edit Settings'); ?>">
				<?php echo JText::_('Settings'); ?>
			</a>
		</li>
	<?php } ?>
	</ul>
<?php } ?>

<div class="entry-container">
	<div class="aside">
	<?php 
	$limit = $this->filters['limit']; 
	$this->filters['limit'] = 5;
	?>
		<div class="container blog-popular-entries">
			<h4><?php echo JText::_('PLG_GROUPS_BLOG_POPULAR_ENTRIES'); ?></h4>
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
			<p><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-popular-entries -->

		<div class="container blog-recent-entries">
			<h4><?php echo JText::_('PLG_GROUPS_BLOG_RECENT_ENTRIES'); ?></h4>
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
			<p><?php echo JText::_('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></p>
		<?php } ?>
		</div><!-- / .blog-recent-entries -->
	<?php
	$this->filters['limit'] = $limit; 
	?>
	</div><!-- /.aside -->
	
	<div class="subject">
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
					<a class="icon-delete delete" href="<?php echo JRoute::_($this->row->link('delete')); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_DELETE'); ?>">
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
			$author = Hubzero_User_Profile::getInstance($this->row->get('created_by'));
			if (is_object($author) && $author->get('name')) 
			{
			?>
			<div class="entry-author">
				<h3><?php echo JText::_('About the author'); ?></h3>
				<p class="entry-author-photo"><img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($author, 0); ?>" alt="" /></p>
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
							<?php echo JText::_('This author has yet to provide a bio.'); ?>
						<?php } ?>
					</p>
				</div>
			</div>
			<?php
			}
			?>
		</div>
	</div><!-- /.subject -->
	<div class="clear"></div>

	<?php if ($this->row->get('allow_comments')) { ?>
		<div class="aside aside-below">
			<p>
				<a class="add btn" href="#post-comment">
					<?php echo JText::_('PLG_GROUPS_BLOG_ADD_A_COMMENT'); ?>
				</a>
			</p>
		</div><!-- / .aside -->
		
		<div class="subject below">
			<h3 class="below_heading">
				<a name="comments"></a>
				<?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_HEADER'); ?>
			</h3>
			<?php if ($this->row->comments('count') > 0) { ?>
				<?php 
					$view = new Hubzero_Plugin_View(
						array(
							'folder'  => 'groups',
							'element' => 'blog',
							'name'    => 'comments',
							'layout'  => '_list'
						)
					);
					$view->group      = $this->group;
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
					<?php echo JText::_('PLG_GROUPS_BLOG_NO_COMMENTS'); ?>
				</p>
			<?php } ?>
		</div><!-- / .subject -->
		<div class="clear"></div>

		<div class="aside aside-below">
			<table class="wiki-reference" summary="Wiki Syntax Reference">
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
	
		<div class="subject below">
			<h3 class="below_heading">
				<a name="post-comment"></a>
				<?php echo JText::_('Post a comment'); ?>
			</h3>

			<form method="post" action="<?php echo JRoute::_($this->row->link()); ?>" id="commentform">
				<p class="comment-member-photo">
					<?php
						$jxuser = Hubzero_User_Profile::getInstance($juser->get('id'));
						$anon = 1;
						if (!$juser->get('guest')) 
						{
							$anon = 0;
						}
					?>
					<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, $anon); ?>" alt="" />
				</p>
				<fieldset>
					<?php
						$replyto = $this->row->comment(JRequest::getInt('reply', 0));
						if ($replyto->exists()) 
						{
							ximport('Hubzero_View_Helper_Html');
							$name = JText::_('PLG_GROUPS_BLOG_ANONYMOUS');
							if (!$replyto->get('anonymous')) 
							{
								$xuser = Hubzero_User_Profile::getInstance($replyto->get('created_by'));
								if (is_object($xuser) && $xuser->get('name')) 
								{
									$name = '<a href="'.JRoute::_('index.php?option=com_members&id=' . $replyto->get('created_by')) . '">' . $this->escape(stripslashes($xuser->get('name'))) . '</a>';
								}
							}
					?>
					<blockquote cite="c<?php echo $replyto->get('id'); ?>">
						<p>
							<strong><?php echo $name; ?></strong> 
							<span class="comment-date-at">@</span><span class="time"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('time'); ?></time></span> 
							<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $replyto->get('created'); ?>"><?php echo $replyto->created('date'); ?></time></span>
						</p>
						<p><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($replyto->get('content')), 300, 0); ?></p>
					</blockquote>
					<?php } ?>

					<?php if (!$this->juser->get('guest')) { ?>
						<label for="comment_content">
							Your <?php echo ($replyto->exists()) ? 'reply' : 'comments'; ?>: <span class="required"><?php echo JText::_('PLG_GROUPS_BLOG_REQUIRED'); ?></span>
							<?php echo $editor->display('comment[content]', 'comment_content', '', '', '40', '15'); ?>
						</label>

						<label id="comment-anonymous-label">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							<?php echo JText::_('PLG_GROUPS_BLOG_POST_ANONYMOUS'); ?>
						</label>

						<p class="submit">
							<input type="submit" name="submit" value="<?php echo JText::_('Submit'); ?>" />
						</p>
					<?php } else { ?>
						<p class="warning">
							You must <a href="/login?return=<?php echo base64_encode(JRoute::_($this->row->link() . '#post-comment', false, true)); ?>">log in</a> to post comments.
						</p>
					<?php } ?>

					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->get('id'); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $replyto->get('id'); ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->juser->get('id'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="active" value="blog" />
					<input type="hidden" name="action" value="savecomment" />

					<div class="sidenote">
						<p>
							<strong><?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_KEEP_POLITE'); ?></strong>
						</p>
						<p>Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/wiki/Help:WikiFormatting" class="popup">Wiki syntax</a> is supported.</p>
					</div>
				</fieldset>
			</form>
		</div><!-- / .subject -->
	<?php } //end if allow comments ?>
</div><!-- /.entry-container -->
