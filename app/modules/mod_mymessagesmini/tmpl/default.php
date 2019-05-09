<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<li class="component-parent" id="account-messages">
	  <a class="component-button"><span class="nav-icon-groups"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/envelope.svg") ?></span><span>My Messages</span><span class="nav-icon-more"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/chevron-right.svg") ?></span></a>
	  <div class="component-panel">
	    <header><h2>My Messages</h2></header>
	    <a class="component-button"><span class="nav-icon-back"><?php echo file_get_contents(PATH_CORE . DS . "assets/icons/chevron-left.svg") ?></span>Back</a>
	      <div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>

<?php if ($this->getError())
{
	echo '<p class="error">' . $this->getError() . '</p>' . "\n";
}
else
{
	// Push the module CSS to the template
	$this->css();
	?>

<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
		<?php if (count($this->rows) <= 0) { ?>
			<p><em><?php echo Lang::txt('MOD_MYMESSAGESMINI_NO_MESSAGES'); ?></em></p>
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
			<p class="note"><?php echo Lang::txt('MOD_MYMESSAGESMINI_YOU_HAVE_MORE', $this->limit, $this->total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages')); ?></p>
		<?php } ?>

		<ul class="module-nav">
			<li><a class="icon-email-alt" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages'); ?>"><?php echo Lang::txt('MOD_MYMESSAGESMINI_ALL_MESSAGES'); ?></a></li>
			<li><a class="icon-plus" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&task=settings'); ?>"><?php echo Lang::txt('MOD_MYMESSAGESMINI_MESSAGE_SETTINGS'); ?></a></li>
		</ul>
		
	</div>
	<?php
}?>
  </div>
 </div>
</li>
