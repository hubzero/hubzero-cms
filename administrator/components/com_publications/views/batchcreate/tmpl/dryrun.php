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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$i = 1;
$skipFields = array('license_type', 'state', 'main', 'secret', 'access');
?>
<p id="recordcount"><?php echo JText::_('COM_PUBLICATIONS_BATCH_NUMBER_RECORDS'); ?>: <?php echo count($this->items); ?></p>
<ul class="pubitems" id="resultlist">
<?php foreach ($this->items as $item) { ?>
	<li<?php if (count($item['errors']) > 0) { echo ' class="problem"'; } ?>>
	<h5><?php echo JText::_('COM_PUBLICATIONS_BATCH_RECORD') . ' ' . $i . ': ' . $item['version']->title; ?></h5>
	<table class="records">
		<tr>
			<td class="key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_TYPE'); ?></td>
			<td><?php echo $item['type']; ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_CATEGORY'); ?></td>
			<td><?php echo $item['category']; ?></td>
		</tr>
		<?php foreach ($item['version'] as $key => $value) {
			if (!$value || in_array($key, $skipFields)) {
				continue;
			}
			?>
			<tr>
				<td class="key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_' . strtoupper($key)); ?></td>
				<td><?php echo $value; ?></td>
			</tr>
		<?php } ?>
			<tr<?php if (!$item['license']) { echo ' class="missing"'; } ?>>
				<td class="key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_LICENSE'); ?></td>
				<td><?php echo $item['license'] ? $item['license']->title : 'N/A'; ?></td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_TAGS'); ?></td>
				<td><?php if (!empty($item['tags'])) { ?>
					<ol class="tags">
						<?php foreach ($item['tags'] as $tag) { echo '<li>' . $tag . '</li>'; } ?>
					</ol>
			<?php } else { echo 'N/A'; } ?>
				</td>
			</tr>
			<tr<?php if (empty($item['authors'])) { echo ' class="missing"'; } ?>>
				<td class="key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_AUTHORS'); ?></td>
				<td>
					<table class="filelist">
						<thead>
						<tr>
							<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_UID'); ?></th>
							<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_AUTHOR_NAME'); ?></th>
							<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_AUTHOR_ORG'); ?></th>
							<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_AUTHOR_OWNER'); ?></th>
						</tr>
						</thead>
						<tbody>
		<?php if (!empty($item['authors'])) {
			foreach ($item['authors'] as $authorRecord) {
			?>
						<tr<?php if ($authorRecord['error']) { echo ' class="missing"'; } ?>>
							<td><?php echo $authorRecord['author']->user_id; ?></td>
							<td><?php echo $authorRecord['error'] ? ' <span class="block prominent">' . $authorRecord['error'] . '</span>' : ''; ?><?php echo $authorRecord['author']->name; ?></td>
							<td><?php echo $authorRecord['author']->organization; ?></td>
							<td><?php echo $authorRecord['owner'] ? JText::_('JYES') : JText::_('JNO'); ?></td>
						</tr>
		<?php } } ?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_PUBLICATIONS_FIELD_FILES'); ?></td>
				<td>
					<table class="filelist">
						<thead>
						<tr>
							<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_TYPE'); ?></th>
							<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_FILE_PATH'); ?></th>
							<th><?php echo JText::_('COM_PUBLICATIONS_FIELD_TITLE'); ?></th>
						</tr>
						</thead>
						<tbody>
		<?php if (!empty($item['files'])) {
			foreach ($item['files'] as $filerecord) {
			?>
						<tr<?php if ($filerecord['error']) { echo ' class="missing"'; } ?>>
							<td><?php echo $filerecord['type']; ?></td>
							<td><?php echo $filerecord['error'] ? ' <span class="block prominent">' . $filerecord['error'] . '</span>' : ''; ?><?php echo $filerecord['attachment']->path; ?></td>
							<td><?php echo $filerecord['attachment']->title; ?></td>
						</tr>
		<?php } } ?>
						</tbody>
					</table>
				</td>
			</tr>
	</table>
	</li>
<?php $i++; }  ?>
</ul>
<input type="hidden" name="dryrun" id="dryrun" value="1" />
