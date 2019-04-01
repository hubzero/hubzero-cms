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

if ($this->file->get('converted'))
{
	$slabel = $this->file->get('type') == 'folder' ? Lang::txt('PLG_PROJECTS_FILES_REMOTE_FOLDER') : Lang::txt('PLG_PROJECTS_FILES_REMOTE_FILE');
}

$multi = isset($this->multi) && $this->multi ? '[]' : '';

?>
<li><img src="<?php echo $this->file->getIcon(); ?>" alt="<?php echo $this->file->get('name'); ?>" />
<?php echo $this->file->get('name'); ?>
<?php if ($this->file->get('converted')) { echo '<span class="remote-file">' . $slabel . '</span>'; } ?>
<?php if ($this->file->get('converted') && $this->file->get('originalPath')) { echo '<span class="remote-file faded">' . Lang::txt('PLG_PROJECTS_FILES_CONVERTED_FROM_ORIGINAL'). ' ' . basename($this->file->get('originalPath')); if ($this->file->get('originalFormat')) { echo ' (' . $this->file->get('originalPath') . ')'; } echo '</span>'; } ?>

<?php if (isset($this->skip) && $this->skip == true) { echo '<span class="file-skipped">' . Lang::txt('PLG_PROJECTS_FILES_SKIPPED') . '</span>'; } ?>
<?php echo $this->file->get('type') == 'folder'
	? '<input type="hidden" name="folder' . $multi . '" value="' . urlencode($this->file->get('name')) . '" />'
	: '<input type="hidden" name="asset' . $multi . '" value="' . urlencode($this->file->get('name')) . '" />'; ?>

<?php if (isset($this->extras)) { echo $this->extras; } ?>
</li>