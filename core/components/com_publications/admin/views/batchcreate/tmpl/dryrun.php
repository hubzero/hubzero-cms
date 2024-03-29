<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
$i = 1;
$skipFields = array('license_type', 'state', 'main', 'secret', 'access');
?>
<p id="recordcount"><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_NUMBER_RECORDS'); ?>: <?php echo count($this->items); ?></p>
<ul class="pubitems" id="resultlist">
<?php foreach ($this->items as $item) { ?>
	<li<?php if (count($item['errors']) > 0) { echo ' class="problem"'; } ?>>
		<h5><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_RECORD') . ' ' . $i . ': ' . $item['version']->title; ?></h5>
		<table class="records">
			<tbody>
				<tr>
					<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TYPE'); ?></td>
					<td><?php echo $item['type']; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CATEGORY'); ?></td>
					<td><?php echo $item['category']; ?></td>
				</tr>
				<?php foreach ($item['version'] as $key => $value) {
					if (!$value || in_array($key, $skipFields))
					{
						continue;
					}
					?>
					<tr>
						<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_' . strtoupper($key)); ?></td>
						<td><?php echo $value; ?></td>
					</tr>
				<?php } ?>
				<tr<?php if (!$item['license']) { echo ' class="missing"'; } ?>>
					<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_LICENSE'); ?></td>
					<td><?php echo $item['license'] ? $item['license']->title : 'N/A'; ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TAGS'); ?></td>
					<td>
						<?php if (!empty($item['tags'])) { ?>
							<ol class="tags">
								<?php foreach ($item['tags'] as $tag) { echo '<li>' . $tag . '</li>'; } ?>
							</ol>
						<?php } else { echo 'N/A'; } ?>
					</td>
				</tr>
				<tr<?php if (empty($item['authors'])) { echo ' class="missing"'; } ?>>
					<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_AUTHORS'); ?></td>
					<td>
						<table class="filelist">
							<thead>
								<tr>
									<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_UID'); ?></th>
									<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_NAME'); ?></th>
									<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_ORG'); ?></th>
									<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_AUTHOR_OWNER'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php if (!empty($item['authors'])) { ?>
								<?php foreach ($item['authors'] as $authorRecord) { ?>
									<tr<?php if ($authorRecord['error']) { echo ' class="missing"'; } ?>>
										<td><?php echo $authorRecord['author']->user_id; ?></td>
										<td><?php echo $authorRecord['error'] ? ' <span class="block prominent">' . $authorRecord['error'] . '</span>' : ''; ?><?php echo $authorRecord['author']->name; ?></td>
										<td><?php echo $authorRecord['author']->organization; ?></td>
										<td><?php echo $authorRecord['owner'] ? Lang::txt('JYES') : Lang::txt('JNO'); ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_FILES'); ?></td>
					<td>
						<table class="filelist">
							<thead>
							<tr>
								<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TYPE'); ?></th>
								<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_SUBTYPE'); ?></th>
								<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_FILE_PATH'); ?></th>
								<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TITLE'); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php if (!empty($item['files'])) { ?>
								<?php foreach ($item['files'] as $filerecord) { ?>
									<tr<?php if ($filerecord['error']) { echo ' class="missing"'; } ?>>
										<td><?php echo $filerecord['type']; ?></td>
										<td><?php echo $filerecord['subtype']; ?></td>
										<td><?php echo $filerecord['error'] ? ' <span class="block prominent">' . $filerecord['error'] . '</span>' : ''; ?><?php echo $filerecord['attachment']->path; ?></td>
										<td><?php echo $filerecord['attachment']->title; ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_METADATA'); ?></td>
					<td>
						<table class="filelist">
							<thead>
							<tr>
								<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS'); ?></th>
								<th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_TEXT'); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php if (!empty($item['metadata'])) { ?>
								<?php foreach ($item['metadata'] as $alias => $text) { ?>
									<tr>
										<td><?php echo $alias; ?></td>
										<td><?php echo htmlspecialchars($text, ENT_COMPAT); ?></td>
									</tr>
								<?php } ?>
							<?php } ?>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</li>
<?php $i++; }  ?>
</ul>
<input type="hidden" name="dryrun" id="dryrun" value="1" />
