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

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$yearFormat  = "%Y";
$monthFormat = "%m";
$dayFormat   = "%d";
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$yearFormat  = "Y";
	$monthFormat = "m";
	$dayFormat   = "d";
	$tz = true;
}
if (!$this->article->modified || $this->article->modified == '0000-00-00 00:00:00')
{
	$this->article->modified = $this->article->created;
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<p>
		<a class="icon-main main-page btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('Main page'); ?></a>
	</p>
</div>
<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
<?php } ?>
	<div class="aside">
		<div class="container">
			<h3><?php echo JText::_('Categories'); ?></h3>
			<ul class="categories">
				<li>
					<a<?php if ($this->catid == 0) { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=all'); ?>">
						<?php echo JText::_('All Articles'); ?>
					</a>
				</li>
<?php
		if (count($this->categories) > 0) 
		{
			$html = '';
			foreach ($this->categories as $row)
			{
				$html .= "\t" . '<li><a ';
				if ($this->catid == $row->id) {
					$html .= 'class="active" ';
				}
				$html .= 'href="' . JRoute::_('index.php?option=' . $this->option . '&section=' . $row->alias) . '">' . $this->escape(stripslashes($row->title)) . ' <span class="item-count">' . $row->numitems . '</span></a>';
				if (count($this->subcategories) > 0 && $this->catid == $row->id) 
				{
					$html .= "\t" . '<ul class="categories">' . "\n";
					foreach ($this->subcategories as $cat)
					{
						$html .= "\t\t" . '<li><a ';
						if ($this->article->category == $cat->id) 
						{
							$html .= 'class="active" ';
						}
						$html .= 'href="' . JRoute::_('index.php?option=' . $this->option . '&section=' . $this->category->alias . '&category=' . $cat->alias) . '">' . $this->escape(stripslashes($cat->title)) . ' <span class="item-count">' . $cat->numitems . '</span></a></li>' . "\n";
					}
					$html .= "\t" . '</ul>' . "\n";
				}
				$html .= '</li>' . "\n";
			}
		}

		echo $html;
?>
			</ul>
		</div><!-- / .container -->
	</div><!-- / .aside -->
	<div class="subject">
		<div class="container" id="entry-<?php echo $this->article->id; ?>">
			<div class="container-block">
				<h3><?php echo $this->escape(stripslashes($this->article->title)); ?></h3>
				<div class="entry-content">
					<?php echo stripslashes($this->article->fulltxt); ?>
				</div>
			<?php if (count($this->tags) > 0) { ?>
				<div class="entry-tags">
					<p>Tags:</p>
					<ol class="tags">
				<?php
					foreach ($this->tags as $tag)
					{
						echo '<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag['tag']).'" rel="tag">'.$this->escape(stripslashes($tag['raw_tag'])).'</a></li>'."\n";
					}
				?>
					</ol>
				</div><!-- / .entry-tags -->
			<?php } ?>

				<p class="voting">
					<?php 
						$view = new JView(array(
							'name' => $this->controller,
							'layout' => 'vote'
						));
						$view->option = $this->option;
						$view->item = $this->article;
						$view->type = 'entry';
						$view->vote = $this->vote;
						$view->id = $this->article->id;
						$view->display();
					?>
				</p>

				<p class="entry-details">
					Last updated @ 
					<span class="entry-time"><?php echo JHTML::_('date', $this->article->modified, $timeformat, $tz); ?></span> on 
					<span class="entry-date"><?php echo JHTML::_('date', $this->article->modified, $dateformat, $tz); ?></span>
				</p>

				<div class="clearfix"></div>
			</div><!-- / .container-block -->
		</div><!-- / .container -->
	</div><!-- / .subject -->
</div><!-- / .main section -->

<?php 
if ($this->config->get('allow_comments')) {
	$d = ($this->article->modified) ? $this->article->modified : $this->article->created;
	$year  = intval(substr($d, 0, 4));
	$month = intval(substr($d, 5, 2));
	$day   = intval(substr($d, 8, 2));

	switch ($this->config->get('comments_close', 'never'))
	{
		case 'day':
			$dt = mktime(0, 0, 0, $month, ($day+1), $year);
		break;
		case 'week':
			$dt = mktime(0, 0, 0, $month, ($day+7), $year);
		break;
		case 'month':
			$dt = mktime(0, 0, 0, ($month+1), $day, $year);
		break;
		case '6months':
			$dt = mktime(0, 0, 0, ($month+6), $day, $year);
		break;
		case 'year':
			$dt = mktime(0, 0, 0, $month, $day, ($year+1));
		break;
		case 'never':
		default:
			$dt =mktime(0, 0, 0, $month, $day, $year);
		break;
	}

	$pdt = strftime($yearFormat, $dt) . '-' . strftime($monthFormat, $dt) . '-' . strftime($dayFormat, $dt) . ' 00:00:00';
	$today = date('Y-m-d H:i:s', time());
?>		
<div class="below section">
	<h3 class="comments-title">
		<!-- <a name="comments"></a> -->
		Comments on this entry
		<?php
		if ($this->config->get('feeds_enabled')) {
			if ($this->comment_total > 0) {
				$feed = JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'/comments.rss');
				if (substr($feed, 0, 4) != 'http') 
				{
					$jconfig =& JFactory::getConfig();
					$live_site = rtrim(JURI::base(),'/');
						
					$feed = $live_site . DS . ltrim($feed, DS);
				}
				$feed = str_replace('https://', 'http://', $feed);
		?>
				<a class="feed" href="<?php echo $feed; ?>" title="<?php echo JText::_('Comments Feed'); ?>"><?php echo JText::_('Comments Feed'); ?></a>
		<?php 
			}
		}
		?>
	</h3>
	<div class="aside">
	<?php if ($this->config->get('close_comments') == 'never' || ($this->config->get('close_comments') != 'now' && $today < $pdt)) { ?>
		<p>
			<a class="icon-add add btn" href="#post-comment"><?php echo JText::_('Add a comment'); ?></a>
		</p>
	<?php } ?>
	</div>
	<div class="subject">
<?php 
ximport('Hubzero_User_Profile');
ximport('Hubzero_User_Profile_Helper');
if ($this->comments) {
?>
	<ol class="comments">
<?php 
	$cls = 'even';

	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => '',
		'pagename' => $this->article->alias,
		'pageid'   => $this->article->id,
		'filepath' => '',
		'domain'   => ''
	);
	ximport('Hubzero_Wiki_Parser');
	$p =& Hubzero_Wiki_Parser::getInstance();

	foreach ($this->comments as $comment)
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';

		if ($this->article->created_by == $comment->created_by) {
			$cls .= ' author';
		}

		$name = JText::_('COM_KB_ANONYMOUS');

		$xuser = Hubzero_User_Profile::getInstance($comment->created_by);
		if (!$comment->anonymous) {
			if (is_object($xuser) && $xuser->get('name')) {
				$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$comment->created_by).'">'.$this->escape(stripslashes($xuser->get('name'))).'</a>';
			}
		}

		if ($comment->reports) {
			$content = '<p class="warning">'.JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
		} else {
			$content = $p->parse($comment->content, $wikiconfig);
		}

		$comment->like = 0;
		$comment->dislike = 0;
