<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

$text = ($this->profile->isNew() ? Lang::txt('JACTION_CREATE') : Lang::txt('JACTION_EDIT'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $text, 'user');
if ($canDo->get('core.edit') || $canDo->get('core.create'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
if ($canDo->get('core.create') && $canDo->get('core.manage'))
{
	Toolbar::save2new();
}
Toolbar::cancel();
Toolbar::divider();
Toolbar::help('user');

$this->css();

Html::behavior('switcher', 'submenu');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">

	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="member-nav">
						<li><a href="#" onclick="return false;" id="account" class="active"><?php echo Lang::txt('COM_MEMBERS_SECTION_ACCOUNT'); ?></a></li>
						<li><a href="#" onclick="return false;" id="profile"><?php echo Lang::txt('COM_MEMBERS_SECTION_PROFILE'); ?></a></li>
						<li><a href="#" onclick="return false;" id="password"><?php echo Lang::txt('COM_MEMBERS_SECTION_PASSWORD'); ?></a></li>
						<?php if (!$this->profile->isNew()): ?>
							<li><a href="#" onclick="return false;" id="groups"><?php echo Lang::txt('COM_MEMBERS_SECTION_GROUPS'); ?></a></li>
							<li><a href="#" onclick="return false;" id="hosts"><?php echo Lang::txt('COM_MEMBERS_SECTION_HOSTS'); ?></a></li>
							<li><a href="#" onclick="return false;" id="messaging"><?php echo Lang::txt('COM_MEMBERS_SECTION_MESSAGING'); ?></a></li>
						<?php endif; ?>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="member-document">
		<div id="page-account" class="tab">
			<?php echo $this->loadTemplate('user'); ?>
		</div>

		<div id="page-profile" class="tab">
			<?php echo $this->loadTemplate('profile'); ?>
		</div>

		<div id="page-password" class="tab">
			<?php echo $this->loadTemplate('password'); ?>
		</div>

		<?php if (!$this->profile->isNew()): ?>
			<div id="page-groups" class="tab">
				<?php echo $this->loadTemplate('groups'); ?>
			</div>

			<div id="page-hosts" class="tab">
				<?php echo $this->loadTemplate('hosts'); ?>
			</div>

			<div id="page-messaging" class="tab">
				<?php echo $this->loadTemplate('messaging'); ?>
			</div>
		<?php endif; ?>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
