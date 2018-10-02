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
?>
<table class="listing" id="projectlist">
	<thead>
		<tr>
			<th scope="col" class="th_image" colspan="2"></th>
			<th scope="col"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></th>
			<th scope="col"><?php echo Lang::txt('COM_PROJECTS_OWNER'); ?></th>
	<?php if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive'))) { ?>
		<?php if ($this->filters['reviewer'] == 'sensitive') {  ?>
			<th scope="col"><?php echo Lang::txt('COM_PROJECTS_TYPE_OF_DATA'); ?></th>
			<th scope="col"><?php echo Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS'); ?></th>
		<?php } ?>
		<?php if ($this->filters['reviewer'] == 'sponsored') {  ?>
			<th scope="col"><?php echo Lang::txt('COM_PROJECTS_SPS_INFO'); ?></th>
			<th scope="col"><?php echo Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS'); ?></th>
		<?php } ?>
			<th scope="col"></th>
	<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->rows as $row)
		{
			if ($row->get('owned_by_group') && !$row->groupOwner())
			{
				continue; // owner group has been deleted
			}

			// Display List of items
			$this->view('_item')
			     ->set('option', $this->option)
			     ->set('filters', $this->filters)
			     ->set('model', $this->model)
			     ->set('row', $row)
			     ->display();
		}
		?>
	</tbody>
</table>