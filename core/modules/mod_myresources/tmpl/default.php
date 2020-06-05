<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if (!$this->no_html)
{
	// Push the module CSS to the template
	$this->css();
	$this->js();
?>
	<ul class="module-nav">
		<li>
			<a class="icon-browse" href="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=contributions&area=resources'); ?>">
				<?php echo Lang::txt('MOD_MYRESOURCES_ALL_PUBLICATIONS'); ?>
			</a>
		</li>
	</ul>
	<form method="get" action="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard'); ?>" data-module="<?php echo $this->module->id; ?>" id="myresources-form" enctype="multipart/form-data">
<?php } ?>
		<div id="myresources-content">
			<?php if (!$this->contributions) { ?>
				<p><?php echo Lang::txt('MOD_MYRESOURCES_NONE_FOUND'); ?></p>
			<?php } else { ?>
				<ul class="expandedlist">
					<?php
					for ($i=0; $i < count($this->contributions); $i++)
					{
						// Determine css class
						switch ($this->contributions[$i]->published)
						{
							case 1:
								$class = 'published';
								break;
							case 2:
								$class = 'draft';
								break;
							case 3:
								$class = 'pending';
								break;
							case 0:
								$class = 'deleted';
								break;
						}

						$thedate = Date::of($this->contributions[$i]->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
						?>
						<li class="<?php echo $class; ?>">
							<a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->contributions[$i]->id); ?>">
								<?php echo \Hubzero\Utility\Str::truncate(stripslashes($this->contributions[$i]->title), 40); ?>
							</a>
							<span class="under">
								<?php echo $thedate . ' &nbsp; ' . $this->escape(stripslashes($this->contributions[$i]->typetitle)); ?>
							</span>
						</li>
						<?php
					}
					?>
				</ul>
			<?php } ?>
		</div>
<?php if (!$this->no_html) { ?>
	</form>
<?php }
