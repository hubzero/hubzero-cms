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

Html::behavior('tooltip');
?>

<h2 class="modal-title"><?php echo Lang::txt('COM_MODULES_TYPE_CHOOSE')?></h2>

<table id="new-modules-list" class="adminlist">
	<thead>
		<tr>
			<th scope="col"><?php echo Lang::txt('JGLOBAL_TITLE'); ?></th>
			<th scope="col"><?php echo Lang::txt('COM_MODULES_HEADING_MODULE'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->items as &$item) : ?>
		<tr>
			<?php
			// Prepare variables for the link.

			$link = 'index.php?option=com_modules&task=add&eid='. $item->extension_id;
			$name = $this->escape($item->name);
			$desc = $this->escape($item->desc);
			?>
			<td>
				<span class="editlinktip hasTip" title="<?php echo $name.' :: '.$desc; ?>"><a href="<?php echo Route::url($link); ?>" target="_top"><?php echo $name; ?></a></span>
			</td>
			<td>
				<?php echo $this->escape($item->module); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<div class="clr"></div>
