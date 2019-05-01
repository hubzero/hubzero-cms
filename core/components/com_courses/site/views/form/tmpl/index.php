<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('form.css')
     ->css('tablesorter.themes.blue.css', 'system')
     ->js('select.js')
     ->js('jquery.tablesorter.min', 'system');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
	<?php if ($this->errors): ?>
		<ul class="errors">
		<?php foreach ($this->errors as $error): ?>
			<li><?php echo $error; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="pdf" accept="application/pdf" required autofocus />
		<input type="hidden" name="task" value="upload" />
		<button type="submit"><?php echo Lang::txt('COM_COURSES_UPLOAD'); ?></button>
	</form>

	<h2><?php echo Lang::txt('COM_COURSES_SELECT_PREVIOUS_PDF'); ?></h2>
	<table class="tablesorter">
		<thead>
			<tr>
				<th><?php echo Lang::txt('COM_COURSES_HEADER_TITLE'); ?></th>
				<th><?php echo Lang::txt('COM_COURSES_HEADER_CREATED'); ?></th>
				<th><?php echo Lang::txt('COM_COURSES_HEADER_UPDATED'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach (\Components\Courses\Models\PdfForm::getActiveList() as $form): ?>
			<tr>
				<td>
					<span class="title"><?php echo $form['title'] ?></span>
					<form action="<?php echo Route::url('index.php?option=com_courses&controller=form'); ?>" method="get">
						<input type="hidden" name="task" value="deploy" />
						<input type="hidden" name="formId" value="<?php echo $form['id'] ?>" />
						<button type="submit"><?php echo Lang::txt('COM_COURSES_DEPLOY'); ?></button>
					</form>
					<form action="<?php echo Route::url('index.php?option=com_courses&controller=form'); ?>" method="get">
						<input type="hidden" name="task" value="layout" />
						<input type="hidden" name="formId" value="<?php echo $form['id'] ?>" />
						<button type="submit"><?php echo Lang::txt('JACTION_EDIT'); ?></button>
					</form>
					<br />
					<?php if ($deps = \Components\Courses\Models\PdfFormDeployment::forForm($form['id'])): ?>
					<table class="tablesorter nested">
						<thead>
							<tr>
								<th><?php echo Lang::txt('COM_COURSES_HEADER_DEPLOYMENT'); ?></th>
								<th><?php echo Lang::txt('COM_COURSES_HEADER_USER'); ?></th>
								<th><?php echo Lang::txt('COM_COURSES_HEADER_START_DATE'); ?></th>
								<th><?php echo Lang::txt('COM_COURSES_HEADER_END_DATE'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($deps as $dep): ?>
							<tr>
								<td>
									<span class="state"><?php echo $dep->getState() ?></span>
									<a href="<?php echo Route::url($this->base . '&task=showDeployment&id='.$dep->getId().'&formId='.$form['id']); ?>">
										<?php echo $dep->getLink() ?>
									</a>
								</td>
								<td>
									<?php echo $this->escape($dep->getUserName()) ?></td><td><?php echo date('Y-m-d H:i', strtotime($dep->getStartTime())) ?>
								</td>
								<td>
									<?php echo date('Y-m-d H:i', strtotime($dep->getEndTime())) ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<?php endif; ?>
				</td>
				<td><?php echo date('Y-m-d H:i', strtotime($form['created'])) ?></td>
				<td><?php echo date('Y-m-d H:i', strtotime($form['updated'])) ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</section>