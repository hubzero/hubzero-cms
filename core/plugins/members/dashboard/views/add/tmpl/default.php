<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
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
			<?php $cls = (in_array($module->id, $this->mymodules)) ? ' class="installed"' : '' ; ?>
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

					<?php if (count($xml->images->image) > 0) : ?>
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