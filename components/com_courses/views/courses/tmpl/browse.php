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

$this->css('browse.css');

//$maxtextlen = 42;
$juser = JFactory::getUser();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->config->get('access-create-course')) { ?>
	<div id="content-header-extra">
		<p>
			<a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=course&task=new'); ?>"><?php echo JText::_('COM_COURSES_CREATE_COURSE'); ?></a>
		</p>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
<?php } ?>

<section class="main section">
	<form class="section-inner" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>" method="get">
		<div class="subject">

			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_COURSES_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('COM_COURSES_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('COM_COURSES_SEARCH_LABEL'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COURSES_SEARCH_PLACEHOLDER'); ?>" />
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
						$url .= ($this->filters['group']  ? '&group=' . $this->escape($this->filters['group'])   : '');

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

			<?php if ($this->filters['group']) { ?>
				<div class="course-group">
					<?php
					$group = \Hubzero\User\Group::getInstance($this->filters['group']);

					list($width, $height) = $group->getLogo('size');
					$atts = ($width > $height ? 'height="50"' : 'width="50"');
					?>
					<p class="course-group-img">
						<a href="<?php echo JRoute::_('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
							<img src="<?php echo $group->getLogo(); ?>" <?php echo $atts; ?> alt="<?php echo $this->escape($group->get('description')); ?>" />
						</a>
					</p>
					<p class="course-group-description">
						<?php echo JText::_('COM_COURSES_BROUGHT_BY_GROUP'); ?>
					</p>
					<h3 class="course-group-title">
						<a href="<?php echo JRoute::_('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
							<?php echo $this->escape($group->get('description')); ?>
						</a>
					</h3>
				</div>
			<?php } ?>

			<div class="container">
				<?php
				$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
				$qs .= ($this->filters['index']  ? '&index=' . $this->escape($this->filters['index'])   : '');
				$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
				$qs .= ($this->filters['group']  ? '&group=' . $this->escape($this->filters['group'])   : '');
				?>
				<ul class="entries-menu order-options">
					<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=title' . $qs); ?>" title="<?php echo JText::_('COM_COURSES_SORT_BY_TITLE'); ?>"><?php echo JText::_('COM_COURSES_SORT_TITLE'); ?></a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'alias') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=alias' . $qs); ?>" title="<?php echo JText::_('COM_COURSES_SORT_BY_ALIAS'); ?>"><?php echo JText::_('COM_COURSES_SORT_ALIAS'); ?></a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'popularity') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse&sortby=popularity' . $qs); ?>" title="<?php echo JText::_('COM_COURSES_SORT_BY_POPULARITY'); ?>"><?php echo JText::_('COM_COURSES_SORT_POPULARITY'); ?></a></li>
				</ul>
				<div class="clearfix"></div>

				<h3>
					<?php
						$s = $this->filters['start']+1;
						$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

						if ($this->filters['search'] != '')
						{
							if ($this->filters['tag'] != '')
							{
								echo JText::sprintf('COM_COURSES_SEARCH_FOR_IN_WITH', $this->escape($this->filters['search']), $this->escape($this->filters['tag']));
							}
							else
							{
								echo JText::sprintf('COM_COURSES_SEARCH_FOR_IN', $this->escape($this->filters['search']));
							}
						}
						else if ($this->filters['tag'] != '')
						{
							echo JText::sprintf('COM_COURSES_COURSES_WITH', $this->escape($this->filters['tag']));
						}
						else
						{
							echo JText::_('COM_COURSES');
						}
						/*if ($this->filters['index']) { ?>
							echo JText::_('starting with "%s"', strToUpper($this->filters['index']);
						}*/
					?>
					<?php if ($this->courses->total() > 0) { ?>
						<span><?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?></span>
					<?php } ?>
				</h3>

				<ol class="courses entries">
				<?php
				if ($this->courses->total() > 0)
				{
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
						<li<?php echo ($status) ? ' class="' . $status . '"' : ''; ?>>
							<article>
								<h4>
									<a class="entry-title" href="<?php echo JRoute::_($course->link()); ?>">
										<?php echo $this->escape($course->get('title')); ?>
									</a>
								</h4>
								<p class="course-identity">
									<a href="<?php echo JRoute::_($course->link()); ?>">
										<?php if ($logo = $course->logo()) { ?>
											<img src="<?php echo $logo; ?>" alt="<?php echo $this->escape($course->get('title')); ?>" />
										<?php } else { ?>
											<span></span>
										<?php } ?>
									</a>
								</p>
								<dl class="entry-meta">
									<dt>
										<span>
											<?php echo JText::sprintf('COM_COURSES_COURSE_NUMBER', $course->get('id')); ?>
										</span>
									</dt>
									<dd class="instructors">
										<?php
											$instructors = $course->instructors();
											if (count($instructors) > 0)
											{
												$names = array();
												foreach ($instructors as $i)
												{
													$instructor = \Hubzero\User\Profile::getInstance($i->get('user_id'));

													$names[] = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $i->get('user_id')) . '">' . $this->escape(stripslashes($instructor->get('name'))) . '</a>';
												}
										?>
												<?php echo JText::_('COM_COURSES_COURSE_INSTRUCTORS'); ?>: <span class="entry-instructors"><?php echo implode(', ', $names); ?></span>
										<?php
											}
										?>
									</dd>
								</dl>
								<p class="entry-content">
									<?php echo \Hubzero\Utility\String::truncate($course->get('blurb'), 200); ?>
								</p>
							</article>
						</li>
				<?php
					} // for loop
				} else { ?>
					<li class="no-results"><p class="warning"><?php echo JText::_('COM_COURSES_NO_RESULTS_FOUND'); ?></p></li>
				<?php } ?>
				</ol>

				<?php
				$this->pageNav->setAdditionalUrlParam('index', $this->filters['index']);
				$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

				echo $this->pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
		<aside class="aside">
			<div class="container">
				<h3><?php echo JText::_('COM_COURSES_FINDING_A_COURSE'); ?></h3>
				<p><?php echo JText::_('COM_COURSES_FINDING_A_COURSE_EXPLANATION'); ?></p>
			</div><!-- / .container -->
			<div class="container">
				<h3><?php echo JText::_('COM_COURSES_POPULAR_CATEGORIES'); ?></h3>
				<?php
				$tags = $this->model->tags('cloud', 20, $this->filters['tag']);
				if ($tags) {
					echo $tags;
				} else {
					echo '<p>' . JText::_('COM_COURSES_POPULAR_CATEGORIES_NONE') . '</p>';
				} ?>
			</div><!-- / .container -->
		</aside><!-- / .aside -->
	</form>
</section><!-- / .main section -->
