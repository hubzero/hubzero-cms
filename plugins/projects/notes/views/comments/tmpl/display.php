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
?>
	<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->escape($this->title); ?></h2>
		<?php
		if (!$this->page->isStatic())
		{
			$view = new JView(array(
				'base_path' => $this->base_path,
				'name'      => 'page',
				'layout'    => 'authors'
			));
			$view->page     = $this->page;
			$view->display();
		}
		?>
	</header><!-- /#content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php
	$view = new JView(array(
		'base_path' => $this->base_path,
		'name'      => 'page',
		'layout'    => 'submenu'
	));
	$view->option     = $this->option;
	$view->controller = $this->controller;
	$view->page       = $this->page;
	$view->task       = $this->task;
	$view->config     = $this->config;
	$view->sub        = $this->sub;
	$view->display();
?>

<?php if (!$this->sub) { ?>
<section class="section">
	<div class="subject">
<?php } ?>
		<p><?php echo JText::_('COM_WIKI_COMMENTS_EXPLANATION'); ?></p>
<?php if (!$this->sub) { ?>
	</div><!-- / .subject -->
	<div class="aside">
		<p><a href="<?php echo JRoute::_($this->page->link('addcomment') . '#commentform'); ?>" class="icon-add add btn"><?php echo JText::_('COM_WIKI_ADD_COMMENT'); ?></a></p>
	</div><!-- / .aside -->
</section><!-- / .section -->
<?php } ?>

