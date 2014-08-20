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

$field  = strtolower(JRequest::getWord('field', ''));
$action = strtolower(JRequest::getWord('action', ''));

$offerings = $this->course->offerings(array(
	'available' => false,
	'sort'      => 'publish_up'
), true);

$this->css('course.css')
     ->js()
     ->js('courses.overview.js');
?>
<header id="content-header">
	<h2><?php echo JText::_('COM_COURSES'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-browse browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=browse'); ?>">
				<?php echo JText::_('COM_COURSES_CATALOG'); ?>
			</a>
		</p>
	</div>
</header>

<section class="course section intro<?php echo ($this->course->get('logo')) ? ' with-identity' : ''; ?>">
	<div class="section-inner">
		<div class="subject">
			<?php if (($field == 'blurb' || $field == 'tags') && $this->course->access('edit', 'course')) { ?>
				<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" class="form-inplace" method="post">
					<label for="field_title">
						<input type="text" name="course[title]" id="field_title" value="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
					</label>

					<label for="field_blurb">
						<textarea name="course[blurb]" id="field_blurb" cols="50" rows="5"><?php echo $this->escape(stripslashes($this->course->get('blurb'))); ?></textarea>
					</label>

					<label for="actags">
						<?php echo JText::_('COM_COURSES_FIELD_TAGS'); ?>

						<?php
						JPluginHelper::importPlugin( 'hubzero' );
						$dispatcher = JDispatcher::getInstance();
						$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->course->tags('string'))) );
						$tf = implode("\n", $tf);

						if ($tf) {
							echo $tf;
						} else { ?>
							<input type="text" name="tags" id="actags" value="<?php echo $this->escape($this->couse->tags('string')); ?>" />
						<?php } ?>

						<span class="hint"><?php echo JText::_('COM_COURSES_FIELD_TAGS_HINT'); ?></span>
					</label>

					<p class="submit">
						<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_COURSES_SAVE'); ?>" />
						<a class="btn btn-secondary" href="<?php echo JRoute::_($this->course->link()); ?>">
							<?php echo JText::_('COM_COURSES_CANCEL'); ?>
						</a>
					</p>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="course" />
					<input type="hidden" name="task" value="save" />

					<?php echo JHTML::_('form.token'); ?>

					<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
					<input type="hidden" name="course[id]" value="<?php echo $this->escape($this->course->get('id')); ?>" />
					<input type="hidden" name="course[alias]" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
				</form>
			<?php } else { ?>
				<?php if ($this->course->access('edit', 'course')) { ?>
					<div class="manager-options">
						<a class="icon-edit btn btn-secondary" href="<?php echo JRoute::_($this->course->link() . '&task=edit&field=blurb'); ?>">
							<?php echo JText::_('COM_COURSES_EDIT'); ?>
						</a>
						<span><strong><?php echo JText::_('COM_COURSES_FIELDS_TITLE_BLURB'); ?></strong></span>
					</div>
				<?php } ?>

				<div id="course-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
					<h2>
						<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
					</h2>
				</div>
				<p>
					<?php echo $this->escape(stripslashes($this->course->get('blurb'))); ?>
				</p>

				<?php echo $this->course->tags('cloud'); ?>

				<?php if ($this->course->get('group_id')) { ?>
					<div class="course-group">
						<?php
						$group = \Hubzero\User\Group::getInstance($this->course->get('group_id'));

						list($width, $height) = $group->getLogo('size');
						$atts = ($width > $height ? 'height="50"' : 'width="50"');
						?>
						<p class="course-group-img">
							<a href="<?php echo JRoute::_('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
								<img src="<?php echo $group->getLogo(); ?>" <?php echo $atts; ?> alt="<?php echo $this->escape(stripslashes($group->get('description'))); ?>" />
							</a>
						</p>
						<p class="course-group-description">
							<?php echo JText::_('COM_COURSES_BROUGHT_BY_GROUP'); ?>
						</p>
						<h3 class="course-group-title">
							<a href="<?php echo JRoute::_('index.php?option=com_courses&task=browse&group=' . $group->get('cn')); ?>">
								<?php echo $this->escape(stripslashes($group->get('description'))); ?>
							</a>
						</h3>
					</div>
				<?php } ?>
			<?php } ?>
		</div><!-- / .subject -->
		<aside class="aside">
			<p class="course-identity">
				<?php if ($logo = $this->course->logo()) { ?>
					<img src="<?php 
						$size = $this->course->logo('size');
						echo $logo;
						?>" class="<?php echo ($size['width'] >= $size['height'] ? 'landscape' : 'portrait'); ?>" alt="<?php echo $this->escape($this->course->get('title')); ?>" />
				<?php } else { ?>
					<span></span>
				<?php } ?>
			</p>
		</aside><!-- / .aside -->
	</div>
