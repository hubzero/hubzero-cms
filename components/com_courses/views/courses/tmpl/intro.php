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

$this->css('introduction', 'system')
     ->css('intro.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php if ($this->config->get('access-create-course')) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=course&task=new'); ?>"><?php echo JText::_('COM_COURSES_CREATE_COURSE'); ?></a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
	<?php } ?>
</header>

<?php
if (count($this->notifications) > 0)
{
	foreach ($this->notifications as $notification)
	{
		echo '<p class="' . $this->escape($notification['type']) . '">' . $notification['message'] . '</p>';
	}
}
?>

<section id="introduction" class="section">

	<div class="grid">
		<div class="col span8">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_COURSES_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<input type="text" name="search" value="" placeholder="<?php echo JText::_('What do you want to learn about?'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container -->
			<p><strong>Watch</strong> lectures taught by world-class professors, <strong>learn</strong> at your own pace, <strong>test</strong> your knowledge, and <strong>earn</strong> recognition for your achievements.</p>
			<p><a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">More on how courses work &raquo;</a></p>
		</div>
		<div class="col span3 offset1 omega">
			<div>
				<a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
					<?php echo JText::_('Browse the catalog'); ?>
				</a>
			</div>
		</div>
	</div>

</section><!-- / #introduction.section -->

<section class="section">

	<?php if ($this->config->get("intro_popularcourses", 1)) : ?>
		<?php if (count($this->popularcourses) > 0) { ?>
			<div class="popularcourses">
				<?php
				$count = 0;
				foreach ($this->popularcourses as $course)
				{
					if ($count == 0)
					{
						echo '<div class="grid">';
					}
					$this->view('_course')
					     ->set('count', $count)
					     ->set('columns', 2)
					     ->set('course', $course)
					     ->display();

					if ($count == 2)
					{
						$count = 0;
						echo '</div>';
					}
					else
					{
						$count++;
					}
				}
				?>
			</div><!-- / .popularcourses clearfix top -->
		<?php } else { ?>
			<p class="info"><?php echo JText::sprintf('COM_COURSES_NO_POPULAR_COURSES', $this->user->get('id')); ?></p>
		<?php } ?>
	<?php endif; ?>

</section><!-- / .section -->
