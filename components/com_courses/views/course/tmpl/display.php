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

/*$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$tz = true;
}*/

//get objects
//$config   =& JFactory::getConfig();
//$database =& JFactory::getDBO();

$offerings = $this->course->offerings(array(
	'available' => true, 
	'sort'      => 'publish_up'
));
/*if (!$offerings->total())
{
	$offering = $offerings->fetch('first');
}
else
{
	$offering = new CoursesModelOffering(0, $this->course->get('id'));
}*/

Hubzero_Document::addComponentScript('com_courses', 'assets/js/courses.overview');
?>
<div id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
	<h2>
		<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
	</h2>
	<?php if ($this->course->get('logo')) { ?>
	<p class="course-identity">
		<img src="/site/courses/<?php echo $this->course->get('id'); ?>/<?php echo $this->course->get('logo'); ?>" alt="<?php echo JText::_('Course logo'); ?>" />
	</p>
	<?php } ?>
	<p id="page_identity">
		<a class="icon-browse browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
			<?php echo JText::_('Course catalog'); ?>
		</a>
	</p>
</div>

<div class="course section intro">
	<div class="aside">
	<?php
$c = 0;
if ($offerings->total())
{
	foreach ($offerings as $offering) 
	{
		if (!$offering->isAvailable())
		{
			continue;
		}
		$c++;

		$controls = '';

		if ($this->sections)
		{
			foreach ($this->sections as $section)
			{
				if (isset($section['controls']) && $section['controls'] != '') 
				{
					$controls = $section['controls'];
				}
			}
		}

		if (!$controls) 
		{
			$memberships = $offering->membership();
			if (!count($memberships))
			{
				$memberships[] = new CoursesModelMember(JFactory::getUser()->get('id'), $this->course->get('id'), $offering->get('id'));
			}

			$mng  = -1;
			$last = '';
			foreach ($memberships as $membership)
			{
				$cur  = ($membership->get('offering_id') ? $membership->get('offering_id') : $offering->get('id')) . '-';
				$cur .= ($membership->get('section_id') ? $offering->section($membership->get('section_id'))->get('alias') : $offering->section()->get('alias'));

				if ($cur == $last || $mng == $offering->get('id'))
				{
					continue;
				}
				$last = $cur;

				// If they're a course level manager
				if ($membership->get('course_id') && !$membership->get('section_id') && !$membership->get('student'))
				{
					$mng = $offering->get('id');

					// Get the default section
					$dflt = $offering->section('__default');
					if (!$dflt->exists())
					{
						// No default? Get the first in the list
						$dflt = $offering->sections()->fetch('first');
					}
				?>
			<div class="offering-info">
				<table>
					<tbody>
						<tr>
							<th scope="row"><?php echo JText::_('Offering:'); ?></th>
							<td>
								<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo JText::_('Section:'); ?></th>
							<td>
								<?php echo $offering->sections()->total() > 1 ? JText::_('--') : $this->escape(stripslashes($dflt->get('title'))); ?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php if ($offering->sections()->total() > 1) { ?>
				<div class="btn-group-wrap">
					<div class="btn-group dropdown">
						<a class="btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias'). '&offering=' . $offering->get('alias') . ($dflt->get('alias') != '__default' ? ':' . $dflt->get('alias') : '')); ?>"><?php echo $this->escape(stripslashes($dflt->get('title'))); ?></a>
						<span class="btn dropdown-toggle"></span>
						<ul class="dropdown-menu">
						<?php 
						foreach ($offering->sections() as $section) 
						{
							// Skip the default
							if ($section->get('alias') == $dflt->get('alias'))
							{
								continue;
							}
							?>
							<li>
								<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias'). '&offering=' . $offering->get('alias') . ($section->get('alias') != '__default' ? ':' . $section->get('alias') : '')); ?>">
									<?php echo $this->escape(stripslashes($section->get('title'))); ?>
								</a>
							</li>
							<?php
						}
						?>
						</ul>
						<div class="clear"></div>
					</div><!-- /btn-group -->
				</div>
				<?php } else { ?>
				<p>
					<a class="outline btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias'). '&offering=' . $offering->get('alias') . ($dflt->get('alias') != '__default' ? ':' . $dflt->get('alias') : '')); ?>">
						<?php echo JText::_('Access Course'); ?>
					</a>
				</p>
				<?php } ?>
			</div><!-- / .offering-info -->
					<?php
				}
				else
				{
					?>
			<div class="offering-info">
				<table>
					<tbody>
						<tr>
							<th scope="row"><?php echo JText::_('Offering:'); ?></th>
							<td>
								<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo JText::_('Section:'); ?></th>
							<td>
								<?php echo ($membership->get('section_id') ? $this->escape(stripslashes($offering->section($membership->get('section_id'))->get('title'))) : $this->escape(stripslashes($offering->section()->get('title')))); ?>
							</td>
						</tr>
					</tbody>
				</table>
			<?php if ($offering->access('view', 'section') || $this->course->isStudent()) { //$this->course->isManager() ?>
				<p>
					<a class="outline btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
						<?php echo JText::_('Access Course'); ?>
					</a>
				</p>
			<?php } else if ($offering->section()->get('enrollment') != 2) { ?>
				<p>
					<a class="enroll btn" href="<?php echo JRoute::_($offering->link('enroll')); ?>">
						<?php echo JText::_('Enroll in Course'); ?>
					</a>
				</p>
			<?php } ?>
			</div><!-- / .offering-info -->
				<?php
				}
			}
		} else {
			echo '<div class="offering-info">' . $controls . '</div><!-- / .offering-info -->';
		}
	}
}
if (!$c)
{
	?>
		<div class="offering-info">
			<p>
				<?php echo JText::_('No offerings available.'); ?>
			</p>
		</div><!-- / .offering-info -->
	<?php
}
?>
	</div><!-- / .aside -->
	<div class="subject">
		<p>
			<?php echo $this->escape(stripslashes($this->course->get('blurb'))); ?>
		</p>

		<?php echo $this->course->tags('cloud'); ?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .course section intro -->

