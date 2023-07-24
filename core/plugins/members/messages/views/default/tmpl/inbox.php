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

<form action="<?php echo Route::url($this->member->link() . '&active=messages'); ?>" method="post">
	<div id="filters">
		<div class="hz-v-align">
			<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_FROM'); ?>
			<div class="hz-input-combo">
				<input type="hidden" name="inaction" value="inbox" />
				<select class="option" name="filter">
					<option value=""><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_ALL'); ?></option>
					<?php
					if ($this->components)
					{
						foreach ($this->components as $component)
						{
							$component = substr($component->component, 4);
							$sbjt  = '<option value="'.$component.'"';
							$sbjt .= ($component == $this->filters['filter']) ? ' selected="selected"' : '';
							$sbjt .= '>'.$component.'</option>'."\n";
							echo $sbjt;
						}
					}
					?>
				</select>
				<input class="btn" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_FILTER'); ?>" />
			</div>
		</div>
	</div>

	<div id="actions">
		<div class="hz-input-combo">
			<select class="option" name="action">
				<option value=""><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_WITH_SELECTED'); ?></option>
				<option value="markasread"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_MARK_AS_READ'); ?></option>
				<option value="markasunread"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_MARK_AS_UNREAD'); ?></option>
				<option value="sendtoarchive"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_ARCHIVE'); ?></option>
				<option value="sendtotrash"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_TRASH'); ?></option>
			</select>
			<input type="hidden"name="activetab" value="inbox" />
			<input class="btn" type="submit" value="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MSG_APPLY'); ?>" />
		</div>
	</div>
	<br class="clear" />

	<table class="data">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="msgall" id="msgall" value="all" /></th>
				<th scope="col"> </th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
				<th scope="col"> </th>
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
					$pageNav->setAdditionalUrlParam('task', 'inbox');
					$pageNav->setAdditionalUrlParam('action', '');

					echo $pageNav->render();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if ($this->rows->count()) : ?>
				<?php foreach ($this->rows as $row) : ?>
					<?php
						//get the message status
						$status = ($row->whenseen && $row->whenseen != '0000-00-00 00:00:00') ? '<span class="read">read</span>' : '<span class="unread">unread</span>';

						//get the component that created message
						$component = (substr($row->component, 0, 4) == 'com_') ? substr($row->component, 4) : $row->component;

						//get the message subject
						$subject = $row->subject;

						//support - special
						if ($component == 'support')
						{
							$fg = explode(' ', $row->subject);
							$fh = array_pop($fg);
							$subject = implode(' ', $fg);
						}

						//subject link
						$subject_cls  = 'message-link';
						$subject_cls .= ($row->whenseen && $row->whenseen != '0000-00-00 00:00:00') ? "" : " unread";
					?>
					<tr>
						<td class="check">
							<input class="chkbox" type="checkbox" id="msg<?php echo $row->id; ?>" value="<?php echo $row->id; ?>" name="mid[]" />
						</td>
						<td class="status">
							<?php echo $status; ?>
						</td>
						<td>
							<a class="<?php echo $subject_cls; ?>" href="<?php echo Route::url($this->member->link() . '&active=messages&msg=' . $row->id); ?>">
								<?php echo $subject; ?>
							</a>
						</td>
						<td>
							<?php
							if (substr($row->type, -8) == '_message')
							{
								$from = Lang::txt('JANONYMOUS');
								if (!$row->anonymous)
								{
									$u = User::getInstance($row->created_by);
									$from = '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $u->get('id')) . '">' . $u->get('name') . '</a>';
								}
								echo $from;
							}
							else
							{
								echo Lang::txt('PLG_MEMBERS_MESSAGES_SYSTEM', $component);
							}
							?>
						</td>
						<td>
							<time datetime="<?php echo $row->created; ?>"><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
						</td>
						<td>
							<a title="<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_DELETE_TITLE'); ?>" class="trash tooltips" href="<?php echo Route::url($this->member->link() . '&active=messages&mid[]=' . $row->id . '&action=sendtotrash&activetab=inbox&' . Session::getFormToken() . '=1'); ?>">
								<?php echo Lang::txt('PLG_MEMBERS_MESSAGES_TRASH'); ?>
							</a>
						</td>
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
