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

$href = JRoute::_($this->base . '&asset=' . $this->a->get('id'));

if ($this->a->get('type') == 'video')
{
	$href = JRoute::_($this->base . '&active=outline&unit=' . $this->unit->get('alias') . '&b=' . $this->ag->get('alias'));
}

?>

<li id="asset_<?= $this->a->get('id') ?>" class="asset-item asset <?= $this->a->get('type') ?> <?= $this->a->get('subtype') ?> <?= ($this->a->get('state') == 0) ? ' notpublished' : ' published' ?>">
	<div class="sortable-assets-handle"></div>
	<div class="asset-item-title title toggle-editable"><?= $this->escape(stripslashes($this->a->get('title'))) ?></div>
	<div class="title-edit">
		<form action="/api/courses/asset/save" class="asset-title-form">
			<input class="title-text" name="title" type="text" value="<?= $this->a->get('title') ?>" />
			<input class="asset-title-save" type="submit" value="Save" />
			<input class="asset-title-reset" type="reset" value="Cancel" />
			<input type="hidden" name="course_id" value="<?= $this->course->get('id') ?>" />
			<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
			<input type="hidden" name="id" value="<?= $this->a->get('id') ?>" />
		</form>
	</div>
	<a class="asset-preview" href="<?= $href ?>" title="preview"></a>
	<a class="asset-edit" href="#" title="edit"></a>
	<?php if ($this->a->get('type') == 'form') : ?>
		<a class="asset-edit-deployment" href="#" title="edit deployment"<?php echo ($this->a->get('state') != 1) ? ' style="display:none;"': ''; ?>></a>
	<?php endif; ?>
	<a class="asset-delete" href="#" title="delete"></a>
	<form action="/api/courses/asset/togglepublished" class="next-step-publish">
		<span class="next-step-publish">
			<label class="published-label" for="published">
				<span class="published-label-text"><?= ($this->a->get('state') == 0) ? 'Mark as reviewed and publish?' : 'Published' ?></span>
				<input 
					class="uniform published-checkbox"
					name="published"
					type="checkbox"
					<?= ($this->a->get('state') == 0) ? '' : 'checked="checked"' ?> />
				<input type="hidden" class="asset_id" name="id" value="<?= $this->a->get('id') ?>" />
				<input type="hidden" name="course_id" value="<?= $this->course->get('id') ?>" />
				<input type="hidden" name="scope_id" value="<?= $this->ag->get('id') ?>" />
				<input type="hidden" name="scope" value="asset_group" />
				<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />
			</label>
		</span>
	</form>
	<div class="restore">
		<button>Restore</button>
	</div>
</li>