<section class="main section">
	<div class="subject">
		<?php if ($this->sub) { ?>
			<a href="<?php echo JRoute::_($this->page->link('addcomment') . '#commentform'); ?>" class="btn"><?php echo JText::_('COM_WIKI_ADD_COMMENT'); ?></a>
		<?php } ?>
		<h3 id="commentlist-title"><?php echo JText::_('COMMENTS'); ?></h3>

		<?php
		$filters = array('version' => '');
		if ($this->v)
		{
			$filters['version'] = 'AND version=' . $this->v;
		}

		if ($this->page->comments('list', $filters)->total())
		{
			$view = new JView(array(
				'base_path' => JPATH_ROOT . '/components/com_wiki',
				'name'      => 'comments',
				'layout'    => '_list'
			));
			$view->parent     = 0;
			$view->page       = $this->page;
			$view->option     = $this->option;
			$view->comments   = $this->page->comments();
			$view->config     = $this->config;
			$view->depth      = 0;
			$view->version    = $this->v;
			$view->cls        = 'odd';
			$view->display();
		}
		else
		{
			if ($this->v)
			{
				echo '<p>No comments found for this version.</p>';
			}
			else
			{
				echo '<p>No comments found. Be the first to add a comment!</p>';
			}
		}
		?>
	</div><!-- / .subject -->
	<div class="aside">
		<form action="<?php echo JRoute::_($this->page->link('comments')); ?>" method="get">
			<fieldset class="controls">
				<label for="filter-version">
					<?php echo JText::_('COM_WIKI_COMMENT_REVISION'); ?>:
					<select name="version" id="filter-version">
						<option value=""><?php echo JText::_('ALL'); ?></option>
						<?php
						foreach ($this->page->revisions('list') as $ver)
						{
						?>
						<option value="<?php echo $ver->get('version'); ?>"<?php echo ($this->v == $ver->get('version')) ? ' selected="selected"' : ''; ?>>Version <?php echo $ver->get('version'); ?></option>
						<?php
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
</section><!-- / .main section -->

<?php if (isset($this->mycomment) && is_a($this->mycomment, 'WikiModelComment')) { ?>
<form action="<?php echo JRoute::_($this->page->link('comments')); ?>" method="post" id="commentform">
	<section class="below section">
		<div class="subject">
			<h3 id="commentform-title">
				<?php echo JText::_('COM_WIKI_ADD_COMMENT'); ?>
			</h3>

			<p class="comment-member-photo">
				<?php
				$juser = JFactory::getUser();
				$anon = (!$juser->get('guest')) ? 0 : 1;
				?>
				<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($juser, $anon); ?>" alt="Member photo" />
			</p>
			<fieldset>
			<?php if (!$this->mycomment->get('parent')) { ?>
				<fieldset>
					<legend><?php echo JText::_('COM_WIKI_FIELD_RATING'); ?>:</legend>
					<label><input class="option" id="review_rating_1" name="comment[rating]" type="radio" value="1"<?php if ($this->mycomment->get('rating') == 1) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x2729;&#x2729;&#x2729;&#x2729; <?php echo JText::_('COM_WIKI_FIELD_RATING_ONE'); ?></label>
					<label><input class="option" id="review_rating_2" name="comment[rating]" type="radio" value="2"<?php if ($this->mycomment->get('rating') == 2) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x2729;&#x2729;&#x2729;</label>
					<label><input class="option" id="review_rating_3" name="comment[rating]" type="radio" value="3"<?php if ($this->mycomment->get('rating') == 3) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x2729;&#x2729;</label>
					<label><input class="option" id="review_rating_4" name="comment[rating]" type="radio" value="4"<?php if ($this->mycomment->get('rating') == 4) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x2729;</label>
					<label><input class="option" id="review_rating_5" name="comment[rating]" type="radio" value="5"<?php if ($this->mycomment->get('rating') == 5) { $html .= ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x272D; <?php echo JText::_('COM_WIKI_FIELD_RATING_FIVE'); ?></label>
				</fieldset>
			<?php } ?>
				<label>
					<?php echo JText::_('COM_WIKI_FIELD_COMMENTS'); ?>:
					<?php
					$editor = WikiHelperEditor::getInstance();
					echo $editor->display('comment[ctext]', 'ctext', $this->mycomment->get('ctext'), '', '35', '15');
					?>
				</label>

				<input type="hidden" name="comment[created]" value="<?php echo $this->escape($this->mycomment->get('created')); ?>" />
				<input type="hidden" name="comment[id]" value="<?php echo $this->escape($this->mycomment->get('id')); ?>" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape($this->mycomment->get('created_by')); ?>" />
				<input type="hidden" name="comment[status]" value="<?php echo $this->escape($this->mycomment->get('status')); ?>" />
				<input type="hidden" name="comment[version]" value="<?php echo $this->escape($this->mycomment->get('version')); ?>" />
				<input type="hidden" name="comment[parent]" value="<?php echo $this->escape($this->mycomment->get('parent')); ?>" />
				<input type="hidden" name="comment[pageid]" value="<?php echo $this->escape($this->mycomment->get('pageid')); ?>" />

				<input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->get('pagename')); ?>" />
				<input type="hidden" name="scope" value="<?php echo $this->escape($this->page->get('scope')); ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

			<?php if ($this->sub) { ?>
				<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
				<input type="hidden" name="action" value="savecomment" />
			<?php } else { ?>
				<input type="hidden" name="task" value="savecomment" />
			<?php } ?>

				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="anonymous" id="comment-anonymous" value="1"<?php if ($this->mycomment->get('anonymous') != 0) { echo ' checked="checked"'; } ?> />
					<?php echo JText::_('COM_WIKI_FIELD_ANONYMOUS'); ?>
				</label>

				<?php echo JHTML::_('form.token'); ?>

				<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" /></p>
				<div class="sidenote">
					<p>
						<strong>Please keep comments relevant to this entry. Comments deemed inappropriate may be removed.</strong>
					</p>
					<p>
						Line breaks and paragraphs are automatically converted. URLs (starting with http://) or email addresses will automatically be linked. <a href="<?php echo JRoute::_('index.php?option=com_wiki&pagename=Help:WikiFormatting'); ?>" class="popup">Wiki syntax</a> is supported.
					</p>
				</div>
			</fieldset>
		</div><!-- / .subject -->
		<div class="aside">
			<table class="wiki-reference">
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
	</section><!-- / .below section -->
</form>
<?php } ?>
