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
?>
<div class="width-100">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_CONFIG_OTHER_SETTINGS', $this->section); ?></span></legend>

		<?php
		foreach ($this->values as $key => $val):
			if (is_array($val)):
				foreach ($val as $k => $v):
					?>
					<div class="input-wrap">
						<label for="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>_<?php echo $k; ?>"><?php echo $key; ?></label>
						<input type="text" name="hzother[<?php echo $this->section; ?>][<?php echo $key; ?>][<?php echo $k; ?>]" id="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>_<?php echo $k; ?>" value="<?php echo $this->escape($v); ?>" />
					</div>
					<?php
				endforeach;
			else:
				?>
				<div class="input-wrap">
					<label for="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>"><?php echo $key; ?></label>
					<input type="text" name="hzother[<?php echo $this->section; ?>][<?php echo $key; ?>]" id="hzform_<?php echo $this->section; ?>_<?php echo $key; ?>" value="<?php echo $this->escape($val); ?>" />
				</div>
				<?php
			endif;
		endforeach;
		?>
	</fieldset>
</div>
