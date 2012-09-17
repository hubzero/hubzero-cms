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
?>
<?php if ($this->canpost || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
	<ul id="page_options">
	<?php if ($this->canpost) { ?>
		<li>
			<a class="add btn" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=new'); ?>">
				<?php echo JText::_('New entry'); ?>
			</a>
		</li>
	<?php } ?>
	<?php if ($this->authorized == 'manager' || $this->authorized == 'admin') { ?>
		<li>
			<a class="config btn" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=settings'); ?>" title="<?php echo JText::_('Edit Settings'); ?>">
				<?php echo JText::_('Settings'); ?>
			</a>
		</li>
	<?php } ?>
	</ul>
<?php } ?>

<div class="entry-container">
	<div class="aside">
		<div class="blog-popular-entries">
			<h4><?php echo JText::_('Popular Entries'); ?></h4>
			<ol>
				<?php if ($this->popular) { ?>
					<?php foreach ($this->popular as $row) { ?>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>">
								<?php echo stripslashes($row->title); ?>
							</a>
						</li>
					<?php } ?>
				<?php } ?>
			</ol>
		</div><!-- / .blog-popular-entries -->
		
		<div class="blog-recent-entries">
			<h4><?php echo JText::_('Recent Entries'); ?></h4>
			<ol>
				<?php if ($this->recent) { ?>
					<?php foreach ($this->recent as $row) { ?>
						<li>
							<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias); ?>">
								<?php echo stripslashes($row->title); ?>
							</a>
						</li>
					<?php } ?>
				<?php } ?>
			</ol>
		</div><!-- / .blog-recent-entries -->
	</div><!-- /.aside -->
	
	<div class="subject">
		<div class="entry" id="e<?php echo $this->row->id; ?>">
			<h2 class="entry-title">
				<?php echo stripslashes($this->row->title); ?>
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
					<a href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date', $this->row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date', $this->row->publish_up, $this->yearFormat, $this->tz).'/'.$this->row->alias.'#comments'); ?>">
						<?php echo JText::sprintf('PLG_GROUPS_BLOG_NUM_COMMENTS', $this->comment_total); ?>
					</a>
				</dd>
			<?php } else { ?>
				<dd class="comments">
					<span>
						<?php echo JText::_('PLG_GROUPS_BLOG_COMMENTS_OFF'); ?>
					</span>
				</dd>
			<?php } ?>
			<?php if ($this->juser->get('id') == $this->row->created_by || $this->authorized == 'manager' || $this->authorized == 'admin') { ?>
				<dd class="state">
				<?php 
					switch ($this->row->state)
					{
						case 1:
							$state = JText::_('Public');
							break;
						case 2:
							$state = JText::_('Registered members');
							break;
						case 0:
						default:
							$state = JText::_('Private');
							break;
					}
					echo $state;
				?>
				</dd>
				<dd class="entry-options">
					<a class="edit" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=edit&entry='.$this->row->id); ?>" title="<?php echo JText::_('Edit'); ?>">
						<span><?php echo JText::_('Edit'); ?></span>
					</a>
					<a class="delete" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&task=delete&entry='.$this->row->id); ?>" title="<?php echo JText::_('Delete'); ?>">
						<span><?php echo JText::_('Delete'); ?></span>
					</a>
				</dd>
			<?php } ?>
			</dl>

			<div class="entry-content">
				<?php echo $this->row->content; ?>
				<?php echo $this->tags; ?>
			</div>

			<?php 
			$author = Hubzero_User_Profile::getInstance($this->row->created_by);
			if (is_object($author) && $author->get('name')) 
			{
			?>
			<div class="entry-author">
				<h3><?php echo JText::_('About the author'); ?></h3>
				<p class="entry-author-photo"><img src="<?php echo BlogHelperMember::getMemberPhoto($author, 0); ?>" alt="" /></p>
				<div class="entry-author-content">
					<h4>
						<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->row->created_by); ?>">
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

