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

// Get our asset model
$asset = new CoursesModelAsset(JRequest::getInt('asset_id', null));

// Get the asset groups
$assetgroups = array();
foreach ($this->course->offering()->units() as $unit) :
	foreach ($unit->assetgroups() as $agt) :
		foreach ($agt->children() as $ag) :
			$assetgroups[] = array('id'=>$ag->get('id'), 'title'=>$ag->get('title'));
		endforeach;
	endforeach;
endforeach;
?>

<div class="edit-asset">
	<h3>Edit Asset</h3>

	<form action="/api/courses/asset/save" method="POST" class="edit-form">

		<p>
			<label for="title">Title:</label>
			<input type="text" name="title" value="<?= $asset->get('title') ?>" placeholder="Asset Title" />
		</p>

		<p>
			<label for="scope_id">Attach to:</label>
			<select name="scope_id">
				<? foreach ($assetgroups as $assetgroup) : ?>
					<? $selected = ($assetgroup['id'] == $this->scope_id) ? 'selected' : ''; ?>
					<option value="<?= $assetgroup['id'] ?>" <?= $selected ?>><?= $assetgroup['title'] ?></option>
				<? endforeach; ?>
			</select>
		</p>

		<input type="hidden" name="course_id" value="<?= $this->course->get('id') ?>" />
		<input type="hidden" name="original_scope_id" value="<?= $this->scope_id ?>" />
		<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
		<input type="hidden" name="id" value="<?= $asset->get('id') ?>" />

		<input type="submit" value="Submit" class="submit" />
		<input type="button" value="Cancel" class="cancel" />

	</form>
</div>