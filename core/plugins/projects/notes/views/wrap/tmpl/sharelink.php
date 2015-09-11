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

$stamp = $this->publicStamp ? $this->publicStamp->stamp : NULL;

if ($stamp) {
?>
	<p class="publink"><?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_LINK') . ' <a href="' . trim(Request::base(), DS) . Route::url('index.php?option=' . $this->option . '&action=get') . '?s=' . $stamp .'" rel="external">' . trim(Request::base(), DS) . Route::url('index.php?option=' . $this->option . '&action=get&s=' . $stamp) . '</a>'; ?>
	<?php if ($this->project->isPublic()) {
		$act = $this->publicStamp->listed ? 'unlist' : 'publist'; ?>
	<span><?php echo Lang::txt('COM_PROJECTS_NOTES_THIS_PAGE_IS'); ?>  <strong class="<?php echo $this->publicStamp->listed ? 'green' : 'urgency'; ?>"><?php echo $this->publicStamp->listed ? Lang::txt('COM_PROJECTS_NOTES_LISTED') : Lang::txt('COM_PROJECTS_NOTES_UNLISTED'); ?></strong>. <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes&p=' . $this->page->get('id')) . '&amp;action=share'; ?>" class="showinbox"><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_SETTINGS'); ?> &rsaquo;</a></span>
	<?php } ?>
	</p>

<?php } else { ?>
	<p class="publink"><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_GET_LINK'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes&p=' . $this->page->get('id')) . '&amp;action=share'; ?>" class="showinbox"><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_GENERATE_LINK'); ?></a></p>
<?php } ?>
