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

$entry_year = substr($this->row->publish_up, 0, 4);//intval(JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz));
$entry_month = substr($this->row->publish_up, 5, 2);//intval(JHTML::_('date',$this->row->publish_up, '%B', 0));
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<p>
		<a class="archive" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=archive'); ?>">
			<?php echo JText::_('Archive'); ?>
		</a>
	</p>
</div>

<div class="main section">
	<div class="aside">
<?php if ($this->config->get('access-create-entry')) { ?>
		<p>
			<a class="add" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=new'); ?>">
				<?php echo JText::_('New entry'); ?>
			</a>
		</p>
<?php } ?>
 	<div class="container blog-entries-years">
		<h4><?php echo JText::_('Entries By Year'); ?></h4>
		<ol>
<?php 
if ($this->firstentry) {
	$start = intval(substr($this->firstentry,0,4));
	$now = date("Y");
	//$mon = date("m");
	for ($i=$now, $n=$start; $i >= $n; $i--)
	{
?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&year=' . $i); ?>">
					<?php echo $i; ?>
				</a>
<?php
				if ($i == $entry_year) {
?>
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
<?php
				}
?>
			</li>
<?php 
	}
}
?>
		</ol>
	</div><!-- / .blog-entries-years -->
	<div class="container blog-popular-entries">
		<h4><?php echo JText::_('Popular Entries'); ?></h4>
		<ol>
<?php 
if ($this->popular) {
	foreach ($this->popular as $row)
	{
?>
			<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
	}
}
?>
		</ol>
	</div><!-- / .blog-popular-entries -->
	<div class="container blog-recent-entries">
		<h4><?php echo JText::_('Recent Entries'); ?></h4>
		<ol>
<?php 
if ($this->recent) {
	foreach ($this->recent as $row)
	{
?>
			<li><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $row->publish_up, $this->monthFormat, $this->tz) . '/' . $row->alias); ?>"><?php echo stripslashes($row->title); ?></a></li>
<?php 
	}
}
?>
		</ol>
	</div><!-- / .blog-recent-entries -->
</div><!-- / .aside -->

<div class="subject">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->row) { ?>
	<div class="entry" id="e<?php echo $this->row->id; ?>">
		
		<h2 class="entry-title">
			<?php echo stripslashes($this->row->title); ?>
		<?php /*if ($juser->get('id') == $this->row->created_by) { ?>
			<span class="state"><?php 
				switch ($this->row->state)
				{
					case 1:
						echo JText::_('Public');
					break;
					case 2:
						echo JText::_('Registered members');
					break;
					case 0:
					default:
						echo JText::_('Private');
					break;
				}
				?></span>
			<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=edit&entry='.$this->row->id); ?>" title="<?php echo JText::_('Edit'); ?>"><?php echo JText::_('Edit'); ?></a>
			<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=delete&entry='.$this->row->id); ?>" title="<?php echo JText::_('Delete'); ?>"><?php echo JText::_('Delete'); ?></a>
		<?php }*/ ?>
		</h2>

		<dl class="entry-meta">
			<dt>
				<span>
					<?php echo JText::sprintf('Entry #%s', $this->row->id); ?>
				</span>
			</dt>
			<dd class="date">
				<time datetime="<?php echo $this->row->publish_up; ?>">
					<?php echo JHTML::_('date', $this->row->publish_up, $this->dateFormat, $this->tz); ?>
				</time>
			</dd>
			<dd class="time">
				<time datetime="<?php echo $this->row->publish_up; ?>">
					<?php echo JHTML::_('date', $this->row->publish_up, $this->timeFormat, $this->tz); ?>
				</time>
			</dd>
		<?php if ($this->row->allow_comments == 1) { ?>
			<dd class="comments">
				<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias.'#comments'); ?>">
					<?php echo JText::sprintf('COM_BLOG_NUM_COMMENTS', $this->comment_total); ?>
				</a>
			</dd>
		<?php } else { ?>
			<dd class="comments">
				<span>
					<?php echo JText::_('COM_BLOG_COMMENTS_OFF'); ?>
				</span>
			</dd>
		<?php } ?>
		<?php if ($juser->get('id') == $this->row->created_by) { ?>
			<dd class="state">
				<?php 
					switch ($this->row->state)
					{
						case 1:
							echo JText::_('Public');
						break;
						case 2:
							echo JText::_('Registered members');
						break;
						case 0:
						default:
							echo JText::_('Private');
						break;
					}
				?>
			</dd>
			<dd class="entry-options">
				<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=edit&entry=' . $this->row->id); ?>" title="<?php echo JText::_('Edit'); ?>">
					<span><?php echo JText::_('Edit'); ?></span>
				</a>
				<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=delete&entry=' . $this->row->id); ?>" title="<?php echo JText::_('Delete'); ?>">
					<span><?php echo JText::_('Delete'); ?></span>
				</a>
			</dd>
		<?php } ?>
		</dl>

		<?php /*><p class="entry-author">Posted by <cite><a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->created_by); ?>"><?php echo stripslashes($creator->get('name')); ?></a></cite></p> ?>
<?php }*/ ?>
		<div class="entry-content">
			<?php echo $this->row->content; ?>
