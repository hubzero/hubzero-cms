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

Html::behavior('core');

$this->css()
     ->css('hubs')
     ->js('hubs')
     ->js();
?>

<div id="dialog-confirm"></div>
<div id="dialog-message"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a class="icon-reply btn" href="<?php echo Route::url($this->base . $this->start); ?>">
					<?php echo Lang::txt('COM_TIME_HUBS_ALL_HUBS'); ?>
				</a>
			</li>
			<li>
				<a class="icon-edit btn" href="<?php echo Route::url($this->base . '&task=edit&id=' . $this->row->id); ?>">
					<?php echo Lang::txt('COM_TIME_HUBS_EDIT'); ?>
				</a>
			</li>
			<li class="last">
				<a class="delete icon-delete btn" href="<?php echo Route::url($this->base . '&task=delete&id=' . $this->row->id); ?>">
					<?php echo Lang::txt('COM_TIME_HUBS_DELETE'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_hubs">
		<div class="container readonly">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
				<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<div class="grid">
				<div class="col span-third">
					<h3 class="headings"><?php echo Lang::txt('COM_TIME_HUBS_DETAILS'); ?></h3>
					<div class="grouping name-grouping">
						<label for="name"><?php echo Lang::txt('COM_TIME_HUBS_NAME'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->name)); ?>
					</div>

					<div class="grouping liaison-grouping">
						<label for="liaison"><?php echo Lang::txt('COM_TIME_HUBS_LIAISON'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->liaison)); ?>
					</div>

					<div class="grouping anniversary-grouping">
						<label for="anniversary_date"><?php echo Lang::txt('COM_TIME_HUBS_ANNIVERSARY_DATE'); ?>:</label>
						<?php echo ($this->row->anniversary_date != '0000-00-00') ? Date::of($this->row->anniversary_date)->toLocal('m/d/y') : ''; ?>
					</div>

					<div class="grouping support-grouping">
						<label for="support_level"><?php echo Lang::txt('COM_TIME_HUBS_SUPPORT_LEVEL'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->support_level)); ?>
					</div>

					<div class="grouping tasks-grouping">
						<label for="active_tasks"><?php echo Lang::txt('COM_TIME_HUBS_ACTIVE_TASKS'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->tasks()->areActive()->count())); ?>
					</div>

					<div class="grouping hours-grouping">
						<label for="total_hours"><?php echo Lang::txt('COM_TIME_HUBS_TOTAL_HOURS'); ?>:</label>
						<?php echo $this->escape(stripslashes($this->row->totalHours())); ?>
					</div>
				</div>

				<div class="col span-third">
					<h3 class="headings"><?php echo Lang::txt('COM_TIME_HUBS_CONTACTS'); ?></h3>

					<?php foreach ($this->row->contacts as $contact) : ?>
						<div class="contact-entry">
							<div class="contact-name"><?php  echo $this->escape(stripslashes($contact->name));  ?></div>
							<div class="contact-phone"><?php echo $this->escape(stripslashes($contact->phone)); ?></div>
							<div class="contact-email"><?php echo $this->escape(stripslashes($contact->email)); ?></div>
							<div class="contact-role"><?php  echo $this->escape(stripslashes($contact->role));  ?></div>
						</div>
					<?php endforeach; ?>
					<?php if (!$this->row->contacts->count()) : ?>
						<p><?php echo Lang::txt('COM_TIME_HUBS_NO_CONTACTS'); ?></p>
					<?php endif; ?>
				</div>

				<div class="col span-third omega">
					<h3 class="headings"><?php echo Lang::txt('COM_TIME_HUBS_NOTES'); ?></h3>
					<?php if ($this->row->notes) : ?>
						<div class="hub-notes">
							<div class="inner">
								<?php echo $this->row->notes; ?>
							</div>
						</div>
					<?php else : ?>
						<p>
							<?php echo Lang::txt('COM_TIME_HUBS_NO_NOTES'); ?>
						</p>
					<?php endif; ?>
				</div>
			</div><!-- / .grid -->

			<div class="time-allotments">
				<h3 class="headings"><?php echo Lang::txt('COM_TIME_HUBS_ALLOTMENTS'); ?></h3>

				<?php foreach ($this->row->allotments as $allotment) : ?>
					<div class="allotment-entry">
						<div class="allotment-start_date"><?php  echo $this->escape($allotment->start_date);  ?></div>
						<div class="allotment-end_date"><?php echo $this->escape($allotment->end_date); ?></div>
						<div class="allotment-hours"><?php echo $this->escape($allotment->hours); ?></div>
					</div>
				<?php endforeach; ?>
				<?php if (!$this->row->allotments->count()) : ?>
					<p><?php echo Lang::txt('COM_TIME_HUBS_NO_ALLOTMENTS'); ?></p>
				<?php endif; ?>
			</div>
		</div><!-- / .container -->
	</section>
</div>