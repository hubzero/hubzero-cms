<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<p>
		<a href="<?php echo JRoute::_('index.php?option='.$this->option); ?>">&larr; Main page</a>
	</p>
</div>
<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<div class="aside">
		<div class="container">
			<h3>Categories</h3>
<?php
		$html = '<ul class="categories">'."\n";
		if ($this->catid == 0) {
			$cls = ' class="active"';
		} else {
			$cls = '';
		}
		$html .= "\t".'<li><a'.$cls.' href="'.JRoute::_('index.php?option='.$this->option.'&section=all').'">'.JText::_('All Articles').'</li>'."\n";
		if (count($this->categories) > 0) {
			foreach ($this->categories as $row) 
			{
				$html .= "\t".'<li><a ';
				if ($this->catid == $row->id) {
					$html .= ' class="active"';
				}
				$html .= 'href="'.JRoute::_('index.php?option='.$this->option.'&section='.$row->alias).'">'.Hubzero_View_Helper_Html::xhtml($row->title).'</a>';
				if (count($this->subcategories) > 0 && $this->catid == $row->id) {
					//$html .= '<h4>'.JText::_('SUBCATEGORIES').'</h4>'."\n";
					$html .= "\t".'<ul class="categories">'."\n";
					foreach ($this->subcategories as $cat) 
					{
						$html .= "\t\t".'<li><a ';
						if ($this->article->category == $cat->id) {
							$html .= ' class="active"';
						}
						$html .= 'href="'. JRoute::_('index.php?option='.$this->option.'&section='.$this->category->alias.'&category='. $cat->alias) .'">'. stripslashes($cat->title) .'</a> ('.$cat->numitems.')</li>'."\n";
					}
					$html .= "\t".'</ul>'."\n";
				}
				$html .= '</li>'."\n";
			}
		}
		$html .= '</ul>'."\n";
		
		echo $html;
?>
		</div><!-- / .container -->
	</div><!-- / .aside -->
	<div class="subject">
		<div class="container" id="entry-<?php echo $this->article->id; ?>">
			<div class="container-block">
				<h3><?php echo stripslashes($this->article->title); ?></h3>
				<div class="entry-content">
					<?php echo stripslashes( $this->article->fulltext ); ?>
				</div>
<?php 
		if (count($this->tags) > 0) { 
?>
				<div class="entry-tags">
					<p>Tags:</p>			
					<ol class="tags">
<?php
					foreach ($this->tags as $tag)
					{
						$tag['raw_tag'] = str_replace( '&amp;', '&', $tag['raw_tag'] );
						$tag['raw_tag'] = str_replace( '&', '&amp;', $tag['raw_tag'] );
						
						echo "\t\t\t\t\t\t".'<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>'."\n";
					}
?>
					</ol>
				</div><!-- / .entry-tags -->
<?php 
		}
?>			
				<p class="voting">
<?php 
	$view = new JView( array('name'=>'vote') );
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
					<span class="entry-time"><?php echo JHTML::_('date',$this->article->modified, '%I:%M %p', 0); ?></span> on 
					<span class="entry-date"><?php echo JHTML::_('date',$this->article->modified, '%d %b %Y', 0); ?></span>
				</p>
				
				<div class="clearfix"></div>
			</div><!-- / .container-block -->
		</div><!-- / .container -->
<?php 
if ($this->config->get('allow_comments')) { 
	$d = ($this->article->modified) ? $this->article->modified : $this->article->created;
	$year = intval(substr($d,0,4));
	$month = intval(substr($d,5,2));
	$day = intval(substr($d,8,2));

	switch ($this->config->get('close_comments')) 
	{
		case 'day': 
			$dt = mktime(0,0,0,$month,($day+1),$year);
		break;
		case 'week': 
			$dt = mktime(0,0,0,$month,($day+7),$year);
		break;
		case 'month':
			$dt = mktime(0,0,0,($month+1),$day,$year);
		break;
		case '6months':
			$dt = mktime(0,0,0,($month+6),$day,$year);
		break;
		case 'year':
			$dt = mktime(0,0,0,$month,$day,($year+1));
		break;
		case 'never': 
		default:
			$dt =mktime(0,0,0,$month,$day,$year);
		break;
	}

	$pdt = strftime("%Y", $dt ).'-'.strftime("%m", $dt ).'-'.strftime("%d", $dt ).' 00:00:00';
	
	$today = date( 'Y-m-d H:i:s', time() );
?>		
<div class="below">
	<h3 class="comments-title">
		<a name="comments"></a>
		Comments on this entry
<?php
if ($this->config->get('feeds_enabled')) {
	if ($this->comment_total > 0) {
		$feed = JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'/comments.rss');
		if (substr($feed, 0, 4) != 'http') {
			if (substr($feed, 0, 1) != DS) {
				$feed = DS.$feed;
			}
			$jconfig =& JFactory::getConfig();
			$feed = $jconfig->getValue('config.live_site').$feed;
		}
		$feed = str_replace('https:://','http://',$feed);
?>
		<a class="feed" href="<?php echo $feed; ?>" title="<?php echo JText::_('Comments Feed'); ?>"><?php echo JText::_('Comments Feed'); ?></a>
<?php 
	} 
}
?>
	</h3>
