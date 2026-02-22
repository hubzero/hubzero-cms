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
     ->js('jquery.fancyselect', 'system')
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

// define base link
$base_link = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&task=pages';

// define return link
$return      = Request::getString('return', '');
$return_link = $base_link;
if ($return != '') {
    if (filter_var(base64_decode($return), FILTER_VALIDATE_URL)) {
        $return_link = base64_decode($return);
    }
}

// default group page vars
$id        = $this->page->get('id', '');
$gidNumber = $this->page->get('gidNumber', '');
$category  = $this->page->get('category', '');
$alias     = $this->page->get('alias', '');
$title     = $this->page->get('title', '');
$content   = stripslashes($this->version->get('content', ''));
$version   = $this->version->get('version', 0);
$ordering  = $this->page->get('ordering', null);
$state     = $this->page->get('state', 1);
$privacy   = $this->page->get('privacy', 'default');
$home      = $this->page->get('home', 0);
$parent    = $this->page->get('parent', 0);

// determine comments setting
$groupParams = new \Hubzero\Config\Registry($this->group->get('params'));
$groupCommentSetting = $groupParams->get('page_comments', $this->config->get('page_comments', 3));
$groupCommentSettingString = ($groupCommentSetting == 1) ? 'Yes' : 'No';
$comments  = intval($this->page->get('comments', 3));

// default some form vars
$pageHeading = Lang::txt("COM_GROUPS_PAGES_ADD_PAGE");

// if we are in edit mode
if ($this->page->get('id')) {
    $pageHeading = Lang::txt("COM_GROUPS_PAGES_EDIT_PAGE", $title);
}
?>
<header id="content-header">
    <h2><?php echo $pageHeading; ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <li><a class="icon-prev prev btn" href="<?php echo Route::url($base_link); ?>">
                <?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_PAGES'); ?></a></li>
        </ul>
    </div>
</header>

