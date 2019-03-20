<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Zach Weidner <zweidner@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_PACKAGES'), 'install');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}
Toolbar::help('warnings');

$example = Component::path('com_installer') . '/config/composer.json.dist';
?>
<div id="installer-warnings">
	<form action="<?php echo Route::url('index.php?option=com_installer&controller=warnings'); ?>" method="post" name="adminForm" id="item-form">
		<div class="input-wrap">
			<p class="error"><?php echo Lang::txt('COM_INSTALLER_MSG_WARNINGS_MISSING_COMPOSER', PATH_APP . '/composer.json'); ?></p>
			<?php
			if (file_exists($example)):
				$contents = file_get_contents($example);
				if ($contents):
					?>
					<label for="sample"><?php echo Lang::txt('COM_INSTALLER_MSG_WARNINGS_MISSING_COMPOSER_SAMPLE'); ?></label>
					<textarea name="sample" id="sample" cols="100" rows="28"><?php
					$site = preg_replace('/[^a-zA-Z0-9\-]/', '', strtolower(Config::get('sitename')));

					$contents = json_decode($contents);
					$contents->name = $site . '/' . $site . '-app';
					$contents = json_encode($contents, JSON_PRETTY_PRINT);
					$contents = str_replace('\/', '/', $contents);

					echo $contents;
					?></textarea>
					<?php
				endif;
			endif;
			?>
		</div>
	</form>
</div>
