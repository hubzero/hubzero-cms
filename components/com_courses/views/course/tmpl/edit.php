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

$this->css('course.css')
     ->js();

//tag editor
JPluginHelper::importPlugin( 'hubzero' );
$dispatcher = JDispatcher::getInstance();
$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->course->tags('string'))) );

//build back link
$host = JRequest::getVar("HTTP_HOST", '', "SERVER");
$referrer = JRequest::getVar("HTTP_REFERER", '', "SERVER");

//check to make sure referrer is a valid url
//check to make sure the referrer is a link within the HUB
if (filter_var($referrer, FILTER_VALIDATE_URL) === false || $referrer == '' || strpos($referrer, $host) === false)
{
	$link = JRoute::_('index.php?option=' . $this->option);
}
else
{
	$link = $referrer;
}

//if we are in edit mode we want to redirect back to course
if ($this->task == 'edit')
{
	$link = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias'));
	$title = 'Back to Course';
}
else
{
	$title = 'Back';
}

if (!$this->course->exists())
{
	$default = "=== About ===

Eneter text about your course.

=== Audience ===

Who is this course appropriate for?

=== Prerequisites ===

This course is intended to be broadly accessible to those with a background in ''fill in here'' and the following prerequisites are recommended:

 * A working knowledge of ''fill in here''
 * A basic understanding of ''fill in here''";
	$this->course->set('description', $default);
}
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="prev btn" href="<?php echo $link; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<nav id="steps">
	<ol class="steps-5">
		<li id="step-1" class="active">Creating a catalog entry</li>
		<li id="step-2">Describe your course</li>
		<li id="step-3">Create an offering</li>
		<li id="step-4">Fill out a syllabus</li>
		<li id="step-5">Make public</li>
	</ol>
</nav>

<section class="main section">
	<?php
		foreach ($this->notifications as $notification) {
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>

<?php if ($this->task != 'new' && !$this->course->get('published')) { ?>
	<p class="warning"><?php echo JText::_('COM_COURSES_STATUS_NEW_COURSE'); ?></p>
<?php } ?>

	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<!-- <h3>Looking for a course?</h3>
			<p>Browse the course catalog. Courses can be found by category (tags), searching, popularity, or title.</p>

			<h3>What happens when I click "save"?</h3>
			<p>Then something else. Duh.</p> -->
			<h3>Step 1</h3>
			<p>Here is where we'll create a catalog entry and start describing the course. We'll build an outline and add files in a later step.</p>
		</div>
		<fieldset id="top_box">
			<legend><?php echo JText::_('Creating a catalog entry'); ?></legend>
<?php if ($this->task != 'new') { ?>
			<input name="alias" type="hidden" value="<?php echo $this->course->get('alias'); ?>" />
<?php } else { ?>
			<label class="course_alias_label" for="course_alias_field">
				<?php echo JText::_('Course identifier'); ?> <span class="required"><?php echo JText::_('COM_COURSES_REQUIRED'); ?></span>
				<input name="course[alias]" id="course_alias_field" type="text" size="35" value="<?php echo $this->escape($this->course->get('alias')); ?>" autocomplete="off" />
				<span class="hint"><?php echo JText::_('This is a short identifier used for URLs, catalogs, etc. Allowed characters are letters, numbers, dashes, underscores, and periods. Example: biology101, chem.501'); ?></span>
			</label>
<?php } ?>

			<label for="field-title">
				<?php echo JText::_('COM_COURSES_TITLE'); ?> <span class="required"><?php echo JText::_('COM_COURSES_REQUIRED'); ?></span>
				<input type="text" name="course[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
			</label>

			<label for="field_blurb">
				<?php echo JText::_('Brief description'); ?> <span class="optional"><?php echo JText::_('COM_COURSES_OPTIONAL'); ?></span>
				<textarea name="course[blurb]" id="field-blurb" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->course->get('blurb'))); ?></textarea>
				<span class="hint">
					A brief, one or two sentences about your course. Think of this as the text you would see in a course catalog.
				</span>
			</label>

			<label for="field_description">
				<?php echo JText::_('Overview'); ?> <span class="optional"><?php echo JText::_('COM_COURSES_OPTIONAL'); ?></span>

				<?php
					echo \JFactory::getEditor()->display('course[description]', $this->escape(stripslashes($this->course->get('description'))), '', '', 35, 30, false, 'field_description');
				?>
				<span class="hint"><a class="popup" href="<?php echo JRoute::_('index.php?option=com_wiki&scope=&pagename=Help:WikiFormatting'); ?>">Wiki formatting</a> is allowed.</span>

				<dl>
					<dt>What this is:</dt>
					<dd>This is a general "about" page. Typically, this would include descriptions of audience and prerequisites.</dd>

					<dt>What this is <i>not</i>:</dt>
					<dd>A syllabus or detailed outline. You will have an opportunity to fill that out later.</dd>
				</dl>
			</label>

			<label for="actags">
				<?php echo JText::_('Tags'); ?> <span class="optional"><?php echo JText::_('COM_COURSES_OPTIONAL'); ?></span>

				<?php if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" id="actags" value="<?php echo $this->couse->tags('string'); ?>" />
				<?php } ?>

				<span class="hint">These are keywords that describe your course and will help people find it when browsing, searching, or viewing related content. <?php echo JText::_('COM_COURSES_FIELD_TAGS_HINT'); ?></span>
			</label>
		</fieldset>
		<div class="clear"></div>

		<div class="clear"></div>
		<input type="hidden" name="course[state]" value="<?php echo $this->course->get('state'); ?>" />
		<input type="hidden" name="course[id]" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />

		<?php echo JHTML::_('form.token'); ?>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('Save'); ?>" />
		</p>
	</form>
</section><!-- / .section -->
