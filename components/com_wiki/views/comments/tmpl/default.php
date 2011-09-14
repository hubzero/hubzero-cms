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
defined('_JEXEC') or die( 'Restricted access' );

$params = new JParameter( $this->page->params );

if ($this->sub) {
	$hid = 'sub-content-header';
	$uid = 'section-useroptions';
	$sid = 'sub-section-menu';
} else {
	$hid = 'content-header';
	$uid = 'useroptions';
	$sid = 'sub-menu';
}
?>
<div id="<?php echo $hid; ?>">
	<h2><?php echo $this->title; ?></h2>
	<?php echo WikiHtml::authors( $this->page, $params ); ?>
</div><!-- /#content-header -->

<?php echo WikiHtml::subMenu( $this->sub, $this->option, $this->page->pagename, $this->page->scope, $this->page->state, $this->task, $params, $this->editauthorized ); ?>

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
				<input type="submit" value="<?php echo JText::_('GO'); ?>" />
<?php if ($this->sub) { ?>
				<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
<?php } ?>
			</fieldset>
		</form>
	</div><!-- / .aside -->
	<div class="subject">
<?php
if ($this->comments) {
	$view = new JView( array('name'=>'comments','layout'=>'list','base_path'=>JPATH_ROOT.DS.'components'.DS.'com_wiki') );
	$view->option = $this->option;
	$view->page = $this->page;
	$view->comments = $this->comments;
	$view->c = '';
	$view->level = 1;
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
					<label><input class="option" id="review_rating_1" name="rating" type="radio" value="1"<?php if ($this->mycomment->rating == 1) { $html .= ' checked="checked"'; } ?> /> <img src="/components/com_wiki/images/stars/1.gif" alt="1 star" /> <?php echo JText::_('WIKI_FIELD_RATING_ONE'); ?></label>
					<label><input class="option" id="review_rating_2" name="rating" type="radio" value="2"<?php if ($this->mycomment->rating == 2) { $html .= ' checked="checked"'; } ?> /> <img src="/components/com_wiki/images/stars/2.gif" alt="2 stars" /></label>
					<label><input class="option" id="review_rating_3" name="rating" type="radio" value="3"<?php if ($this->mycomment->rating == 3) { $html .= ' checked="checked"'; } ?> /> <img src="/components/com_wiki/images/stars/3.gif" alt="3 stars" /></label>
					<label><input class="option" id="review_rating_4" name="rating" type="radio" value="4"<?php if ($this->mycomment->rating == 4) { $html .= ' checked="checked"'; } ?> /> <img src="/components/com_wiki/images/stars/4.gif" alt="4 stars" /></label>
					<label><input class="option" id="review_rating_5" name="rating" type="radio" value="5"<?php if ($this->mycomment->rating == 5) { $html .= ' checked="checked"'; } ?> /> <img src="/components/com_wiki/images/stars/5.gif" alt="5 stars" /> <?php echo JText::_('WIKI_FIELD_RATING_FIVE'); ?></label>
				</fieldset>
<?php } ?>
				<label>
					<?php echo JText::_('WIKI_FIELD_COMMENTS'); ?>:
					<?php
					ximport('Hubzero_Wiki_Editor');
					$editor =& Hubzero_Wiki_Editor::getInstance();
					echo $editor->display('ctext', 'ctext', $this->mycomment->ctext, '', '35', '10');
					?>
				</label>

				<input type="hidden" name="created" value="<?php echo $this->mycomment->created; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->mycomment->id; ?>" />
				<input type="hidden" name="created_by" value="<?php echo $this->mycomment->created_by; ?>" />
				<input type="hidden" name="status" value="<?php echo $this->mycomment->status; ?>" />
				<input type="hidden" name="version" value="<?php echo $this->mycomment->version; ?>" />
				<input type="hidden" name="parent" value="<?php echo $this->mycomment->parent; ?>" />
				<input type="hidden" name="pageid" value="<?php echo $this->mycomment->pageid; ?>" />
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
						Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="/wiki/Help:WikiFormatting" class="popup 400x500">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .below section -->
<?php } ?>
