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
?>

<li class="asset-group-item" id="assetgroupitem_<?= $this->ag->get('id') ?>">
	<div class="sortable-handle"></div>
	<div class="uploadfiles">
		<p>Drag files here to upload</p>
		<form action="/api/courses/assetnew" class="uploadfiles-form">
			<input type="file" name="files[]" class="fileupload" multiple />
			<input type="hidden" name="course_id" value="<?= $this->course->get('id') ?>" />
			<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
			<input type="hidden" name="scope_id" value="<?= $this->ag->get('id') ?>" />
		</form>
	</div>
	<div class="asset-group-item-container">
		<div class="asset-group-item-title title toggle-editable"><?= $this->ag->get('title') ?></div>
		<div class="title-edit">
			<form action="/api/courses/assetgroupsave" class="title-form">
				<input class="uniform title-text" name="title" type="text" value="<?= $this->ag->get('title') ?>" />
				<input class="uniform title-save" type="submit" value="Save" />
				<input class="uniform title-reset" type="reset" value="Cancel" />
				<input type="hidden" name="course_id" value="<?= $this->course->get('id') ?>" />
				<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
				<input type="hidden" name="id" value="<?= $this->ag->get('id') ?>" />
			</form>
		</div>
<?php
// Loop through the assets
if ($this->ag->assets()->total())
{
?>
		<ul class="assets-list sortable-assets">
<?php
		foreach ($this->ag->assets() as $a)
		{
			// Don't put deleted assets here
			if($a->get('state') != COURSES_ASSET_DELETED)
			{
				$view = new JView(
						array(
							'name'      => 'manage',
							'layout'    => 'asset_partial')
					);
				$view->base   = $this->base;
				$view->course = $this->course;
				$view->unit   = $this->unit;
				$view->ag     = $this->ag;
				$view->a      = $a;
				$view->display();
			}
		}
?>
		</ul>
<?php
}
else // no assets in this asset group
{
?>
	<ul class="assets-list sortable-assets">
		<li class="asset-item asset missing nofiles">
			No files
			<span class="next-step-upload">
				Upload files &rarr;
			</span>
		</li>
	</ul>
<?php
}
?>
	</div>
</li>
<div class="clear"></div>