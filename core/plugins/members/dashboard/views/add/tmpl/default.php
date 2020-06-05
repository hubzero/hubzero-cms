<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="module-list">
	<h2>
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_TITLE'); ?>
	</h2>
	<ul class="module-list-triggers">
		<?php foreach ($this->modules as $module) : ?>
			<?php $cls = (in_array($module->id, $this->mymodules)) ? ' class="installed"' : ''; ?>
			<li <?php echo $cls; ?>>
				<a href="javascript:void(0);" data-module="<?php echo $module->id; ?>">
					<?php echo $module->title; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<ul class="module-list-content">
		<?php foreach ($this->modules as $module) : ?>
			<li class="<?php echo $module->id; ?>">
				<div class="module-title-bar">

					<?php if (in_array($module->id, $this->mymodules)) : ?>
						<a href="javascript:void(0);" class="btn button icon-extract" disabled="disabled">
							<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_INSTALLED'); ?>
						</a>
					<?php else : ?>
						<a href="javascript:void(0);" data-module="<?php echo $module->id; ?>" class="btn button btn-info icon-extract install-module">
							<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_INSTALL'); ?>
						</a>
					<?php endif; ?>

					<h3><?php echo $module->title; ?></h3>
				</div>
				<dl class="module-details">
				<?php
				$xml = null;
				if (file_exists(PATH_APP . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml'))
				{
					$xml = simplexml_load_file(PATH_APP . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml');
				}
				else if (file_exists(PATH_CORE . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml'))
				{
					$xml = simplexml_load_file(PATH_CORE . DS . 'modules' . DS . $module->module . DS . $module->module . '.xml');
				}
				?>
				<?php if ($xml) : ?>
					<?php if (isset($xml->attributes()->version)) : ?>
						<dt><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_VERSION'); ?></dt>
						<dd><?php echo $xml->attributes()->version; ?></dd>
					<?php endif; ?>

					<?php if ($xml->description != 'MOD_CUSTOM_XML_DESCRIPTION') : ?>
						<dt><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_DESCRIPTION'); ?></dt>
						<dd><?php
						if (!strstr($xml->description, ' '))
						{
							Lang::load($module->module, PATH_APP . DS . 'modules' . DS . $module->module) ||
							Lang::load($module->module, PATH_CORE . DS . 'modules' . DS . $module->module);
							$xml->description = Lang::txt($xml->description);
						}
						echo $xml->description; ?></dd>
					<?php endif; ?>

					<?php if (isset($xml->images) && isset($xml->images->image) && !empty($xml->images->image)) : ?>
						<dt><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES_MODULE_SCREENSHOTS'); ?></dt>
						<dd>
							<?php foreach ($xml->images->image as $image) : ?>
								<img src="<?php echo $image; ?>" />
							<?php endforeach; ?>
						</dd>
					<?php endif; ?>
				<?php endif; ?>
				</dl>
			</li>
		<?php endforeach; ?>
	</ul>
</div>