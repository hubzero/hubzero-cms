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
defined('_JEXEC') or die( 'Restricted access' );

$mode = $this->page->params->get('mode', 'wiki');
?>
	<div id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->escape($this->title); ?></h2>
<?php
if (!$mode || ($mode && $mode != 'static')) 
{
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'authors'
	));
	$view->option   = $this->option;
	$view->page     = $this->page;
	$view->task     = $this->task;
	$view->config   = $this->config;
	//$view->revision = $first;
	$view->display();
}
?>
	</div><!-- /#content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php /*if ($this->warning) { ?>
	<p class="warning"><?php echo $this->warning; ?></p>
<?php }*/ ?>

<?php
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'submenu'
	));
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->page   = $this->page;
	$view->task   = $this->task;
	$view->config = $this->config;
	$view->sub    = $this->sub;
	$view->display();
?>
<div class="section">
	<div class="aside">
		<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&task=addcomment#commentform'); ?>" class="add"><?php echo JText::_('WIKI_ADD_COMMENT'); ?></a></p>
	</div><!-- / .aside -->
	<div class="subject">
		<p><?php echo JText::_('WIKI_COMMENTS_EXPLANATION'); ?></p>
	</div><!-- / .subject -->
</div><!-- / .section -->
<div class="clear"></div>

<div class="main section">
	<h3 id="commentlist-title"><?php echo JText::_('COMMENTS'); ?></h3>

	<div class="aside">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename); ?>" method="get">
			<fieldset class="controls">
				<label>
					<?php echo JText::_('WIKI_COMMENT_REVISION'); ?>:
					<select name="version">
						<option value=""><?php echo JText::_('ALL'); ?></option>
<?php
if (count($this->versions) > 1) {
	foreach ($this->versions as $ver)
	{
?>
						<option value="<?php echo $ver->version; ?>"<?php echo ($this->v == $ver->version) ? ' selected="selected"' : ''; ?>>Version <?php echo $ver->version; ?></option>
<?php
	}
}
?>
					</select>
				</label>
				<input type="hidden" name="task" value="comments" />
				<p class="submit"><input type="submit" value="<?php echo JText::_('GO'); ?>" /></p>
<?php if ($this->sub) { ?>
				<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
<?php } ?>
			</fieldset>
		</form>
	</div><!-- / .aside -->
	<div class="subject">
<?php
if ($this->comments) {
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'comments',
		'layout'    => 'list'
	));
	$view->option = $this->option;
	$view->page = $this->page;
	$view->comments = $this->comments;
	$view->c = '';
	$view->level = 1;
	$view->config = $this->config;
	$view->display();
} else {
	if ($this->v) {
		echo '<p>No comments found for this version.</p>';
	} else {
		echo '<p>No comments found. Be the first to add a comment!</p>';
	}
}
?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->

<?php if (is_object($this->mycomment)) { ?>
<div class="below section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename); ?>" method="post" id="commentform">
		<h3 id="commentform-title">
			<a name="commentform"></a>
			<?php echo JText::_('WIKI_ADD_COMMENT'); ?>
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
			<p class="comment-member-photo">
<?php 
		ximport('Hubzero_User_Profile_Helper');
		$juser = JFactory::getUser();
		if (!$juser->get('guest')) {
			$jxuser = new Hubzero_User_Profile();
			$jxuser->load( $juser->get('id') );
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
				<img src="<?php echo $thumb; ?>" alt="Member photo" />
			</p>
			<fieldset>
<?php if (!$this->mycomment->parent) { ?>
				<fieldset>
					<legend><?php echo JText::_('WIKI_FIELD_RATING'); ?>:</legend>
					<label><input class="option" id="review_rating_1" name="comment[rating]" type="radio" value="1"<?php if ($this->mycomment->rating == 1) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x2729;&#x2729;&#x2729;&#x2729; <?php echo JText::_('WIKI_FIELD_RATING_ONE'); ?></label>
					<label><input class="option" id="review_rating_2" name="comment[rating]" type="radio" value="2"<?php if ($this->mycomment->rating == 2) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x2729;&#x2729;&#x2729;</label>
					<label><input class="option" id="review_rating_3" name="comment[rating]" type="radio" value="3"<?php if ($this->mycomment->rating == 3) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x2729;&#x2729;</label>
					<label><input class="option" id="review_rating_4" name="comment[rating]" type="radio" value="4"<?php if ($this->mycomment->rating == 4) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x2729;</label>
					<label><input class="option" id="review_rating_5" name="comment[rating]" type="radio" value="5"<?php if ($this->mycomment->rating == 5) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x272D; <?php echo JText::_('WIKI_FIELD_RATING_FIVE'); ?></label>
				</fieldset>
<?php } ?>
				<label>
					<?php echo JText::_('WIKI_FIELD_COMMENTS'); ?>:
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('comment[ctext]', 'ctext', $this->mycomment->ctext, '', '35', '15');
					?>
				</label>

				<input type="hidden" name="comment[created]" value="<?php echo $this->mycomment->created; ?>" />
				<input type="hidden" name="comment[id]" value="<?php echo $this->mycomment->id; ?>" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $this->mycomment->created_by; ?>" />
				<input type="hidden" name="comment[status]" value="<?php echo $this->mycomment->status; ?>" />
				<input type="hidden" name="comment[version]" value="<?php echo $this->mycomment->version; ?>" />
				<input type="hidden" name="comment[parent]" value="<?php echo $this->mycomment->parent; ?>" />
				<input type="hidden" name="comment[pageid]" value="<?php echo $this->mycomment->pageid; ?>" />
				
				<input type="hidden" name="pagename" value="<?php echo $this->page->pagename; ?>" />
				<input type="hidden" name="scope" value="<?php echo $this->page->scope; ?>" />
				
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="savecomment" />

<?php if ($this->sub) { ?>
				<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
<?php } ?>
	
				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="anonymous" id="comment-anonymous" value="1"<?php if ($this->mycomment->anonymous != 0) { echo ' checked="checked"'; } ?> />
					<?php echo JText::_('WIKI_FIELD_ANONYMOUS'); ?>
				</label>

				<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" /></p>
				<div class="sidenote">
					<p>
						<strong>Please keep comments relevant to this entry. Comments deemed inappropriate may be removed.</strong>
					</p>
					<p>
						Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/wiki/Help:WikiFormatting" class="popup">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .below section -->
<?php } ?>
