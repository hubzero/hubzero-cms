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

if ($this->isUser) : ?>
	<div class="section-edit-container">
		<?php if ($this->registration == Components\Members\Models\Profile\Field::STATE_READONLY) : ?>
			<p class="notice warning"><?php echo Lang::txt('PLG_MEMBERS_PROFILE_READONLY', $this->title); ?></p>
		<?php else : ?>
			<div class="section-edit-content">
				<form action="<?php echo Route::url('index.php?option=com_members'); ?>" method="post" data-section-registation="<?php echo $this->registration_field; ?>" data-section-profile="<?php echo $this->profile_field; ?>">
					<span class="section-edit-errors"></span>

					<div class="input-wrap">
						<?php echo $this->inputs; ?>
					</div>
					<div class="input-wrap">
						<?php echo $this->access; ?>
					</div>

					<input type="submit" class="section-edit-submit btn" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_SAVE'); ?>" />
					<input type="reset" class="section-edit-cancel btn" value="<?php echo Lang::txt('PLG_MEMBERS_PROFILE_CANCEL'); ?>" />
					<input type="hidden" name="field_to_check[]" value="<?php echo $this->registration_field; ?>" />
					<input type="hidden" name="option" value="com_members" />
					<input type="hidden" name="controller" value="profiles" />
					<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="no_html" value="1" />
					<?php echo Html::input('token'); ?>
				</form>
			</div>
		<?php endif; ?>
	</div>
<?php endif;?>