?>
		<li class="comment <?php echo $cls; ?>" id="c<?php echo $comment->id; ?>">
			<p class="comment-member-photo">
				<span class="comment-anchor"><!-- <a name="#c<?php echo $comment->id; ?>"></a> --></span>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($xuser, $comment->anonymous); ?>" alt="" />
			</p>
			<div class="comment-content">
				<p class="comment-voting voting">
					<?php
						$view = new JView(array(
							'name' => $this->controller,
							'layout' => 'vote'
						));
						$view->option = $this->option;
						$view->item = $comment;
						$view->type = 'comment';
						$view->vote = (isset($comment->vote)) ? $comment->vote : '';
						$view->id = $comment->id;
						$view->display();
					?>
				</p><!-- / .voting -->
				<p class="comment-title">
					<strong><?php echo $name; ?></strong> 
					<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#c'.$comment->id); ?>" title="<?php echo JText::_('COM_KB_PERMALINK'); ?>">
						<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $timeformat, $tz); ?></time></span> 
						<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $dateformat, $tz); ?></time></span>
					</a>
				</p>
				<?php echo $content; ?>
			<?php if (!$comment->reports) { ?>
				<p class="comment-options">
				<?php
				if ($this->config->get('close_comments') == 'never' || ($this->config->get('close_comments') != 'now' && $today < $pdt)) {
					$rtrn = JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'?reply='.$comment->id.'#post-comment');
					if ($this->juser->get('guest')) {
						$lnk = '/login?return='. base64_encode($rtrn);
					} else {
						$lnk = $rtrn;
					}
				?>
					<a class="icon-reply reply" href="<?php echo $lnk; ?>"><?php echo JText::_('Reply'); ?></a>
				<?php } ?>
					<a class="icon-abuse abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=kb&id='.$comment->id.'&parent='.$this->article->id); ?>">Report abuse</a>
				</p>
			<?php } ?>
			</div>