</section><!-- / .course section intro -->

<?php if ($this->course->access('edit', 'course') && !$offerings->total()) { ?>
	<section class="course section intro offering-help">
		<div class="section-inner">
			<div class="subject">
				<p>
					<strong><?php echo JText::_('COM_COURSES_COURSE_NEEDS_AN_OFFERING'); ?></strong></p>
					<?php echo JText::_('COM_COURSES_COURSE_NEEDS_AN_OFFERING_EXPLANATION'); ?>
				</p>
			</div><!-- / .subject -->
			<aside class="aside">
				<p>
					<a class="icon-add btn" id="add-offering" href="<?php echo JRoute::_($this->course->link() . '&task=newoffering'); ?>">
						<?php echo JText::_('COM_COURSES_CREATE_OFFERING'); ?>
					</a>
				</p>
			</aside><!-- / .aside -->
		</div>
	</section><!-- / .course section intro offering-help -->
<?php } ?>

<section class="course section">
	<div class="subject">
		<ul class="sub-menu">
			<?php
			if ($action == 'addpage')
			{
				$this->active = '';
			}
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
							$pathway = JFactory::getApplication()->getPathway();
							$pathway->addItem($cat[$name], $url);

							if ($this->active != 'overview')
							{
								$document = JFactory::getDocument();
								$document->setTitle($document->getTitle() . ': ' . $cat[$name]);
							}
							if ($this->isPage)
							{
								$this->isPage = $name;
							}
						}
						?>
						<li id="sm-<?php echo $i; ?>"<?php echo (strtolower($name) == $this->active) ? ' class="active"' : ''; ?>>
							<a class="tab" data-rel="<?php echo $name; ?>" href="<?php echo $url; ?>">
								<span><?php echo $this->escape($cat[$name]); ?></span>
							</a>
						</li>
						<?php
						$i++;
					}
				}
			}
			?>
		<?php if ($this->course->access('edit', 'course')) { ?>
			<li class="add-page">
				<a class="icon-add tab" href="<?php echo JRoute::_($this->course->link() . '&action=addpage'); ?>">
					<?php echo JText::_('PLG_COURSES_PAGES_ADD_PAGE'); ?>
				</a>
			</li>
		<?php } ?>
		</ul>

		<?php
		foreach ($this->notifications as $notification)
		{
			echo '<p class="' . $notification['type'] . '">' . $notification['message'] . '</p>';
		}

		if (($action == 'addpage' || $action == 'editpage') && $this->course->access('edit', 'course'))
		{
			$page = $this->course->page($this->active);
			?>
			<div class="inner-section" id="addpage-section">
				<form action="<?php echo JRoute::_($this->course->link()); ?>" class="form-inplace" method="post">
					<fieldset>
						<div class="grid">
							<div class="col span-half">
								<label for="field-title">
									<?php echo JText::_('PLG_COURSES_PAGES_FIELD_TITLE'); ?> <span class="required"><?php echo JText::_('PLG_COURSES_PAGES_REQUIRED'); ?></span>
									<input type="text" name="page[title]" id="field-title" value="<?php echo $this->escape(stripslashes($page->get('title'))); ?>" />
									<span class="hint"><?php echo JText::_('PLG_COURSES_PAGES_FIELD_TITLE_HINT'); ?></span>
								</label>
							</div>
							<div class="col span-half omega">
								<label for="field-url">
									<?php echo JText::_('PLG_COURSES_PAGES_FIELD_ALIAS'); ?> <span class="optional"><?php echo JText::_('PLG_COURSES_PAGES_OPTINAL'); ?></span>
									<input type="text" name="page[url]" id="field-url" value="<?php echo $this->escape(stripslashes($page->get('url'))); ?>" />
									<span class="hint"><?php echo JText::_('PLG_COURSES_PAGES_FIELD_ALIAS_HINT'); ?></span>
								</label>
							</div>
						</div>

						<label for="field_description">
							<?php
								echo \JFactory::getEditor()->display('page[content]', $this->escape(stripslashes($page->get('content'))), '', '', 35, 50, false, 'field_content');
							?>
						</label>

						<p class="submit">
							<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_COURSES_SAVE'); ?>" />
							<a class="btn btn-secondary" href="<?php echo JRoute::_($this->course->link()); ?>">
								<?php echo JText::_('COM_COURSES_CANCEL'); ?>
							</a>
						</p>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="course" />
						<input type="hidden" name="task" value="savepage" />

						<?php echo JHTML::_('form.token'); ?>

						<input type="hidden" name="gid" value="<?php echo $this->escape($this->course->get('alias')); ?>" />
						<input type="hidden" name="page[id]" value="<?php echo $this->escape($page->get('id')); ?>" />
						<input type="hidden" name="page[alias]" value="<?php echo $this->escape($page->get('alias')); ?>" />
						<input type="hidden" name="page[course_id]" value="<?php echo $this->course->get('id'); ?>" />
						<input type="hidden" name="page[section_id]" value="0" />
						<input type="hidden" name="page[offering_id]" value="0" />
					</fieldset>
				</form>
			</div>
			<?php
		}
		elseif ($this->sections)
		{
			$k = 0;
			foreach ($this->sections as $section)
			{
				if ($section['html'] != '')
				{
					?>
					<div class="inner-section" id="<?php echo $section['name']; ?>-section">
						<?php if ($this->course->access('edit', 'course') && $this->isPage) { ?>
							<div class="manager-options">
								<a class="icon-error btn btn-secondary btn-danger" href="<?php echo JRoute::_($this->course->link() . '&active=' . $this->isPage . '&task=deletepage'); ?>">
									<?php echo JText::_('COM_COURSES_DELETE'); ?>
								</a>
								<a class="icon-edit btn btn-secondary" href="<?php echo JRoute::_($this->course->link() . '&active=' . $this->isPage . '&action=editpage'); ?>">
									<?php echo JText::_('COM_COURSES_EDIT'); ?>
								</a>
								<span><strong><?php echo JText::_('COM_COURSES_PAGE_CONTENTS'); ?></strong></span>
							</div>
						<?php } ?>
						<?php echo $section['html']; ?>
					</div><!-- / .inner-section -->
					<?php
				}
				$k++;
			}
		}
		?>
	</div><!-- / .subject -->
	<aside class="aside">
		<?php
		$c = 0;
		if ($offerings->total())
		{
			$found = false;
			$now = JFactory::getDate()->toSql();

			// If the user is a manager
			if ($this->course->isManager())
			{
				foreach ($offerings as $offering)
				{
					if ($offering->isDeleted())
					{
						continue;
					}
					$c++;

					// Try to load the default section
					$dflt = $offering->section('!!default!!');
					if (!$dflt->exists())
					{
						// No default? Get the first in the list
						if (!$offering->sections()->total())
						{
							$offering->makeSection();
						}
						$dflt = $offering->sections()->fetch('first');
					}
					$offering->section($dflt->get('alias'));

					$found = true;
					?>
					<div class="offering-info">
						<table>
							<tbody>
								<tr>
									<th scope="row"><?php echo JText::_('COM_COURSES_OFFERING'); ?>:</th>
									<td>
										<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
									</td>
								</tr>
						<?php if ($offering->sections()->total() > 1) { ?>
							</tbody>
						</table>
						<div class="btn-group-wrap">
							<div class="btn-group dropdown">
								<a class="btn" href="<?php echo JRoute::_($offering->link('enter')); ?>"><?php echo $this->escape(stripslashes($dflt->get('title'))); ?></a>
								<span class="btn dropdown-toggle"></span>
								<ul class="dropdown-menu">
								<?php
								foreach ($offering->sections() as $section)
								{
									// Skip the default
									if ($section->get('alias') == $dflt->get('alias') || $section->isDeleted())
									{
										continue;
									}
									// Set the section
									$offering->section($section->get('id'));
									?>
									<li>
										<a href="<?php echo JRoute::_($offering->link()); ?>">
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
								<tr>
									<th scope="row"><?php echo JText::_('COM_COURSES_SECTION'); ?>:</th>
									<td>
										<?php echo $offering->sections()->total() > 1 ? JText::_('COM_COURSES_SECTIONS_MANY') : $this->escape(stripslashes($dflt->get('title'))); ?>
									</td>
								</tr>
							</tbody>
						</table>
						<p>
							<a class="access btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
								<?php echo JText::_('COM_COURSES_ACCESS_COURSE'); ?>
							</a>
						</p>
						<?php } ?>
					</div><!-- / .offering-info -->
					<?php
				}
			}
			// If the user is a student
			else if ($this->course->isStudent())
			{
				foreach ($offerings as $offering)
				{
					if (!$offering->isAvailable())
					{
						continue;
					}
					$c++;

					foreach ($offering->sections() as $sect)
					{
						// If section is in draft mode or not published
						if ($sect->isDraft() || !$sect->isPublished())
						{
							continue;
						}
						// If section hasn't started
						if ($sect->get('publish_up') != '0000-00-00 00:00:00' && $sect->get('publish_up') > $now)
						{
							continue;
						}
						// If a publish down time is set and that time happened before now
						if ($sect->get('publish_down') != '0000-00-00 00:00:00' && $sect->get('publish_down') <= $now)
						{
							continue;
						}
						// If not already a member and enrollment is closed
						if (!$sect->isMember())
						{
							continue;
						}

						$found = true;

						$offering->section($sect->get('alias'));
						?>
						<div class="offering-info">
							<table>
								<tbody>
									<tr>
										<th scope="row"><?php echo JText::_('COM_COURSES_OFFERING'); ?>:</th>
										<td>
											<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo JText::_('COM_COURSES_SECTION'); ?>:</th>
										<td>
											<?php echo $this->escape(stripslashes($offering->section()->get('title'))); ?>
										</td>
									</tr>
								</tbody>
							</table>
							<p>
								<a class="access btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
									<?php echo JText::_('COM_COURSES_ACCESS_COURSE'); ?>
								</a>
							</p>
						</div><!-- / .offering-info -->
						<?php
					}
				}
			}

			if (!$found)
			{
				foreach ($offerings as $offering)
				{
					if (!$offering->isAvailable())
					{
						continue;
					}
					$c++;

					foreach ($offering->sections() as $sect)
					{
						// If section is in draft mode or not published
						if ($sect->isDraft() || !$sect->isPublished())
						{
							continue;
						}
						// If section hasn't started or has ended
						if (!$sect->started() || $sect->ended())
						{
							continue;
						}
						// If a publish down time is set and that time happened before now
						if ($sect->get('publish_down') != '0000-00-00 00:00:00' && $sect->get('publish_down') <= $now)
						{
							continue;
						}
						// If not already a member and enrollment is closed
						if (!$sect->isMember() && $sect->get('enrollment') == 2)
						{
							continue;
						}

						$offering->section($sect->get('alias'));
						?>
						<div class="offering-info">
							<table>
								<tbody>
									<tr>
										<th scope="row"><?php echo JText::_('COM_COURSES_OFFERING'); ?>:</th>
										<td>
											<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><?php echo JText::_('COM_COURSES_SECTION'); ?>:</th>
										<td>
											<?php echo $this->escape(stripslashes($offering->section()->get('title'))); ?>
										</td>
									</tr>
								</tbody>
							</table>
						<?php if ($offering->section()->isMember()) { ?>
							<p>
								<a class="access btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
									<?php echo JText::_('COM_COURSES_ACCESS_COURSE'); ?>
								</a>
							</p>
						<?php } else if ($offering->section()->get('enrollment') != 2) { ?>
							<p>
								<a class="enroll btn" href="<?php echo JRoute::_($offering->link('enroll')); ?>">
									<?php echo JText::_('COM_COURSES_ACCESS_COURSE'); ?>
								</a>
							</p>
						<?php } ?>
						<?php if ($offering->section()->params('preview', 0)) { ?>
							<p>
								<a class="preview btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
									<?php echo JText::_('COM_COURSES_PREVIEW_COURSE'); ?>
								</a>
							</p>
						<?php } ?>
						</div><!-- / .offering-info -->
						<?php
					}
				}
			}
		}
		if (!$c)
		{
			?>
				<div class="offering-info">
					<p>
						<?php echo JText::_('COM_COURSES_NO_OFFERINGS_AVAILABLE'); ?>
					</p>
				</div><!-- / .offering-info -->
			<?php
		}
		?>
		<?php
		if ($this->course->access('edit', 'course'))
		{
			?>
			<div class="manager-options">
				<a class="icon-edit btn btn-secondary" id="manage-instructors" href="<?php echo JRoute::_($this->course->link() . '&task=instructors'); ?>">
					<?php echo JText::_('COM_COURSES_MANAGE'); ?>
				</a>
				<span><strong><?php echo JText::_('COM_COURSES_MANAGE_INSTRUCTORS'); ?></strong></span>
			</div>
			<?php
		}
		$instructors = $this->course->instructors();
		if (count($instructors) > 0)
		{
		?>
		<div class="course-instructors" data-bio-length="200">
			<h3>
				<?php echo (count($instructors) > 1) ? JText::_('COM_COURSES_ABOUT_THE_INSTRUCTORS') : JText::_('COM_COURSES_ABOUT_THE_INSTRUCTOR'); ?>
			</h3>
			<?php
			foreach ($instructors as $i)
			{
				$this->view('_instructor')
				     ->set('biolength', 200)
				     ->set('instructor', \Hubzero\User\Profile::getInstance($i->get('user_id')))
				     ->display();
			}
			?>
		</div>
		<?php
		}
		else
		{
		?>
		<div class="course-instructors-none">
			<?php echo JText::_('COM_COURSES_NO_INSTRUCTORS_FOUND'); ?>
		</div>
		<?php
		}
		?>
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
		?>
	</aside><!-- / .aside -->
</section><!-- / .course section -->

<?php
JPluginHelper::importPlugin('courses');

$after = JDispatcher::getInstance()->trigger('onCourseViewAfter', array($this->course));
if ($after && count($after) > 0) { ?>
<section class="below course section">
	<?php echo implode("\n", $after); ?>
</section><!-- / .course section -->
<?php } ?>