<?php if ($this->tags) { ?>
			<?php echo $this->tags; ?>
<?php } ?>
		</div>
<?php 
	if ($this->config->get('show_authors')) {
		$author = new Hubzero_User_Profile();
		$author->load($this->row->created_by);
		if (is_object($author) && $author->get('name')) 
		{
?>
		<div class="entry-author">
			<h3><?php echo JText::_('About the author'); ?></h3>
			<p class="entry-author-photo"><img src="<?php echo BlogHelperMember::getMemberPhoto($author, 0); ?>" alt="" /></p>
			<div class="entry-author-content">
				<h4>
					<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->created_by); ?>">
						<?php echo stripslashes($author->get('name')); ?>
					</a>
				</h4>
			<?php if ($author->get('bio')) { ?>
				<p class="entry-author-bio">
					<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($author->get('bio')), 300, 0); ?>
				</p>
			<?php } ?>
				<div class="clearfix"></div>
			</div>
		</div>
<?php
		}
	}
?>
	</div><!-- / .entry -->
<?php } ?>
</div><!-- / .subject -->
<div class="clear"></div>
</div><!-- / .main section -->

<?php if ($this->row->allow_comments == 1) { ?>
<div class="below section">
	<h3>
		<a name="comments"></a>
		<?php echo JText::_('Comments on this entry'); ?>
<?php
	$feed = JRoute::_('index.php?option=' . $this->option . '&task=' . JHTML::_('date', $this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias . '/comments.rss');
	if (substr($feed, 0, 4) != 'http') {
		$jconfig =& JFactory::getConfig();
		$live_site = rtrim(JURI::base(),'/');
		
		$feed = $live_site . ltrim($feed, DS);
	}
	$feed = str_replace('https:://', 'http://', $feed);
?>
		<a class="feed" href="<?php echo $feed; ?>" title="<?php echo JText::_('Comments RSS Feed'); ?>"><?php echo JText::_('Feed'); ?></a>
	</h3>
	<div class="aside">
		<p>
			<a class="add" href="#post-comment">
				<?php echo JText::_('Add a comment'); ?>
			</a>
		</p>
	</div><!-- / .aside -->
	<div class="subject">
<?php 
if ($this->comments) {
?>
		<ol class="comments">
<?php 
	$cls = 'even';

	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => 'blog',
		'pagename' => $this->row->alias,
		'pageid'   => 0,
		'filepath' => $this->config->get('uploadpath'),
		'domain'   => ''
	);
	ximport('Hubzero_Wiki_Parser');
	$p =& Hubzero_Wiki_Parser::getInstance();

	foreach ($this->comments as $comment)
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';

		$name = JText::_('COM_BLOG_ANONYMOUS');
		if (!$comment->anonymous) {
			//$xuser =& JUser::getInstance($comment->created_by);
			$xuser = new Hubzero_User_Profile();
			$xuser->load($comment->created_by);
			if (is_object($xuser) && $xuser->get('name')) {
				$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$comment->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
			}
			
			if ($this->row->created_by == $comment->created_by) {
				$cls .= ' author';
			}
		}

		if ($comment->reports) {
			$content = '<p class="warning">'.JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
		} else {
			$content = $p->parse(stripslashes($comment->content), $wikiconfig);
		}
?>
			<li class="comment <?php echo $cls; ?>" id="c<?php echo $comment->id; ?>">
				<a name="#c<?php echo $comment->id; ?>"></a>
				<p class="comment-member-photo">
					<img src="<?php echo BlogHelperMember::getMemberPhoto($xuser, $comment->anonymous); ?>" alt="" />
				</p>
				<div class="comment-content">
					<p class="comment-title">
						<strong><?php echo $name; ?></strong> 
						<a class="permalink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias.'#c'.$comment->id); ?>" title="<?php echo JText::_('COM_BLOG_PERMALINK'); ?>">
							<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $this->timeFormat, $this->tz); ?></time></span> 
							<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $this->dateFormat, $this->tz); ?></time></span>
						</a>
					</p>
				<?php echo $content; ?>