<?php
		if ($comment->replies) {
?>
			<ol class="comments">
			<?php
			foreach ($comment->replies as $reply)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				if ($this->article->created_by == $reply->created_by) {
					$cls .= ' author';
				}

				$name = JText::_('COM_KB_ANONYMOUS');

				$xuser = Hubzero_User_Profile::getInstance($reply->created_by);

				if (!$reply->anonymous) {
					if (is_object($xuser) && $xuser->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$reply->created_by).'">'.$this->escape(stripslashes($xuser->get('name'))).'</a>';
					}
				}

				if ($reply->reports) {
					$content = '<p class="warning">'.JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
				} else {
					$content = $p->parse($reply->content, $wikiconfig);
				}

				$reply->like = 0;
				$reply->dislike = 0;
				?>
				<li class="comment <?php echo $cls; ?>" id="c<?php echo $reply->id; ?>">
					<p class="comment-member-photo">
						<span class="comment-anchor"><!-- <a name="#c<?php echo $reply->id; ?>"></a> --></span>
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($xuser, $reply->anonymous); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="comment-voting voting">
							<?php
							$view = new JView(array(
								'name' => $this->controller,
								'layout' => 'vote'
							));
							$view->option = $this->option;
							$view->item = $reply;
							$view->type = 'comment';
							$view->vote = (isset($reply->vote)) ? $reply->vote : '';
							$view->id = $reply->id;
							$view->display();
							?>
						</p><!-- / .voting -->
						<p class="comment-title">
							<strong><?php echo $name; ?></strong> 
							<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#c'.$reply->id); ?>" title="<?php echo JText::_('COM_KB_PERMALINK'); ?>">
								<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, $timeformat, $tz); ?></time></span> 
								<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, $dateformat, $tz); ?></time></span>
							</a>
						</p>
						<?php echo $content; ?>
					<?php if (!$reply->reports) { ?>
						<p class="comment-options">
						<?php if ($this->config->get('close_comments') == 'never' || ($this->config->get('close_comments') != 'now' && $today < $pdt)) { ?>
							<a class="icon-reply reply" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'?reply='.$reply->id.'#post-comment'); ?>">Reply</a>
						<?php } ?>
							<a class="icon-abuse abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=kb&id='.$reply->id.'&parent='.$this->article->id); ?>">Report abuse</a>
						</p>
					<?php } ?>
					</div>
<?php
					if ($reply->replies) {
?>
					<ol class="comments">
<?php
					foreach ($reply->replies as $response)
					{
						$cls = ($cls == 'even') ? 'odd' : 'even';

						if ($this->article->created_by == $response->created_by) {
							$cls .= ' author';
						}

						$name = JText::_('COM_KB_ANONYMOUS');
						$xuser = Hubzero_User_Profile::getInstance($response->created_by);
						if (!$response->anonymous) {
							if (is_object($xuser) && $xuser->get('name')) {
								$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$response->created_by).'">'.$this->escape(stripslashes($xuser->get('name'))).'</a>';
							}
						}

						if ($response->reports) {
							$content = '<p class="warning">'.JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
						} else {
							$content = $p->parse($response->content, $wikiconfig);
						}

						$response->like = 0;
						$response->dislike = 0;
?>
						<li class="comment <?php echo $cls; ?>" id="c<?php echo $response->id; ?>">
							<p class="comment-member-photo">
								<span class="comment-anchor"><!-- <a name="#c<?php echo $response->id; ?>"></a> --></span>
								<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($xuser, $response->anonymous); ?>" alt="" />
							</p>
							<div class="comment-content">
								<p class="comment-voting voting">
								<?php
									$view = new JView(array(
										'name' => $this->controller,
										'layout' => 'vote'
									));
									$view->option = $this->option;
									$view->item = $response;
									$view->type = 'comment';
									$view->vote = (isset($response->vote)) ? $response->vote : '';
									$view->id = $response->id;
									$view->display();
								?>
								</p><!-- / .voting -->
								<p class="comment-title">
									<strong><?php echo $name; ?></strong> 
									<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#c'.$response->id); ?>" title="<?php echo JText::_('COM_KB_PERMALINK'); ?>">
										<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $response->created; ?>"><?php echo JHTML::_('date', $response->created, $timeformat, $tz); ?></time></span> 
										<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $response->created; ?>"><?php echo JHTML::_('date', $response->created, $dateformat, $tz); ?></time></span>
									</a>
								</p>
								<?php echo $content; ?>
							<?php if (!$response->reports) { ?>
								<p class="comment-options">
									<a class="icon-abuse abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=kb&id='.$response->id.'&parent='.$this->article->id); ?>">Report abuse</a>
								</p>
							<?php } ?>
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
	<p class="no-comments"><?php echo JText::_('There are no comments on this entry.'); ?></p>
<?php
}
?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .below -->

