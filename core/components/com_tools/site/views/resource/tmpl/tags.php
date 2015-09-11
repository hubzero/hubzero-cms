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

// No direct access.
defined('_HZEXEC_') or die();

$showwarning = ($this->version=='current' or !$this->status['published']) ? 0 : 1;

?>
	<div class="explaination">
		<h4><?php echo Lang::txt('COM_TOOLS_TAGS_WHAT_ARE_TAGS'); ?></h4>
		<p><?php echo Lang::txt('COM_TOOLS_TAGS_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo Lang::txt('COM_TOOLS_TAGS_ADD'); ?></legend>
<?php if (!empty($this->fats)) { ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_TAGS_SELECT_FOCUS_AREA'); ?>:</legend>
			<?php
			foreach ($this->fats as $key => $value)
			{
				?>
				<label>
					<input class="option" type="radio" name="tagfa" value="<?php echo $value; ?>"<?php if ($this->tagfa == $value) { echo ' checked="checked "'; } ?> />
					<?php echo $key; ?>
				</label>
				<?php
			}
			?>
		</fieldset>
<?php } ?>
		<label>
			<?php echo Lang::txt('COM_TOOLS_TAGS_ASSIGNED'); ?>:
			<?php
			$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->tags)));

			if (count($tf) > 0) {
				echo $tf[0];
			} else {
				echo '<textarea name="tags" id="tags-men" rows="6" cols="35">'. $this->tags .'</textarea>';
			}
			?>
		</label>
		<p><?php echo Lang::txt('COM_TOOLS_TAGS_NEW_EXPLANATION'); ?></p>
	</fieldset><div class="clear"></div>