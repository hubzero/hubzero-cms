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
<table class="activity">
	<tbody>
<?php
if ($this->entries) {
	foreach ($this->entries as $entry)
	{
?>
		<tr>
			<th scope="row"><?php echo $area; ?></th>
			<td class="author"><a href="<?php echo Route::url('index.php?option=com_members&id='.$entry->created_by); ?>"><?php echo stripslashes($name); ?></a></td>
			<td class="action"><?php echo stripslashes($entry->title); ?></td>
			<td class="date"><?php echo Date::of($entry->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1') . ' @' . Lang::txt('TIME_FORMAT_HZ1')); ?></td>
		</tr>
<?php
	}
} else {
	// Do nothing if there are no events to display
?>
		<tr>
			<td><?php echo Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND'); ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>
