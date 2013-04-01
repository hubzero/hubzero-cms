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

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$tz = true;
}

//get objects
$config   =& JFactory::getConfig();
$database =& JFactory::getDBO();

/*if ($this->course->isStudent())
{
	$student = $this->course->member($this->juser->get('id'));
}*/

$offerings = $this->course->offerings(array('available' => true, 'sort' => 'publish_up'));
if ($offerings)
{
	$offering = $offerings->fetch('first');
}
else
{
	$offering = new CoursesModelOffering(0, $this->course->get('id'));
}
?>
<div id="content-header">
	<h2>
		<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
	</h2>
</div>
<div id="content-header-extra">
	<ul>
		<li>
			<a class="browse btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
				<?php echo JText::_('Browse courses'); ?>
			</a>
		</li>
	</ul>
</div>

<div class="course section">
	<div class="aside">
		<?php
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
		else
		{
			if ($offering->exists()) { ?>
					<div class="offering-info">
						<table>
							<tbody>
								<tr>
									<th scope="row">Starts</th>
									<td>
										<time datetime="<?php echo $offering->get('publish_up'); ?>"><?php echo JHTML::_('date', $offering->get('publish_up'), $dateformat, $tz); ?></time>
									</td>
								</tr>
								<tr>
									<th scope="row">Ends</th>
									<td>
										<time datetime="<?php echo $offering->get('publish_down'); ?>"><?php echo ($offering->get('publish_down') == '0000-00-00 00:00:00') ? JText::_('(never)') : JHTML::_('date', $offering->get('publish_down'), $dateformat, $tz); ?></time>
									</td>
								</tr>
							</tbody>
						</table>
					<?php if ($this->course->isManager() || $this->course->isStudent()) { ?>
						<p>
							<a class="outline btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias')); ?>">
								View outline
							</a>
						</p>
					<?php } else { ?>
						<p>
							<a class="enroll btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias') . '&task=enroll'); ?>">
								Enroll
							</a>
						</p>
					<?php } ?>
					</div>
			<?php }
		}
		?>
	</div>
	
	<div class="subject">
		<div id="sub-menu">
			<ul>
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
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
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
		</div><!-- / #sub-menu -->

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
		<div class="inner-section" id="<?php echo $section['area']; ?>-section">
			<?php echo $section['html']; ?>
		</div><!-- / .inner-section -->
				<?php 
				}
				$k++;
			}
		}
		?>
	</div><!-- / .subject -->
</div><!-- / .course section -->

<?php
JPluginHelper::importPlugin('courses');
$dispatcher =& JDispatcher::getInstance();

$after = $dispatcher->trigger('onCourseViewAfter', array($this->course));
if ($after && count($after) > 0) { ?>
<div class="below course section">
	<?php echo implode("\n", $after); ?>
</div><!-- / .course section -->
<?php } ?>