<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$this->css('tools');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_ZONES') . ': ' . $text, 'tools');
Toolbar::apply();
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('zone');

Html::behavior('modal');
Html::behavior('switcher', 'submenu');

$this->js('zones.js');
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$hn = Request::host();
$websocketEnabled = $this->row->params->get('websocket_enable');
$vncEnabled       = $this->row->params->get('vnc_enable');
$websocketClass   = 'websocket' . (!$websocketEnabled ? ' opaque' : '');
$vncClass         = 'vnc' . (!$vncEnabled ? ' opaque' : '');
$wsDisabled       = !$websocketEnabled ? ' disabled="disabled"' : '';
$vncDisabled      = !$vncEnabled ? ' disabled="disabled"' : '';
$zoneVal          = $this->escape(stripslashes($this->row->get('zone')));
$titleVal         = $this->escape(stripslashes($this->row->get('title')));
$descVal          = $this->escape(stripslashes($this->row->get('description')));
$masterVal        = $this->escape(stripslashes($this->row->get('master')));
$wsServerDefault  = 'ws://' . $hn . ':8080';
$wsSecureDefault  = 'wss://' . $hn . ':8443';
$vncServerDefault = 'http://' . $hn . ':80';
$vncSecureDefault = 'https://' . $hn . ':80';
$wsServerVal      = $this->escape(
    stripslashes($this->row->params->get('websocket_server', $wsServerDefault))
);
$wsSecureVal      = $this->escape(
    stripslashes($this->row->params->get('websocket_secure_server', $wsSecureDefault))
);
$vncServerVal     = $this->escape(
    stripslashes($this->row->params->get('vnc_server', $vncServerDefault))
);
$vncSecureVal     = $this->escape(
    stripslashes($this->row->params->get('vnc_secure_server', $vncSecureDefault))
);
$selLocal  = ($this->row->get('type') == 'local') ? ' selected="selected"' : '';
$selRemote = ($this->row->get('type') == 'remote') ? ' selected="selected"' : '';
$chkUp    = ($this->row->get('state') == 'up') ? ' checked="checked"' : '';
$chkDown  = ($this->row->get('state') == 'down') ? ' checked="checked"' : '';
$chkWs    = $this->row->params->get('websocket_enable') ? ' checked="checked"' : '';
$chkVnc   = $this->row->params->get('vnc_enable') ? ' checked="checked"' : '';
$ajaxUrl  = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=upload&id=' . $this->row->get('id')
    . '&no_html=1&' . Session::getFormToken() . '=1'
);
$filerUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&tmpl=component&id=' . $this->row->get('id')
);
$deleteImgUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&tmpl=component&task=removefile&id=' . $this->row->get('id')
    . '&' . Session::getFormToken() . '=1'
);
$locIframeUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=locations&tmpl=component&zone=' . $this->row->get('id')
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
    <nav role="navigation" class="sub-navigation">
        <div id="submenu-box">
            <div class="submenu-box">
                <div class="submenu-pad">
                    <ul id="submenu">
                        <li><a href="#page-profile" id="profile" class="active">
                            <?php echo Lang::txt('JDETAILS'); ?></a></li>
                        <li><a href="#page-locations" id="locations">
                            <?php echo Lang::txt('COM_TOOLS_FIELDSET_LOCATIONS'); ?></a></li>
                    </ul>
                    <div class="clr"></div>
                </div>
            </div>
            <div class="clr"></div>
        </div>
    </nav><!-- / .sub-navigation -->

    <div id="zone-document">
        <?php if ($this->getError()) { ?>
            <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
        <?php } ?>
        <div id="page-profile" class="tab">
            <div class="grid">
                <div class="col span7">
                    <fieldset class="adminform">
                        <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                        <input type="hidden" name="fields[id]"
                            value="<?php echo $this->row->get('id'); ?>" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="controller"
                            value="<?php echo $this->controller; ?>" />
                        <input type="hidden" name="task" value="save" />

                        <div class="input-wrap"
                            data-hint="<?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_HINT'); ?>">
                            <label for="field-zone">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_ZONE'); ?>:</label>
                            <input type="text" name="fields[zone]" id="field-zone"
                                maxlength="255" value="<?php echo $zoneVal; ?>" />
                            <span class="hint">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_HINT'); ?></span>
                        </div>

                        <div class="input-wrap">
                            <label for="field-zone">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_TITLE'); ?>:</label>
                            <input type="text" name="fields[title]" id="field-title"
                                maxlength="255" value="<?php echo $titleVal; ?>" />
                        </div>

                        <div class="input-wrap">
                            <label for="field-description">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_DESCRIPTION'); ?>:</label>
                            <textarea name="fields[description]" id="field-description"
                                cols="35" rows="2"><?php echo $descVal; ?></textarea>
                        </div>

                        <div class="input-wrap">
                            <label for="field-master">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_MASTER'); ?>:</label>
                            <input type="text" name="fields[master]" id="field-master"
                                maxlength="255" value="<?php echo $masterVal; ?>" />
                        </div>

                        <div class="input-wrap">
                            <label for="field-type">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_TYPE'); ?>:</label>
                            <select name="fields[type]" id="field-type">
                                <option value="local"<?php echo $selLocal; ?>>
                                    <?php echo Lang::txt('COM_TOOLS_FIELD_TYPE_LOCAL'); ?>
                                </option>
                                <option value="remote"<?php echo $selRemote; ?>>
                                    <?php echo Lang::txt('COM_TOOLS_FIELD_TYPE_REMOTE'); ?>
                                </option>
                            </select>
                        </div>

                        <div class="input-wrap">
                            <?php echo Lang::txt('COM_TOOLS_FIELD_STATE'); ?>:<br />
                            <label for="field-state-up">
                                <input class="option" type="radio" name="fields[state]"
                                    id="field-state-up" size="30" value="up"
                                    <?php echo $chkUp; ?> />
                                <?php echo Lang::txt('COM_TOOLS_FIELD_STATE_UP'); ?>
                            </label>
                            <label for="field-state-down">
                                <input class="option" type="radio" name="fields[state]"
                                    id="field-state-down" size="30" value="down"
                                    <?php echo $chkDown; ?> />
                                <?php echo Lang::txt('COM_TOOLS_FIELD_STATE_DOWN'); ?>
                            </label>
                        </div>
                    </fieldset>
                    <fieldset class="adminform">
                        <legend><span>
                            <?php echo Lang::txt('COM_TOOLS_FIEDSET_ZONES_PARAMS'); ?>
                        </span></legend>

                        <div class="input-wrap">
                            <label for="field-zone-params-websocket-enable">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_WEBSOCKET_PROXY_ENABLE'); ?>:
                            </label>
                            <input type="hidden" name="zoneparams[websocket_enable]" value="0" />
                            <input type="checkbox" name="zoneparams[websocket_enable]"
                                id="field-zone-params-websocket-enable"
                                value="1"<?php echo $chkWs; ?> />
                        </div>
                        <div class="input-wrap">
                            <label for="field-zone-params-websocket-server"
                                class="<?php echo $websocketClass; ?>">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_WEBSOCKET_PROXY_SERVER'); ?>:
                            </label>
                            <input type="text" name="zoneparams[websocket_server]"
                                class="<?php echo $websocketClass; ?>"
                                id="field-zone-params-websocket-server"
                                maxlength="255" value="<?php echo $wsServerVal; ?>"
                                <?php echo $wsDisabled; ?> />
                        </div>
                        <div class="input-wrap">
                            <label for="field-zone-params-websocket-secure-server"
                                class="<?php echo $websocketClass; ?>">
                                <?php
                                echo Lang::txt('COM_TOOLS_FIELD_ZONE_WEBSOCKET_PROXY_SECURE_SERVER');
                                ?>:
                            </label>
                            <input type="text" name="zoneparams[websocket_secure_server]"
                                class="<?php echo $websocketClass; ?>"
                                id="field-zone-params-websocket-secure-server"
                                maxlength="255" value="<?php echo $wsSecureVal; ?>"
                                <?php echo $wsDisabled; ?> />
                        </div>
                        <div class="input-wrap">
                            <label for="field-zone-params-vnc-enable">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_VNC_PROXY_ENABLE'); ?>:
                            </label>
                            <input type="hidden" name="zoneparams[vnc_enable]" value="0" />
                            <input type="checkbox" name="zoneparams[vnc_enable]"
                                id="field-zone-params-vnc-enable"
                                value="1"<?php echo $chkVnc; ?> />
                        </div>
                        <div class="input-wrap">
                            <label for="field-zone-params-vnc-server"
                                class="<?php echo $vncClass; ?>">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_VNC_PROXY_SERVER'); ?>:
                            </label>
                            <input type="text" name="zoneparams[vnc_server]"
                                class="<?php echo $vncClass; ?>"
                                id="field-zone-params-vnc-server"
                                maxlength="255" value="<?php echo $vncServerVal; ?>"
                                <?php echo $vncDisabled; ?> />
                        </div>
                        <div class="input-wrap">
                            <label for="field-zone-params-vnc-secure-server"
                                class="<?php echo $vncClass; ?>">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_ZONE_VNC_PROXY_SECURE_SERVER'); ?>:
                            </label>
                            <input type="text" name="zoneparams[vnc_secure_server]"
                                class="<?php echo $vncClass; ?>"
                                id="field-zone-params-vnc-secure-server"
                                maxlength="255" value="<?php echo $vncSecureVal; ?>"
                                <?php echo $vncDisabled; ?> />
                        </div>
                    </fieldset>
                </div>
                <div class="col span5">
                    <table class="meta">
                        <tbody>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_ID'); ?></th>
                                <td><?php echo $this->escape($this->row->get('id')); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_STATE'); ?></th>
                                <td><?php echo $this->escape($this->row->get('state')); ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <fieldset class="adminform">
                        <legend><span>
                            <?php echo Lang::txt('COM_TOOLS_FIELDSET_IMAGE'); ?>
                        </span></legend>

                        <?php
                        if ($this->row->exists()) {
                            $this->css('fileupload.css')
                                 ->js('jquery.fileuploader.js', 'system');
                            ?>
                            <div id="ajax-uploader"
                                data-action="<?php echo $ajaxUrl; ?>"
                                data-instrucitons="<?php
                                    echo Lang::txt('COM_TOOLS_IMAGE_CLICK_OR_DROP');
                                ?>">
                                <noscript>
                                    <iframe width="100%" height="350" name="filer" id="filer"
                                        frameborder="0"
                                        src="<?php echo $filerUrl; ?>"></iframe>
                                </noscript>
                            </div>
                            <?php
                            $width = 0;
                            $height = 0;
                            $this_size = 0;
                            if ($pic = $this->row->get('picture')) {
                                $path = $this->row->logo('path');

                                $this_size = filesize($path . DS . $pic);
                                list($width, $height, $type, $attr) = getimagesize($path . DS . $pic);
                            } else {
                                $pic  = 'blank.png';
                                $path = '/core/components/com_tools/admin/assets/img';
                            }
                            $imgSrc = '..' . str_replace(PATH_APP, '', $path) . DS . $pic;
                            $imgAlt = Lang::txt('COM_TOOLS_FIELDSET_IMAGE');
                            $imgName = $this->row->get('picture', Lang::txt('COM_TOOLS_IMAGE_NONE'));
                            ?>
                            <table class="formed">
                                <tbody>
                                    <tr>
                                        <td rowspan="6">
                                            <img id="img-display"
                                                src="<?php echo $imgSrc; ?>"
                                                alt="<?php echo $imgAlt; ?>" />
                                        </td>
                                        <td><?php echo Lang::txt('COM_TOOLS_IMAGE_FILE'); ?>:</td>
                                        <td><span id="img-name"><?php echo $imgName; ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo Lang::txt('COM_TOOLS_IMAGE_SIZE'); ?>:</td>
                                        <td><span id="img-size">
                                            <?php echo \Hubzero\Utility\Number::formatBytes($this_size); ?>
                                        </span></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo Lang::txt('COM_TOOLS_IMAGE_WIDTH'); ?>:</td>
                                        <td><span id="img-width"><?php echo $width; ?></span> px</td>
                                    </tr>
                                    <tr>
                                        <td><?php echo Lang::txt('COM_TOOLS_IMAGE_HEIGHT'); ?>:</td>
                                        <td><span id="img-height"><?php echo $height; ?></span> px</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="currentfile"
                                                id="currentfile" value="<?php echo $pic; ?>" />
                                        </td>
                                        <td><a id="img-delete" href="<?php echo $deleteImgUrl; ?>">
                                            [ <?php echo Lang::txt('DELETE'); ?> ]</a></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                        } else {
                            echo '<p class="warning">'
                                . Lang::txt('COM_TOOLS_PICTURE_ADDED_LATER') . '</p>';
                        }
                        ?>
                    </fieldset>
                </div>
            </div>
        </div>

        <div id="page-locations" class="tab">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_TOOLS_FIELDSET_LOCATIONS'); ?></span></legend>
                <?php if ($this->row->get('id')) { ?>
                    <iframe width="100%" height="400" name="locationslist"
                        id="locationslist" frameborder="0"
                        src="<?php echo $locIframeUrl; ?>"></iframe>
                <?php } else { ?>
                    <p><?php echo Lang::txt('COM_TOOLS_LOCATIONS_ADDED_LATER'); ?></p>
                <?php } ?>
            </fieldset>
        </div>
    </div>

    <?php echo Html::input('token'); ?>
</form>
