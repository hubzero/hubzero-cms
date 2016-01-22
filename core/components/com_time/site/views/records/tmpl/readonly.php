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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('records')
     ->js('records')
     ->js();

HTML::behavior('core');
?>

<div id="dialog-confirm"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div id="content-header-extra">
		<ul id="useroptions">
			<?php if ($this->row->isMine() || $this->row->iCanProxy()) : ?>
				<li>
					<a class="icon-reply btn" href="<?php echo Route::url($this->base . $this->start); ?>">
						<?php echo Lang::txt('COM_TIME_RECORDS_ALL_RECORDS'); ?>
					</a>
				</li>
				<li>
					<a class="edit icon-edit btn" href="<?php echo Route::url($this->base . '&task=edit&id=' . $this->row->id); ?>">
						<?php echo Lang::txt('COM_TIME_RECORDS_EDIT'); ?>
					</a>
				</li>
				<?php if ($this->row->isMine()) : ?>
					<li class="last">
						<a class="delete icon-delete btn" href="<?php echo Route::url($this->base . '&task=delete&id=' . $this->row->id); ?>">
							<?php echo Lang::txt('COM_TIME_RECORDS_DELETE'); ?>
						</a>
					</li>
				<?php endif; ?>
			<?php else : ?>
				<li class="last">
					<a class="icon-reply btn" href="<?php echo Route::url($this->base . $this->start); ?>">
						<?php echo Lang::txt('COM_TIME_RECORDS_ALL_RECORDS'); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_records">
		<div class="container readonly">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
					<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="grid">
				<div class="readonly col span-half">
					<h3 class="headings"><?php echo Lang::txt('COM_TIME_RECORDS_DETAILS'); ?></h3>
					<div class="grouping uname-group">
						<label for="uname"><?php echo Lang::txt('COM_TIME_RECORDS_USER'); ?>:</label>
						<?php echo $this->escape($this->row->user->name); ?>
					</div>

					<div class="grouping hub-group">
						<label for="hub"><?php echo Lang::txt('COM_TIME_RECORDS_HUB'); ?>:</label>
						<?php echo $this->escape($this->row->task->hub->name); ?>
					</div>

					<div class="grouping task-group">
						<label for="task"><?php echo Lang::txt('COM_TIME_RECORDS_TASK'); ?>:</label>
						<?php echo $this->escape($this->row->task->name); ?>
					</div>

					<div class="grouping time-group">
						<label for="time"><?php echo Lang::txt('COM_TIME_RECORDS_TIME'); ?>:</label>
						<?php echo $this->escape($this->row->time); ?> hour(s)
					</div>

					<div class="grouping date-group">
						<label for="date"><?php echo Lang::txt('COM_TIME_RECORDS_DATE'); ?>:</label>
						<?php echo ($this->row->date != '0000-00-00 00:00:00') ? Date::of($this->row->date)->toLocal('m/d/y') : ''; ?>
					</div>
				</div>

				<div class="readonly col span-half omega">
					<h3 class="headings"><?php echo Lang::txt('COM_TIME_RECORDS_DESCRIPTION'); ?></h3>
					<?php if ($this->row->description) : ?>
						<div class="hub-notes">
							<div class="inner">
								<p>
									<?php echo $this->row->description; ?>
								</p>
							</div>
						</div>
					<?php else : ?>
						<p>
							<?php echo Lang::txt('COM_TIME_RECORDS_NO_DESCRIPTION'); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
</div>