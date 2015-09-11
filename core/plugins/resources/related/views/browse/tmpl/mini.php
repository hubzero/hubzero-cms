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
<div class="container" id="whatsrelated">
	<h3><?php echo Lang::txt('PLG_RESOURCES_RELATED_HEADER'); ?></h3>

	<?php if ($this->related) { ?>
		<ul>
		<?php foreach ($this->related as $line) { ?>
			<?php
			if ($line->section != 'Topic')
			{
				// Get the SEF for the resource
				if ($line->alias)
				{
					$sef = Route::url('index.php?option=' . $this->option . '&alias=' . $line->alias);
				}
				else
				{
					$sef = Route::url('index.php?option=' . $this->option . '&id=' . $line->id);
				}
				$class = 'series';
			}
			else
			{
				if ($line->group_cn != '' && $line->scope != '')
				{
					$sef = Route::url('index.php?option=com_groups&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
				else
				{
					$sef = Route::url('index.php?option=com_wiki&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
				$class = 'wiki';
			}
			?>
			<li class="<?php echo $class; ?>">
				<a href="<?php echo $sef; ?>">
					<?php echo ($line->section == 'Series') ? '<span>' . Lang::txt('PLG_RESOURCES_RELATED_PART_OF') . '</span> ' : ''; ?>
					<?php echo $this->escape(stripslashes($line->title)); ?>
				</a>
			</li>
		<?php } ?>
		</ul>
	<?php } else { ?>
		<p><?php echo Lang::txt('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND'); ?></p>
	<?php } ?>
</div>