<?php if ($this->row->allow_comments == 1) { ?>
		
		<?php
			/* How to do this ... ?
			$feed = JRoute::_('index.php?option=com_members&id='.$this->row->created_by.'&active=blog&task='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'/comments.rss');
			if (substr($feed, 0, 4) != 'http') {
				if (substr($feed, 0, 1) != DS) {
					$feed = DS.$feed;
				}
				$jconfig =& JFactory::getConfig();
				$live_site = rtrim(JURI::base(),'/');

				$feed = $live_site . $feed;
			}
			$feed = str_replace('https:://','http://',$feed);

			<a class="feed" href="<?php echo $feed; ?>" title="<?php echo JText::_('Comments RSS Feed'); ?>"><?php echo JText::_('Comments RSS Feed'); ?></a>
			*/
		?>

		<div class="aside aside-below">
			<p>
				<a class="add btn" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#post-comment'); ?>">
					<?php echo JText::_('Add a comment'); ?>
				</a>
			</p>
		</div><!-- / .aside -->
		
		<div class="subject below">
			<h3 class="below_heading">
				<a name="comments"></a>
				<?php echo JText::_('Comments on this entry'); ?>
			</h3>
			<?php if ($this->comments) { ?>
				<ol class="comments">
					<?php foreach ($this->comments as $comment) { ?>
						<?php
							$name = JText::_('PLG_GROUPS_BLOG_ANONYMOUS');
							$xuser = "";
							if (!$comment->anonymous) {
								//$xuser =& JUser::getInstance($comment->created_by);
								$xuser = Hubzero_User_Profile::getInstance($comment->created_by);
								if (is_object($xuser) && $xuser->get('name')) {
									$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$comment->created_by).'">'.$this->escape(stripslashes($xuser->get('name'))).'</a>';
								}
							}

							if ($comment->reports) {
								$content = '<p class="warning">'.JText::_('PLG_GROUPS_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
							} else {
								$content = $this->p->parse("\n" . stripslashes($comment->content), $this->wikiconfig, false);
							}
						?>
						<li class="comment <?php echo $cls; ?>" id="c<?php echo $comment->id; ?>">
							<a name="c<?php echo $comment->id; ?>"></a>
							<p class="comment-member-photo">
								<img src="<?php echo BlogHelperMember::getMemberPhoto($xuser, $comment->anonymous); ?>" alt="" />
							</p>
							<div class="comment-content">
								<p class="comment-title">
									<strong><?php echo $name; ?></strong> 
									<a class="permalink" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz).'/'.$this->row->alias.'#c'.$comment->id); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_PERMALINK'); ?>">
										<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $this->timeFormat, $this->tz); ?></time></span> 
										<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $comment->created; ?>"><?php echo JHTML::_('date', $comment->created, $this->dateFormat, $this->tz); ?></time></span>
									</a>
								</p>
								<?php echo $content; ?>
							
								<?php if (!$comment->reports) { ?>
									<p class="comment-options">
										<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$comment->id.'&parent='.$this->row->id); ?>">Report abuse</a> | 
										<a class="reply" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz).'/'.$this->row->alias.'?reply='.$comment->id.'#post-comment'); ?>">Reply</a>
									</p>
								<?php } ?>
							</div>
							
							<?php if ($comment->replies) { ?>
								<ol class="comments">
									<?php foreach ($comment->replies as $reply) { ?>
										<?php 
											$name = JText::_('PLG_GROUPS_BLOG_ANONYMOUS');
											$xuser = "";
											if (!$reply->anonymous) {
												$xuser = Hubzero_User_Profile::getInstance($reply->created_by);
												if (is_object($xuser) && $xuser->get('name')) {
													$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$reply->created_by).'">'.$this->escape(stripslashes($xuser->get('name'))).'</a>';
												}
											}

											if ($reply->reports) {
												$content = '<p class="warning">'.JText::_('PLG_GROUPS_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
											} else {
												$content = $this->p->parse("\n" . stripslashes($reply->content), $this->wikiconfig, false);
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
													<a class="permalink" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz).'/'.$this->row->alias.'#c'.$reply->id); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_PERMALINK'); ?>">
														<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, $this->timeFormat, $this->tz); ?></time></span> 
														<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $reply->created; ?>"><?php echo JHTML::_('date', $reply->created, $this->dateFormat, $this->tz); ?></time></span>
													</a>
												</p>
												<?php echo $content; ?>
												
												<?php if (!$reply->reports) { ?>
													<p class="comment-options">
														<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$reply->id.'&parent='.$this->row->id); ?>">Report abuse</a> | 
														<a class="reply" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz).'/'.$this->row->alias.'?reply='.$reply->id.'#post-comment'); ?>">Reply</a>
													</p>
												<?php } ?>
											</div>
											<?php if ($reply->replies) { ?>
												<ol class="comments">
													<?php foreach ($reply->replies as $response) { ?>
														<?php 
															$name = JText::_('PLG_GROUPS_BLOG_ANONYMOUS');
															$xuser = "";
															if (!$response->anonymous) {
																$xuser = Hubzero_User_Profile::getInstance($response->created_by);
																if (is_object($xuser) && $xuser->get('name')) {
																	$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$response->created_by).'">'.stripslashes($xuser->get('name')).'</a>';
																}
															}

															if ($response->reports) {
																$content = '<p class="warning">'.JText::_('PLG_GROUPS_BLOG_COMMENT_REPORTED_AS_ABUSIVE').'</p>';
															} else {
																$content = $this->p->parse("\n" . stripslashes($response->content), $this->wikiconfig , false);
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
																	<a class="permalink" href="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, $this->yearFormat, $this->tz).'/'.JHTML::_('date',$this->row->publish_up, $this->monthFormat, $this->tz).'/'.$this->row->alias.'#c'.$response->id); ?>" title="<?php echo JText::_('PLG_GROUPS_BLOG_PERMALINK'); ?>">
																		<span class="comment-date-at">@</span> <span class="time"><time datetime="<?php echo $response->created; ?>"><?php echo JHTML::_('date', $response->created, $this->timeFormat, $this->tz); ?></time></span> 
																		<span class="comment-date-on">on</span> <span class="date"><time datetime="<?php echo $response->created; ?>"><?php echo JHTML::_('date', $response->created, $this->dateFormat, $this->tz); ?></time></span>
																	</a>
																</p>
																<?php echo $content; ?>

																<?php if (!$response->reports) { ?>
																	<p class="comment-options">
																		<a class="abuse" href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=blog&id='.$response->id.'&parent='.$this->row->id); ?>">Report abuse</a>
																	</p>
																<?php } ?>
															</div>
														<li>
													<?php } ?>
												</ol>
											<?php } ?>
										<li>
									<?php } ?>
								</ol>
							<?php } ?>
						</li>
					<?php } ?>
				</ol>
			<?php } else { ?>
				<p class="no-comments">There are no comments at this time.</p>
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
				Post a comment
			</h3>
			
			<form method="post" action="<?php echo JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias); ?>" id="commentform">
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
						if ($this->replyto->id) {
							ximport('Hubzero_View_Helper_Html');
							$name = JText::_('PLG_GROUPS_BLOG_ANONYMOUS');
							if (!$this->replyto->anonymous) {
								$xuser = Hubzero_User_Profile::getInstance($this->replyto->created_by);
								if (is_object($xuser) && $xuser->get('name')) {
									$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$this->replyto->created_by).'">'.$this->escape(stripslashes($xuser->get('name'))).'</a>';
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
									<?php 
										$reply_content = Hubzero_View_Helper_Html::shortenText(stripslashes($this->replyto->content), 300, 0);
										echo $this->p->parse("\n".stripslashes($reply_content), $this->wikiconfig, false);
									?>
								</p>
							</blockquote>
					<?php } ?>
				
					<?php if (!$this->juser->get('guest')) { ?>
						<label>
							Your <?php echo ($this->replyto->id) ? 'reply' : 'comments'; ?>: <span class="required">required</span>
							<?php echo $editor->display('comment[content]', 'comment_content', '', '', '40', '15'); ?>
							
						</label>
					
						<label id="comment-anonymous-label">
							<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
							Post anonymously
						</label>
			
						<p class="submit">
							<input type="submit" name="submit" value="Submit" />
						</p>
					<?php } else { ?>
						<?php $rtrn = JRoute::_('index.php?option=com_groups&gid='.$this->group->cn.'&active=blog&scope='.JHTML::_('date',$this->row->publish_up, '%Y', 0).'/'.JHTML::_('date',$this->row->publish_up, '%m', 0).'/'.$this->row->alias.'#post-comment'); ?>
						<p class="warning">
							You must <a href="/login?return=<?php echo base64_encode($rtrn); ?>">log in</a> to post comments.
						</p>
					<?php } ?>
				
					<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
					<input type="hidden" name="comment[id]" value="0" />
					<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->id; ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->replyto->id; ?>" />
					<input type="hidden" name="comment[created]" value="" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->juser->get('id'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="active" value="blog" />
					<input type="hidden" name="task" value="savecomment" />
			
					<div class="sidenote">
						<p><strong>Please keep comments relevant to this entry.</strong></p>
						<p>Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/topics/Help:WikiFormatting" class="popup">Wiki syntax</a> is supported.</p>
					</div>
				</fieldset>
			</form>
		</div><!-- / .subject -->
<?php } //end if allow comments ?>
</div><!-- /.entry-container -->