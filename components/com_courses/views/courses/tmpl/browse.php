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
 * the GNU Lesser General Public License as state by the Free Software
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

//$maxtextlen = 42;
$juser =& JFactory::getUser();
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<?php if ($this->config->get('access-create-course')) { ?>
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=course&task=new'); ?>"><?php echo JText::_('Create Course'); ?></a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>

<?php
	foreach ($this->notifications as $notification) {
		echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
	}
?>

<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>" method="get">
	<div class="main section">
		<div class="aside">
			<div class="container">
				<h3><?php echo JText::_('Finding a course'); ?></h3>
				<p><?php echo JText::_('Use the sorting and filtering options to see courses listed alphabetically by title, alias, or popularity.'); ?></p>
				<p><?php echo JText::_('Use the "Search" to find specific courses by title or description if you would like to check out their offerings.'); ?></p>
			</div><!-- / .container -->
			<div class="container">
				<h3><?php echo JText::_('Popular Categories'); ?></h3>
				<?php 
				$tags = $this->model->tags('cloud', 20, $this->filters['tag']);
				if ($tags) {
					echo $tags;
				} else {
					echo '<p>' . JText::_('No categories have been set.') . '</p>';
				} ?>
			</div><!-- / .container -->
		</div><!-- / .aside -->
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search for Courses'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
					<input type="hidden" name="index" value="<?php echo $this->escape($this->filters['index']); ?>" />
				</fieldset>
				<?php if ($this->filters['tag']) { ?>
				<fieldset class="applied-tags">
					<ol class="tags">
					<?php
					$url  = 'index.php?option=' . $this->option . '&task=browse';
					$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
					$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
					$url .= ($this->filters['index']  ? '&index=' . $this->escape($this->filters['index'])   : '');

					$tags = $this->model->parseTags($this->filters['tag']);
					foreach ($tags as $tag)
					{
						?>
						<li>
							<a href="<?php echo JRoute::_($url . '&tag=' . implode(',', $this->model->parseTags($this->filters['tag'], $tag))); ?>">
								<?php echo $this->escape(stripslashes($tag)); ?>
								<span class="remove">x</a>
							</a>
						</li>
						<?php
					}
					?>
					</ol>
				</fieldset>
				<?php } ?>
			</div><!-- / .container -->

			<div class="container">
				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				$qs .= ($this->filters['index']  ? '&index=' . $this->escape($this->filters['index'])   : '');
				$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
				?>
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=title' . $qs); ?>" title="Sort by title">&darr; Title</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'alias') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=alias' . $qs); ?>" title="Sort by alias">&darr; Alias</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'popularity') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=popularity' . $qs); ?>" title="Sort by popularity">&darr; Popularity</a></li>
				</ul>

				<?php
				$url  = 'index.php?option=' . $this->option . '&task=browse';
				$url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				$url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
				$url .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');

				$html  = '<a href="' . JRoute::_($url) . '"';
				if ($this->filters['index'] == '') 
				{
					$html .= ' class="active-index"';
				}
				$html .= '>' . JText::_('ALL') . '</a> '."\n";

				$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
				foreach ($letters as $letter)
				{
					$html .= '<a href="' . JRoute::_($url . '&index=' . strtolower($letter)) . '"';
					if ($this->filters['index'] == strtolower($letter)) 
					{
						$html .= ' class="active-index"';
					}
					$html .= '>' . strtoupper($letter) . '</a> ' . "\n";
				}
				?>
				<div class="clearfix"></div>

				<table class="courses entries">
					<caption>
					<?php
						$s = $this->filters['start']+1;
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '') {
							echo 'Search for "'.$this->escape($this->filters['search']).'" in ';
						}
						?>
						<?php echo JText::_('Courses'); ?> 
						<?php
						if ($this->filters['tag'] != '') {
							echo 'with tag "'.$this->escape($this->filters['tag']).'"';
						}
					?>
						<?php if ($this->filters['index']) { ?>
							<?php echo JText::_('starting with'); ?> "<?php echo strToUpper($this->filters['index']); ?>"
						<?php } ?>
					<?php if ($this->courses->total() > 0) { ?>
						<span>(<?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?>)</span>
					<?php } ?>
					</caption>
					<thead>
						<tr>
							<th colspan="2<?php //echo ($this->config->get('access-admin-component')) ? '4' : '3'; ?>">
								<span class="index-wrap">
									<span class="index">
										<?php echo $html; ?>
									</span>
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
				<?php
				if ($this->courses->total() > 0) 
				{
					ximport('Hubzero_User_Profile_Helper');
					ximport('Hubzero_View_Helper_Html');
					foreach ($this->courses as $course)
					{
						//get status
						$status = '';

						//determine course status
						if ($course->get('state') == 1)
						{
							if ($course->access('manage'))
							{
								$status = 'manager';
							}
						}
						else
						{
							$status = 'new';
						}
				?>
						<tr<?php echo ($status) ? ' class="' . $status . '"' : ''; ?>>
							<th>
								<span class="entry-id"><?php echo $course->get('id'); ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $course->get('alias')); ?>">
									<?php echo $this->escape(stripslashes($course->get('title'))); ?>
								</a><br />
							<?php
								$instructors = $course->instructors();
								if (count($instructors) > 0) 
								{
									$names = array();
									foreach ($instructors as $i)
									{
										$instructor = Hubzero_User_Profile::getInstance($i->get('user_id'));

										$names[] = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $i->get('user_id')) . '">' . $this->escape(stripslashes($instructor->get('name'))) . '</a>';
									}
							?>
								<span class="entry-details">
									Instructors: <span class="entry-instructors"><?php echo implode(', ', $names); ?></span>
								</span>
							<?php
								}
							?>
								<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($course->get('blurb')), 200); ?>
							</td>
						<?php /*if ($course->access('manage')) { ?>
							<td>
								<span class="<?php echo $status; ?> status"><?php
									switch ($status)
									{
										case 'manager': echo JText::_('COM_COURSES_STATUS_MANAGER'); break;
										case 'new': echo JText::_('COM_COURSES_STATUS_NEW_COURSE'); break;
										case 'member': echo JText::_('COM_COURSES_STATUS_APPROVED'); break;
										default: break;
									}
								?></span>
							</td>
					<?php }*/ ?>
						</tr>
				<?php 
					} // for loop 
				} else { ?>
						<tr>
							<td colspan="<?php echo ($this->authorized) ? '4' : '3'; ?>">
								<p class="warning"><?php echo JText::_('No results found'); ?></p>
							</td>
						</tr>
				<?php } ?>
					</tbody>
				</table>

				<?php
				$this->pageNav->setAdditionalUrlParam('index', $this->filters['index']);
				$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

				echo $this->pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
	</div><!-- / .main section -->
	<div class="clear"></div>
</form>
