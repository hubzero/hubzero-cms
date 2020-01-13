<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$prereqs = $this->prereqs;
$userId = $this->userId;
$userIsAdmin = $this->userIsAdmin;
?>

<table>

	<thead>
		<tr>
			<th>
				<?php echo Lang::txt('COM_FORMS_FIELDS_ORDER'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_HEADINGS_FORM_ID'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_HEADINGS_FORM_NAME'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_HEADINGS_COMPLETION_STATUS'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_HEADINGS_SUBMITTED'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_HEADINGS_ACCEPTED'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_HEADINGS_REVIEWED_BY'); ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php
			foreach ($prereqs as $prereq):
				$this->view('_users_prereq_progress_item')
				->set('prereq', $prereq)
				->set('userId', $userId)
				->set('userIsAdmin', $userIsAdmin)
				->display();
			endforeach;
		?>
	</tbody>

</table>
