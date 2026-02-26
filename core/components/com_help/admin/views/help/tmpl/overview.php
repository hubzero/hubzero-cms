<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_HELP'), 'help');

$this->css()
    ->js();

$baseUrl = 'index.php?option=' . $this->option;
$helpUrl = $baseUrl . '&tmpl=help&component=';
?>
<form action="<?php echo Route::url($baseUrl); ?>"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <div class="grid col-row">
        <div class="col span4">
            <h3><?php echo Lang::txt('COM_HELP_USERS'); ?></h3>
            <ul>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_members'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_USER_ACCOUNTS'); ?>
                    </a>
                </li>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_groups'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_USER_GROUPS'); ?>
                    </a>
                </li>
            </ul>
            <h3><?php echo Lang::txt('COM_HELP_MENUS'); ?></h3>
            <ul>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_menus'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_MENU_MANAGER'); ?>
                    </a>
                </li>
            </ul>
            <h3><?php echo Lang::txt('COM_HELP_CONTENT'); ?></h3>
            <ul>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_content'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_ARTICLE_MANAGER'); ?>
                    </a>
                </li>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_categories'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_CATEGORY_MANAGER'); ?>
                    </a>
                </li>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_media'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_MEDIA_MANAGER'); ?>
                    </a>
                </li>
            </ul>
            <h3><?php echo Lang::txt('COM_HELP_EXTENSIONS'); ?></h3>
            <ul>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_modules'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_MODULE_MANAGER'); ?>
                    </a>
                </li>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_plugins'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_PLUGIN_MANAGER'); ?>
                    </a>
                </li>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_templates'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_TEMPLATE_MANAGER'); ?>
                    </a>
                </li>
                <li>
                    <?php $url = Route::url($helpUrl . 'com_languages'); ?>
                    <a target="help-page" href="<?php echo $url; ?>">
                        <?php echo Lang::txt('COM_HELP_LANGUAGE_MANAGER'); ?>
                    </a>
                </li>
            </ul>
            <h3><?php echo Lang::txt('Components'); ?></h3>
            <ul>
                <?php foreach ($this->components as $component) { ?>
                    <li>
                        <?php $url = Route::url($helpUrl . $component->element); ?>
                        <a target="help-page" href="<?php echo $url; ?>">
                            <?php echo Lang::txt($component->text); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col span8">
            <?php $iframeSrc = Route::url($helpUrl . 'com_help&page=index'); ?>
            <iframe id="help-page" src="<?php echo $iframeSrc; ?>"></iframe>
        </div>
    </div>
</form>
