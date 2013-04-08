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
defined('_JEXEC') or die('Restricted access');

$results = null;
$notes = $this->model->notes($this->filters);
if ($notes)
{
	foreach ($notes as $note)
	{
		$ky = $note->get('scope_id'); //$note->get('scope') . '_' . $note->get('scope_id');
		if (!isset($results[$ky]))
		{
			$results[$ky] = array();
		}
		$results[$ky][] = $note;
	}
}

$base = 'index.php?option=com_courses&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias');
?>
<form action="<?php echo JRoute::_($base . '&active=notes'); ?>" method="get">
	<fieldset class="filters">
		<div class="filters-inner">
			<ul>
				<li>
					<a class="download btn" href="<?php echo JRoute::_($base . '&active=notes&action=download&frmt=txt'); ?>">
						<span><?php echo JText::_('Download'); ?></span>
					</a>
					<!-- <ul>
						<li>
							<a class="download" href="<?php echo JRoute::_($base . '&active=notes&action=download&frmt=txt'); ?>">
								<span><?php echo JText::_('Text file (txt)'); ?></span>
							</a>
						</li>
						<li>
							<a class="download" href="<?php echo JRoute::_($base . '&active=notes&action=download&frmt=csv'); ?>">
								<span><?php echo JText::_('Comma-separated values (csv)'); ?></span>
							</a>
						</li>
					</ul> -->
				</li>
			</ul>
			<div class="clear"></div>
			<p>
				<label for="filter-search">
					<span><?php echo JText::_('Search'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search notes'); ?>" />
				</label>
				<input type="submit" class="filter-submit" value="<?php echo JText::_('Go'); ?>" />
			</p>
		</div><!-- / .filters-inner -->
	</fieldset>
</form>

	<div class="notes-wrap">
	<?php
	if ($results)
	{
		foreach ($results as $id => $notes)
		{
			$lecture = new CoursesModelAssetgroup($id);
			$unit = CoursesModelUnit::getInstance($lecture->get('unit_id'));
?>
<div class="section">
	<h3><?php echo $this->escape(stripslashes($lecture->get('title'))); ?></h3>
<?php
			foreach ($notes as $note)
			{
	?>
	<div class="jSticky-medium static" id="note-<?php echo $note->get('id'); ?>" data-id="<?php echo $note->get('id'); ?>">
		<div class="jSticky-header">
			<?php if ($note->get('timestamp') != '00:00:00') { ?>
				<a href="<?php echo str_replace('%3A', ':', JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $lecture->get('alias') . '&time=' . $this->escape($note->get('timestamp')))); ?>" class="time"><?php echo $this->escape($note->get('timestamp')); ?></a>
			<?php } ?>
		</div>
		<div class="jStickyNote">
			<textarea name="note_<?php echo $note->get('id'); ?>"><?php echo $this->escape(stripslashes($note->get('content'))); ?></textarea>
		</div>
		<a class="jSticky-delete" href="<?php echo JRoute::_($base . '&active=notes&action=delete&note=' . $note->get('id')); ?>" title="<?php echo JText::_('Delete note'); ?>">x</a>
	</div>
	<?php
			}
?>
	<div class="clear"></div>
</div>
<?php
		}
?>
<script type="text/javascript">
jQuery(document).ready(function(jQuery){
	var $ = jQuery;

	var url = "<?php echo '/index.php?option=com_courses&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=notes&no_html=1&note='; ?>";

	$('#page_content textarea').each(function(i, el){
		$(el).on('keyup', function (e) {
			var id  = $(this).parent().parent().attr('data-id'),
				txt = $(this).val();

			typewatch(function () {
				$.getJSON(url + id + '&action=save&txt=' + txt, {}, function(data) {
					// Nothing going on here...
				});
			}, 500);
		});
	});
});
</script>
<?php
	}
	else
	{
?>
<div id="notes-introduction">
	<div class="instructions">
		<!-- <ol>
			<li><?php echo JText::_('Find images, files, links or text you want to share.'); ?></li>
			<li><?php echo JText::_('Click on "New post" button.'); ?></li>
			<li><?php echo JText::_('Add anything extra you want (tags are nice).'); ?></li>
			<li><?php echo JText::_('Done!'); ?></li>
		</ol> -->
		<p><?php echo JText::_('You currently have no notes.'); ?></p>
	</div><!-- / .instructions -->
	<div class="questions">
		<p><strong><?php echo JText::_('What are notes?'); ?></strong></p>
		<p><?php echo JText::_('Some text here'); ?></p>
		<p><strong><?php echo JText::_('How do I add a note?'); ?></strong></p>
		<p><?php echo JText::_('Some text here'); ?></p>
	</div><!-- / .post-type -->
</div><!-- / #collection-introduction -->
<?php
	}
	?>
	</div>
