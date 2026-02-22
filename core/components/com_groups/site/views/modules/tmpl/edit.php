<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// add styles & scripts
$this->css()
     ->js()
     ->css('jquery.fancyselect.css', 'system')
     ->js('jquery.fancyselect', 'system');

// define base link
$base_link = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&task=pages#modules';

// get module menus
$menus = $this->module->menu('list');
$activeMenu = (!$this->module->get('id')) ? array(0) : array();
foreach ($menus as $menu) {
    $activeMenu[] = $menu->get('pageid');
}
?>
<header id="content-header">
    <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_EDIT_MODULE'); ?>
    <?php $txt = Lang::txt('COM_GROUPS_PAGES_ADD_MODULE'); ?>
    <h2><?php echo ($this->module->get('id')) ? $txt1 : $txt; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <li><a class="icon-prev prev btn" href="<?php echo Route::url($base_link); ?>">
                <?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_MODULES'); ?>
            </a></li>
        </ul>
    </div>
</header>

<section class="main section edit-group-module">
    <?php foreach ($this->notifications as $notification) { ?>
        <p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
    <?php } ?>

    <?php $url = Route::url(
        'index.php?option=com_groups&cn=' .
        $this->group->get('cn') .
        '&controller=modules&task=save'
    ); ?>
    <form action="<?php echo $url; ?>" method="post" id="hubForm" class="full">
        <div class="grid">
            <div class="col span9">
                <fieldset>
                    <legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_DETAILS'); ?></legend>

                    <label for="field-title">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_MODULE_TITLE'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="required"><?php echo $txt; ?></span>
                        <input
                            type="text"
                            name="module[title]"
                            id="field-title"
                            value="<?php echo $this->escape(stripslashes($this->module->get('title'))); ?>" />
                    </label>
                    <label for="field-content">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_MODULE_CONTENT'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="required"><?php echo $txt; ?></span>
                        <?php
                            $allowPhp      = true;
                            $allowScripts  = true;
                            $startupMode   = 'wysiwyg';
                            $showSourceBtn = true;

                            // only allow super groups to use php & scrips
                            // strip out php and scripts if somehow it made it through
                        if (!$this->group->isSuperGroup()) {
                            $allowPhp     = false;
                            $allowScripts = false;
                            $content      = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $this->module->
                                get('content'));
                            $content      = preg_replace('/<\?[\s\S]*?\?>/', '', $this->module->get('content'));
                        }

                            // open in source mode if contains php or scripts
                        if (
                            strstr(stripslashes($this->module->get('content')), '<script>') ||
                                strstr(stripslashes($this->module->get('content')), '<?php')
                        ) {
                            $startupMode  = 'source';
                            //$showSourceBtn = false;
                        }

                            //build config
                            $config = array(
                                'startupMode'                 => $startupMode,
                                'sourceViewButton'            => $showSourceBtn,
                                'contentCss'                  => $this->stylesheets,
                                'fileBrowserWindowWidth'      => 1200,
                                'fileBrowserBrowseUrl'        => Route::url('index.php?option=com_groups&cn=' . $this->
                                    group->get('cn') . '&controller=media&task=filebrowser&tmpl=component'),
                                'fileBrowserImageBrowseUrl'   => Route::url('index.php?option=com_groups&cn=' . $this->
                                    group->get('cn') . '&controller=media&task=filebrowser&tmpl=component'),
                                'allowPhpTags'                => $allowPhp,
                                'allowScriptTags'             => $allowScripts
                            );

                            // if super group add to templates
                            if ($this->group->isSuperGroup()) {
                                $config['templates_replace'] = false;
                                $config['templates_files'] = array(
                                    'pagelayouts' => substr(PATH_APP, strlen(PATH_ROOT)) .
                                        '/site/groups/' . $this->group->get('gidNumber') .
                                        '/template/assets/js/pagelayouts.js'
                                );
                            }

                            // display with ckeditor
                            $editor = new \Hubzero\Html\Editor('ckeditor');
                            echo $editor->display(
                                'module[content]',
                                stripslashes($this->module->get('content')),
                                '100%',
                                '100px',
                                0,
                                0,
                                false,
                                'field-content',
                                null,
                                null,
                                $config
                            );
                            ?>
                    </label>
                </fieldset>

                <fieldset>
                    <legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_MENU_ASSIGNMENT'); ?></legend>
                    <label for="field-assignment">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="required"><?php echo $txt; ?></span>
                        <select name="menu[assignment]" id="field-assignment" class="fancy-select">
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_ALL'); ?>
                            <option value="0"><?php echo $txt; ?></option>
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_SELECTED'); ?>
                            <option <?php if (!in_array(0, $activeMenu)) {
                                echo 'selected="selected"';
                                    } ?> value=""><?php echo $txt; ?></option>
                        </select>
                    </label>

                    <?php $v1 = Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION'); ?>
                    <?php $v0 = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                    <label for="field-assignment-menu"><strong><?php echo $v1; ?>:</strong> <span class="optional">
                        <?php echo $v0; ?></span></label>
                    <fieldset class="assignment" <?php if (in_array(0, $activeMenu)) :
                        ?>disabled="disabled"<?php
                                                 endif; ?>>
                        <label>
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION_ALL'); ?>
                            <button id="selectall"><?php echo $txt; ?></button>
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION_CLEAR'); ?>
                            <button id="clearselection"><?php echo $txt; ?></button>
                        </label>
                        <?php foreach ($this->pages as $page) : ?>
                            <label>
                                <?php
                                $ckd = (in_array($page->get('id'), $activeMenu) || in_array(0, $activeMenu))
                                    ? 'checked="checked"' : '';
                                ?>
                                <input
                                    type="checkbox"
                                    class="option"
                                    <?php echo $ckd; ?>
                                    name="menu[assigned][]"
                                    value="<?php echo $page->get('id'); ?>" /> <?php echo $page->get('title'); ?>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                </fieldset>
            </div>
            <div class="col span3 omega">
                <fieldset>
                    <legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_PUBLISH'); ?></legend>

                    <label for="field-state">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt?></span>
                        <select name="module[state]" id="field-state" class="fancy-select">
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS_PUBLISHED'); ?>
                            <option value="1"><?php echo $txt; ?></option>
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS_UNPUBLISHED'); ?>
                            <option value="0"><?php echo $txt; ?></option>
                        </select>
                    </label>
                </fieldset>
                <div class="form-controls cf">
                    <a href="<?php echo $base_link; ?>" class="cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
                    <?php $txt = Lang::txt('COM_GROUPS_PAGES_SAVE_MODULE'); ?>
                    <button type="submit" class="btn btn-info opposite save icon-save"><?php echo $txt; ?></button>
                </div>

                <fieldset>
                    <legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_SETTINGS'); ?></legend>
                    <label for="field-position">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_MODULE_POSITION'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt?></span>
                        <input
                            type="text"
                            name="module[position]"
                            id="field-position"
                            value="<?php echo $this->escape(stripslashes($this->module->get('position'))); ?>" />
                    </label>
                    <?php if ($this->module->get('id')) : ?>
                        <label for="field-ordering">
                            <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_MODULE_ORDERING'); ?>
                            <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                            <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt?></span>
                            <select name="module[ordering]" id="field-ordering" class="fancy-select">
                                <?php foreach ($this->order as $k => $order) : ?>
                                    <?php
                                    $sel = ($order->get('title') == $this->module->get('title'))
                                        ? 'selected="selected"' : '';
                                    ?>
                                    <option <?php echo $sel;?> value="<?php echo ($k + 1); ?>"><?php echo ($k + 1) .
                                        '. ' .
                                        $order->get('title'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    <?php endif;?>
                </fieldset>
            </div>
        </div>

        <input type="hidden" name="module[id]" value="<?php echo $this->module->get('id'); ?>" />
        <input type="hidden" name="option" value="com_groups" />
        <input type="hidden" name="controller" value="modules" />
        <input
            type="hidden"
            name="return"
            value="<?php echo $this->escape(Request::getString('return', '', 'get')); ?>" />
        <input type="hidden" name="task" value="save" />
    </form>
</section>