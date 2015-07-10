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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="icon-browse btn" href="<?php echo Route::url('index.php?option='. $this->option . '&controller=posts'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_VIEW_POSTS'); ?></a>
			</li>
			<li class="last">
				<a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_ADD_FEED'); ?></a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<form class="contentForm">
		<div id="page-main">
		<?php if (count($this->feeds) > 0): ?>
			<table class="entries">
				<thead class="table-head">
					<tr>
						<th scope="col"><?php echo Lang::txt('COM_FEEDAGGREGATOR_COL_NAME'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_FEEDAGGREGATOR_COL_URL'); ?></th>
						<th scope="col"><?php echo Lang::txt('COM_FEEDAGGREGATOR_COL_ACTIONS'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->feeds as $feed): ?>
					<tr class='shade-table'>
						<td><?php echo $feed->name; ?></td>
						<td><a href="<?php echo $feed->url; ?>"><?php echo $feed->url; ?></a></td>
						<td>
							<a class="btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&task=PostsById&id=' . $feed->id); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_VIEW_POST'); ?>s</a>
							<a class="btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=feeds&task=edit&id=' . $feed->id); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_EDIT'); ?></a>
							<?php if ($feed->enabled == '1'):?>
								<a class="btn disableBtn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=feeds&task=status&action=disable&id=' . $feed->id); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_DISABLE'); ?></a>
							<?php elseif ($feed->enabled == '0'): ?>
								<a class="btn enableBtn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=feeds&task=status&action=enable&id=' . $feed->id); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_ENABLE'); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<p>
				<?php echo Lang::txt('COM_FEEDAGGREGATOR_NO_RESULTS'); ?><br />
				<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=feeds&task=new'); ?>"><?php echo Lang::txt('COM_FEEDAGGREGATOR_ADD_FEED'); ?></a>
			</p>
		<?php endif; ?>
		</div>
	</form>
</section><!-- /.main section -->
