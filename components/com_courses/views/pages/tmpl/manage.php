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

$base = 'index.php?option=com_courses&gid=' . $this->course->get('alias') . '&offering=manage&unit=' . $this->offering->get('alias') . '&b=pages';
?>

<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="prev btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias')); ?>">
				<?php echo JText::_('Back to Offering'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>">
		<?php echo $notification['message']; ?>
	</p>
<?php } ?>

<div class="main section">
	<form name="coursePages" action="index.php" method="post" id="hubFrm">
		<div class="aside">
			<p>This is where you can manage any of the course offering's custom content pages. There is no limit to the number of custom pages and pages can contain text, images, links to files. The pages support wiki syntax that is used throughout the hub.</p>
			<p>
				<a class="add btn" href="<?php echo JRoute::_($base . '&c=new'); ?>">Add a New Course Page</a>
			</p>
		</div><!-- / .aside -->
		<div class="subject">

		<div class="container">
			<ul class="entries-menu filter-options">
				<li>
					<a<?php echo ($this->filters['state'] == 'active') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&state=active'); ?>" title="<?php echo JText::_('COM_ANSWERS_FILTER_ALL_TITLE'); ?>">
						<?php echo JText::_('Active'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['state'] == 'inactive') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&state=inactive'); ?>" title="<?php echo JText::_('COM_ANSWERS_FILTER_OPEN_TITLE'); ?>">
						<?php echo JText::_('Inactive'); ?>
					</a>
				</li>
			</ul>

			<table class="pages entries">
				<caption>Manage Pages</caption>
				<thead>
					<tr>
						<th scope="col">
							<?php echo JText::_('Title'); ?>
						</th>
						<th scope="col" colspan="3">
							<?php echo JText::_('Ordering'); ?>
						</th>
						<th scope="col">
							<?php echo JText::_('Edit'); ?>
						</th>
						<th scope="col">
							<?php echo JText::_('Deactive'); ?>
						</th>
						<th scope="col">
							<?php echo JText::_('Preview'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php if (count($this->pages) > 0) { ?>
					<?php $counter = 0; ?>
					<?php foreach ($this->pages as $page) { ?>
						<?php $counter++; //$cls = ($counter % 2 == 0) ? 'odd' : 'even'; ?>
						<tr class="<?php echo ($counter % 2 == 0) ? 'odd' : 'even'; ?>">
							<td>
								<span class="entry-title">
									<?php echo $this->escape(stripslashes($page['title'])); ?>
								</span>
							</td>
							<td class="order">
								<?php echo $page['porder']; ?> <!-- <input type="text" disabled="disabled" value="<?php echo $page['porder']; ?>" /> -->
							</td>
							<td class="up">
								<?php if ($page['porder'] > 1) {?>
									<a class="tooltips" title="Move Page Up :: Move '<?php echo $this->escape(stripslashes($page['title'])); ?>' up" href="<?php echo JRoute::_($base . '&c=move&dir=up&page=' . $page['id']); ?>">Up</a>
								<?php } ?>
							</td>
							<td class="down">
								<?php if ($page['porder'] < $this->high_order_pages) { ?>
									<a class="tooltips" title="Move Page Down :: Move '<?php echo $this->escape(stripslashes($page['title'])); ?>' down"  href="<?php echo JRoute::_($base . '&c=move&dir=down&page=' . $page['id']); ?>">Down</a>
								<?php } ?>
							</td>
							<td>
								<a class="edit" href="<?php echo JRoute::_($base . '&c=edit&page=' . $page['id']); ?>">Edit</a>
							</td>
							<td>
								<a class="deactivate" href="<?php echo JRoute::_($base . '&c=deactivate&page=' . $page['id']); ?>">Deactivate</a>
							</td>
							<td class="quick">
								<a title="Preview :: Preview this page" href="#active-page-<?php echo $counter; ?>-preview" class="quick-view tooltips" rel="">Quick View</a>
								<?php /*<div id="active-page-<?php echo $counter; ?>-preview" class="preview">
									<h3 class="header">Page Preview: <?php echo $page['title']; ?></h3>
									<div class="parsed">
										<?php echo $this->parser->parse( "\n".stripslashes($page['content']), $this->wikiconfig ); ?>
									</div>
								</div> */ ?>
							</td>
						</tr>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="7">Currently this course does not have an active pages.</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div><!-- / .container -->

		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div>