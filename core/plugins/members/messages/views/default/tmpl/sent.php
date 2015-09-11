<?php
/**
 * @package     hubzero-cms
 * @author      Christopher Smoak <csmoak@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<form action="<?php echo Route::url($this->member->getLink() . '&active=messages&task=sent'); ?>" method="post">
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
					<?php echo $this->pagenavhtml; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->rows) : ?>
				<?php foreach ($this->rows as $row) : ?>
					<?php
						//get the component that created message
						$component = (substr($row->component,0,4) == 'com_') ? substr($row->component,4) : $row->component;

						//url to view message
						$url = Route::url($this->member->getLink() . '&active=messages&msg=' . $row->id);

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

						//get who the message is to
						$to = '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $row->uid) . '">' . $row->name . '</a>';

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
