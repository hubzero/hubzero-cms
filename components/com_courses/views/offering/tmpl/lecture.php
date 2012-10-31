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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

ximport('Hubzero_User_Profile');
ximport('Hubzero_User_Profile_Helper');

$unit = $this->course->offering()->unit($this->unit);
if (!$unit)
{
	JError::raiseError(404, JText::_('uh-oh'));
}

$lecture = $unit->assetgroup($this->group);
if (!$lecture)
{
	JError::raiseError(404, JText::_('uh-oh'));
}

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&active=outline';
$current = $unit->assetgroups()->key();

if (!$this->course->offering()->access('view')) { ?>
	<p class="info"><?php echo JText::_('Access to the "Syllabus" section of this course is restricted to members only. You must be a member to view the content.'); ?></p>
<?php } else { ?>
	
	<div id="steps" class="section">
		<p>
			<?php echo $unit->get('title'); ?>
		</p>
		<ol class="steps-<?php echo $unit->assetgroups()->total(); ?> active-<?php echo ($current + 1); ?>">
<?php foreach ($unit->assetgroups() as $key => $assetgroup) { ?>
			<li id="step-<?php echo ($key + 1); ?>"<?php echo ($assetgroup->get('id') == $lecture->get('id')) ? ' class="active"' : ($key <= $current ? ' class="completed"' : ''); ?>><?php echo $this->escape(stripslashes($assetgroup->get('title'))); ?></li>
<?php } ?>
		</ol>
	</div>
	
	<div class="video container" style="text-align: center;">
		<div class="video-wrap" style="width: 640px; margin: 0 auto; text-align: left;">
			<h3>
				<?php echo $lecture->get('title'); ?>
			</h3>
			<img src="/components/com_courses/assets/img/video.png" width="640" height="390" />
			<p>
				<?php // $unit->assetgroups() refers to the groupings (lectures, homeworks). We need children() for individual lectures. 
				//echo $unit->assetgroup()->key();
				//echo $this->group; 
				//echo $current;
				$lecture->key($current);
				//var_dump($unit->isFirst());
				//print_r($lecture->key()); ?>
<?php 
//if ($this->course->offering()->units()->isFirst() && $unit->assetgroups()->isFirst()) { 
if ($unit->isFirst() && $lecture->isFirst()) { ?>
				<span class="prev btn">
					Prev
				</span>
<?php } else { ?>
				<a class="prev btn" href="<?php echo JRoute::_($base . '&a=' . $unit->get('alias') . '&b=' . $lecture->sibling('prev')->get('alias')); ?>">
					Prev
				</a>
<?php } ?>
<?php 

$uAlias = $unit->get('alias');
$gAlias = '';

// If the last unit AND last asstegroup in the unit
if ($this->course->offering()->units()->isLast() && $unit->assetgroups()->isLast()) 
{
	$gAlias = '';
}
else
{
	// If NOT the last assetgroup
	if (!$unit->assetgroups()->isLast())
	{
		$gAlias = $unit->assetgroups()->fetch('next')->get('alias');
	}
	// If the last assetgroup AND NOT the last unit
	if ($unit->assetgroups()->isLast() && !$this->course->offering()->units()->isLast())
	{
		// Get the alias of the next unit
		$next = $this->course->offering()->units()->fetch('next');
		// Make sure it's published
		if ($next->available())
		{
			$uAlias = $next->get('alias');
			// Does the next unit have any assetgroups?
			if ($next->assetgroups()->total())
			{
				// Get the alias of the next assetgroup
				$gAlias = $next->assetgroups(0)->get('alias');
			}
		}
	}
}

if (!$uAlias || !$gAlias) { ?>
				<span class="next btn">
					Next
				</span>
<?php } else { ?>
				<a class="next btn" href="<?php echo JRoute::_($base . '&a=' . $uAlias . '&b=' . $gAlias); ?>">
					Next
				</a>
<?php } ?>
			</p>
			<p>
				<?php echo $lecture->get('description'); ?>
			</p>
		</div>
	</div>
	
	<div class="below section">
			<h3>
				<a name="comments"></a>
				<?php echo JText::_('Discussion for this lecture'); ?>
			</h3>

			<!-- <div class="aside">
				<p>
					<a class="add btn" href="#post-comment">
						Add a comment			</a>
				</p>
			</div>/ .aside 
			<div class="subject">-->
				<ol class="comments">
					<li class="comment odd" id="c1">
						<a name="#c1"></a>
						<p class="comment-member-photo">
							<img src="/site/members/01058/Batman-by-Alex-Ross2_thumb.jpg" alt="" />
						</p>
						<div class="comment-content">
							<p class="comment-title">
								<strong><a href="/members/1058">Bruce Wayne</a></strong> 
								<a class="permalink" href="/blog/2012/09/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit#c1" title="permalink">
									<span class="comment-date-at">@</span> <span class="time"><time datetime="2012-09-07 16:42:36">04:42 PM</time></span> 
									<span class="comment-date-on">on</span> <span class="date"><time datetime="2012-09-07 16:42:36">07 Sep 2012</time></span>
								</a>
							</p>
						<p>Phasellus a turpis ac magna aliquam fermentum vel ac nisl. Aenean aliquam velit hendrerit dolor ultricies gravida. Nullam vehicula dolor at velit egestas vitae mollis leo faucibus. Duis non elit vel nibh ultricies ornare. Pellentesque eget viverra ipsum. Mauris odio eros, adipiscing a lobortis vitae, aliquam nec lectus.
		</p>					<p class="comment-options">
								<a class="abuse" href="/support/reportabuse/1?category=blog&amp;parent=2">Report abuse</a> | 
								<a class="reply" href="/blog/2012/09/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit?reply=1#post-comment">Reply</a>
							</p>
						</div>
					</li>
					<li class="comment even" id="c2">
						<a name="#c2"></a>
						<p class="comment-member-photo">
							<img src="/site/members/01059/4960213_f520_thumb.jpg" alt="" />
						</p>
						<div class="comment-content">
							<p class="comment-title">
								<strong><a href="/members/1059">Clark Kent</a></strong> 
								<a class="permalink" href="/blog/2012/09/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit#c2" title="permalink">
									<span class="comment-date-at">@</span> <span class="time"><time datetime="2012-09-10 07:54:11">07:54 AM</time></span> 
									<span class="comment-date-on">on</span> <span class="date"><time datetime="2012-09-10 07:54:11">10 Sep 2012</time></span>
								</a>
							</p>
							<p>Etiam nulla eros, convallis ut eleifend sed, ullamcorper at dolor. Praesent vitae arcu at ipsum laoreet ornare viverra quis tellus. In tortor tortor, euismod hendrerit mollis sit amet, tristique sed justo. Maecenas quis dolor nulla, sit amet dapibus sem. Quisque sagittis erat quis massa rhoncus eleifend. Vestibulum sollicitudin dui eget est rhoncus sed auctor arcu semper. Suspendisse sem diam, euismod vitae dapibus at, fermentum ut arcu.
</p><p><strong>Macro &#8220;fileindex&#8221; not allowed.</strong>
</p><p>Ut dapibus ultrices lacus, et cursus sem mattis vitae. Sed convallis feugiat turpis sit amet auctor. Nulla rhoncus, nunc in dictum suscipit, elit dolor mollis dui, vitae rutrum elit nunc in lacus. Morbi nibh elit, fermentum quis pellentesque ac, varius non nulla. Ut massa tellus, euismod et dapibus vel, laoreet vitae enim.
</p><p>&#8212;&#8212;
</p><p>Ut posuere adipiscing ligula et molestie. Proin neque arcu, tincidunt in fermentum non, viverra sed augue. Donec quis odio sagittis nunc dignissim sollicitudin porttitor vel nisi.
</p>							<p class="comment-options">
															<a class="abuse" href="/support/reportabuse/2?category=blog&amp;parent=2">Report abuse</a> | 
								<a class="reply" href="/blog/2012/09/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit?reply=2#post-comment">Reply</a>
							</p>
						</div>
					</li>
					<li class="comment odd author" id="c4">
						<a name="#c4"></a>
						<p class="comment-member-photo">
							<img src="/site/members/01008/z_thumb.png" alt="" />
						</p>
						<div class="comment-content">
							<p class="comment-title">
								<strong><a href="/members/1008">Shawn Rice</a></strong> 
								<a class="permalink" href="/blog/2012/09/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit#c4" title="permalink">
									<span class="comment-date-at">@</span> <span class="time"><time datetime="2012-09-10 08:03:54">08:03 AM</time></span> 
									<span class="comment-date-on">on</span> <span class="date"><time datetime="2012-09-10 08:03:54">10 Sep 2012</time></span>
								</a>
							</p>
							<p>Donec sed diam a quam adipiscing sollicitudin ut vitae purus. Aenean bibendum scelerisque erat id dapibus. Quisque ut condimentum urna. Vivamus a urna et orci commodo dictum nec non sapien. Phasellus urna massa, bibendum et imperdiet eu, commodo et sem.
</p>
<strong>Wiki HTML blocks not allowed</strong>
<p>Etiam bibendum luctus ipsum, ac <b>tincidunt leo</b> pulvinar ut. Ut ac mauris nisl.
</p>									<p class="comment-options">
																	<a class="abuse" href="/support/reportabuse/4?category=blog&amp;parent=2">Report abuse</a>
							</p>
						</div>
					</li>
					<li class="comment even" id="c3">
						<a name="#c3"></a>
						<p class="comment-member-photo">
							<img src="/site/members/01059/4960213_f520_thumb.jpg" alt="" />
						</p>
						<div class="comment-content">
							<p class="comment-title">
								<strong><a href="/members/1059">Clark Kent</a></strong> 
								<a class="permalink" href="/blog/2012/09/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit#c3" title="permalink">
									<span class="comment-date-at">@</span> <span class="time"><time datetime="2012-09-10 07:57:56">07:57 AM</time></span> 
									<span class="comment-date-on">on</span> <span class="date"><time datetime="2012-09-10 07:57:56">10 Sep 2012</time></span>
								</a>
							</p>
						<p>Cras imperdiet scelerisque sapien nec pellentesque. Aliquam vitae nulla rutrum lorem consequat convallis. Nullam vel risus ac nunc eleifend hendrerit a at metus. Suspendisse sed elit ante, eu mollis mi. Vivamus molestie odio sit amet tortor euismod vestibulum. Phasellus ipsum est, condimentum ut facilisis at, congue quis lectus.
		</p>					<p class="comment-options">
								<a class="abuse" href="/support/reportabuse/3?category=blog&amp;parent=2">Report abuse</a> | 
								<a class="reply" href="/blog/2012/09/lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit?reply=3#post-comment">Reply</a>
							</p>
						</div>
					</li>
		</ol>
	</div>
	<div class="section below">
		<h3>
			<a name="post-comment"></a>
			Post a comment
		</h3>
		<form method="post" action="<?php echo JRoute::_($base . '&a=' . $unit->get('alias') . '&b=' . $lecture->get('alias')); ?>" id="commentform">
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
				<label>
					Your comments: <span class="required">required</span>
	<?php
				if (!$juser->get('guest')) {
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('comment[content]', 'commentcontent', '', '', '40', '15');
				} else { 
					$rtrn = JRoute::_($base . '&a=' . $unit->get('alias') . '&b=' . $lecture->get('alias') . '#post-comment');
	?>
					<p class="warning">
						You must <a href="/login?return=<?php echo base64_encode($rtrn); ?>">log in</a> to post comments.
					</p>
	<?php
				}
	?>
				</label>

	<?php if (!$juser->get('guest')) { ?>
				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1" />
					Post anonymously
				</label>

				<p class="submit">
					<input type="submit" name="submit" value="Submit" />
				</p>
	<?php } ?>
				<input type="hidden" name="comment[id]" value="0" />
				<input type="hidden" name="comment[entry_id]" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="comment[parent]" value="0" />
				<input type="hidden" name="comment[created]" value="" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $juser->get('id'); ?>" />

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
				<input type="hidden" name="unit" value="<?php echo $unit->get('alias'); ?>" />
				<input type="hidden" name="group" value="<?php echo $lecture->get('alias'); ?>" />
				<input type="hidden" name="active" value="outline" />
				<input type="hidden" name="task" value="view" />
				<input type="hidden" name="action" value="savecomment" />

				<div class="sidenote">
					<p>
						<strong>Please keep comments relevant to this entry.</strong>
					</p>
					<p>
						Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiFormatting'); ?>">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</form>
	</div><!-- /.subject -->
<?php } ?>