<div class="course section">
	<div class="aside">
		<?php
		$instructors = $this->course->instructors();
		if (count($instructors) > 0) 
		{
		?>
		<div class="course-instructors" data-bio-length="200">
			<h3>
				<?php echo (count($instructors) > 1) ? JText::_('About the Instructors') : JText::_('About the Instructor'); ?>
			</h3>
			<?php
			ximport('Hubzero_View_Helper_Html');

			foreach ($instructors as $i)
			{
				$view = new JView(array(
					'name'   => 'course',
					'layout' => '_instructor'
				));
				$view->biolength  = 200;
				$view->instructor = Hubzero_User_Profile::getInstance($i->get('user_id'));
				$view->display();
			}
			?>
		</div>
		<?php
		}

		if ($this->sections)
		{
			foreach ($this->sections as $section)
			{
				if ($section['metadata'] != '') 
				{
					echo $section['metadata'];
				}
			}
		}
		?>
	</div><!-- / .aside -->

	<div class="subject">

		<ul class="sub-menu">
			<?php
			if ($this->cats)
			{
				$i = 1;
				foreach ($this->cats as $cat)
				{
					$name = key($cat);
					if ($name != '') 
					{
						$url = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&active=' . $name);

						if (strtolower($name) == $this->active) 
						{
							$pathway =& JFactory::getApplication()->getPathway();
							$pathway->addItem($cat[$name], $url);

							if ($this->active != 'overview') 
							{
								$document =& JFactory::getDocument();
								$document->setTitle($document->getTitle() . ': ' . $cat[$name]);
							}
						}
						?>
						<li id="sm-<?php echo $i; ?>"<?php echo (strtolower($name) == $this->active) ? ' class="active"' : ''; ?>>
							<a class="tab" rel="<?php echo $name; ?>" href="<?php echo $url; ?>">
								<span><?php echo $this->escape($cat[$name]); ?></span>
							</a>
						</li>
						<?php
						$i++;
					}
				}
			}
			?>
		</ul>

		<?php
		foreach ($this->notifications as $notification) 
		{
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
		?>

		<?php
		if ($this->sections)
		{
			$k = 0;
			foreach ($this->sections as $section)
			{
				if ($section['html'] != '') 
				{
					?>
					<div class="inner-section" id="<?php echo $section['name']; ?>-section">
						<?php echo $section['html']; ?>
					</div><!-- / .inner-section -->
					<?php 
				}
				$k++;
			}
		}
		?>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .course section -->

<?php
JPluginHelper::importPlugin('courses');

$after = JDispatcher::getInstance()->trigger('onCourseViewAfter', array($this->course));
if ($after && count($after) > 0) { ?>
<div class="below course section">
	<?php echo implode("\n", $after); ?>
</div><!-- / .course section -->
<?php } ?>