<?php 		if (!$comment->reports) { ?>
					<p class="comment-options">
						<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$comment->id.'&parent='.$this->row->id); ?>"><?php echo JText::_('Report abuse'); ?></a> | 
<?php
$rtrn = JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias.'?reply='.$comment->id.'#post-comment');
if ($juser->get('guest')) {
	$lnk = '/login?return='. base64_encode($rtrn);
} else {
	$lnk = $rtrn;
}
?>
						<a class="reply" href="<?php echo $lnk; ?>"><?php echo JText::_('Reply'); ?></a>
					</p>
<?php 		} ?>
				</div>
<?php
		if ($comment->replies) {
?>
				<ol class="comments">
<?php
			foreach ($comment->replies as $reply)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				$name = JText::_('COM_BLOG_ANONYMOUS');
				if (!$reply->anonymous) {
					//$xuser =& JUser::getInstance($reply->created_by);
					$xuser = new Hubzero_User_Profile();
					$xuser->load($reply->created_by);
					if (is_object($xuser) && $xuser->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$reply->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
					}
					
					if ($this->row->created_by == $reply->created_by) {
						$cls .= ' author';
					}
				}

				if ($reply->reports) {
					$content = '<p class="warning">'.JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
				} else {
					$content = $p->parse(stripslashes($reply->content), $wikiconfig);
				}
?>
					<li class="comment <?php echo $cls; ?>" id="c<?php echo $reply->id; ?>">
						<a name="#c<?php echo $reply->id; ?>"></a>
						<p class="comment-member-photo">
							<img src="<?php echo BlogHelperMember::getMemberPhoto($xuser, $reply->anonymous); ?>" alt="" />
						</p>
						<div class="comment-content">
							<p class="comment-title">
								<strong><?php echo $name; ?></strong> 
								<a class="permalink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias.'#c'.$reply->id); ?>" title="<?php echo JText::_('COM_BLOG_PERMALINK'); ?>">
									<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, $this->timeFormat, $this->tz); ?></time></span> 
									<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, $this->dateFormat, $this->tz); ?></time></span>
								</a>
							</p>
							<?php echo $content; ?>
<?php 				if (!$reply->reports) { ?>
							<p class="comment-options">
								<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$reply->id.'&parent='.$this->row->id); ?>">Report abuse</a> | 
								<a class="reply" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias.'?reply='.$reply->id.'#post-comment'); ?>">Reply</a>
							</p>
<?php 				} ?>
						</div>
<?php
					if ($reply->replies) {
?>
						<ol class="comments">
<?php
					foreach ($reply->replies as $response)
					{
						$cls = ($cls == 'even') ? 'odd' : 'even';

						$name = JText::_('COM_BLOG_ANONYMOUS');
						if (!$response->anonymous) {
							//$xuser =& JUser::getInstance($reply->created_by);
							$xuser = new Hubzero_User_Profile();
							$xuser->load($response->created_by);
							if (is_object($xuser) && $xuser->get('name')) {
								$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$response->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
							}
							
							if ($this->row->created_by == $response->created_by) {
								$cls .= ' author';
							}
						}

						if ($response->reports) {
							$content = '<p class="warning">'.JText::_('COM_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
						} else {
							$content = $p->parse(stripslashes($response->content), $wikiconfig);
						}
?>
							<li class="comment <?php echo $cls; ?>" id="c<?php echo $response->id; ?>">
								<a name="#c<?php echo $response->id; ?>"></a>
								<p class="comment-member-photo">
									<img src="<?php echo BlogHelperMember::getMemberPhoto($xuser, $response->anonymous); ?>" alt="" />
								</p>
								<div class="comment-content">
									<p class="comment-title">
										<strong><?php echo $name; ?></strong> 
										<a class="permalink" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date', $this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date', $this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias.'#c'.$response->id); ?>" title="<?php echo JText::_('COM_BLOG_PERMALINK'); ?>">
											<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $response->created; ?>"><?php echo JHTML::_('date', $response->created, $this->timeFormat, $this->tz); ?></time></span> 
											<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $response->created; ?>"><?php echo JHTML::_('date', $response->created, $this->dateFormat, $this->tz); ?></time></span>
										</a>
									</p>
									<?php echo $content; ?>
<?php 					if (!$response->reports) { ?>
									<p class="comment-options">
										<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$response->id.'&parent='.$this->row->id); ?>"><?php echo JText::_('Report abuse'); ?></a>
									</p>
<?php 					} ?>
								</div>
							</li>
<?php
					}