<section class="main section edit-group-page">
    <?php foreach ($this->notifications as $notification) { ?>
        <p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
    <?php } ?>

    <?php $url = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>
    <form action="<?php echo $url . '&controller=pages&task=save'; ?>" method="POST" id="hubForm" class="full">

        <div class="grid">
            <div class="col span9">
                <fieldset>
                    <legend><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_DETAILS'); ?></legend>
                    <label for="field-title">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_TITLE'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="required"><?php echo $txt; ?></span>
                        <?php $readonly = ($home) ? 'readonly="readonly"' : ''; ?>
                        <input
                            type="text"
                            name="page[title]"
                            id="field-title"
                            value="<?php echo $this->escape(stripslashes($title)); ?>" <?php echo $readonly; ?> />
                    </label>
                    <label for="field-url">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_URL'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt; ?></span>
                        <input
                            type="text"
                            name="page[alias]"
                            id="field-url"
                            value="<?php echo $this->escape($alias); ?>" <?php echo $readonly; ?> />
                        <span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_URL_HINT'); ?></span>
                    </label>
                    <label for="pagecontent">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_CONTENT'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="required"><?php echo $txt; ?></span>
                        <?php
                            $allowPhp      = true;
                            $allowScripts  = true;
                            $startupMode   = 'wysiwyg';
                            $showSourceBtn = true;

                            // only allow super groups to use php & scrips
                            // strip out php and scripts if somehow it made it through
                        if (!is_object($this->group->params)) {
                            $this->group->params = new \Hubzero\Config\Registry($this->group->params);
                        }
                        if (!$this->group->params->get('page_trusted', 0)) {
                            if (!$this->group->isSuperGroup()) {
                                $allowPhp     = false;
                                $allowScripts = false;
                                $content      = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
                                $content      = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
                            }
                        }

                            // open in source mode if contains php or scripts
                        if (
                            strstr(stripslashes($content), '<script>') ||
                                strstr(stripslashes($content), '<?php')
                        ) {
                            $startupMode  = 'source';
                            //$showSourceBtn = false;
                        }

                            //build config
                            $config = array(
                                'startupMode'                 => $startupMode,
                                'sourceViewButton'            => $showSourceBtn,
                                'contentCss'                  => $this->stylesheets,
                                //'autoGrowMinHeight'           => 500,
                                'height'                      => '500px',
                                'fileBrowserWindowWidth'      => 1200,
                                'fileBrowserBrowseUrl'        => Route::url(
                                    'index.php?option=com_groups&cn=' .
                                    $this->group->get('cn') .
                                    '&controller=media&task=filebrowser&tmpl=component&' .
                                    Session::getFormToken() . '=1',
                                    false
                                ),
                                'fileBrowserImageBrowseUrl'   => Route::url(
                                    'index.php?option=com_groups&cn=' .
                                    $this->group->get('cn') .
                                    '&controller=media&task=filebrowser&tmpl=component&' .
                                    Session::getFormToken() . '=1',
                                    false
                                ),
                                'fileBrowserUploadUrl'        => Route::url(
                                    'index.php?option=com_groups&cn=' .
                                    $this->group->get('cn') .
                                    '&controller=media&task=ckeditorupload&tmpl=component&' .
                                    Session::getFormToken() . '=1',
                                    false
                                ),
                                'allowPhpTags'                => $allowPhp,
                                'allowScriptTags'             => $allowScripts
                            );

                            // if super group add to templates
                            if ($this->group->isSuperGroup()) {
                                $config['templates_replace'] = false;
                                $config['templates_files']   = array('pagelayouts' => '/app/site/groups/' .
                                    $this->group->get('gidNumber') .
                                    '/template/assets/js/pagelayouts.js');
                            }

                            // display with ckeditor
                            $editor = App::get('editor'); //new \Hubzero\Html\Editor('ckeditor');
                            echo $editor->display(
                                'pageversion[content]',
                                $this->escape($content),
                                '100%',
                                '400',
                                0,
                                0,
                                false,
                                'pagecontent',
                                null,
                                null,
                                $config
                            );
                            ?>

                    </label>
                </fieldset>
            </div>
            <div class="col span3 omega">
                <fieldset>
                    <legend><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PUBLISH'); ?></legend>
                    <label>
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="required"><?php echo $txt; ?></span>
                        <select name="page[state]" class="fancy-select" <?php echo $readonly; ?>>
                            <?php $pubTxt = Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS_PUBLISHED'); ?>
                            <?php $unpubTxt = Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS_UNPUBLISHED'); ?>
                            <option value="1" <?php if ($state == 1) {
                                echo "selected";
                                              } ?>><?php echo $pubTxt; ?></option>
                            <option value="0" <?php if ($state == 0) {
                                echo "selected";
                                              } ?>><?php echo $unpubTxt; ?></option>
                        </select>
                    </label>

                    <label>
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="required"><?php echo $txt; ?></span>
                        <?php
                            $access = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'overview');
                        switch ($access) {
                            case 'anyone':
                                $name = Lang::txt('COM_GROUPS_PLUGIN_ANYONE');
                                break;
                            case 'registered':
                                $name = Lang::txt('COM_GROUPS_PLUGIN_REGISTERED');
                                break;
                            case 'members':
                                $name = Lang::txt('COM_GROUPS_PLUGIN_MEMBERS');
                                break;
                        }
                        ?>
                        <?php $inheritTxt = Lang::txt(
                            'COM_GROUPS_PAGES_PAGE_PRIVACY_INHERIT',
                            $name
                        ); ?>
                        <?php $privateTxt = Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY_PRIVATE'); ?>
                        <select name="page[privacy]" class="fancy-select">
                            <option value="default" <?php if ($privacy == "default") {
                                echo 'selected="selected"';
                                                    } ?>><?php echo $inheritTxt; ?></option>
                            <option value="members" <?php if ($privacy == "members") {
                                echo 'selected="selected"';
                                                    } ?>><?php echo $privateTxt; ?></option>
                        </select>
                    </label>

                    <?php if ($this->page->get('id')) : ?>
                        <label>
                            <strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSIONS'); ?>:</strong> <br />
                            <?php $val = Route::url(
                                'index.php?option=com_groups&cn=' .
                                $this->group->get('cn') .
                                '&controller=pages&task=versions&pageid=' .
                                $this->page->get('id')
                            ); ?>
                            <a class="btn icon-history" href="<?php echo $val; ?>">
                                <?php $txt = Lang::txt(
                                    'COM_GROUPS_PAGES_PAGE_VERSIONS_BROWSE',
                                    $this->page->versions()->count()
                                ); ?>
                                <?php echo $txt; ?>
                            </a>
                        </label>
                    <?php endif; ?>
                </fieldset>

                <div class="form-controls cf">
                    <?php $url = Route::url($return_link); ?>
                    <a href="<?php echo $url; ?>" class="cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
                    <div class="btn-group save">
                        <?php $txt = Lang::txt('COM_GROUPS_PAGES_SAVE_PAGE'); ?>
                        <button type="submit" class="btn btn-info btn-main icon-save"><?php echo $txt; ?></button>
                        <span class="btn dropdown-toggle btn-info"></span>
                        <ul class="dropdown-menu">
                            <li><a class="icon-save active" data-action="save" href="javascript:void(0);">
                                <?php echo Lang::txt('COM_GROUPS_PAGES_SAVE_PAGE'); ?></a></li>
                            <li><a class="icon-apply" data-action="apply" href="javascript:void(0);">
                                <?php echo Lang::txt('COM_GROUPS_PAGES_APPLY_PAGE'); ?></a></li>
                        </ul>
                    </div>
                </div>

                <fieldset>
                    <legend><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_SETTINGS'); ?></legend>

                    <label for="page-category" class="page-category-label">
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt; ?></span>
                        <?php $val = Route::url(
                            'index.php?option=com_groups&cn=' .
                            $this->group->get('gidNumber') .
                            '&controller=categories&task=add&no_html=1'
                        ); ?>
                        <select name="page[category]" class="page-category" data-url="<?php echo $val; ?>">
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_OPTION_NULL'); ?>
                            <option value=""><?php echo $txt; ?></option>
                            <?php foreach ($this->categories as $pageCategory) : ?>
                                <?php $sel = ($category == $pageCategory->get('id')) ? 'selected="selected"' : ''; ?>
                                <option
                                    <?php echo $sel; ?>
                                    data-color="#<?php echo $pageCategory->get('color'); ?>"
                                    value="<?php echo $pageCategory->get('id'); ?>"><?php echo $pageCategory->
                                    get('title'); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php $txt = Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_OPTION_OTHER'); ?>
                            <option value="other"><?php echo $txt; ?></a>
                        </select>
                        <span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_HINT'); ?></span>
                    </label>

                    <?php if ($this->page->get('home') == 0) : ?>
                        <label for="page-parent" class="page-parent-label">
                            <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_PARENT'); ?>
                            <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                            <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt; ?></span>
                            <select name="page[parent]" class="page-parent">
                                <?php foreach ($this->pages as $page) : ?>
                                    <?php if ($page->get('id') == $id) {
                                        continue;
                                    } ?>
                                    <?php $sel = ($parent == $page->get('id')) ? 'selected="selected"' : ''; ?>
                                    <option <?php echo $sel; ?> value="<?php echo $page->get('id'); ?>">
                                        <?php echo $page->heirarchyIndicator(' &ndash; ') . $page->get('title'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PARENT_HINT'); ?></span>
                        </label>
                    <?php endif; ?>

                    <?php if ($this->page->get('id') && $this->page->get('home') == 0) : ?>
                        <label for="page-ordering">
                            <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_ORDER'); ?>
                            <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                            <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt; ?></span>
                            <select name="page[left]" class="page-ordering fancy-select">
                                <?php foreach ($this->pages as $page) : ?>
                                    <?php $sel = ($page->get('title') == $title) ? 'selected="selected"' : ''; ?>
                                    <option
                                        <?php echo $sel; ?>
                                        data-parent="<?php echo $page->get('parent'); ?>"
                                        value="<?php echo $page->get('lft'); ?>">
                                        <?php echo $page->get('lft') . ' ' . $page->get('title'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_ORDER_HINT'); ?></span>
                        </label>
                    <?php endif; ?>

                    <hr class="divider" />

                    <label>
                        <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS'); ?>
                        <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                        <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt; ?></span>
                        <?php $inheritTxt = Lang::txt(
                            'COM_GROUPS_PAGES_PAGE_COMMENTS_INHERIT',
                            $groupCommentSettingString
                        ); ?>
                        <?php $noTxt = Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO'); ?>
                        <?php $yesTxt = Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES'); ?>
                        <?php $lockTxt = Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK'); ?>
                        <select name="page[comments]" class="fancy-select">
                            <option value="3" <?php if ($comments === 3) {
                                echo "selected";
                                              } ?>><?php echo $inheritTxt; ?></option>
                            <option value="0" <?php if ($comments === 0) {
                                echo "selected";
                                              } ?>><?php echo $noTxt; ?></option>
                            <option value="1" <?php if ($comments === 1) {
                                echo "selected";
                                              } ?>><?php echo $yesTxt; ?></option>
                            <option value="2" <?php if ($comments === 2) {
                                echo "selected";
                                              } ?>><?php echo $lockTxt; ?></option>
                        </select>
                        <span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_HINT'); ?></span>
                    </label>

                    <?php if ($this->group->isSuperGroup() && count($this->pageTemplates) > 0) : ?>
                        <hr class="divider" />

                        <label for="page-template">
                            <?php $txt1 = Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE'); ?>
                            <?php $txt = Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?>
                            <strong><?php echo $txt1; ?>:</strong> <span class="optional"><?php echo $txt; ?></span>
                            <select name="page[template]" class="fancy-select">
                                <?php $txt = Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE_OPTION_NULL'); ?>
                                <option value=""><?php echo $txt; ?></option>
                                <?php foreach ($this->pageTemplates as $name => $file) : ?>
                                    <?php
                                        $tmpl = str_replace('.php', '', $file);
                                        $sel  = ($this->page->get('template') == $tmpl) ? 'selected="selected"' : ''; ?>
                                    <?php $v2 = $sel; ?>
                                    <?php $v1 = $tmpl; ?>
                                    <?php $v0 = $name; ?>
                                    <option <?php echo $v2; ?> value="<?php echo $v1; ?>"><?php echo $v0; ?></option>
                                <?php endforeach;?>
                            </select>
                            <span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE_HINT'); ?></span>
                        </label>
                    <?php endif; ?>
                </fieldset>
            </div>
        </div>

        <?php echo Html::input('token'); ?>

        <input type="hidden" name="page[id]" value="<?php echo $id; ?>" />
        <input type="hidden" name="option" value="com_groups" />
        <input type="hidden" name="controller" value="pages" />
        <input
            type="hidden"
            name="return"
            value="<?php echo $this->escape(Request::getString('return', '', 'get')); ?>" />
        <input type="hidden" name="task" value="save" />
    </form>
</section>