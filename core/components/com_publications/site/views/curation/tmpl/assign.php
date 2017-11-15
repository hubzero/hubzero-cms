<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->ajax)
{
	$this->css('curation.css')
		->js('curation.js');
}
?>
<div id="abox-content" class="curation-wrap">
	<h3><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_VIEW'); ?></h3>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=curation'); ?>" method="post" id="hubForm-ajax" name="curation-form" class="curation-history">
		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->pub->id; ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->pub->version->id; ?>" />
			<input type="hidden" name="task" value="assign" />
			<input type="hidden" name="confirm" value="1" />
		</fieldset>

		<p class="info"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_INSTRUCT'); ?></p>

		<label for="owner">
			<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN_CHOOSE'); ?>
			<?php
			$selected = $this->pub->curator() ? $this->pub->curator('name') : '';
			$mc = Event::trigger( 'hubzero.onGetSingleEntryWithSelect', array(array('members', 'owner', 'owner', '', $selected,'','owner')) );
			if (count($mc) > 0) {
				echo $mc[0];
			} else { ?>
				<input type="text" name="owner" id="owner" value="" size="35" maxlength="200" />
			<?php } ?>
			<?php if ($selected) { ?>
				<input type="hidden" name="selected" value="<?php echo $this->pub->curator; ?>" />
			<?php } ?>
		</label>

		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PUBLICATIONS_SAVE'); ?>" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('COM_PUBLICATIONS_CANCEL'); ?>" />
			<?php } else { ?>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=curation'); ?>" class="btn btn-cancel"><?php echo Lang::txt('COM_PUBLICATIONS_CANCEL'); ?></a>
			<?php } ?>
		</p>
	</form>
</div>
