<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$pages = $this->pages;
$response = $this->response;
$userId = $this->userId;
?>

<table>

	<thead>
		<tr>
			<th>
				<?php echo Lang::txt('COM_FORMS_FIELDS_ORDER'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_FIELDS_TITLE'); ?>
			</th>
			<th>
				<?php echo Lang::txt('COM_FORMS_HEADINGS_COMPLETION_STATUS'); ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<?php
			foreach ($pages as $page):
				$this->view('_users_page_progress_item')
				->set('page', $page)
				->set('response', $response)
				->set('userId', $userId)
				->display();
			endforeach;
		?>
	</tbody>

</table>