?>
						</ol>
<?php
					}
?>
					</li>
<?php
			}
?>
				</ol>
<?php
		}
?>
			</li>
<?php
	}
?>
		</ol>
<?php
} else {
?>
		<p class="no-comments">
			<?php echo JText::_('There are no comments at this time.'); ?>
		</p>
<?php
}
?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below section -->

<div class="below section">
	<h3>
		<a name="post-comment"></a>
		<?php echo JText::_('Post a comment'); ?>
	</h3>

	<div class="aside">
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
	<div class="subject">
		<form method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias); ?>" id="commentform">
			<p class="comment-member-photo">
<?php
				if (!$juser->get('guest')) {
					$jxuser = new Hubzero_User_Profile();
					$jxuser->load($juser->get('id'));
					$thumb = BlogHelperMember::getMemberPhoto($jxuser, 0);
				} else {
					$config =& JComponentHelper::getParams('com_members');
					$thumb = DS . ltrim($config->get('defaultpic'), DS);
					$thumb = BlogHelperMember::thumbit($thumb);
				}
?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
			<fieldset>
<?php
			if (!$juser->get('guest')) {
				if ($this->replyto->id) {
					ximport('Hubzero_View_Helper_Html');
					$name = JText::_('COM_BLOG_ANONYMOUS');
					if (!$this->replyto->anonymous) {
						//$xuser =& JUser::getInstance($reply->created_by);
						$xuser = new Hubzero_User_Profile();
						$xuser->load($this->replyto->created_by);
						if (is_object($xuser) && $xuser->get('name')) {
							$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$this->replyto->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
						}
					}
?>
				<blockquote cite="c<?php echo $this->replyto->id ?>">
					<p>
						<strong><?php echo $name; ?></strong> 
						<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $this->replyto->created; ?>"><?php echo JHTML::_('date', $this->replyto->created, $this->timeFormat, $this->tz); ?></time></span> 
						<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $this->replyto->created; ?>"><?php echo JHTML::_('date', $this->replyto->created, $this->dateFormat, $this->tz); ?></time></span>
					</p>
					<p>
						<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($this->replyto->content), 300, 0); ?>
					</p>
				</blockquote>
<?php
				}
			}
?>
				<label for="commentcontent">
					Your <?php echo ($this->replyto->id) ? 'reply' : 'comments'; ?>: <span class="required">required</span>
<?php
				if (!$juser->get('guest')) {
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('comment[content]', 'commentcontent', '', '', '40', '15');
				} else {
					$rtrn = JRoute::_('index.php?option=' . $this->option . '&task='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz) . '/' . JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz) . '/' . $this->row->alias.'#post-comment');
?>
					<p class="warning">
						You must <a href="<?php echo JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)); ?>">log in</a> to post comments.
					</p>
<?php
				}
?>
				</label>

<?php if (!$juser->get('guest')) { ?>
				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
					<?php echo JText::_('Post anonymously'); ?>
				</label>

				<p class="submit">
					<input type="submit" name="submit" value="Submit" />
				</p>
<?php } ?>
				<input type="hidden" name="comment[id]" value="0" />
				<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="comment[parent]" value="<?php echo $this->replyto->id; ?>" />
				<input type="hidden" name="comment[created]" value="" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="savecomment" />

				<div class="sidenote">
					<p>
						<strong>Please keep comments relevant to this entry.</strong>
					</p>
					<p>
						Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</form>
	</div><!-- / .subject -->
</div><!-- / .below section -->
<?php } ?>
