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
 * @author    Patrick Mulligan <jpmulligan@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<p><a class="btn primary" href="#collectionForm" id="add-collection"><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD', $this->type->type);?></a></p>
<form action="<?php echo Route::url($this->resource->link());?>"  method="post" id="collectionForm" class="full" style="display:none;">
	<fieldset>
		<legend><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD', $this->type->type);?></legend>
		<div class="grid">
			<?php if ($this->resources->count() > 0): ?>
			<div class="col span12">
				<label for="pid">
					<?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_SELECT', $this->type->type);?>					
				</label>
				<select name="pid" id="pid">
					<option value="" selected><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_SELECT_PLACEHOLDER', $this->type->type);?></option>					
					<?php foreach ($this->resources as $entry): ?>
						<option value="<?php echo $entry->id;?>"><?php echo $entry->title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col span12">
				<p class="or">OR</p>
			</div>
			<?php endif; ?>
			<div class="col span12" id="new-series-add">
				<label><?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_ADD_NEW', $this->type->type);?></label>
				<label for="resource-title">
					<?php echo Lang::txt('PLG_RESOURCES_COLLECTIONS_TITLE');?>
				</label>
				<input type="text" name="resource-title" value="" />
			</div>
		</div>
	</fieldset>
	<p class="submit">
		<input type="hidden" name="childid" value="<?php echo $this->resource->id;?>"/>
		<input type="hidden" name="controller" value="attachments" />
		<input type="hidden" name="task" value="create" />
		<input type="hidden" name="type" value="<?php echo $this->type->id;?>" />
		<input type="submit" value="Add">
	</p>
</form>
