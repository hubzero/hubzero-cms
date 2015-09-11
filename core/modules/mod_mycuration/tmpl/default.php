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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
	<ul class="module-nav">
		<li><a class="icon-browse" href="<?php echo Route::url('index.php?option=com_publications&controller=curation'); ?>"><?php echo Lang::txt('MOD_MYCURATION_ALL_TASKS'); ?></a></li>
	</ul>

	<h4>
		<a href="<?php echo Route::url('index.php?option=com_publications&controller=curation&assigned=1'); ?>">
			<?php echo Lang::txt('MOD_MYCURATION_ASSIGNED'); ?>
			<span><?php echo Lang::txt('MOD_MYCURATION_VIEW_ALL'); ?></span>
		</a>
	</h4>
	<?php if (count($this->rows) <= 0) { ?>
		<p><em><?php echo Lang::txt('MOD_MYCURATION_NO_ITEMS'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
		<?php
		foreach ($this->rows as $row)
		{
			$class = $row->state == 5 ? 'status-pending' : 'status-wip';
			?>
			<li class="curation-task <?php echo $class; ?>">
				<a href="<?php echo $row->state == 5 ? Route::url('index.php?option=com_publications&controller=curation&id=' . $row->id) : Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_number); ?>"><img src="<?php echo Route::url('index.php?option=com_publications&id=' . $row->id . '&v=' . $row->version_id) . '/Image:thumb'; ?>" alt="" />
				<?php echo $row->title . ' v.' . $row->version_label; ?></a>
				<span><?php if ($row->state == 5) { ?><a href="<?php echo Route::url('index.php?option=com_publications&controller=curation&id=' . $row->id); ?>"><?php echo Lang::txt('MOD_MYCURATION_REVIEW'); ?></a><?php } ?><?php if ($row->state == 7) { echo Lang::txt('MOD_MYCURATION_PENDING_CHANGES');  } ?></span>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } ?>
</div>