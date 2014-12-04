<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

$this->css()
     ->js();
?>
<ul id="page_options">
	<li>
		<a class="icon-folder categories btn" href="<?php echo JRoute::_($base); ?>"><?php echo JText::_('PLG_GROUPS_FORUM_ALL_CATEGORIES'); ?></a>
	</li>
</ul>

<section class="main section">
	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } ?>

	<form action="<?php echo JRoute::_($base); ?>" method="post">
		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SEARCH'); ?>" />
			<fieldset class="entry-search">
				<legend><?php echo JText::_('PLG_GROUPS_FORUM_SEARCH_LEGEND'); ?></legend>
				<label for="entry-search-field"><?php echo JText::_('PLG_GROUPS_FORUM_SEARCH_LABEL'); ?></label>
				<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
				<input type="hidden" name="active" value="forum" />
				<input type="hidden" name="action" value="search" />
			</fieldset>
		</div><!-- / .container -->

		<div class="container">
			<table class="entries">
				<caption>
					<?php echo JText::sprintf('PLG_GROUPS_FORUM_SEARCH_FOR', $this->escape($this->filters['search'])); ?>
				</caption>
				<tbody>
		<?php
		if ($this->thread->posts('list', $this->filters)->total() > 0)
		{
			foreach ($this->thread->posts() as $row)
			{
				$title = $this->escape(stripslashes($row->get('title')));
				$title = preg_replace('#' . $this->filters['search'] . '#i', "<span class=\"highlight\">\\0</span>", $title);

				$name = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
				if (!$row->get('anonymous'))
				{
					$name = $this->escape(stripslashes($row->creator('name')));
					$name = ($row->creator('public') ? '<a href="' . JRoute::_($row->creator()->getLink()) . '">' . $name . '</a>' : $name);
				}
				$cls = array();
				if ($row->get('closed'))
				{
					$cls[] = 'closed';
				}
				if ($row->get('sticky'))
				{
					$cls[] = 'sticky';
				}
				?>
					<tr<?php if (count($cls) > 0) { echo ' class="' . implode(' ', $cls) . '"'; } ?>>
						<th>
							<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_($base . '&scope=' . $this->sections[$this->categories[$row->get('category_id')]->get('section_id')]->get('alias') . '/' . $this->categories[$row->get('category_id')]->get('alias') . '/' . $row->get('thread')); ?>">
								<span><?php echo $title; ?></span>
							</a>
							<span class="entry-details">
								<span class="entry-date">
									<?php echo $row->created('date'); ?>
								</span>
								<?php echo JText::_('by'); ?>
								<span class="entry-author">
									<?php echo $name; ?>
								</span>
							</span>
						</td>
						<td>
							<span><?php echo JText::_('PLG_GROUPS_FORUM_SECTION'); ?></span>
							<span class="entry-details section-name">
								<?php echo $this->escape(\Hubzero\Utility\String::truncate($this->sections[$this->categories[$row->get('category_id')]->get('section_id')]->get('title'), 100, array('exact' => true))); ?>
							</span>
						</td>
						<td>
							<span><?php echo JText::_('PLG_GROUPS_FORUM_CATEGORY'); ?></span>
							<span class="entry-details category-name">
								<?php echo $this->escape(\Hubzero\Utility\String::truncate($this->categories[$row->get('category_id')]->get('title'), 100, array('exact' => true))); ?>
							</span>
						</td>
					</tr>
				<?php } ?>
			<?php } else { ?>
					<tr>
						<td><?php echo JText::_('PLG_GROUPS_FORUM_CATEGORY_EMPTY'); ?></td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
			<?php
				jimport('joomla.html.pagination');
				$pageNav = new JPagination(
					$this->thread->posts('count', $this->filters),
					$this->filters['start'],
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
				$pageNav->setAdditionalUrlParam('active', 'forum');
				//$pageNav->setAdditionalUrlParam('scope', $this->filters['section'] . '/' . $this->filters['category']);
				$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
			?>
		</div><!-- / .container -->
	</form>
</section><!-- /.main -->