<?php 
ximport('Hubzero_User_Profile');
ximport('Hubzero_User_Profile_Helper');
if ($this->comments) {
?>
	<ol class="comments">
<?php 
	$cls = 'even';

	JPluginHelper::importPlugin( 'hubzero' );
	$dispatcher =& JDispatcher::getInstance();
	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => '',
		'pagename' => $this->article->alias,
		'pageid'   => $this->article->id,
		'filepath' => '',
		'domain'   => '' 
	);
	$result = $dispatcher->trigger( 'onGetWikiParser', array($wikiconfig, true) );
	$p = (is_array($result) && !empty($result)) ? $result[0] : null;
	
	foreach ($this->comments as $comment) 
	{
		$cls = ($cls == 'even') ? 'odd' : 'even';
		
		if ($this->article->created_by == $comment->created_by) {
			$cls .= ' author';
		}
		
		$name = JText::_('COM_KB_ANONYMOUS');
		if (!$comment->anonymous) {
			//$xuser =& JUser::getInstance( $comment->created_by );
			$xuser = new Hubzero_User_Profile();
			$xuser->load( $comment->created_by );
			if (is_object($xuser) && $xuser->get('name')) {
				$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$comment->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
			}
		}
		
		if ($comment->reports) {
			$content = '<p class="warning">'.JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
		} else {
			$content = (is_object($p)) ? $p->parse( stripslashes($comment->content) ) : nl2br(stripslashes($comment->content));
		}
		
		$comment->like = 0;
		$comment->dislike = 0;
?>
		<li class="comment <?php echo $cls; ?>" id="c<?php echo $comment->id; ?>">
			<p class="comment-member-photo">
				<span class="comment-anchor"><a name="#c<?php echo $comment->id; ?>"></a></span>
				<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($xuser, $comment->anonymous); ?>" alt="" />
			</p>
			<div class="comment-content">
				<p class="voting">
<?php
						$view = new JView( array('name'=>'vote') );
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
					<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#c'.$comment->id); ?>" title="<?php echo JText::_('COM_KB_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$comment->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$comment->created, '%d %b, %Y', 0); ?></span></a>
				</p>
				<?php echo $content; ?>
<?php 		if (!$comment->reports) { ?>
				<p class="comment-options">
					<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=kb&id='.$comment->id.'&parent='.$this->article->id); ?>">Report abuse</a> | 
<?php
if ($this->config->get('close_comments') == 'never' || ($this->config->get('close_comments') != 'now' && $today < $pdt)) { 
	$rtrn = JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'?reply='.$comment->id.'#post-comment');
	if ($this->juser->get('guest')) {
		$lnk = '/login?return='. base64_encode($rtrn);
	} else {
		$lnk = $rtrn;
	}
?>
					<a class="reply" href="<?php echo $lnk; ?>">Reply</a>
<?php } ?>
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

				if ($this->article->created_by == $reply->created_by) {
					$cls .= ' author';
				}

				$name = JText::_('COM_KB_ANONYMOUS');
				if (!$reply->anonymous) {
					//$xuser =& JUser::getInstance( $reply->created_by );
					$xuser = new Hubzero_User_Profile();
					$xuser->load( $reply->created_by );
					if (is_object($xuser) && $xuser->get('name')) {
						$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$reply->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
					}
				}

				if ($reply->reports) {
					$content = '<p class="warning">'.JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
				} else {
					$content = (is_object($p)) ? $p->parse( stripslashes($reply->content) ) : nl2br(stripslashes($reply->content));
				}
				
				$reply->like = 0;
				$reply->dislike = 0;
?>
				<li class="comment <?php echo $cls; ?>" id="c<?php echo $reply->id; ?>">
					<p class="comment-member-photo">
						<span class="comment-anchor"><a name="#c<?php echo $reply->id; ?>"></a></span>
						<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($xuser, $reply->anonymous); ?>" alt="" />
					</p>
					<div class="comment-content">
						<p class="voting">
<?php
							$view = new JView( array('name'=>'vote') );
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
							<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#c'.$reply->id); ?>" title="<?php echo JText::_('COM_KB_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$reply->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$reply->created, '%d %b, %Y', 0); ?></span></a>
						</p>
						<?php echo $content; ?>
<?php 				if (!$reply->reports) { ?>
						<p class="comment-options">
							<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=kb&id='.$reply->id.'&parent='.$this->article->id); ?>">Report abuse</a> | 
<?php if ($this->config->get('close_comments') == 'never' || ($this->config->get('close_comments') != 'now' && $today < $pdt)) { ?>
							<a class="reply" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'?reply='.$reply->id.'#post-comment'); ?>">Reply</a>
<?php } ?>
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

						if ($this->article->created_by == $response->created_by) {
							$cls .= ' author';
						}

						$name = JText::_('COM_KB_ANONYMOUS');
						if (!$response->anonymous) {
							//$xuser =& JUser::getInstance( $reply->created_by );
							$xuser = new Hubzero_User_Profile();
							$xuser->load( $response->created_by );
							if (is_object($xuser) && $xuser->get('name')) {
								$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$response->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
							}
						}

						if ($response->reports) {
							$content = '<p class="warning">'.JText::_('COM_KB_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
						} else {
							$content = (is_object($p)) ? $p->parse( stripslashes($response->content) ) : nl2br(stripslashes($response->content));
						}
						
						$response->like = 0;
						$response->dislike = 0;
?>
						<li class="comment <?php echo $cls; ?>" id="c<?php echo $response->id; ?>">
							<p class="comment-member-photo">
								<span class="comment-anchor"><a name="#c<?php echo $response->id; ?>"></a></span>
								<img src="<?php echo Hubzero_User_Profile_Helper::getMemberPhoto($xuser, $response->anonymous); ?>" alt="" />
							</p>
							<div class="comment-content">
								<p class="voting">
<?php
									$view = new JView( array('name'=>'vote') );
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
									<a class="permalink" href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->section->alias.'&category='.$this->category->alias.'&alias='.$this->article->alias.'#c'.$response->id); ?>" title="<?php echo JText::_('COM_KB_PERMALINK'); ?>">@ <span class="time"><?php echo JHTML::_('date',$response->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$response->created, '%d %b, %Y', 0); ?></span></a>
								</p>
								<?php echo $content; ?>
<?php 					if (!$response->reports) { ?>
								<p class="comment-options">
									<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=kb&id='.$response->id.'&parent='.$this->article->id); ?>">Report abuse</a>
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
	<p class="no-comments">There are no comments on this entry.</p>
<?php
} 
?>
		</div><!-- / .below -->
	</div><!-- / .subject -->
	
	<div class="clear"></div>
<div class="below">
	<h3 class="post-comment-title">
		Post a comment
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
				<span class="comment-anchor"><a name="post-comment"></a></span>
<?php
				if (!$this->juser->get('guest')) {
					$jxuser = new Hubzero_User_Profile();
					$jxuser->load( $this->juser->get('id') );
					$thumb = Hubzero_User_Profile_Helper::getMemberPhoto($jxuser, 0);
				} else {
					$config =& JComponentHelper::getParams( 'com_members' );
					$thumb = $config->get('defaultpic');
					if (substr($thumb, 0, 1) != DS) {
						$thumb = DS.$dfthumb;
					}
					$thumb = Hubzero_User_Profile_Helper::thumbit($thumb);
				}
?>
				<img src="<?php echo $thumb; ?>" alt="" />
			</p>
			<fieldset>
<?php
			if (!$this->juser->get('guest')) {
				if ($this->replyto->id) {
					ximport('Hubzero_View_Helper_Html');
					$name = JText::_('COM_KB_ANONYMOUS');
					if (!$this->replyto->anonymous) {
						//$xuser =& JUser::getInstance( $reply->created_by );
						$xuser = new Hubzero_User_Profile();
						$xuser->load( $this->replyto->created_by );
						if (is_object($xuser) && $xuser->get('name')) {
							$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$this->replyto->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
						}
					}
?>
				<blockquote cite="c<?php echo $this->replyto->id ?>">
					<p>
						<strong><?php echo $name; ?></strong> 
						@ <span class="time"><?php echo JHTML::_('date',$this->replyto->created, '%I:%M %p', 0); ?></span> on <span class="date"><?php echo JHTML::_('date',$this->replyto->created, '%d %b, %Y', 0); ?></span>
					</p>
					<p><?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($this->replyto->content), 300, 0); ?></p>
				</blockquote>
<?php
				}
			}
			
			if ($this->config->get('close_comments') == 'never' || ($this->config->get('close_comments') != 'now' && $today < $pdt)) {
?>
				<label>
					Your <?php echo ($this->replyto->id) ? 'reply' : 'comments'; ?>: <span class="required">required</span>
<?php
				if (!$this->juser->get('guest')) {
?>
					<textarea name="comment[content]" id="comment-content" rows="15" cols="40"></textarea>
<?php
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

<?php 			if (!$this->juser->get('guest')) { ?>
				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
					Post anonymously
				</label>

				<p class="submit">
					<input type="submit" name="submit" value="Submit" />
				</p>
<?php 			} ?>
<?php 		} else { ?>
	<p class="warning">
		Comments are closed on this entry.
	</p>
<?php 		} ?>
				<input type="hidden" name="comment[id]" value="0" />
				<input type="hidden" name="comment[entry_id]" value="<?php echo $this->article->id; ?>" />
				<input type="hidden" name="comment[parent]" value="<?php echo $this->replyto->id; ?>" />
				<input type="hidden" name="comment[created]" value="" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $this->juser->get('id'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="savecomment" />

				<div class="sidenote">
					<p>
						<strong>Please keep comments relevant to this entry.</strong>
					</p>
					<p>
						Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup 400x500">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</form>
	</div><!-- / .subject -->
	</div><!-- / .below -->
<?php } else { ?>
	</div><!-- / .subject -->
<?php } // if ($this->config->get('allow_comments')) ?>
</div><!-- / .main section -->