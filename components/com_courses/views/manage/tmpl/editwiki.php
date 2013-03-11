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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<div class="wiki-edit">
	<h3>Create a note</h3>

	<form action="/api/courses/asset/new" method="POST" class="wiki-edit-form">

		<p>
			<label for="title">Title:</label>
			<input type="text" name="title" placeholder="Note title - will defeault to first 25 characters of note body" />
			<span class="wiki-asterisk">*only applies to wiki style notes, not inline outline notes</small>
		</p>

		<label for="content">Content: </label><span class="required">*required</span>
<?
		ximport('Hubzero_Wiki_Editor');
		$editor =& Hubzero_Wiki_Editor::getInstance();

		echo $editor->display('content', 'content', '', 'no-footer', '35', '20');
?>

<? // @TODO: implement asset insertion to wiki body! ?>

<!--		<div class="wiki-include-assets">
			<div class="wiki-assets-inner">
				<p class="help">Drag an asset from below, to the text box above to include it in your note.</p>
				<ul>
<?
					$assetgroups = array();
					foreach ($this->course->offering()->units() as $unit) :
						foreach($unit->assetgroups() as $agt) :
							foreach($agt->children() as $ag) :
								$assetgroups[] = array('id'=>$ag->get('id'), 'title'=>$ag->get('title'));
								if ($ag->assets()->total()) :
									foreach ($ag->assets() as $a) :
										//echo "<li>" . $a->get('title') . "</li>";
									endforeach;
								endif;
							endforeach;
						endforeach;
					endforeach;
?>
				</ul>
			</div>
		</div> -->

		<p>
			<label for="style">Display as:</label>
			<input type="radio" name="style" value="wiki" checked />a full page wiki asset
			<input type="radio" name="style" value="note" />an inline outline note
		</p>

		<p>
			<label for="scope_id">Attach to:</label>
			<select name="scope_id">
				<? foreach($assetgroups as $assetgroup) : ?>
					<? $selected = ($assetgroup['id'] == $this->scope_id) ? 'selected' : ''; ?>
					<option value="<?= $assetgroup['id'] ?>" <?= $selected ?>><?= $assetgroup['title'] ?></option>
				<? endforeach; ?>
			</select>
		</p>

		<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
		<input type="hidden" name="type" value="note" />

		<input type="submit" value="Submit" class="wiki-submit" />
		<input type="button" value="Cancel" class="wiki-cancel" />

	</form>
</div>