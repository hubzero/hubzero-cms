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

// No direct access
defined('_HZEXEC_') or die();

$data    = $this->data;
$row     = $this->data->row;
$title   = $row->title ? $row->title : $row->path;
$viewer  = $this->data->viewer;

$row->path = Route::url('index.php?option=com_publications&id=' . $row->object_id);
$details = rtrim(Request::base(), '/') . '/' . ltrim($row->path, '/');
?>
	<li>
		<span class="item-options">
			<?php if ($viewer == 'edit') { ?>
				<span>
					<a href="<?php echo Route::url($data->editUrl . '&action=orderdown&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-movedown" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEDOWN'); ?>">&darr;</a>
					<a href="<?php echo Route::url($data->editUrl . '&action=orderup&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-moveup" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MOVEUP'); ?>">&uarr;</a>
					<a href="<?php echo Route::url($data->editUrl . '&action=deleteitem&aid=' . $data->id . '&p=' . $data->props); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
				</span>
			<?php } ?>
		</span>
		<span class="item-title link-type">
			<a href="<?php echo $row->path; ?>" rel="external"><?php echo $title; ?></a>
			<span class="item-details"><?php echo $details; ?></span>
		</span>
	</li>