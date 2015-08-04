<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('import.css')
     ->js('import.js');

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';
?>
<section id="import" class="section">
	<div class="section-inner">
		<?php if ($this->messages): ?>
		<?php //foreach ($this->messages as $message) { ?>
			<p class="<?php //echo $message['type']; ?>"><?php echo $message['message']; ?></p>
		<?php //} ?>
		<?php endif; ?>
		<ul id="steps">
			<li><a href="<?php echo Route::url($base . '&action=import'); ?>" class="active"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3'); ?><span><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME'); ?></span></a></li>
		</ul><!-- / #steps -->

		<form id="hubForm" class="full" enctype="multipart/form-data" method="post" action="<?php echo Route::url($base . '&action=upload'); ?>">
			<fieldset>
				<legend><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD'); ?>:</legend>

				<div class="grid">
					<div class="col span6">
						<label for="citations_file">
							<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD_FILE'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
							<input type="file" name="citations_file" id="citations_file" />
							<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD_MAX'); ?></span>
						</label>
					</div>
					<div class="col span6 omega">
						<p>
							<strong><?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_ACCEPTABLE'); ?></strong><br />
							<?php echo implode($this->accepted_files, '<br />'); ?>
						</p>
					</div>
				</div>
			</fieldset>

			<p class="submit">
				<input type="submit" class="btn btn-success" name="submit" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD'); ?>" />

				<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_CANCEL'); ?>
				</a>
			</p>

			<?php echo Html::input('token'); ?>
			<input type="hidden" name="option" value="com_groups" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="active" value="citations" />
			<input type="hidden" name="action" value="upload" />
		</form>
	</div>
</section><!-- / .section -->
