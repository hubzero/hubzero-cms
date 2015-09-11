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

$base = $this->course->offering()->link();

HTML::behavior('core');

?>

<div class="header">
	<a href="#" class="trash btn icon-trash">Deleted Assets</a>
	<a href="<?php echo Route::url($base . '&active=outline'); ?>" class="done btn icon-check">Done</a>
	<h3><?php echo $this->title; ?></h2>
</div>

<div id="dialog-confirm" class="dialog">This is a dialog box</div>

<div class="error-box">
	<p class="error-close"></p>
	<p class="error-message">There was an error</p>
</div>

<div id="outline-main" class="outline-main">
	<div class="delete-tray">
		<h4>Deleted Assets</h4>
		<ul class="assets-deleted">

<?php
			foreach ($this->course->offering()->units() as $unit) :
				foreach ($unit->assetgroups() as $agt) :
					foreach ($agt->children() as $ag) :
						if ($ag->assets()->total()) :
							foreach ($ag->assets() as $a) :
								if ($a->isDeleted()) :
									$this->view('asset_partial')
									     ->set('base', $base)
									     ->set('course', $this->course)
									     ->set('unit', $unit)
									     ->set('ag', $ag)
									     ->set('a', $a)
									     ->display();
								endif;
							endforeach;
						endif;
					endforeach;
				endforeach;
			endforeach;
?>

		</ul>
	</div>

	<ul class="unit">

		<?php foreach ($this->course->offering()->units() as $unit) : ?>
		<li class="unit-item" id="unit_<?php echo $unit->get('id') ?>">
			<div class="unit-title-arrow"></div>
			<div class="unit-edit-container">
				<div class="title unit-title">
					<div class="unit-title-value"><?php echo $unit->get('title'); ?></div>
					<div class="edit">edit</div>
				</div>
				<div class="clear"></div>
				<div class="unit-edit">
					<div class="unit-edit-wrap">
						<form action="<?php echo Request::base(true); ?>/api/courses/unit/save" class="unit-edit-form">
							<label for="title">Title:</label>
							<input class="unit-edit-text" name="title" type="text" value="<?php echo $unit->get('title'); ?>" placeholder="title" />
							<input class="unit-edit-save" type="submit" value="Save" />
							<input class="unit-edit-reset" type="reset" value="Cancel" />
							<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
							<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
							<input type="hidden" name="section_id" value="<?php echo $this->course->offering()->section()->get('id') ?>" />
							<input type="hidden" name="id" value="<?php echo $unit->get('id'); ?>" />
						</form>
					</div>
					<div class="unit-prerequisites">
						<?php
							$this->view('_prerequisites')
							     ->set('scope', 'unit')
							     ->set('scope_id', $unit->get('id'))
							     ->set('section_id', $this->course->offering()->section()->get('id'))
							     ->set('items', clone($this->course->offering()->units()))->display();
						?>
					</div>
				</div>
			</div>
			<div class="progress-container">
				<div class="progress-indicator"></div>
			</div>
			<div class="clear"></div>

			<ul class="asset-group-type-list">

			<?php foreach ($unit->assetgroups() as $agt) : ?>

				<li class="asset-group-type-item <?php echo ($agt->get('state') == '1') ? 'published' : 'unpublished' ?>">
					<div class="asset-group-type-item-container">
						<div class="asset-group-title-container">
							<div class="asset-group-title title">
								<div class="asset-group-title-edit edit">edit</div>
								<div class="title"><?php echo $agt->get('title'); ?></div>
							</div>
							<form action="<?php echo Request::base(true); ?>/api/courses/assetgroup/save">
								<div class="label-input-pair">
									<label for="title">Title:</label>
									<input class="" name="title" type="text" value="<?php echo $agt->get('title') ?>" />
								</div>
								<div class="label-input-pair">
									<label for="state">Published:</label>
									<select name="state">
										<option value="0"<?php echo ($agt->get('state') == '0') ? ' selected="selected"' : '' ?>>No</option>
										<option value="1"<?php echo ($agt->get('state') == '1') ? ' selected="selected"' : '' ?>>Yes</option>
									</select>
								</div>
								<div class="label-input-pair">
									<label for="description">Short Description:</label>
									<input type="text" name="description" value="<?php echo $agt->get('description') ?>">
									<span><?php echo Lang::txt('PLG_COURSES_OUTLINE_FIELD_SHORT_DESCRIPTION_HINT'); ?></span>
								</div>
								<input class="asset-group-title-save" type="submit" value="Save" />
								<input class="asset-group-title-cancel" type="reset" value="Cancel" />
								<input type="hidden" name="course_id" value="<?php echo $this->course->get('id') ?>" />
								<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias') ?>" />
								<input type="hidden" name="id" value="<?php echo $agt->get('id') ?>" />
							</form>
						</div>
						<div class="asset-group-container">
							<ul class="asset-group sortable">

<?php
				// Loop through our asset groups
				foreach ($agt->children() as $ag)
				{
					$this->view('asset_group_partial')
					     ->set('base', $base)
					     ->set('course', $this->course)
					     ->set('unit', $unit)
					     ->set('ag', $ag)
					     ->display();
				}

				// Now display assets directly attached to the asset group type
				if ($agt->assets()->total())
				{
					$this->view('asset_group_partial')
					     ->set('base', $base)
					     ->set('course', $this->course)
					     ->set('unit', $unit)
					     ->set('ag', $agt)
					     ->display();
				}
?>

								<li class="add-new asset-group-item">
									Add a new <?php echo (substr($agt->get('title'), -3) == 'ies') ? strtolower(preg_replace('/ies$/', 'y', $agt->get('title'))) : strtolower(rtrim($agt->get('title'), 's')); ?>
									<form action="<?php echo Request::base(true); ?>/api/courses/assetgroup/save">
										<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
										<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
										<input type="hidden" name="unit_id" value="<?php echo $unit->get('id'); ?>" />
										<input type="hidden" name="parent" value="<?php echo $agt->get('id'); ?>" />
									</form>
								</li>
							</ul>
						</div>
					</div>
				</li>

			<?php endforeach; // foreach asset groups ?>

			</ul>
<?php
			if ($unit->assets()->total())
			{
?>
				<ul class="assets-list">
<?php
				foreach ($unit->assets() as $a)
				{
					$href = Route::url($base . '&asset=' . $a->get('id'));
					if ($a->get('type') == 'video')
					{
						$href = Route::url($base . '&active=outline&a=' . $unit->get('alias'));
					}
					echo '<li class="asset-group-item"><a class="asset ' . $a->get('type') . '" href="' . $href . '">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
				}
?>
				</ul>
<?php
			}
?>
		</li>

		<?php endforeach; // foreach unit ?>

		<li class="add-new unit-item">
			Add a new unit
			<form action="<?php echo Request::base(true); ?>/api/courses/unit/save">
				<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
				<input type="hidden" name="offering_id" value="<?php echo $this->course->offering()->get('id'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
				<input type="hidden" name="section_id" value="<?php echo $this->course->offering()->section()->get('id'); ?>" />
			</form>
		</li>
	</ul>
</div>

<div class="session-expired">
	<h3>Session Expired</h3>
	<p>Sorry. Your session has expired. You must login again to proceed.</p>
	<p><a href="<?php echo Route::url('index.php?option=com_users&view=login&return='.base64_encode($_SERVER['REQUEST_URI'])); ?>" class="btn btn-warning">Login</a>
</div>