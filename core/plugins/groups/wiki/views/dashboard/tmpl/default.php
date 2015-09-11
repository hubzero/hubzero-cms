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

if ($this->rows) { ?>
	<table class="activity" id="wiki-list">
		<tbody>
		<?php
		$cls = 'even';
		foreach ($this->rows as $row)
		{
			$name = Lang::txt('WIKI_AUTHOR_UNKNOWN');
			$user = User::getInstance($row->created_by);
			if (is_object($user) && $user->get('name'))
			{
				$name = $user->get('name');
			}

			if ($row->version > 1)
			{
				$t = Lang::txt('WIKI_EDITED');
				$c = 'wiki-edited';
			}
			else
			{
				$t = Lang::txt('WIKI_CREATED');
				$c = 'wiki-created';
			}

			$cls = ($cls == 'even') ? 'odd' : 'even';
			?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><span class="<?php echo $c; ?>"><?php echo $t; ?></span></th>
				<td><a href="<?php echo Route::url('index.php?option='.$this->option.'&pagename='.$row->pagename.'&scope='.$row->scope); ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td class="author"><a href="<?php echo Route::url('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo $name; ?></a></td>
				<td class="date"><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_GROUPS_WIKI_NO_RESULTS_FOUND'); ?></p>
<?php }