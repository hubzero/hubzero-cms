<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<form action="<?php echo Route::url($this->member->link() . '&active=messages&task=sent'); ?>" method="post">
	<table class="data">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_TO'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_DATE_SENT'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					$pageNav = new \Hubzero\Pagination\Paginator(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					$pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
					$pageNav->setAdditionalUrlParam('active', 'messages');
					$pageNav->setAdditionalUrlParam('task', 'sent');
					$pageNav->setAdditionalUrlParam('action', '');

					echo $pageNav->render();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->rows) : ?>
				<?php foreach ($this->rows as $row) : ?>
					<?php
						//get the component that created message
						$component = (substr($row->component, 0, 4) == 'com_') ? substr($row->component, 4) : $row->component;

						//url to view message
						$url = Route::url($this->member->link() . '&active=messages&msg=' . $row->id);

						//get the message subject
						$subject = $row->subject;

						//support - special
						if ($component == 'support')
						{
							$fg = explode(' ', $row->subject);
							$fh = array_pop($fg);
							$subject = implode(' ', $fg);
						}

						//get the message
						$preview = ($row->message) ? '<h3>' . Lang::txt('PLG_MEMBERS_MESSAGES_PREVIEW') . '</h3>' . nl2br(stripslashes($row->message)) : '';

						//subject link
						$subject_cls = 'message-link';

						$subject  = "<a class=\"{$subject_cls}\" href=\"{$url}\">{$subject}</a>";

						// Check for identity masking flag
						if (strpos($row->type, '_anonymous') === false)
						{
							// Display who the message is to
							$to = '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $row->uid) . '">' . $row->name . '</a>';
						}
						else
						{
							// Mask the destination
							$to = 'Anonymous';
						}

						//date received
						$date = Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
					?>
					<tr>
						<td><?php echo $subject; ?></td>
						<td><?php echo $to; ?></td>
						<td><?php echo $date; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="6"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_NONE'); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<?php echo Html::input('token'); ?>
</form>
