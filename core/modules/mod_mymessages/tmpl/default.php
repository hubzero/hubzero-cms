<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError())
{
	echo '<p class="error">' . $this->getError() . '</p>' . "\n";
}
else
{
	// Push the module CSS to the template
	$this->css();
	?>
	<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
		<ul class="module-nav">
			<li><a class="icon-email-alt" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages'); ?>"><?php echo Lang::txt('MOD_MYMESSAGES_ALL_MESSAGES'); ?></a></li>
			<li><a class="icon-plus" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&task=settings'); ?>"><?php echo Lang::txt('MOD_MYMESSAGES_MESSAGE_SETTINGS'); ?></a></li>
		</ul>

		<?php if (count($this->rows) <= 0) { ?>
			<p><em><?php echo Lang::txt('MOD_MYMESSAGES_NO_MESSAGES'); ?></em></p>
		<?php } else { ?>
			<ul class="expandedlist">
				<?php
				foreach ($this->rows as $row)
				{
					$cls = 'box';
					if ($row->actionid)
					{
						$cls = 'actionitem';
					}
					if ($row->component == 'support' || $row->component == 'com_support')
					{
						$fg = explode(' ', $row->subject);
						$fh = array_pop($fg);
						$row->subject = implode(' ', $fg);
					}
					?>
					<li class="<?php echo $cls; ?>">
						<a href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&msg=' . $row->id); ?>">
							<?php echo $this->escape(stripslashes($row->subject)); ?>
						</a>
						<span>
							<span>
								<time datetime="<?php echo $this->escape($row->created); ?>"><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
							</span>
						</span>
					</li>
					<?php
				}
				?>
			</ul>
		<?php } ?>
		<?php if ($this->total > $this->limit) { ?>
			<p class="note"><?php echo Lang::txt('MOD_MYMESSAGES_YOU_HAVE_MORE', $this->limit, $this->total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages')); ?></p>
		<?php } ?>
	</div>
	<?php
}