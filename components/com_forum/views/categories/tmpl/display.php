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

$this->css()
     ->js();

$juser = JFactory::getUser();

$this->category->set('section_alias', $this->filters['section']);
?>

<header id="content-header">
	<h2><?php echo JText::_('COM_FORUM'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-folder categories btn" href="<?php echo JRoute::_($this->category->link('base')); ?>">
				<?php echo JText::_('COM_FORUM_ALL_CATEGORIES'); ?>
			</a>
		</p>
	</div>
</header>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<?php foreach ($this->notifications as $notification) { ?>
				<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
			<?php } ?>

			<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
				<div class="container data-entry">
					<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_FORUM_SEARCH'); ?>" />
					<fieldset class="entry-search">
						<legend><span><?php echo JText::_('COM_FORUM_SEARCH_LEGEND'); ?></span></legend>

						<label for="entry-search-field"><?php echo JText::_('COM_FORUM_SEARCH_LABEL'); ?></label>
						<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_FORUM_SEARCH_PLACEHOLDER'); ?>" />

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="categories" />
						<input type="hidden" name="task" value="search" />
					</fieldset>
				</div><!-- / .container -->

				<div class="container">
					<ul class="entries-menu order-options">
						<li>
							<a<?php echo ($this->filters['sortby'] == 'created') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($this->category->link('here', '&sortby=created')); ?>" title="<?php echo JText::_('COM_FORUM_SORT_BY_CREATED'); ?>">
								<?php echo JText::_('COM_FORUM_SORT_CREATED'); ?>
							</a>
						</li>
						<li>
							<a<?php echo ($this->filters['sortby'] == 'activity') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($this->category->link('here', '&sortby=activity')); ?>" title="<?php echo JText::_('COM_FORUM_SORT_BY_ACTIVITY'); ?>">
								<?php echo JText::_('COM_FORUM_SORT_ACTIVITY'); ?>
							</a>
						</li>
						<li>
							<a<?php echo ($this->filters['sortby'] == 'replies') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($this->category->link('here', '&sortby=replies')); ?>" title="<?php echo JText::_('COM_FORUM_SORT_BY_NUM_POSTS'); ?>">
								<?php echo JText::_('COM_FORUM_SORT_NUM_POSTS'); ?>
							</a>
						</li>
						<li>
							<a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($this->category->link('here', '&sortby=title')); ?>" title="<?php echo JText::_('COM_FORUM_SORT_BY_TITLE'); ?>">
								<?php echo JText::_('COM_FORUM_SORT_TITLE'); ?>
							</a>
						</li>
					</ul>

					<table class="entries">
						<caption>
							<?php
							if ($this->filters['search'])
							{
								if ($this->category->get('title'))
								{
									echo JText::sprintf('COM_FORUM_SEARCH_FOR_IN', $this->escape($this->filters['search']), $this->escape(stripslashes($this->category->get('title'))));
								}
								else
								{
									echo JText::sprintf('COM_FORUM_SEARCH_FOR', $this->escape($this->filters['search']));
								}
							} else {
								echo JText::sprintf('COM_FORUM_SEARCH_IN', $this->escape(stripslashes($this->category->get('title'))));
							}
							?>
						</caption>
						<tbody>
							<?php
							if ($this->category->threads('list', $this->filters)->total() > 0)
							{
								foreach ($this->category->threads() as $row)
								{
									$name = JText::_('COM_FORUM_ANONYMOUS');
									if (!$row->get('anonymous'))
									{
										$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->creator('id')) . '">' . $this->escape(stripslashes($row->creator('name'))) . '</a>';
									}
									$cls = array();
									if ($row->isClosed())
									{
										$cls[] = 'closed';
									}
									if ($row->isSticky())
									{
										$cls[] = 'sticky';
									}

									$row->set('category', $this->filters['category']);
									$row->set('section', $this->filters['section']);
									?>
									<tr<?php if (count($cls) > 0) { echo ' class="' . implode(' ', $cls) . '"'; } ?>>
										<th>
											<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
										</th>
										<td>
											<a class="entry-title" href="<?php echo JRoute::_($row->link()); ?>">
												<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
											</a>
											<span class="entry-details">
												<span class="entry-date">
													<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
												</span>
												<?php echo JText::sprintf('COM_FORUM_BY_USER', '<span class="entry-author">' . $name . '</span>'); ?>
											</span>
										</td>
										<td>
											<span><?php echo ($row->posts('count')); ?></span>
											<span class="entry-details">
												<?php echo JText::_('COM_FORUM_COMMENTS'); ?>
											</span>
										</td>
										<td>
											<span><?php echo JText::_('COM_FORUM_LAST_POST'); ?></span>
											<span class="entry-details">
												<?php
													$lastpost = $row->lastActivity();
													if ($lastpost->exists())
													{
															$lname = JText::_('COM_FORUM_ANONYMOUS');
															if (!$lastpost->get('anonymous'))
															{
																$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $lastpost->creator('id')) . '">' . $this->escape(stripslashes($lastpost->creator('name'))) . '</a>';
															}
														?>
														<span class="entry-date">
															<time datetime="<?php echo $lastpost->created(); ?>"><?php echo $lastpost->created('date'); ?></time>
														</span>
														<?php echo JText::sprintf('COM_FORUM_BY_USER', '<span class="entry-author">' . $lname . '</span>'); ?>
												<?php } else { ?>
														<?php echo JText::_('COM_FORUM_NONE'); ?>
												<?php } ?>
											</span>
										</td>
										<?php if ($this->config->get('access-manage-thread') || $this->config->get('access-edit-thread') || $this->config->get('access-delete-thread')) { ?>
											<td class="entry-options">
												<?php if ($this->config->get('access-manage-thread') || ($this->config->get('access-edit-thread') && $row->get('created_by') == $juser->get('id'))) { ?>
													<a class="icon-edit edit" href="<?php echo JRoute::_($row->link('edit')); ?>">
														<?php echo JText::_('COM_FORUM_EDIT'); ?>
													</a>
												<?php } ?>
												<?php if ($this->config->get('access-manage-thread') || ($this->config->get('access-delete-thread') && $row->get('created_by') == $juser->get('id'))) { ?>
													<a class="icon-delete delete" data-txt-confirm="<?php echo JText::_('COM_FORUM_CONFIRM_DELETE'); ?>" href="<?php echo JRoute::_($row->link('delete')); ?>">
														<?php echo JText::_('COM_FORUM_DELETE'); ?>
													</a>
												<?php } ?>
											</td>
										<?php } ?>
									</tr>
								<?php } ?>
							<?php } else { ?>
								<tr>
									<td><?php echo JText::_('COM_FORUM_CATEGORY_EMPTY'); ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<?php
					jimport('joomla.html.pagination');
					$pageNav = new JPagination(
						$this->category->threads('count', $this->filters),
						$this->filters['start'],
						$this->filters['limit']
					);
					$pageNav->setAdditionalUrlParam('section', $this->filters['section']);
					$pageNav->setAdditionalUrlParam('category', $this->filters['category']);
					$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
					echo $pageNav->getListFooter();
					?>
					<div class="clearfix"></div>
				</div><!-- / .container -->
			</form>
		</div><!-- /.subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo JText::_('COM_FORUM_LAST_POST'); ?></h3>
				<p>
					<?php
					$last = $this->category->lastActivity();
					if ($last->exists())
					{
						$lname = JText::_('COM_FORUM_ANONYMOUS');
						if (!$last->get('anonymous'))
						{
							$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $last->creator('id')) . '">' . $this->escape(stripslashes($last->creator('name'))) . '</a>';
						}
						$last->set('category', $this->filters['category']);
						$last->set('section', $this->filters['section']);
					?>
						<a href="<?php echo JRoute::_($last->link()); ?>" class="entry-date">
							<span class="entry-date-at"><?php echo JText::_('COM_FORUM_AT'); ?></span>
							<span class="icon-time time"><time datetime="<?php echo $last->created(); ?>"><?php echo $last->created('time'); ?></time></span>
							<span class="entry-date-on"><?php echo JText::_('COM_FORUM_ON'); ?></span>
							<span class="icon-date date"><time datetime="<?php echo $last->created(); ?>"><?php echo $last->created('date'); ?></time></span>
						</a>
						<span class="entry-author">
							<?php echo JText::sprintf('COM_FORUM_BY_USER', $lname); ?>
						</span>
					<?php } else { ?>
						<?php echo JText::_('COM_FORUM_NONE'); ?>
					<?php } ?>
				</p>
			</div><!-- / .container -->

			<?php if ($this->config->get('access-create-thread')) { ?>
				<div class="container">
					<h3><?php echo JText::_('COM_FORUM_CREATE_YOUR_OWN'); ?></h3>
					<?php if (!$this->category->isClosed()) { ?>
						<p>
							<?php echo JText::_('COM_FORUM_CREATE_YOUR_OWN_DISCUSSION'); ?>
						</p>
						<p>
							<a class="icon-add add btn" href="<?php echo JRoute::_($this->category->link('newthread')); ?>"><?php echo JText::_('COM_FORUM_NEW_DISCUSSION'); ?></a>
						</p>
					<?php } else { ?>
						<p class="warning">
							<?php echo JText::_('COM_FORUM_CATEGORY_CLOSED'); ?>
						</p>
					<?php } ?>
				</div><!-- / .container -->
			<?php } ?>
		</aside><!-- / .aside -->
	</div>
</section><!-- /.main -->