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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js();

$results = null;
$notes = $this->model->notes($this->filters);
if ($notes)
{
	foreach ($notes as $note)
	{
		$ky = $note->get('scope_id');
		if (!isset($results[$ky]))
		{
			$results[$ky] = array();
		}
		$results[$ky][] = $note;
	}
}

$base = $this->offering->link();
?>

<?php if ($this->course->offering()->section()->access('view')) : ?>
<form action="<?php echo JRoute::_($base . '&active=notes'); ?>" method="get">
	<fieldset class="filters">
		<div class="filters-inner">
			<ul>
				<li>
					<a class="download btn" href="<?php echo JRoute::_($base . '&active=notes&action=download&frmt=txt'); ?>">
						<span><?php echo JText::_('PLG_COURSES_NOTES_DOWNLOAD'); ?></span>
					</a>
				</li>
			</ul>
			<div class="clear"></div>
			<p>
				<label for="filter-search">
					<span><?php echo JText::_('PLG_COURSES_NOTES_SEARCH'); ?></span>
					<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('PLG_COURSES_NOTES_SEARCH_NOTES'); ?>" />
				</label>
				<input type="submit" class="filter-submit" value="<?php echo JText::_('PLG_COURSES_NOTES_GO'); ?>" />
			</p>
		</div><!-- / .filters-inner -->
	</fieldset>
</form>

<div class="notes-wrap">
<?php if ($results) { ?>
	<?php
	foreach ($results as $id => $notes)
	{
		$lecture = new CoursesModelAssetgroup($id);
		$unit = CoursesModelUnit::getInstance($lecture->get('unit_id'));
	?>
	<div class="section">
		<h3><?php echo $this->escape(stripslashes($lecture->get('title'))); ?></h3>
		<?php foreach ($notes as $note) { ?>
		<div class="jSticky-medium static<?php if ($note->get('access')) { echo ' annotation'; } ?>" id="note-<?php echo $note->get('id'); ?>" data-id="<?php echo $note->get('id'); ?>">
			<div class="jSticky-header">
				<?php if ($note->get('timestamp') && $note->get('timestamp') != '00:00:00') { ?>
					<a href="<?php echo str_replace('%3A', ':', JRoute::_($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $lecture->get('alias') . '&time=' . $this->escape($note->get('timestamp')))); ?>" class="time"><?php echo $this->escape($note->get('timestamp')); ?></a>
				<?php } ?>
			</div>
			<div class="jStickyNote">
				<textarea name="note_<?php echo $note->get('id'); ?>"><?php echo $this->escape(stripslashes($note->get('content'))); ?></textarea>
			</div>
			<a class="jSticky-delete" href="<?php echo JRoute::_($base . '&active=notes&action=delete&note=' . $note->get('id')); ?>" title="<?php echo JText::_('PLG_COURSES_NOTES_DELETE_NOTE'); ?>">x</a>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
	<?php } ?>
	<script type="text/javascript">
	jQuery(document).ready(function(jQuery){
		var $ = jQuery;

		var url = "<?php echo JURI::base(true) . '/' . $this->offering->link() . '&active=notes&no_html=1&note='; ?>";

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
<?php } else { ?>
	<div id="notes-introduction">
		<div class="instructions">
			<ol>
				<li><?php echo JText::_('PLG_COURSES_NOTES_STEP1'); ?></li>
				<li><?php echo JText::_('PLG_COURSES_NOTES_STEP2'); ?></li>
				<li><?php echo JText::_('PLG_COURSES_NOTES_STEP3'); ?></li>
				<li><?php echo JText::_('PLG_COURSES_NOTES_STEP4'); ?></li>
			</ol>
		</div><!-- / .instructions -->
		<div class="questions">
			<p><strong><?php echo JText::_('PLG_COURSES_NOTES_WHERE_IS_SAVE_BUTTON'); ?></strong></p>
			<p><?php echo JText::_('PLG_COURSES_NOTES_WHERE_IS_SAVE_BUTTON_EXPLANATION'); ?></p>
			<p><strong><?php echo JText::_('PLG_COURSES_NOTES_WHO_CAN_SEE_MY_NOTES'); ?></strong></p>
			<p><?php echo JText::_('PLG_COURSES_NOTES_WHO_CAN_SEE_MY_NOTES_EXPLANATION'); ?></p>
		</div><!-- / .post-type -->
	</div><!-- / #collection-introduction -->
<?php } ?>
</div>
<?php else : ?>
	<?php
		$this->view('_not_enrolled')
		     ->set('course', $this->course)
		     ->set('option', $this->option)
		     ->set('message', JText::_('PLG_COURSES_NOTES_ENROLLMENT_REQUIRED'))
		     ->display();
	?>
<?php endif; ?>