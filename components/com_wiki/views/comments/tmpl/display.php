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

if (!$this->sub)
{
	$this->css()
	     ->js();
}
?>
	<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->escape($this->title); ?></h2>
		<?php
		if (!$this->page->isStatic())
		{
			$this->view('authors', 'page')
			     ->setBasePath($this->base_path)
			     ->set('page', $this->page)
			     ->display();
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
	$this->view('submenu', 'page')
	     ->setBasePath($this->base_path)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('page', $this->page)
	     ->set('task', $this->task)
	     ->set('sub', $this->sub)
	     ->display();
?>

<?php if (!$this->sub) { ?>
<section class="section">
	<div class="subject">
<?php } ?>
		<p><?php echo JText::_('COM_WIKI_COMMENTS_EXPLANATION'); ?></p>
<?php if (!$this->sub) { ?>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><a href="<?php echo JRoute::_($this->page->link('addcomment') . '#commentform'); ?>" class="icon-add add btn"><?php echo JText::_('COM_WIKI_ADD_COMMENT'); ?></a></p>
	</aside><!-- / .aside -->
</section><!-- / .section -->
<?php } ?>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<?php if ($this->sub) { ?>
				<p class="comment-add-btn">
					<a href="<?php echo JRoute::_($this->page->link('addcomment') . '#commentform'); ?>" class="icon-add add btn"><?php echo JText::_('COM_WIKI_ADD_COMMENT'); ?></a>
				</p>
			<?php } ?>
			<h3 id="commentlist-title"><?php echo JText::_('COM_WIKI_COMMENTS'); ?></h3>

			<?php
			$filters = array('version' => '');
			if ($this->v)
			{
				$filters['version'] = $this->v;
			}

			if ($this->page->comments('list', $filters)->total())
			{
				$this->view('_list', 'comments')
				     ->setBasePath(JPATH_ROOT . '/components/com_wiki')
				     ->set('parent', 0)
				     ->set('page', $this->page)
				     ->set('option', $this->option)
				     ->set('comments', $this->page->comments())
				     ->set('config', $this->config)
				     ->set('depth', 0)
				     ->set('version', $this->v)
				     ->set('cls', 'odd')
				     ->display();
			}
			else
			{
				if ($this->v)
				{
					echo '<p>' . JText::_('COM_WIKI_NO_COMMENTS_FOR_VERSION') . '</p>';
				}
				else
				{
					echo '<p>' . JText::_('COM_WIKI_NO_COMMENTS') . '</p>';
				}
			}
			?>
		</div><!-- / .subject -->
		<aside class="aside">
			<form action="<?php echo JRoute::_($this->page->link('comments')); ?>" method="get">
				<fieldset class="controls">
					<label for="filter-version">
						<?php echo JText::_('COM_WIKI_COMMENT_REVISION'); ?>:
						<select name="version" id="filter-version">
							<option value=""><?php echo JText::_('COM_WIKI_ALL'); ?></option>
							<?php
							foreach ($this->page->revisions('list') as $ver)
							{
							?>
							<option value="<?php echo $ver->get('version'); ?>"<?php echo ($this->v == $ver->get('version')) ? ' selected="selected"' : ''; ?>><?php echo JText::sprintf('COM_WIKI_VERSION_NUM', $ver->get('version')); ?></option>
							<?php
							}
							?>
						</select>
					</label>
					<p class="submit"><input type="submit" class="btn" value="<?php echo JText::_('COM_WIKI_GO'); ?>" /></p>
				<?php if ($this->sub) { ?>
					<input type="hidden" name="action" value="comments" />
					<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
				<?php } else { ?>
					<input type="hidden" name="task" value="comments" />
				<?php } ?>
				</fieldset>
			</form>
		</aside><!-- / .aside -->
	</div>
</section><!-- / .main section -->

<?php if (isset($this->mycomment) && is_a($this->mycomment, 'WikiModelComment')) { ?>
<section class="below section">
	<form action="<?php echo JRoute::_($this->page->link('comments')); ?>" method="post" id="commentform" class="section-inner">
		<div class="subject">
			<h3 id="commentform-title">
				<?php echo JText::_('COM_WIKI_ADD_COMMENT'); ?>
			</h3>
			<p class="comment-member-photo">
				<?php
				$juser = JFactory::getUser();
				$anon = (!$juser->get('guest')) ? 0 : 1;
				?>
				<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($juser, $anon); ?>" alt="<?php echo JText::_('COM_WIKI_MEMBER_PICTURE'); ?>" />
			</p>
			<fieldset>
			<?php if (!$this->mycomment->get('parent')) { ?>
				<fieldset>
					<legend><?php echo JText::_('COM_WIKI_FIELD_RATING'); ?>:</legend>
					<label><input class="option" id="review_rating_1" name="comment[rating]" type="radio" value="1"<?php if ($this->mycomment->get('rating') == 1) { echo ' checked="checked"'; } ?> /> &#x272D;&#x2729;&#x2729;&#x2729;&#x2729; <?php echo JText::_('COM_WIKI_FIELD_RATING_ONE'); ?></label>
					<label><input class="option" id="review_rating_2" name="comment[rating]" type="radio" value="2"<?php if ($this->mycomment->get('rating') == 2) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x2729;&#x2729;&#x2729;</label>
					<label><input class="option" id="review_rating_3" name="comment[rating]" type="radio" value="3"<?php if ($this->mycomment->get('rating') == 3) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x2729;&#x2729;</label>
					<label><input class="option" id="review_rating_4" name="comment[rating]" type="radio" value="4"<?php if ($this->mycomment->get('rating') == 4) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x2729;</label>
					<label><input class="option" id="review_rating_5" name="comment[rating]" type="radio" value="5"<?php if ($this->mycomment->get('rating') == 5) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x272D; <?php echo JText::_('COM_WIKI_FIELD_RATING_FIVE'); ?></label>
				</fieldset>
			<?php } ?>
				<label>
					<?php echo JText::_('COM_WIKI_FIELD_COMMENTS'); ?>:
					<?php
					echo WikiHelperEditor::getInstance()->display('comment[ctext]', 'ctext', $this->mycomment->get('ctext'), '', '35', '15');
					?>
				</label>

				<input type="hidden" name="comment[created]" value="<?php echo $this->escape($this->mycomment->get('created')); ?>" />
				<input type="hidden" name="comment[id]" value="<?php echo $this->escape($this->mycomment->get('id')); ?>" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape($this->mycomment->get('created_by')); ?>" />
				<input type="hidden" name="comment[status]" value="<?php echo $this->escape($this->mycomment->get('status', 1)); ?>" />
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
					<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($this->mycomment->get('anonymous') != 0) { echo ' checked="checked"'; } ?> />
					<?php echo JText::_('COM_WIKI_FIELD_ANONYMOUS'); ?>
				</label>

				<?php echo JHTML::_('form.token'); ?>

				<p class="submit"><input type="submit" class="btn" value="<?php echo JText::_('COM_WIKI_SUBMIT'); ?>" /></p>
				<div class="sidenote">
					<p>
						<strong><?php echo JText::_('COM_WIKI_COMMENT_KEEP_RELEVANT'); ?></strong>
					</p>
					<p>
						<?php echo JText::_('COM_WIKI_COMMENT_FORMATTING_HINT'); ?>
					</p>
				</div>
			</fieldset>
		</div><!-- / .subject -->
		<aside class="aside">
			<table class="wiki-reference">
				<caption><?php echo JText::_('COM_WIKI_SYNTAX_REFERENCE'); ?></caption>
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
		</aside><!-- / .aside -->
	</form>
</section><!-- / .below section -->
<?php } ?>
