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

$this->js('select2', 'system')
     ->css('select2', 'system')
     ->css('jquery.ui.css', 'system');

$this->css()
     ->css('hubs')
     ->js('hubs')
     ->js('time');

HTML::behavior('core');
?>

<div id="dialog-confirm"></div>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
	<div id="content-header-extra">
		<ul id="useroptions">
			<?php if ($this->row->id && $this->permissions->can('edit.permissions')) : ?>
				<li>
					<?php $permRoute = Route::url('index.php?option=' . $this->option . '&controller=permissions&scope=Hub&scope_id=' . $this->row->id . '&tmpl=component'); ?>
					<a class="icon-config btn permissions-button" href="<?php echo $permRoute; ?>">
						<?php echo Lang::txt('COM_TIME_HUBS_PERMISSIONS'); ?>
					</a>
				</li>
			<?php endif; ?>
			<li class="last">
				<a class="icon-reply btn" href="<?php echo Route::url($this->base . $this->start); ?>">
					<?php echo Lang::txt('COM_TIME_HUBS_ALL_HUBS'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_hubs">
		<div class="container">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
					<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<form action="<?php echo Route::url($this->base . '&task=save'); ?>" method="post">
				<div class="grouping" id="name-group">
					<label for="name"><?php echo Lang::txt('COM_TIME_HUBS_NAME'); ?>:</label>
					<input type="text" name="name" id="name" value="<?php echo $this->escape(stripslashes($this->row->name)); ?>" size="50" />
				</div>

				<label for="contact"><?php echo Lang::txt('COM_TIME_HUBS_CONTACTS'); ?>:</label>
				<?php foreach ($this->row->contacts as $contact) : ?>
					<div class="grouping contact-grouping grid" id="contact-<?php echo $contact->id; ?>-group">
						<div class="col span4">
							<input type="text" name="contacts[<?php echo $contact->id; ?>][name]" id="" value="<?php echo $this->escape(stripslashes($contact->name)); ?>" />
						</div>
						<div class="col span2">
							<input type="text" name="contacts[<?php echo $contact->id; ?>][phone]" id="" value="<?php echo $this->escape(stripslashes($contact->phone)); ?>" />
						</div>
						<div class="col span2">
							<input type="text" name="contacts[<?php echo $contact->id; ?>][email]" id="" value="<?php echo $this->escape(stripslashes($contact->email)); ?>" />
						</div>
						<div class="col span2">
							<input type="text" name="contacts[<?php echo $contact->id; ?>][role]" id="" value="<?php echo $this->escape(stripslashes($contact->role)); ?>" />
							<input type="hidden" name="contacts[<?php echo $contact->id; ?>][id]" value="<?php echo $contact->id; ?>" />
						</div>
						<div class="col span2 omega">
							<a href="<?php echo Route::url($this->base . '&task=deletecontact&id=' . $contact->id); ?>" class="btn btn-danger icon-delete delete_contact" title="Delete contact">Delete</a>
						</div>
					</div>
				<?php endforeach; ?>

				<div class="grouping grid" id="new-contact-group">
					<div class="col span4">
						<input type="text" name="contacts[new][name]" id="new_name" placeholder="name" class="new_contact" />
					</div>
					<div class="col span2">
						<input type="text" name="contacts[new][phone]" id="new_phone" placeholder="phone" class="new_contact" />
					</div>
					<div class="col span2">
						<input type="text" name="contacts[new][email]" id="new_email" placeholder="email" class="new_contact" />
					</div>
					<div class="col span2">
						<input type="text" name="contacts[new][role]" id="new_role" placeholder="role" class="new_contact" />
					</div>
					<div class="col span2 omega">
						<a href="#" id="save_new_contact" class="btn btn-success icon-save save_contact" title="Save contact">Save</a>
					</div>
				</div>

				<div class="grouping" id="liaison-group">
					<label for="liaison"><?php echo Lang::txt('COM_TIME_HUBS_LIAISON'); ?>:</label>
					<input type="text" name="liaison" id="liaison" value="<?php echo $this->escape(stripslashes($this->row->liaison)); ?>" size="50" />
				</div>

				<div class="grouping" id="anniversary-group">
					<label for="anniversary_date"><?php echo Lang::txt('COM_TIME_HUBS_ANNIVERSARY_DATE'); ?>:</label>
					<input class="hadDatepicker" type="text" name="anniversary_date" id="anniversary_date" value="<?php echo $this->escape(stripslashes($this->row->anniversary_date)); ?>" size="50" />
				</div>

				<div class="grouping" id="support-group">
					<label for="support_level"><?php echo Lang::txt('COM_TIME_HUBS_SUPPORT_LEVEL'); ?>:</label>
					<select name="support_level" id="support_level">
						<option <?php echo ($this->row->support_level == 'Classic Support') ? 'selected="selected" ' : ''; ?>value="Classic Support">
							Classic Support
						</option>
						<option <?php echo ($this->row->support_level == 'Standard Support') ? 'selected="selected" ' : ''; ?>value="Standard Support">
							Standard Support
						</option>
						<option <?php echo ($this->row->support_level == 'Bronze Support') ? 'selected="selected" ' : ''; ?>value="Bronze Support">
							Bronze Support
						</option>
						<option <?php echo ($this->row->support_level == 'Silver Support') ? 'selected="selected" ' : ''; ?>value="Silver Support">
							Silver Support
						</option>
						<option <?php echo ($this->row->support_level == 'Gold Support') ? 'selected="selected" ' : ''; ?>value="Gold Support">
							Gold Support
						</option>
						<option <?php echo ($this->row->support_level == 'Platinum Support') ? 'selected="selected" ' : ''; ?>value="Platinum Support">
							Platinum Support
						</option>
					</select>
				</div>

				<div class="grouping" id="notes-group">
					<label for="notes"><?php echo Lang::txt('COM_TIME_HUBS_NOTES'); ?>:</label>
					<?php echo $this->editor('notes', $this->escape($this->row->notes('raw')), 35, 6, 'notes', array('class' => 'minimal no-footer')); ?>
				</div>

				<fieldset>
					<legend><?php echo Lang::txt('COM_TIME_HUBS_ALLOTMENTS'); ?>:</legend>
					<?php foreach ($this->row->allotments as $allotment) : ?>
						<div class="grouping allotment-grouping grid" id="allotment-<?php echo $allotment->id; ?>-group">
							<div class="col span4">
								<input type="text" name="allotments[<?php echo $allotment->id; ?>][start_date]" id="" value="<?php echo $this->escape($allotment->start_date); ?>" class="hadDatepicker" />
							</div>
							<div class="col span4">
								<input type="text" name="allotments[<?php echo $allotment->id; ?>][end_date]" id="" value="<?php echo $this->escape($allotment->end_date); ?>" class="hadDatepicker" />
							</div>
							<div class="col span2">
								<input type="text" name="allotments[<?php echo $allotment->id; ?>][hours]" id="" value="<?php echo $this->escape($allotment->hours); ?>" />
							</div>
							<div class="col span2 omega">
								<input type="hidden" name="allotments[<?php echo $allotment->id; ?>][id]" value="<?php echo $allotment->id; ?>" />
								<a href="<?php echo Route::url($this->base . '&task=deleteallotment&id=' . $allotment->id); ?>" class="btn btn-danger icon-delete delete_contact" title="Delete allotment">Delete</a>
							</div>
						</div>
					<?php endforeach; ?>

					<div class="grouping grid" id="new-allotment-group">
						<div class="col span4">
							<input type="text" name="allotments[new][start_date]" id="new_start_date" placeholder="YYYY-MM-DD" class="hadDatepicker new_allotment" />
						</div>
						<div class="col span4">
							<input type="text" name="allotments[new][end_date]" id="new_end_date" placeholder="YYYY-MM-DD" class="hadDatepicker new_allotment" />
						</div>
						<div class="col span2">
							<input type="text" name="allotments[new][hours]" id="new_hours" placeholder="hours" class="new_allotment" />
						</div>
						<div class="col span2 omega">
							<a href="#" id="save_new_allotment" class="btn btn-success icon-save save_allotment" title="Save allotment">Save</a>
						</div>
					</div>
				</fieldset>

				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" id="hub_id" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="active" value="hubs" />
				<input type="hidden" name="action" value="save" />

				<p class="submit">
					<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_TIME_HUBS_SUBMIT'); ?>" />
					<a href="<?php echo Route::url($this->base . $this->start); ?>">
						<button class="btn btn-secondary" type="button"><?php echo Lang::txt('COM_TIME_HUBS_CANCEL'); ?></button>
					</a>
				</p>
			</form>
		</div>
	</section>
</div>