<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('import.css')
     ->js('import.js');

$base = $this->member->link() . '&active=citations';
?>
<section id="import" class="section">
	<div class="section-inner">
		<?php foreach ($this->messages as $message) { ?>
			<p class="<?php echo $message['type']; ?>"><?php echo $message['message']; ?></p>
		<?php } ?>

		<ul id="steps">
			<li><a href="<?php echo Route::url($base . '&task=import'); ?>" class="active"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1'); ?><span><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2'); ?><span><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2_NAME'); ?></span></a></li>
			<li><a><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3'); ?><span><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3_NAME'); ?></span></a></li>
		</ul><!-- / #steps -->

		<form id="hubForm" class="full" enctype="multipart/form-data" method="post" action="<?php echo Route::url($base . '&task=upload'); ?>">
			<fieldset>
				<legend><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD'); ?>:</legend>

				<div class="grid">
					<div class="col span6">
						<label for="citations_file">
							<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD_FILE'); ?>: <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
							<input type="file" name="citations_file" id="citations_file" />
							<span class="hint"><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD_MAX'); ?></span>
						</label>
					</div>
					<div class="col span6 omega">
						<p>
							<strong><?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_ACCEPTABLE'); ?></strong><br />
							<?php echo implode($this->accepted_files, '<br />'); ?>
						</p>
					</div>
				</div>
			</fieldset>

			<p class="submit">
				<input type="submit" class="btn btn-success" name="submit" value="<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD'); ?>" />

				<a class="btn btn-secondary" href="<?php echo Route::url($base); ?>">
					<?php echo Lang::txt('PLG_MEMBERS_CITATIONS_CANCEL'); ?>
				</a>
			</p>

			<?php echo Html::input('token'); ?>
			<input type="hidden" name="option" value="com_members" />
			<input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
			<input type="hidden" name="active" value="citations" />
			<input type="hidden" name="action" value="upload" />
		</form>
	</div>
</section><!-- / .section -->
