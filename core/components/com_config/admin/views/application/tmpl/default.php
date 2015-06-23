<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CONFIG_GLOBAL_CONFIGURATION'), 'config.png');
Toolbar::apply('application.apply');
Toolbar::save('application.save');
Toolbar::divider();
Toolbar::cancel('application.cancel');
Toolbar::divider();
Toolbar::help('global_config');

// Load tooltips behavior
Html::behavior('formvalidation');
Html::behavior('switcher', 'submenu');
Html::behavior('tooltip');

// Load submenu template, using element id 'submenu' as needed by behavior.switcher
$this->document->setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'application.cancel' || document.formvalidator.isValid($('#application-form'))) {
			Joomla.submitform(task, document.getElementById('application-form'));
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=com_config');?>" id="application-form" method="post" name="adminForm" class="form-validate">
	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftplogin'); ?>
	<?php endif; ?>
	<div id="config-document">
		<div id="page-site" class="tab">
			<div class="noshow">
				<div class="width-60 fltlft">
					<?php echo $this->loadTemplate('site'); ?>
					<?php echo $this->loadTemplate('metadata'); ?>
				</div>
				<div class="width-40 fltrt">
					<?php echo $this->loadTemplate('seo'); ?>
					<?php echo $this->loadTemplate('cookie'); ?>
				</div>
			</div>
		</div>
		<div id="page-system" class="tab">
			<div class="noshow">
				<div class="width-60 fltlft">
					<?php echo $this->loadTemplate('system'); ?>
				</div>
				<div class="width-40 fltrt">
					<?php echo $this->loadTemplate('debug'); ?>
					<?php echo $this->loadTemplate('cache'); ?>
					<?php echo $this->loadTemplate('session'); ?>
				</div>
			</div>
		</div>
		<div id="page-server" class="tab">
			<div class="noshow">
				<div class="width-60 fltlft">
					<?php echo $this->loadTemplate('server'); ?>
					<?php echo $this->loadTemplate('locale'); ?>
					<?php echo $this->loadTemplate('ftp'); ?>
				</div>
				<div class="width-40 fltrt">
					<?php echo $this->loadTemplate('database'); ?>
					<?php echo $this->loadTemplate('mail'); ?>
				</div>
			</div>
		</div>
		<div id="page-permissions" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('permissions'); ?>
			</div>
		</div>
		<div id="page-filters" class="tab">
			<div class="noshow">
				<?php echo $this->loadTemplate('filters'); ?>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</div>
	<div class="clr"></div>
</form>
