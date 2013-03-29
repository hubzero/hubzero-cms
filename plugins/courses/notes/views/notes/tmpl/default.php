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
$notes = $this->model->notes();
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
?>
	<?php
	if ($results)
	{
		foreach ($results as $id => $notes)
		{
			$lecture = new CoursesModelAssetgroup($id);
?>
<div class="section">
	<h3><?php echo $this->escape(stripslashes($lecture->get('title'))); ?></h3>
<?php
			foreach ($notes as $note)
			{
	?>
	<div class="jSticky-medium static" id="note-<?php echo $note->get('id'); ?>" data-id="<?php echo $note->get('id'); ?>">
		<div class="jSticky-header"></div>
		<div class="jStickyNote">
			<textarea name="note_<?php echo $note->get('id'); ?>"><?php echo $this->escape(stripslashes($note->get('content'))); ?></textarea>
		</div>
		<a class="jSticky-delete" href="<?php echo JRoute::_('index.php?option=com_courses&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=notes&action=delete&note=' . $note->get('id')); ?>" title="<?php echo JText::_('Delete note'); ?>">x</a>
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