<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Collections\Helpers\Permissions::getActions('post');

$text = ($this->task == 'edit'
    ? Lang::txt('JACTION_EDIT')
    : Lang::txt('JACTION_CREATE'));

$dir = $this->row->get('id');
if (!$dir) {
    $dir = 'tmp' . time(); // . rand(0, 100);
}

Toolbar::title(
    Lang::txt('COM_COLLECTIONS') . ': '
    . Lang::txt('COM_COLLECTIONS_ITEMS') . ': ' . $text,
    'collection'
);
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('collection');

Html::behavior('switcher', 'submenu');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->css()
     ->js('jquery.fileuploader.js', 'system')
     ->js();

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$validationMsg = $this->escape(
    Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')
);
?>

<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    class="editform form-validate"
    id="item-form"
    data-invalid-msg="<?php echo $validationMsg; ?>">
<?php if ($this->row->get('id')) { ?>
    <nav role="navigation" class="sub-navigation">
        <ul id="submenu" class="item-nav">
            <li>
                <a href="#page-idetails"
                    id="idetails"
                    class="active">
                    <?php echo Lang::txt('JDETAILS'); ?>
                </a>
            </li>
            <li>
                <a href="#page-iposts" id="iposts">
                    <?php echo Lang::txt('COM_COLLECTIONS_POSTS'); ?>
                </a>
            </li>
        </ul>
    </nav><!-- / .sub-navigation -->

    <div id="item-document">
        <div id="page-idetails" class="tab">
<?php } ?>

            <div class="grid">
                <div class="col span7">
                    <fieldset class="adminform">
                        <legend>
                            <span><?php echo Lang::txt('JDETAILS'); ?></span>
                        </legend>

                        <div class="input-wrap">
                            <?php
                            $titleLabel = Lang::txt('COM_COLLECTIONS_FIELD_TITLE');
                            $titleVal = $this->escape(
                                stripslashes($this->row->get('title'))
                            );
                            ?>
                            <label for="field-title">
                                <?php echo $titleLabel; ?>:
                            </label><br />
                            <input type="text"
                                name="fields[title]"
                                id="field-title"
                                maxlength="255"
                                value="<?php echo $titleVal; ?>" />
                        </div>

                        <div class="input-wrap">
                            <?php
                            $urlLabel = Lang::txt('COM_COLLECTIONS_FIELD_URL');
                            $urlVal = $this->escape(
                                stripslashes($this->row->get('url'))
                            );
                            ?>
                            <label for="field-url">
                                <?php echo $urlLabel; ?>:
                            </label><br />
                            <input type="text"
                                name="fields[url]"
                                id="field-url"
                                maxlength="255"
                                value="<?php echo $urlVal; ?>" />
                        </div>

                        <div class="input-wrap">
                            <?php
                            $descLabel = Lang::txt(
                                'COM_COLLECTIONS_FIELD_DESCRIPTION'
                            );
                            ?>
                            <label for="field-description">
                                <?php echo $descLabel; ?>
                            </label><br />
                            <?php
                            echo $this->editor(
                                'fields[description]',
                                $this->escape($this->row->get('description')),
                                35,
                                10,
                                'field-description',
                                array(
                                    'class' => 'minimal no-footer',
                                    'buttons' => false,
                                )
                            );
                            ?>
                        </div>

                        <?php
                        $tagsHint = Lang::txt(
                            'COM_COLLECTIONS_FIELD_TAGS_HINT'
                        );
                        $tagsLabel = Lang::txt(
                            'COM_COLLECTIONS_FIELD_TAGS'
                        );
                        $tagsVal = $this->escape(
                            stripslashes($this->row->tags('string'))
                        );
                        ?>
                        <div class="input-wrap"
                            data-hint="<?php echo $tagsHint; ?>">
                            <label for="field-tags">
                                <?php echo $tagsLabel; ?>:
                            </label><br />
                            <input type="text"
                                name="tags"
                                id="field-tags"
                                value="<?php echo $tagsVal; ?>" />
                            <span class="hint">
                                <?php echo $tagsHint; ?>
                            </span>
                        </div>
                    </fieldset>

                    <fieldset class="adminform">
                        <div class="input-wrap">
                            <div class="asset-uploader">
                                <div class="grid">
                                    <div class="col span6">
                                        <?php
                                        $clickTxt = Lang::txt(
                                            'COM_COLLECTIONS_CLICK_OR_DROP_FILE'
                                        );
                                        $uploadUrl = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&no_html=1&controller=media&task=upload'
                                        );
                                        $listUrl = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&no_html=1&controller=media&task=list&dir='
                                        );
                                        ?>
                                        <div id="ajax-uploader"
                                            data-txt-instructions="<?php echo $clickTxt; ?>"
                                            data-action="<?php echo $uploadUrl; ?>"
                                            data-list="<?php echo $listUrl; ?>">
                                            <noscript>
                                                <label for="upload">
                                                    <?php echo Lang::txt('COM_COLLECTIONS_FIELD_FILE'); ?>
                                                </label>
                                                <input type="file"
                                                    name="upload"
                                                    id="field-upload" />
                                            </noscript>
                                        </div>
                                    </div>
                                    <div class="col span6">
                                        <?php
                                        $deleteTxt = Lang::txt('JACTION_DELETE');
                                        $addLinkTxt = Lang::txt(
                                            'COM_COLLECTIONS_CLICK_TO_ADD_LINK'
                                        );
                                        $deleteUrl = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&no_html=1&controller=media&task=delete&dir='
                                        );
                                        $createUrl = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&no_html=1&controller=media&task=create&dir='
                                        );
                                        ?>
                                        <div id="link-adder"
                                            data-txt-delete="<?php echo $deleteTxt; ?>"
                                            data-txt-instructions="<?php echo $addLinkTxt; ?>"
                                            data-base="<?php echo $deleteUrl; ?>"
                                            data-action="<?php echo $createUrl; ?>"
                                            data-list="<?php echo $listUrl; ?>">
                                            <noscript>
                                                <label for="add-link">
                                                    <?php echo Lang::txt('COM_COLLECTIONS_FIELD_LINK'); ?>
                                                </label>
                                                <input type="text"
                                                    name="assets[-1][filename]"
                                                    id="add-link"
                                                    value="http://" />
                                                <input type="hidden"
                                                    name="assets[-1][id]"
                                                    value="0" />
                                                <input type="hidden"
                                                    name="assets[-1][type]"
                                                    value="link" />
                                            </noscript>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- / .asset-uploader -->

                            <div id="ajax-uploader-list">
                                <?php
                                $assets = $this->row->assets()->rows();

                                if ($assets->count() > 0) {
                                    $i = 0;
                                    foreach ($assets as $asset) {
                                        $this->view('_asset', 'media')
                                             ->set('i', $i)
                                             ->set('option', $this->option)
                                             ->set('controller', $this->controller)
                                             ->set('asset', $asset)
                                             ->set('no_html', 1)
                                             ->display();

                                        $i++;
                                    }
                                }
                                ?>
                            </div><!-- / .field-wrap -->
                        </div>
                    </fieldset>
                </div>
                <div class="col span5">
                    <table class="meta">
                        <tbody>
                            <?php if (!$this->row->isNew()) { ?>
                                <tr>
                                    <th>
                                        <?php echo Lang::txt('COM_COLLECTIONS_FIELD_ID'); ?>:
                                    </th>
                                    <td>
                                        <?php echo $this->row->get('id'); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th>
                                    <?php echo Lang::txt('COM_COLLECTIONS_FIELD_TYPE'); ?>:
                                </th>
                                <td>
                                    <?php echo $this->row->get('type', 'file'); ?>
                                    <?php
                                    $typeVal = $this->escape(
                                        $this->row->get('type', 'file')
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="fields[type]"
                                        id="field-type"
                                        class="required"
                                        value="<?php echo $typeVal; ?>" />
                                </td>
                            </tr>
                            <?php if ($object_id = $this->row->get('object_id')) { ?>
                                <tr>
                                    <th>
                                        <?php echo Lang::txt('COM_COLLECTIONS_FIELD_OBJECT_ID'); ?>:
                                    </th>
                                    <td>
                                        <?php echo $object_id; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th>
                                    <?php echo Lang::txt('COM_COLLECTIONS_FIELD_CREATOR'); ?>:
                                </th>
                                <td>
                                    <?php
                                    $editor = User::getInstance(
                                        $this->row->get('created_by')
                                    );
                                    echo $this->escape(
                                        stripslashes($editor->get('name'))
                                    );
                                    $createdByVal = $this->escape(
                                        $this->row->get('created_by')
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="fields[created_by]"
                                        id="field-created_by"
                                        value="<?php echo $createdByVal; ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <?php echo Lang::txt('COM_COLLECTIONS_FIELD_CREATED'); ?>:
                                </th>
                                <td>
                                    <?php echo $this->row->get('created'); ?>
                                    <?php
                                    $createdVal = $this->escape(
                                        $this->row->get('created')
                                    );
                                    ?>
                                    <input type="hidden"
                                        name="fields[created]"
                                        id="field-created"
                                        value="<?php echo $createdVal; ?>" />
                                </td>
                            </tr>
                            <?php if ($this->row->get('modified_by')) { ?>
                                <tr>
                                    <th>
                                        <?php echo Lang::txt('COM_COLLECTIONS_FIELD_MODIFIER'); ?>:
                                    </th>
                                    <td>
                                        <?php
                                        $modifier = User::getInstance(
                                            $this->row->get('modified_by')
                                        );
                                        echo $this->escape(
                                            stripslashes($modifier->get('name'))
                                        );
                                        $modByVal = $this->escape(
                                            $this->row->get('modified_by')
                                        );
                                        ?>
                                        <input type="hidden"
                                            name="fields[modified_by]"
                                            id="field-modified_by"
                                            value="<?php echo $modByVal; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo Lang::txt('COM_COLLECTIONS_FIELD_MODIFIED'); ?>:
                                    </th>
                                    <td>
                                        <?php echo $this->row->get('modified'); ?>
                                        <?php
                                        $modVal = $this->escape(
                                            $this->row->get('modified')
                                        );
                                        ?>
                                        <input type="hidden"
                                            name="fields[modified]"
                                            id="field-modified"
                                            value="<?php echo $modVal; ?>" />
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <fieldset class="adminform">
                        <legend>
                            <span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span>
                        </legend>

                        <div class="input-wrap">
                            <?php
                            $stateLabel = Lang::txt(
                                'COM_COLLECTIONS_FIELD_STATE'
                            );
                            ?>
                            <label for="field-state">
                                <?php echo $stateLabel; ?>:
                            </label><br />
                            <select name="fields[state]" id="field-state">
                                <option value="0"<?php if ($this->row->get('state') == 0) {
                                    echo ' selected="selected"';
                                                 } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
                                <option value="1"<?php if ($this->row->get('state') == 1) {
                                    echo ' selected="selected"';
                                                 } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
                                <option value="2"<?php if ($this->row->get('state') == 2) {
                                    echo ' selected="selected"';
                                                 } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
                            </select>
                        </div>

                        <div class="input-wrap">
                            <?php
                            $accessLabel = Lang::txt(
                                'COM_COLLECTIONS_FIELD_ACCESS'
                            );
                            $publicTxt = Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC');
                            $regTxt = Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED');
                            $privTxt = Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE');
                            ?>
                            <label for="field-access">
                                <?php echo $accessLabel; ?>:
                            </label><br />
                            <select name="fields[access]" id="field-access">
                                <option value="0"<?php if ($this->row->get('access') == 0) {
                                    echo ' selected="selected"';
                                                 } ?>><?php echo $publicTxt; ?></option>
                                <option value="1"<?php if ($this->row->get('access') == 1) {
                                    echo ' selected="selected"';
                                                 } ?>><?php echo $regTxt; ?></option>
                                <option value="4"<?php if ($this->row->get('access') == 4) {
                                    echo ' selected="selected"';
                                                 } ?>><?php echo $privTxt; ?></option>
                            </select>
                        </div>
                    </fieldset>
                </div>
            </div>

<?php if ($this->row->get('id')) { ?>
        </div>
        <div id="page-iposts" class="tab">
            <fieldset class="adminform">
                <legend>
                    <span><?php echo Lang::txt('COM_COLLECTIONS_POSTS'); ?></span>
                </legend>

                <?php
                $iframeSrc = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=posts&tmpl=component&item_id='
                    . $this->row->get('id') . '&t=' . time()
                );
                ?>
                <iframe height="500"
                    name="grouper"
                    id="grouper"
                    src="<?php echo $iframeSrc; ?>"></iframe>
            </fieldset>
        </div>
    </div>
<?php } ?>

    <input type="hidden"
        name="fields[id]"
        id="field-id"
        value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden"
        name="dir"
        id="field-dir"
        value="<?php echo $dir; ?>" />
    <?php
    $objectIdVal = $this->escape($this->row->get('object_id'));
    ?>
    <input type="hidden"
        name="fields[object_id]"
        value="<?php echo $objectIdVal; ?>" />
    <input type="hidden"
        name="option"
        value="<?php echo $this->option; ?>" />
    <input type="hidden"
        name="controller"
        value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
