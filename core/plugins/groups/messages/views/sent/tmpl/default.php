<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<h3 class="section-header" id="messages">
	<?php echo Lang::txt('MESSAGES'); ?>
</h3>

<?php if ($this->authorized == 'manager') { ?>
<ul id="page_options">
	<li>
		<a id="new-group-message" class="icon-email message btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages&action=new'); ?>">
			<span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SEND'); ?></span>
		</a>
	</li>
</ul>
<?php } ?>

<div class="section">
	<div class="container">
		<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages'); ?>" method="post">
			<table class="groups entries">
				<caption><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SENT'); ?> <span>(<?php echo count($this->rows); ?>)</span></caption>
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('Subject'); ?></th>
						<th scope="col"><?php echo Lang::txt('Message From'); ?></th>
						<th scope="col"><?php echo Lang::txt('Date Sent'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ($this->rows->count() > 0) { ?>
						<?php foreach ($this->rows as $row) { ?>
							<tr>
								<td><a href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&active=messages&action=viewmessage&msg='.$row->id); ?>"><?php echo $this->escape(stripslashes($row->subject)); ?></a></td>
								<td><a href="<?php echo Route::url('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo $this->escape(stripslashes($row->name)); ?></a></td>
								<td><time datetime="<?php echo $row->created; ?>"><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
						<tr>
							<td colspan="3"><?php echo Lang::txt('No messages found'); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</form>

		<?php
		// Initiate paging
		$pageNav = $this->pagination(
			$this->total,
			$this->filters['start'],
			$this->filters['limit']
		);
		$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
		$pageNav->setAdditionalUrlParam('active', 'messages');

		echo $pageNav->render();
		?>
		<div class="clearfix"></div>
	</div><!-- / .container -->
</div><!-- / .section -->

