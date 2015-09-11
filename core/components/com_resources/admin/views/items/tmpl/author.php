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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
	<li id="author_<?php echo $this->id; ?>">
		<span class="handle"><?php echo Lang::txt('COM_RESOURCES_AUTHOR_DRAG'); ?></span>
		<a class="state trash" data-parent="author_<?php echo $this->id; ?>" href="#" onclick="HUB.Resources.removeAuthor('author_<?php echo $this->id; ?>');return false;"><span><?php echo Lang::txt('JACTION_DELETE'); ?></span></a>
		<?php echo $this->escape(stripslashes($this->name)); ?> (<?php echo $this->id; ?>)
		<br /><?php echo Lang::txt('COM_RESOURCES_AUTHOR_AFFILIATION'); ?>: <input type="text" name="<?php echo $this->id; ?>_organization" value="<?php echo $this->escape(stripslashes($this->org)); ?>" />

		<select name="<?php echo $this->id; ?>_role">
			<option value=""<?php if ($this->role == '') { echo ' selected="selected"'; }?>><?php echo Lang::txt('COM_RESOURCES_ROLE_AUTHOR'); ?></option>
<?php
	if ($this->roles)
	{
		foreach ($this->roles as $role)
		{
?>
			<option value="<?php echo $this->escape($role->alias); ?>"<?php if ($this->role == $role->alias) { echo ' selected="selected"'; }?>><?php echo $this->escape(stripslashes($role->title)); ?></option>
<?php
		}
	}
?>
		</select>
		<input type="hidden" class="authid" name="<?php echo $this->id; ?>authid" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="<?php echo $this->id; ?>_name" value="<?php echo $this->escape(stripslashes($this->name)); ?>" />
	</li>