<div class="below section">
	<h3 class="post-comment-title">
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
		<form method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias); ?>" id="commentform">
			<p class="comment-member-photo">
				<span class="comment-anchor"><!-- <a name="post-comment"></a> --></span>
			<?php
				if (!$this->juser->get('guest')) {
					$anon = 0;
				} else {
					$anon = 1;
				}
				?>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($this->juser, $anon); ?>" alt="" />
			</p>
			<fieldset>
			<?php
			if (!$this->juser->get('guest')) {
				if ($this->replyto->id) {
					ximport('Hubzero_View_Helper_Html');
					$name = JText::_('COM_KB_ANONYMOUS');
					$xuser = Hubzero_User_Profile::getInstance($this->replyto->created_by);
					if (!$this->replyto->anonymous) {
						//$xuser =& JUser::getInstance($reply->created_by);
						if (is_object($xuser) && $xuser->get('name')) {
							$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$this->replyto->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
						}
					}
				?>
				<blockquote cite="c<?php echo $this->replyto->id ?>">
					<p>
						<strong><?php echo $name; ?></strong> 
						<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $this->replyto->created; ?>"><?php echo JHTML::_('date', $this->replyto->created, $timeformat, $tz); ?></time></span> 
						<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $this->replyto->created; ?>"><?php echo JHTML::_('date', $this->replyto->created, $dateformat, $tz); ?></time></span>
					</p>
					<p><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($this->replyto->content), 300, 0); ?></p>
				</blockquote>
				<?php
				}
			}

			if ($this->config->get('close_comments') == 'never' || ($this->config->get('close_comments') != 'now' && $today < $pdt)) {
			?>
				<label for="commentcontent">
					Your <?php echo ($this->replyto->id) ? 'reply' : 'comments'; ?>: <span class="required">required</span>
				<?php
				if (!$this->juser->get('guest')) {
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('comment[content]', 'commentcontent', '', 'minimal', '40', '15');
				} else {
					$rtrn = JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#post-comment');
					?>
					<p class="warning">
						You must <a href="/login?return=<?php echo base64_encode($rtrn); ?>">log in</a> to post comments.
					</p>
					<?php
				}
				?>
				</label>

				<?php if (!$this->juser->get('guest')) { ?>
				<label id="comment-anonymous-label" for="comment-anonymous">
					<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
					Post anonymously
				</label>

				<p class="submit">
					<input type="submit" name="submit" value="Submit" />
				</p>
				<?php } ?>
			<?php } else { ?>
				<p class="warning">
					<?php echo JText::_('Comments are closed on this entry.'); ?>
				</p>
			<?php } ?>
				<input type="hidden" name="comment[id]" value="0" />
				<input type="hidden" name="comment[entry_id]" value="<?php echo $this->article->id; ?>" />
				<input type="hidden" name="comment[parent]" value="<?php echo $this->replyto->id; ?>" />
				<input type="hidden" name="comment[created]" value="" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $this->juser->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="savecomment" />

				<?php echo JHTML::_('form.token'); ?>

				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('Please keep comments relevant to this entry.'); ?></strong>
					</p>
					<p>
						Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiFormatting'); ?>" class="popup">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</form>
	</div><!-- / .subject -->
</div><!-- / .below -->
<?php //} else { ?>

<?php } // if ($this->config->get('allow_comments')) ?>
